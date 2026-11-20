<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-phone', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_phone',
        'attributes'      => fand_icon_block_attributes( 'phone-solid' ),
    ) );
} );

function fand_render_store_phone( $attributes ) {
    $vendor_id = fand_get_current_vendor_id();
    $store     = fand_get_store( $vendor_id );
    if ( ! $store ) return '';
    if ( ! fand_store_field_allowed( $vendor_id, 'store_hide_phone', 'vendor_phone' ) ) return '';

    $phone = fand_get_store_field( 'phone', $store->get_phone() );
    if ( empty( $phone ) ) return '';

    $tel     = preg_replace( '/[^0-9+]/', '', $phone );
    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-phone fand-icon-line' ) );

    return sprintf(
        '<div %1$s><span class="fand-icon">%2$s</span><a href="tel:%3$s">%4$s</a></div>',
        $wrapper,
        fand_get_block_icon( $attributes, 'phone-solid' ),
        esc_attr( $tel ),
        esc_html( $phone )
    );
}
