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
    
// Sécurité : si jamais $address contenait déjà une URL, on récupère la chaîne brute du store
    if ( filter_var( $address, FILTER_VALIDATE_URL ) && method_exists( $store, 'get_address_string' ) ) {
        $address = $store->get_address_string();
    }

    // URL Google Maps mise uniquement dans le href du lien
    $store_lat = get_user_meta( $vendor_id, '_wcfm_store_lat', true );
    $store_lng = get_user_meta( $vendor_id, '_wcfm_store_lng', true );

    $map_url = 'https://google.com/maps/place/' . rawurlencode( $address );
    if ( ! empty( $store_lat ) && ! empty( $store_lng ) ) {
        $map_url .= '/@' . $store_lat . ',' . $store_lng . '&z=16';
    }

    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-address fand-icon-line' ) );

    return sprintf(
        '<div %1$s><span class="fand-icon">%2$s</span><span class="fand-icon-text"><a href="%3$s" target="_blank" rel="noopener noreferrer">%4$s</a></span></div>',
        $wrapper,
        fand_get_block_icon( $attributes, 'map-solid' ),
        esc_url( $map_url ),
        esc_html( $address )
    );
}
