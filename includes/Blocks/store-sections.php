<?php
/**
 * Fonctions communes aux blocs onglets / contenu / sections (plugin gratuit ET Pro).
 * (Le rendu des sections vit maintenant dans un fichier par bloc.)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Onglet courant (même variable que le template WCFM). */
function fand_get_current_store_tab() {
    return (string) get_query_var( 'store_tab' );
}

/** Liens d'onglets (À propos, Politiques, Avis…). */
function fand_render_tabs_section() {
    // Un add-on (ex. page de branche) peut fournir ses propres liens d'onglets
    $custom_tabs = apply_filters( 'fand_storefront_tabs_html', '', fand_get_store_context() );
    if ( '' !== trim( $custom_tabs ) ) {
        return fand_kses_wcfm( $custom_tabs );
    }

    $html = fand_render_wcfm_template( 'store/wcfmmp-view-store-tabs.php', array( 'store_tab' => fand_get_current_store_tab() ) );
    if ( '' === $html ) {
        return '';
    }
    // Même comportement que la page WCFM : navigation normale sur les liens d'onglets
    // (script enqueue dans le pied de page, plus de <script> écrit dans le HTML)
    wp_enqueue_script( 'fand-storefront-blocks-tabs', FAND_STOREFRONT_URL . 'assets/js/fand-storefront-blocks-tabs.js', array( 'jquery' ), FAND_STOREFRONT_VERSION, true );
    return $html;
}

/** Contenu de l'onglet actif (même logique que templates/store/wcfmmp-view-store.php). */
function fand_render_tab_content() {
    $tab = fand_get_current_store_tab();

    switch ( $tab ) {
        case 'about':
        case 'policies':
        case 'reviews':
        case 'followers':
        case 'followings':
        case 'articles':
            return fand_render_wcfm_template( 'store/wcfmmp-view-store-' . $tab . '.php' );
    }

    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    $template = apply_filters( 'wcfmmp_store_default_template', apply_filters( 'wcfmp_store_default_template', 'store/wcfmmp-view-store-products.php', $tab ), $tab );
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    
    if ( 'store/wcfmmp-view-store-products.php' === $template ) {
        return fand_render_products_section();
    }
    return fand_render_wcfm_template( $template );
}
