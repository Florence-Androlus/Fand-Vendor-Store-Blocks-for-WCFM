<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-email', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_email',
        'attributes'      => fand_icon_block_attributes( 'mail-solid' ),
    ) );
} );

function fand_render_store_email( $attributes ) {
    $vendor_id = fand_get_current_vendor_id();
    $store     = fand_get_store( $vendor_id );
    if ( ! $store ) return '';
    if ( ! fand_store_field_allowed( $vendor_id, 'store_hide_email', 'vendor_email' ) ) return '';

    $email = fand_get_store_field( 'email', $store->get_email() );
    if ( empty( $email ) || ! is_email( $email ) ) return '';

    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-email fand-icon-line' ) );

    return sprintf(
        '<div %1$s><span class="fand-icon">%2$s</span><a href="mailto:%3$s">%4$s</a></div>',
        $wrapper,
        fand_get_block_icon( $attributes, 'mail-solid' ),
        esc_attr( antispambot( $email ) ),
        esc_html( antispambot( $email ) )
    );
}
