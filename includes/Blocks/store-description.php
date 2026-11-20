<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-description', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_description',
    ) );
} );

function fand_render_store_description() {
    $vendor_id = fand_get_current_vendor_id();
    $store     = fand_get_store( $vendor_id );
    if ( ! $store ) return '';
    if ( ! fand_store_field_allowed( $vendor_id, 'store_hide_description', '' ) ) return '';

    $description = fand_get_store_field( 'description', $store->get_shop_description() );
    if ( empty( $description ) ) return '';

    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-description' ) );

    return sprintf( '<div %1$s>%2$s</div>', $wrapper, wpautop( wp_kses_post( $description ) ) );
}
