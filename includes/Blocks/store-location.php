<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-location', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_location_block',
        // Sans cette déclaration PHP, l'attribut "title" saisi dans l'éditeur n'arrive pas au rendu
        'attributes'      => array(
            'title' => array( 'type' => 'string', 'default' => '' ),
        ),
    ) );
} );

function fand_render_store_location_block( $attributes ) {
    $vendor_id = fand_get_current_vendor_id();
    if ( ! $vendor_id ) {
        return '';
    }

    $title = isset( $attributes['title'] ) ? $attributes['title'] : '';

    // Un add-on (ex. page de branche) peut fournir ce bloc
    $override = fand_storefront_block_override( 'store-location', $title );
    if ( '' !== trim( $override ) ) {
        return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-location-container' ) ), $override );
    }

    ob_start();
    if ( class_exists( 'WCFMmp_Store_Location' ) ) {
        echo '<div class="widget fand-block-location">';
        if ( '' !== $title ) {
            echo '<div class="sidebar_heading"><h4 class="widget-title">' . esc_html( $title ) . '</h4></div>';
        }
        the_widget( 'WCFMmp_Store_Location', array( 'title' => '' ), array(
            'before_widget' => '<div class="wcfm-content">', 
            'after_widget'  => '</div>',
        ) );
        echo '</div>';
    }
    $html = fand_kses_wcfm( ob_get_clean() );

    if ( '' === trim( $html ) ) {
        return '';
    }

    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-location-container' ) ), $html );
}