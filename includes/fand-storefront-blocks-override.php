<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Page modèle à utiliser. Le plugin ne gère que le modèle Boutique ; un add-on (Fand Pickup Points,
 * par exemple) fournit un autre modèle pour ses propres pages (branches, événements...) avec le filtre
 * fand_storefront_template_page_id, qui reçoit l'ID de la page Boutique et le contexte courant.
 */
function fand_get_store_template_page_id() {
    $ctx     = fand_get_store_context();
    $page_id = get_option( 'fand_custom_store_page_id' );

    return absint( apply_filters( 'fand_storefront_template_page_id', $page_id, $ctx ) );
}

/**
 * Vrai sur une page boutique WCFM, ou sur une page déclarée par un autre plugin.
 */
function fand_is_store_context() {
    if ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() ) {
        return true;
    }
    return (bool) apply_filters( 'fand_storefront_is_store_context', false );
}

/**
 * 1. Réglages > Lecture : la page modèle Boutique. Un add-on peut ajouter ses propres champs avec le
 * filtre fand_storefront_template_page_settings (clé = nom de l'option, valeur = label / none / help).
 * Le groupe doit être 'reading', sinon la valeur ne s'enregistre pas sur cette page.
 */
function fand_get_template_page_settings() {
    $fields = array(
        'fand_custom_store_page_id' => array(
            'label' => __( 'Store template page (Fand Vendor Store Blocks)', 'fand-vendor-store-blocks-for-wcfm' ),
            'none'  => __( '-- Use the default WCFM template --', 'fand-vendor-store-blocks-for-wcfm' ),
            'help'  => __( 'Page used as the template to display WCFM stores.', 'fand-vendor-store-blocks-for-wcfm' ),
        ),
    );

    return apply_filters( 'fand_storefront_template_page_settings', $fields );
}

add_action( 'admin_init', function() {
    foreach ( fand_get_template_page_settings() as $option => $field ) {
        register_setting( 'reading', $option, array( 'sanitize_callback' => 'absint' ) );
        add_settings_field(
            $option,
            $field['label'],
            function() use ( $option, $field ) {
                wp_dropdown_pages( array(
                    'name'             => esc_attr( $option ),
                    'selected'         => absint( get_option( $option ) ),
                    'show_option_none' => esc_html( $field['none'] ),
                ) );
                echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
            },
            'reading',
            'default'
        );
    }
} );

/**
 * Boutique sans produit : la requête principale (archive des produits du vendeur) est vide.
 * Le thème n'entre alors jamais dans la boucle et affiche « aucun contenu » : le contenu de la
 * page modèle (qui est injecté via the_content) n'est donc jamais rendu.
 * On place la page modèle dans la requête pour que la boucle s'exécute une fois.
 */
function fand_ensure_store_loop_has_post( $template_page_id ) {
    global $wp_query;

    if ( ! $wp_query instanceof WP_Query || $wp_query->post_count > 0 ) {
        return;
    }

    $template_post = get_post( $template_page_id );
    if ( ! $template_post ) {
        return;
    }

    $wp_query->posts         = array( $template_post );
    $wp_query->post          = $template_post;
    $wp_query->post_count    = 1;
    $wp_query->found_posts   = 1;
    $wp_query->max_num_pages = 1;
    $wp_query->current_post  = -1;
    $wp_query->is_404        = false;

    status_header( 200 );
}

/**
 * 2. Intercepter le template WCFM pour charger page.php
 */
add_filter( 'template_include', function( $template ) {
    if ( fand_is_store_context() ) {
        $custom_page_id = fand_get_store_template_page_id();
        if ( $custom_page_id ) {
            $page_template = locate_template( array( 'page.php', 'index.php' ) );
            if ( $page_template ) {
                fand_ensure_store_loop_has_post( $custom_page_id );
                return $page_template;
            }
        }
    }
    return $template;
}, 999 );

/**
 * 3. Remplacement strict et unique du contenu de la boutique WCFM
 */
function fand_override_wcfm_store_content( $content ) {
    if ( fand_is_store_context() ) {
        if ( in_the_loop() && is_main_query() ) {

            // Drapeau statique : garantit que le rendu s'exécute UNE SEULE FOIS par page
            static $already_rendered = false;

            if ( $already_rendered ) {
                return ''; // Renvoie vide pour les requêtes secondaires répétées
            }

            $custom_page_id = fand_get_store_template_page_id();
            if ( $custom_page_id ) {
                $page = get_post( $custom_page_id );
                if ( $page ) {
                    $already_rendered = true; // Marque le rendu comme effectué

                    // Analyse et exécute les blocs Gutenberg de votre page modèle. do_blocks() renvoie le rendu
                    // des blocs, chacun échappe/assainit sa propre sortie (voir les rendus de includes/Blocks/).
                    // Le conteneur ajouté ci-dessous est un balisage statique, sans donnée variable.
                    $html = do_blocks( $page->post_content );

                    // Les blocs "sections" reprennent le balisage WCFM : on fournit le conteneur
                    // (#wcfmmp-store) dont dépendent les styles de WCFM, une seule fois par page.
                    if ( preg_match( '/wp:fand-storefront\/store-(tabs|content|products|sidebar|body|hours|location|rating|actions|search|category|widget|widget-area|layout|sidebar-area|main-area|tab-section)/', $page->post_content ) ) {
                        $html = '<div id="wcfmmp-store" class="wcfmmp-single-store-holder"><div id="wcfmmp-store-content" class="wcfmmp-store-page-wrap woocommerce">' . $html . '</div></div>';
                    }
                    return $html;
                }
            }
        }
    }
    return $content;
}
// Priorité 8 (et non 9) : do_blocks() du coeur WordPress tourne à 9. S'il passait avant nous, le bloc Produits
// viderait la boucle principale (in_the_loop() devient faux) et on sortirait ici sans ajouter #wcfmmp-store.
add_filter( 'the_content', 'fand_override_wcfm_store_content', 8 );

