<?php
/**
 * Contexte "route" : permet à un add-on (ex. Fand Pickup Points) de faire afficher la PAGE MODÈLE
 * Fand Storefront sur ses propres URL (page d'une branche, d'un événement…), avec les mêmes blocs
 * que la boutique, mais alimentés par les données de l'add-on.
 *
 * Principe : sur la route de l'add-on, la requête principale est remplacée par celle de la page
 * modèle. Le thème affiche alors cette page avec son template "page" normal (thème classique ou bloc),
 * et les blocs lisent le contexte ci-dessous au lieu de celui de la boutique WCFM.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Contexte par défaut d'une route d'add-on. */
function fand_storefront_route_defaults() {
    return array(
        'type'      => 'branch',   // store | branch (un événement est une branche avec is_event)
        'vendor_id' => 0,          // vendeur propriétaire (pour logo, produits, réglages WCFM…)
        'branch_id' => 0,
        'is_event'  => false,
        'title'     => '',         // titre affiché à la place de celui de la page modèle
    );
}

/** Mémorise le contexte de la requête en cours. */
function fand_storefront_set_route_context( $ctx ) {
    $GLOBALS['fand_storefront_route_ctx'] = wp_parse_args( $ctx, fand_storefront_route_defaults() );
}

function fand_storefront_get_route_context() {
    return isset( $GLOBALS['fand_storefront_route_ctx'] ) ? $GLOBALS['fand_storefront_route_ctx'] : null;
}

/** Les blocs lisent le contexte de la route quand il existe. */
add_filter( 'fand_storefront_context', function( $ctx ) {
    $route = fand_storefront_get_route_context();
    return $route ? $route : $ctx;
}, 5 );

/** La page est une "page boutique" Fand (active la surcharge de template et le conteneur #wcfmmp-store). */
add_filter( 'fand_storefront_is_store_context', function( $is ) {
    return fand_storefront_get_route_context() ? true : $is;
} );

/**
 * Valeur d'un champ pour le contexte courant. Les add-ons répondent au filtre
 * "fand_storefront_field_{clé}" (name, logo, banner, phone, email, address, description).
 */
function fand_get_store_field( $key, $default = '' ) {
    return apply_filters( 'fand_storefront_field_' . $key, $default, fand_get_store_context() );
}

/**
 * À appeler par l'add-on, tôt dans "parse_request", quand l'URL est la sienne.
 *
 * @param array $ctx        Contexte (voir fand_storefront_route_defaults()).
 * @param array $keep       Variables de requête de l'add-on à conserver (ex. branch_slug, tab_slug).
 * @param array $extra_vars Variables de requête à ajouter (ex. store_tab pour l'onglet actif).
 * @return bool true si la page modèle est servie ; false sinon (l'add-on garde alors sa propre vue).
 */
function fand_storefront_serve_model_page( $ctx, $keep = array(), $extra_vars = array() ) {
    fand_storefront_set_route_context( $ctx );

    $page_id = fand_get_store_template_page_id();
    if ( ! $page_id || 'publish' !== get_post_status( $page_id ) ) {
        unset( $GLOBALS['fand_storefront_route_ctx'] );
        return false;
    }

    global $wp;
    // On ne garde que ce qui est demandé : une variable comme product_cat filtrerait la page modèle elle-même
    $kept = array_intersect_key( (array) $wp->query_vars, array_flip( (array) $keep ) );
    $wp->query_vars = array_merge( $kept, (array) $extra_vars, array( 'page_id' => $page_id ) );

    // Évite la redirection vers l'URL de la page modèle et toute détection de 404
    add_filter( 'redirect_canonical', '__return_false' );
    add_filter( 'pre_handle_404', '__return_true' );
    add_action( 'wp', function() { status_header( 200 ); }, 1 );

    return true;
}

/**
 * Permet à un add-on de remplacer le rendu d'un bloc (ex. recherche, catégories, carte, onglets)
 * pour le contexte courant. Renvoie le HTML de l'add-on, ou '' pour garder le rendu normal.
 */
function fand_storefront_block_override( $block, $title = '' ) {
    $html = apply_filters( 'fand_storefront_block_html', '', $block, fand_get_store_context(), $title );
    return is_string( $html ) ? fand_kses_wcfm( $html ) : '';
}

/** Titre de la page = nom de la branche/événement (et non celui de la page modèle). */
add_filter( 'the_title', function( $title, $post_id = 0 ) {
    $route = fand_storefront_get_route_context();
    if ( $route && ! empty( $route['title'] ) && in_the_loop() && (int) $post_id === fand_get_store_template_page_id() ) {
        return esc_html( $route['title'] );
    }
    return $title;
}, 10, 2 );

add_filter( 'document_title_parts', function( $parts ) {
    $route = fand_storefront_get_route_context();
    if ( $route && ! empty( $route['title'] ) ) {
        $parts['title'] = $route['title'];
    }
    return $parts;
} );

/**
 * Produits du vendeur hors page boutique WCFM (le template WCFM lit la requête principale,
 * qui est ici la page modèle). Boucle WooCommerce standard, pagination par ?fpg=N.
 */
function fand_render_vendor_products_loop( $vendor_id ) {
    $vendor_id = absint( $vendor_id );
    if ( ! $vendor_id || ! function_exists( 'wc_get_template_part' ) ) return '';

    $paged = isset( $_GET['fpg'] ) ? max( 1, absint( $_GET['fpg'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification

    // Mêmes réglages que la page boutique WCFM : nombre de produits par page
    $wcfm_options = get_option( 'wcfm_marketplace_options', array() );
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    $per_page     = ! empty( $wcfm_options['store_ppp'] ) ? absint( $wcfm_options['store_ppp'] ) : (int) apply_filters( 'loop_shop_per_page', 12 );
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    
    $tax_query = array( array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => array( 'exclude-from-catalog' ),
        'operator' => 'NOT IN',
    ) );
    // Filtre par catégorie (?product_cat=slug), comme dans la boutique
    if ( ! empty( $_GET['product_cat'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => sanitize_text_field( wp_unslash( $_GET['product_cat'] ) ), // phpcs:ignore WordPress.Security.NonceVerification
        );
    }

    $args = apply_filters( 'fand_storefront_products_query_args', array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'author'         => $vendor_id,
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
        'tax_query'      => $tax_query,
    ), $vendor_id );

    $q = new WP_Query( $args );

    ob_start();
    echo '<div class="woocommerce">';
    if ( $q->have_posts() ) {
        woocommerce_product_loop_start();
        while ( $q->have_posts() ) {
            $q->the_post();
            wc_get_template_part( 'content', 'product' );
        }
        woocommerce_product_loop_end();

        if ( $q->max_num_pages > 1 ) {
            echo '<nav class="woocommerce-pagination fand-pagination">';
            if ( $paged > 1 ) {
                echo '<a class="prev page-numbers" href="' . esc_url( add_query_arg( 'fpg', $paged - 1 ) ) . '">&larr;</a> ';
            }
            echo '<span class="page-numbers current">' . (int) $paged . ' / ' . (int) $q->max_num_pages . '</span>';
            if ( $paged < $q->max_num_pages ) {
                echo ' <a class="next page-numbers" href="' . esc_url( add_query_arg( 'fpg', $paged + 1 ) ) . '">&rarr;</a>';
            }
            echo '</nav>';
        }
    } else {
        echo '<p class="woocommerce-info">' . esc_html__( 'No products were found matching your selection.', 'fand-vendor-store-blocks-for-wcfm' ) . '</p>';
    }
    echo '</div>';
    wp_reset_postdata();

    return fand_kses_wcfm( ob_get_clean() );
}
