<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-logo', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_logo',
        'attributes'      => array(
            'logoSize' => array( 'type' => 'number', 'default' => 100 ),
        ),
    ) );
} );

function fand_render_store_logo( $attributes ) {
    $store = fand_get_store( fand_get_current_vendor_id() );
    if ( ! $store ) return '';

    global $WCFMmp;
    $logo = fand_get_store_field( 'logo', $store->get_avatar() );
    if ( empty( $logo ) && is_object( $WCFMmp ) ) {
        $logo = $WCFMmp->plugin_url . 'assets/images/default_user.png';
    }
    if ( empty( $logo ) ) return '';

    $size    = isset( $attributes['logoSize'] ) ? absint( $attributes['logoSize'] ) : 100;
    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-logo' ) );

    return sprintf(
        '<div %1$s><img src="%2$s" alt="%3$s" width="%4$d" height="%4$d" style="width:%4$dpx;height:%4$dpx;" /></div>',
        $wrapper,
        esc_url( $logo ),
        esc_attr( fand_get_store_field( 'name', $store->get_shop_name() ) ),
        $size
    );
}
