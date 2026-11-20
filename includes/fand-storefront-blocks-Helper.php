<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Contexte de la page courante.
 *
 * Par défaut : la page boutique WCFM d'un vendeur. D'autres plugins (ex. Fand Pickup Points)
 * peuvent fournir leur propre contexte via le filtre 'fand_storefront_context'
 * (ex. page publique d'une branche / d'un événement).
 *
 * @return array { type: store|branch, vendor_id: int, branch_id: int, is_event: bool }
 */
function fand_get_store_context() {
    $ctx = array(
        'type'      => 'store',
        'vendor_id' => fand_get_wcfm_store_page_vendor_id(),
        'branch_id' => 0,
        'is_event'  => false,
    );
    return apply_filters( 'fand_storefront_context', $ctx );
}

/**
 * Base d'URL des boutiques WCFM (WCFM > Réglages > Paramètres du MarketPlace > « Store URL Base »).
 * Par défaut « store », mais elle est modifiable (ex. « magasin », « boutique-vendeur »).
 * WCFM utilise cette même valeur comme nom de variable de requête : on ne doit donc jamais
 * écrire « store » en dur dans le code.
 */
function fand_get_wcfm_store_base() {
    global $WCFMmp;

    $base = '';
    if ( function_exists( 'wcfm_get_option' ) ) {
        $base = wcfm_get_option( 'wcfm_store_url', '' );
    }
    if ( '' === $base && is_object( $WCFMmp ) && ! empty( $WCFMmp->wcfmmp_marketplace_options['wcfm_store_url'] ) ) {
        $base = $WCFMmp->wcfmmp_marketplace_options['wcfm_store_url'];
    }
    if ( '' === $base ) {
        $base = get_option( 'wcfm_store_url', '' );
    }
    $base = is_string( $base ) ? sanitize_title( $base ) : '';

    return (string) apply_filters( 'fand_storefront_store_base', '' !== $base ? $base : 'store' );
}

/**
 * ID du vendeur de la page boutique WCFM courante (0 si autre page).
 */
function fand_get_wcfm_store_page_vendor_id() {
    global $WCFMmp;

    if ( ! function_exists( 'wcfm_is_store_page' ) || ! wcfm_is_store_page() ) {
        return 0;
    }

    $store_user = get_query_var( 'author' );
    if ( $store_user ) {
        return absint( $store_user );
    }

    $store_name = get_query_var( fand_get_wcfm_store_base() );
    if ( ! empty( $store_name ) && is_object( $WCFMmp ) && isset( $WCFMmp->wcfmmp_vendor ) ) {
        $vendor_id = $WCFMmp->wcfmmp_vendor->get_vendor_id_by_slug( $store_name );
        if ( $vendor_id ) {
            return absint( $vendor_id );
        }
    }
    return 0;
}

/**
 * Récupère l'ID du vendeur de la page courante (boutique ou, via filtre, branche/événement).
 */
function fand_get_current_vendor_id() {
    $ctx = fand_get_store_context();
    return isset( $ctx['vendor_id'] ) ? absint( $ctx['vendor_id'] ) : 0;
}

/**
 * Objet boutique WCFM (mis en cache pour la requête).
 */
function fand_get_store( $vendor_id ) {
    static $stores = array();
    $vendor_id = absint( $vendor_id );
    if ( ! $vendor_id || ! function_exists( 'wcfmmp_get_store' ) ) {
        return null;
    }
    if ( ! isset( $stores[ $vendor_id ] ) ) {
        $stores[ $vendor_id ] = wcfmmp_get_store( $vendor_id );
    }
    return $stores[ $vendor_id ];
}

/**
 * Bibliothèque d'icônes SVG (mêmes clés que le sélecteur de l'éditeur).
 */
