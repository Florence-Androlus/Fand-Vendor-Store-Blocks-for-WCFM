<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-name', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_name',
    ) );
} );

function fand_render_store_name() {
    $store = fand_get_store( fand_get_current_vendor_id() );
    if ( ! $store ) return '';

    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-name' ) );

    return sprintf( '<h1 %1$s>%2$s</h1>', $wrapper, esc_html( fand_get_store_field( 'name', $store->get_shop_name() ) ) );
}
