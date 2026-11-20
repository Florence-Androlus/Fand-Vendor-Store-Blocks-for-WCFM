<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-social', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_social',
    ) );
} );

function fand_render_store_social() {
    $vendor_id = fand_get_current_vendor_id();
    $store     = fand_get_store( $vendor_id );
    if ( ! $store ) return '';

    $info = fand_get_store_info( $vendor_id );
    if ( empty( $info['social'] ) || ! $store->has_social() ) return '';
    if ( ! fand_store_field_allowed( $vendor_id, '', 'vendor_social' ) ) return '';

    // Template natif WCFM : mêmes réseaux, mêmes icônes, mêmes liens
    $html    = fand_render_wcfm_template( 'store/wcfmmp-view-store-social.php' );
    
    // On ajoute style="background: transparent !important;" pour écraser le noir de WCFM
    $wrapper = get_block_wrapper_attributes( array( 
        'class' => 'fand-block-social social_area',
        'style' => 'background: transparent !important;' 
    ) );

    return sprintf( '<div %1$s>%2$s</div>', $wrapper, $html );
}
