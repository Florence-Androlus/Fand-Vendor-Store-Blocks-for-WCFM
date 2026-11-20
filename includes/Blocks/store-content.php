<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-content', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_content_block',
    ) );
} );

function fand_render_store_content_block() {
    $content = fand_render_tab_content();
    if ( '' === $content ) {
        return '';
    }
    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-content' ) ), fand_kses_wcfm( $content ) );
}