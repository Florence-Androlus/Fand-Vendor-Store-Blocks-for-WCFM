<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-actions', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_actions_block',
    ) );
} );

function fand_render_store_actions_block() {
    $vendor_id = fand_get_current_vendor_id();
    if ( ! $vendor_id ) {
        return '';
    }

    ob_start();
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    if ( apply_filters( 'wcfm_is_pref_enquiry', true ) && apply_filters( 'wcfmmp_is_allow_store_header_enquiry', true ) && fand_store_field_allowed( $vendor_id, '', 'enquiry' ) ) {
        do_action( 'wcfmmp_store_enquiry', $vendor_id );
    }
    if ( apply_filters( 'wcfm_is_pref_vendor_followers', true ) && apply_filters( 'wcfm_is_allow_store_followers', true ) && fand_store_field_allowed( $vendor_id, '', 'vendor_follower' ) ) {
        do_action( 'wcfmmp_store_follow_me', $vendor_id );
    }
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    
    $html = fand_kses_wcfm( ob_get_clean() );
    
    if ( '' === trim( $html ) ) {
        return '';
    }

    return sprintf( '<div %s><div class="bd_icon_area">%s</div></div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-actions header_right' ) ), $html );
}