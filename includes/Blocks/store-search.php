<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-search', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_search_block',
        // Sans cette déclaration PHP, l'attribut "title" saisi dans l'éditeur n'arrive pas au rendu
        'attributes'      => array(
            'title' => array( 'type' => 'string', 'default' => '' ),
        ),
        // Les "supports" natifs sont appliqués par includes/fand-storefront-blocks-supports.php
    ) );
} );

function fand_render_store_search_block( $attributes ) {
    $vendor_id = fand_get_current_vendor_id();
    if ( ! $vendor_id ) {
        return '';
    }

    $title = isset( $attributes['title'] ) ? $attributes['title'] : '';

    // Un add-on (ex. page de branche) peut fournir ce bloc
    $override = fand_storefront_block_override( 'store-search' );
    if ( '' !== trim( $override ) ) {
        return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-search-container' ) ), $override );
    }

    // 1) Widget WCFM s'il existe et s'affiche
    $inner = fand_render_wcfm_widget( fand_find_wcfm_widget_class( array( 'store_search', 'search' ) ) );

    // 2) Sinon, notre propre recherche
    if ( ! fand_widget_html_is_usable( $inner ) ) {
        $inner = fand_fallback_store_search_html( $vendor_id );
    }

    $html = '<div class="widget fand-block-search">';
    if ( '' !== $title ) {
        $html .= '<div class="sidebar_heading"><h4 class="widget-title">' . esc_html( $title ) . '</h4></div>';
    }
    $html .= $inner . '</div>';

    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-search-container' ) ), $html );
}