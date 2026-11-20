<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-category', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_category_block',
        'attributes'      => array(
            'title' => array( 'type' => 'string', 'default' => '' ),
        ),
        // Les "supports" natifs sont appliqués par includes/fand-storefront-blocks-supports.php
    ) );
} );

function fand_render_store_category_block( $attributes ) {
    $vendor_id = fand_get_current_vendor_id();
    if ( ! $vendor_id ) {
        return '';
    }

    $title = isset( $attributes['title'] ) ? $attributes['title'] : '';

    // Un add-on (ex. page de branche) peut fournir ce bloc
    $override = fand_storefront_block_override( 'store-category', $title );
    if ( '' !== trim( $override ) ) {
        return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-category-container' ) ), $override );
    }

    // Liste déroulante maison (le widget WCFM affiche une longue liste, ingérable avec beaucoup de catégories)
    $inner = fand_fallback_store_categories_html( $vendor_id );

    if ( '' === trim( $inner ) ) {
        return '';
    }

    $html = '<div class="widget fand-block-categories">';
    if ( '' !== $title ) {
        $html .= '<div class="sidebar_heading"><h4 class="widget-title">' . esc_html( $title ) . '</h4></div>';
    }
    $html .= $inner . '</div>';

    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-category-container' ) ), $html );
}