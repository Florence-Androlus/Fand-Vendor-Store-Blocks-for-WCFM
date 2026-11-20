<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-tabs', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_tabs',
    ) );
} );

function fand_render_store_tabs() {
    $html = fand_render_tabs_section();
    if ( '' === $html ) {
        return '';
    }
    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-tabs tab_area' ) ), $html );
}