/**
 * 4. Masquer les titres d'archives généraux (tous thèmes)
 */
add_filter( 'woocommerce_show_page_title', function( $show_title ) {
    if ( fand_is_store_context() ) {
        return false;
    }
    return $show_title;
}, 999 );

add_action( 'wp_enqueue_scripts', function() {
    if ( ! fand_is_store_context() ) {
        return;
    }
    // Style en ligne propre à WordPress (wp_add_inline_style), au lieu d'une balise <style> écrite dans <head>.
    wp_register_style( 'fand-storefront-blocks-store-page', false, array(), FAND_STOREFRONT_VERSION );
    wp_enqueue_style( 'fand-storefront-blocks-store-page' );
    wp_add_inline_style(
        'fand-storefront-blocks-store-page',
        '.wcfm-store-page .woocommerce-products-header__title,
        .wcfmmp-store-page .woocommerce-products-header__title,
        .wcfm-store-page .page-title.archive-title,
        .wcfmmp-store-page .page-title.archive-title,
        .wcfm-store-page .entry-hero,
        .wcfmmp-store-page .entry-hero,
        .wcfm-store-page .page-header,
        .wcfmmp-store-page .page-header { display: none !important; }'
    );
}, 999 );

/**
 * 5. Feuilles de style de WCFM sur les boutiques sans produit
 *
 * Sur certaines pages de boutique (par exemple l'onglet Produits d'une boutique vide), WCFM ne charge
 * pas ses propres CSS : les onglets s'affichent alors en liste à puces. On charge ici les CSS de WCFM
 * (boutique, responsive/mobile et couleurs dynamiques), sans rien redéfinir : ce sont ceux de WCFM,
 * avec les couleurs réglées dans WCFM. Si WCFM les a déjà chargés, on ne fait rien.
 */
function fand_wcfm_style_already_loaded( $handle, $src ) {
    $styles = wp_styles();

    if ( wp_style_is( $handle, 'enqueued' ) || wp_style_is( $handle, 'done' ) ) {
        return true;
    }

    // Même fichier déjà chargé sous un autre nom (WCFM le déclare parfois deux fois)
    $file = basename( strtok( $src, '?' ) );
    foreach ( (array) $styles->queue as $queued ) {
        if ( isset( $styles->registered[ $queued ] ) && is_string( $styles->registered[ $queued ]->src )
            && false !== strpos( $styles->registered[ $queued ]->src, $file ) ) {
            return true;
        }
    }
    return false;
}

add_action( 'wp_enqueue_scripts', 'fand_enqueue_wcfm_store_styles', 99 );
function fand_enqueue_wcfm_store_styles() {
    global $WCFMmp, $WCFM;

    if ( ! fand_is_store_context() || ! is_object( $WCFMmp ) || empty( $WCFMmp->plugin_url ) ) {
        return;
    }

    $ver  = ! empty( $WCFMmp->version ) ? $WCFMmp->version : null;
    $base = $WCFMmp->plugin_url . 'assets/css/min/store/';

    // Mêmes noms (handles) que ceux utilisés par WCFM
    $files = array(
        'wcfmmp_store_css'            => $base . 'wcfmmp-style-store.css',
        'wcfmmp_store_responsive_css' => $base . 'wcfmmp-style-store-responsive.css',
    );
    if ( is_object( $WCFM ) && ! empty( $WCFM->plugin_url ) ) {
        $files['wcfmicon'] = $WCFM->plugin_url . 'assets/fonts/font-awesome/css/wcfmicon.min.css';
    }

    foreach ( $files as $handle => $src ) {
        if ( ! fand_wcfm_style_already_loaded( $handle, $src ) ) {
            wp_enqueue_style( $handle, $src, array(), $ver );
        }
    }

    // Couleurs dynamiques : fichier généré par WCFM à chaque enregistrement de ses réglages de style
    $upload = wp_get_upload_dir();
    $custom = glob( trailingslashit( $upload['basedir'] ) . 'wcfm/wcfmmp-style-custom-*.css' );
    if ( ! empty( $custom ) ) {
        usort( $custom, function ( $a, $b ) { return filemtime( $b ) - filemtime( $a ); } );
        $custom_url = trailingslashit( $upload['baseurl'] ) . 'wcfm/' . basename( $custom[0] );
        if ( ! fand_wcfm_style_already_loaded( 'wcfmmp_style_custom', $custom_url ) ) {
            wp_enqueue_style( 'wcfmmp_style_custom', $custom_url, array(), filemtime( $custom[0] ) );
        }
    }
}
