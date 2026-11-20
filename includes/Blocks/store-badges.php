<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-badges', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_badges',
    ) );
} );

function fand_render_store_badges() {
    $vendor_id = fand_get_current_vendor_id();
    if ( ! $vendor_id ) return '';

    // Hook natif WCFM utilisé par l'en-tête de boutique
    ob_start();
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    do_action( 'wcfmmp_store_mobile_badges', $vendor_id );
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    $badges_html = fand_kses_wcfm( ob_get_clean() );
    if ( '' === trim( $badges_html ) ) return '';

    $wrapper = get_block_wrapper_attributes( array( 'class' => 'fand-block-badges wcfmmp_store_mobile_badges' ) );

    return sprintf( '<div %1$s>%2$s</div>', $wrapper, $badges_html );
}