function fand_get_svg_icon( $key, $size = 18 ) {
    $size = absint( $size );
    $a  = 'viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" aria-hidden="true" focusable="false"';
    $st = 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';

    $svgs = array(
        'phone-solid'    => '<svg ' . $a . ' fill="currentColor"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.21.49 2.53.76 3.88.76a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.27 2.67.76 3.88a1 1 0 01-.27 1.11l-2.37 2.4z"/></svg>',
        'phone-outline'  => '<svg ' . $a . ' ' . $st . '><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>',
        'mobile-solid'   => '<svg ' . $a . ' fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>',
        'map-solid'      => '<svg ' . $a . ' fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/></svg>',
        'map-outline'    => '<svg ' . $a . ' ' . $st . '><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'mail-solid'     => '<svg ' . $a . ' fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',
        'mail-outline'   => '<svg ' . $a . ' ' . $st . '><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'building-solid' => '<svg ' . $a . ' fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3h2v2h-2V6zm0 4h2v2h-2v-2zm-4-4h2v2H8V6zm0 4h2v2H8v-2zm-2 9v-2h12v2H6z"/></svg>',
        'store-solid'    => '<svg ' . $a . ' fill="currentColor"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"/></svg>',
    );

    return isset( $svgs[ $key ] ) ? $svgs[ $key ] : $svgs['phone-solid'];
}

/** Balises et attributs SVG autorisés (liste blanche). */
function fand_svg_allowed_tags() {
    $common = array(
        'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true,
        'stroke-linejoin' => true, 'class' => true, 'opacity' => true, 'transform' => true,
    );
    return array(
        'svg'      => array_merge( $common, array( 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true, 'aria-hidden' => true, 'focusable' => true, 'role' => true ) ),
        'g'        => $common,
        'path'     => array_merge( $common, array( 'd' => true, 'fill-rule' => true, 'clip-rule' => true ) ),
        'circle'   => array_merge( $common, array( 'cx' => true, 'cy' => true, 'r' => true ) ),
        'rect'     => array_merge( $common, array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ) ),
        'line'     => array_merge( $common, array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ) ),
        'polyline' => array_merge( $common, array( 'points' => true ) ),
        'polygon'  => array_merge( $common, array( 'points' => true ) ),
    );
}

/**
 * Nettoie un SVG saisi par l'utilisateur (liste blanche de balises/attributs).
 */
function fand_kses_svg( $svg ) {
    return wp_kses( $svg, fand_svg_allowed_tags() );
}

/**
 * Balises HTML autorisées pour le balisage des templates et widgets WCFM affichés par les blocs :
 * balises d'un article WordPress + formulaires (recherche, catégories) + SVG + attributs data-* / aria.
 * Pas de <script>, <style> ni <iframe>. Filtrable par un add-on : fand_kses_allowed_html.
 */
function fand_get_allowed_html() {
    static $allowed = null;
    if ( null !== $allowed ) {
        return $allowed;
    }

    $allowed = wp_kses_allowed_html( 'post' );

    $global = array(
        'class' => true, 'id' => true, 'style' => true, 'title' => true, 'role' => true, 'tabindex' => true,
        'data-*' => true, 'aria-label' => true, 'aria-labelledby' => true, 'aria-describedby' => true,
        'aria-hidden' => true, 'aria-expanded' => true, 'aria-controls' => true, 'aria-live' => true,
        'aria-current' => true, 'aria-haspopup' => true, 'aria-pressed' => true, 'aria-selected' => true,
        'aria-checked' => true, 'aria-disabled' => true, 'aria-required' => true, 'aria-invalid' => true,
    );

    $extra = array(
        'form'     => array( 'action' => true, 'method' => true, 'enctype' => true, 'novalidate' => true, 'target' => true, 'name' => true, 'autocomplete' => true, 'accept-charset' => true ),
        'input'    => array( 'type' => true, 'name' => true, 'value' => true, 'placeholder' => true, 'checked' => true, 'disabled' => true, 'readonly' => true, 'required' => true, 'min' => true, 'max' => true, 'step' => true, 'maxlength' => true, 'minlength' => true, 'size' => true, 'pattern' => true, 'autocomplete' => true, 'list' => true ),
        'select'   => array( 'name' => true, 'multiple' => true, 'disabled' => true, 'required' => true, 'size' => true, 'autocomplete' => true ),
        'option'   => array( 'value' => true, 'selected' => true, 'disabled' => true, 'label' => true ),
        'optgroup' => array( 'label' => true, 'disabled' => true ),
        'textarea' => array( 'name' => true, 'rows' => true, 'cols' => true, 'placeholder' => true, 'disabled' => true, 'readonly' => true, 'required' => true, 'maxlength' => true, 'minlength' => true ),
        'button'   => array( 'type' => true, 'name' => true, 'value' => true, 'disabled' => true ),
        'label'    => array( 'for' => true ),
        'fieldset' => array( 'disabled' => true, 'name' => true ),
        'legend'   => array(),
        'img'      => array( 'src' => true, 'alt' => true, 'width' => true, 'height' => true, 'srcset' => true, 'sizes' => true, 'loading' => true, 'decoding' => true ),
        'a'        => array( 'href' => true, 'rel' => true, 'target' => true, 'download' => true ),
        'nav'      => array(),
    );
    foreach ( $extra as $tag => $attrs ) {
        $allowed[ $tag ] = array_merge( isset( $allowed[ $tag ] ) ? $allowed[ $tag ] : array(), $attrs, $global );
    }

    foreach ( fand_svg_allowed_tags() as $tag => $attrs ) {
        $allowed[ $tag ] = array_merge( isset( $allowed[ $tag ] ) ? $allowed[ $tag ] : array(), $attrs, $global );
    }

    $allowed = apply_filters( 'fand_kses_allowed_html', $allowed );
    return $allowed;
}

