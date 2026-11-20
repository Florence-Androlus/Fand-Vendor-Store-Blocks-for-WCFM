<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-rating', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_rating_block',
    ) );
} );

function fand_render_store_rating_block() {
    global $WCFMmp;
    
    $vendor_id = fand_get_current_vendor_id();
    if ( ! $vendor_id || ! is_object( $WCFMmp ) || ! isset( $WCFMmp->wcfmmp_reviews ) ) {
        return '';
    }
    
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    if ( ! apply_filters( 'wcfm_is_pref_vendor_reviews', true ) || ! apply_filters( 'wcfm_is_allow_review_rating', true ) ) {
        // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
        return '';
    }
    
    ob_start();
    $WCFMmp->wcfmmp_reviews->show_star_rating( 0, $vendor_id );
    $html = fand_kses_wcfm( ob_get_clean() );
    
    if ( '' === trim( $html ) ) {
        return '';
    }
    
    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-rating' ) ), $html );
}