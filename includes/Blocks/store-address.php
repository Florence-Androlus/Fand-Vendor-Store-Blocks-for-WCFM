<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-address', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_address',
        'attributes'      => fand_icon_block_attributes( 'map-solid' ),
    ) );
} );

function fand_render_store_address( $attributes ) {
    $vendor_id = fand_get_current_vendor_id();
    $store     = fand_get_store( $vendor_id );
    if ( ! $store ) return '';
    if ( ! fand_store_field_allowed( $vendor_id, 'store_hide_address', 'vendor_address' ) ) return '';

    $address = fand_get_store_field( 'address', $store->get_address_string() );
    if ( empty( $address ) ) return '';

    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-address fand-icon-line' ) );

    return sprintf(
        '<div %1$s><span class="fand-icon">%2$s</span><span class="fand-icon-text">%3$s</span></div>',
        $wrapper,
        fand_get_block_icon( $attributes, 'map-solid' ),
        esc_html( $address )
    );
}
