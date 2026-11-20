<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {
    register_block_type( 'fand-storefront/store-products', array(
        'editor_script'   => 'fand-storefront-blocks-js',
        'render_callback' => 'fand_render_store_products_block',
    ) );
} );

function fand_render_store_products_block() {
    $html = fand_render_products_section();
    if ( '' === $html ) {
        return '';
    }
    return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes( array( 'class' => 'fand-block-products' ) ), $html );
}

/**
 * Liste de produits de la boutique.
 *
 * Le template WCFM parcourt la requête principale (have_posts / the_post). Comme notre contenu
 * est affiché depuis la boucle du thème (page.php), on rembobine la requête avant, puis on la
 * marque comme terminée pour que la boucle du thème ne se relance pas.
 */
function fand_render_products_section() {
    global $wp_query;

    // Hors page boutique WCFM (branche, événement) : la requête principale est la page modèle
    $ctx = fand_get_store_context();
    if ( isset( $ctx['type'] ) && 'store' !== $ctx['type'] ) {
        return fand_render_vendor_products_loop( $ctx['vendor_id'] );
    }

    $in_loop = ( $wp_query instanceof WP_Query ) ? $wp_query->in_the_loop : false;

    if ( $wp_query instanceof WP_Query ) {
        $wp_query->rewind_posts();
    }

    $html = fand_render_wcfm_template( 'store/wcfmmp-view-store-products.php' );

    if ( $wp_query instanceof WP_Query ) {
        $wp_query->current_post = $wp_query->post_count - 1;
        wp_reset_postdata();
        // La boucle WCFM met in_the_loop à faux en se terminant : on le rétablit pour les filtres the_content suivants
        $wp_query->in_the_loop = $in_loop;
    }
    return $html;
}