<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-banner', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_banner',
    ) );
} );

function fand_render_store_banner( $attributes, $content ) {
    global $WCFMmp;

    $store = fand_get_store( fand_get_current_vendor_id() );
    if ( ! $store ) return '';

    $options = ( is_object( $WCFMmp ) && ! empty( $WCFMmp->wcfmmp_marketplace_options ) ) ? $WCFMmp->wcfmmp_marketplace_options : array();

    // Bannière par défaut : réglage WCFM, sinon image fournie par WCFM
    $default = '';
    if ( ! empty( $options['store_default_banner'] ) && function_exists( 'wcfm_get_attachment_url' ) ) {
        $default = wcfm_get_attachment_url( $options['store_default_banner'] );
    }
    if ( empty( $default ) && is_object( $WCFMmp ) ) {
        $default = $WCFMmp->plugin_url . 'assets/images/default_banner.jpg';
    }
    
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    $default = apply_filters( 'wcfmmp_store_default_banner', $default );
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

    $banner = fand_get_store_field( 'banner', $store->get_banner() );
    if ( empty( $banner ) ) $banner = $default;

    $mobile = method_exists( $store, 'get_mobile_banner' ) ? $store->get_mobile_banner() : '';
    if ( empty( $mobile ) ) $mobile = $banner;

    $height  = ! empty( $options['store_banner_height'] )  ? absint( $options['store_banner_height'] )  : 0;
    $mheight = ! empty( $options['store_banner_mheight'] ) ? absint( $options['store_banner_mheight'] ) : 0;

    $vars = '--fand-banner:url(' . esc_url( $banner ) . ');--fand-banner-m:url(' . esc_url( $mobile ) . ');';
    if ( $height )  $vars .= '--fand-banner-h:' . $height . 'px;';
    if ( $mheight ) $vars .= '--fand-banner-mh:' . $mheight . 'px;';

    $wrapper = get_block_wrapper_attributes( array(
        'class' => 'fand-block-banner-container',
        'style' => $vars,
    ) );

    // $wrapper est déjà échappé par get_block_wrapper_attributes() ; le contenu des blocs enfants est assaini (liste blanche).
    return sprintf( '<div %1$s>%2$s</div>', $wrapper, fand_kses_wcfm( $content ) );
}