/** Assainit le HTML renvoyé par un template/widget WCFM ou fourni par un add-on avant de l'afficher. */
function fand_kses_wcfm( $html ) {
    return wp_kses( (string) $html, fand_get_allowed_html() );
}

/**
 * Balisage de l'icône d'un bloc (icône choisie ou SVG personnalisé nettoyé).
 */
function fand_get_block_icon( $attributes, $default_key ) {
    $size = isset( $attributes['iconSize'] ) ? absint( $attributes['iconSize'] ) : 18;
    if ( ! empty( $attributes['customSvg'] ) ) {
        return fand_kses_svg( $attributes['customSvg'] );
    }
    $key = isset( $attributes['iconKey'] ) ? $attributes['iconKey'] : $default_key;
    return fand_get_svg_icon( $key, $size );
}

/**
 * Attributs / supports communs aux blocs "ligne avec icône".
 */
function fand_icon_block_attributes( $default_key ) {
    return array(
        'iconKey'   => array( 'type' => 'string', 'default' => $default_key ),
        'customSvg' => array( 'type' => 'string', 'default' => '' ),
        'iconSize'  => array( 'type' => 'number', 'default' => 18 ),
    );
}


/**
 * Infos boutique WCFM (tableau).
 */
function fand_get_store_info( $vendor_id ) {
    $store = fand_get_store( $vendor_id );
    if ( ! $store ) return array();
    $info = $store->get_shop_info();
    return is_array( $info ) ? $info : array();
}

/**
 * Une donnée de contact est-elle affichable ? Respecte le choix « masquer » du vendeur
 * (store_hide_*) et les capacités WCFM (vendor_phone, vendor_email, vendor_address, vendor_social…).
 */
function fand_store_field_allowed( $vendor_id, $hide_key, $capability ) {
    $info = fand_get_store_info( $vendor_id );
    if ( $hide_key && isset( $info[ $hide_key ] ) && 'no' !== $info[ $hide_key ] ) {
        return false;
    }
    if ( $capability && function_exists( 'wcfm_vendor_has_capability' ) && ! wcfm_vendor_has_capability( $vendor_id, $capability ) ) {
        return false;
    }
    return true;
}

/**
 * Affiche un template natif WCFM (templates/store/...) et renvoie le HTML.
 * Les blocs "sections" s'appuient dessus : ils suivent ainsi tous les réglages WCFM et ses hooks.
 */
function fand_render_wcfm_template( $template, $args = array() ) {
    global $WCFMmp;

    $vendor_id = fand_get_current_vendor_id();
    $store     = fand_get_store( $vendor_id );
    if ( ! $store || ! is_object( $WCFMmp ) || ! isset( $WCFMmp->template ) ) {
        return '';
    }

    $args = array_merge( array(
        'store_user' => $store,
        'store_info' => fand_get_store_info( $vendor_id ),
    ), $args );

    ob_start();
    $WCFMmp->template->get_template( $template, $args );
    return fand_kses_wcfm( ob_get_clean() );
}

/**
 * Enregistre un bloc dynamique simple (sans attribut) rendu côté serveur.
 */
function fand_register_simple_block( $name, $callback ) {
    // Supports natifs : voir includes/fand-storefront-blocks-supports.php
    add_action( 'init', function() use ( $name, $callback ) {
        register_block_type( 'fand-storefront/' . $name, array(
            'editor_script'   => 'fand-storefront-blocks-js',
            'render_callback' => $callback,
        ) );
    } );
}
