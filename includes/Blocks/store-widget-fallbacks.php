<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Aides pour les blocs Recherche et Catégories :
 * 1) on cherche le widget WCFM réellement enregistré (sans deviner son nom de classe) ;
 * 2) s'il est absent ou vide, on affiche notre propre version.
 */

/**
 * Trouve la classe d'un widget WCFM déjà enregistré dans WordPress.
 * $keywords : mots-clés par ordre de priorité (en minuscules), ex. array( 'store_search', 'search' ).
 */
function fand_find_wcfm_widget_class( array $keywords ) {
    global $wp_widget_factory;

    if ( empty( $wp_widget_factory ) || empty( $wp_widget_factory->widgets ) ) {
        return '';
    }

    $classes = array_keys( $wp_widget_factory->widgets );

    foreach ( $keywords as $keyword ) {
        foreach ( $classes as $class ) {
            $lower = strtolower( $class );
            if ( false !== strpos( $lower, 'wcfm' ) && false !== strpos( $lower, $keyword ) ) {
                return $class;
            }
        }
    }
    return '';
}

/**
 * Rend un widget WCFM et renvoie son HTML (chaîne vide en cas d'erreur).
 */
function fand_render_wcfm_widget( $class ) {
    if ( '' === $class ) {
        return '';
    }

    $level = ob_get_level();
    ob_start();
    try {
        the_widget( $class, array( 'title' => '' ), array(
            'before_widget' => '<div class="wcfm-content">',
            'after_widget'  => '</div>',
            'before_title'  => '',
            'after_title'   => '',
        ) );
        $html = fand_kses_wcfm( ob_get_clean() );
    } catch ( \Throwable $e ) {
        while ( ob_get_level() > $level ) {
            ob_end_clean();
        }
        return '';
    }

    return trim( (string) $html );
}

/**
 * Le HTML du widget WCFM contient-il quelque chose d'exploitable ?
 */
function fand_widget_html_is_usable( $html ) {
    return '' !== $html && (bool) preg_match( '/<(form|input|select|li|a)\b/i', $html );
}

/**
 * URL de la boutique du vendeur.
 */
function fand_fallback_store_url( $vendor_id ) {
    if ( function_exists( 'wcfmmp_get_store_url' ) ) {
        return wcfmmp_get_store_url( $vendor_id );
    }
    $store = function_exists( 'fand_get_store' ) ? fand_get_store( $vendor_id ) : null;
    if ( $store && method_exists( $store, 'get_shop_url' ) ) {
        return $store->get_shop_url();
    }
    return home_url( '/' );
}

/**
 * Version maison de la recherche de produits dans la boutique.
 */
function fand_fallback_store_search_html( $vendor_id ) {
    $url = fand_fallback_store_url( $vendor_id );
  
    return '<div class="wcfm-content">'
        . '<form role="search" method="get" class="fand-store-search-form" action="' . esc_url( $url ) . '">'
        . '<input type="search" name="s" value="' . esc_attr( get_search_query() ) . '" placeholder="' . esc_attr__( 'Search for a product…', 'fand-vendor-store-blocks-for-wcfm' ) . '" />'
        . '<input type="hidden" name="post_type" value="product" />'    
        . '<button type="submit">' . esc_html__( 'Search', 'fand-vendor-store-blocks-for-wcfm' ) . '</button>'
        . '</form>'
        . '</div>';
}

/**
 * Liste déroulante des catégories : uniquement celles des produits publiés du vendeur.
 * Pratique même avec beaucoup de catégories ; le choix recharge la page boutique filtrée.
 */
function fand_fallback_store_categories_html( $vendor_id ) {
    $product_ids = get_posts( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'author'         => $vendor_id,
        'fields'         => 'ids',
        'posts_per_page' => -1,
        'no_found_rows'  => true,
    ) );
    if ( empty( $product_ids ) ) {
        return '';
    }

    $terms = wp_get_object_terms( $product_ids, 'product_cat', array( 'orderby' => 'name' ) );
    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return '';
    }

    $url     = fand_fallback_store_url( $vendor_id );
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $current = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : '';
    $seen    = array();
    $options = '<option value="' . esc_url( $url ) . '">' . esc_html__( 'All categories', 'fand-vendor-store-blocks-for-wcfm' ) . '</option>';

    foreach ( $terms as $term ) {
        if ( isset( $seen[ $term->term_id ] ) ) {
            continue;
        }
        $seen[ $term->term_id ] = true;

        // Décalage visuel des sous-catégories
        $depth  = count( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
        $indent = $depth ? str_repeat( "\xC2\xA0\xC2\xA0", $depth ) . '– ' : '';

        $options .= sprintf(
            '<option value="%s"%s>%s</option>',
            esc_url( add_query_arg( 'product_cat', $term->slug, $url ) ),
            selected( $current, $term->slug, false ),
            esc_html( $indent . $term->name )
        );
    }
 
    return '<div class="wcfm-content">'
        . '<select class="fand-store-categories-select" aria-label="' . esc_attr__( 'Categories', 'fand-vendor-store-blocks-for-wcfm' ) . '" onchange="if(this.value){window.location.href=this.value;}">'
        . $options
        . '</select>'
        . '</div>';
}