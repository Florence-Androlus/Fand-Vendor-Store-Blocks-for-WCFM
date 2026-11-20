<?php
/**
 * Fonctions communes aux blocs "widget" et aux conteneurs de la barre latérale.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Arguments d'affichage des widgets (identiques à la barre latérale WCFM, filtrables). */
function fand_get_widget_args() {
    // phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
    return apply_filters( 'wcfmmp_store_sidebar_args', array(
        'before_widget' => '<aside class="widget">',
        'after_widget'  => '</aside>',
        'before_title'  => '<div class="sidebar_heading"><h4 class="widget-title">',
        'after_title'   => '</h4></div>',
    ) );
    // phpcs:enable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
}

/** Rend un widget WCFM par son nom de classe PHP. */
function fand_render_widget( $widget_class, $title = '' ) {
    if ( ! fand_get_current_vendor_id() || empty( $widget_class ) || ! class_exists( $widget_class ) ) return '';

    ob_start();
    the_widget( $widget_class, array( 'title' => $title ), fand_get_widget_args() );
    return fand_kses_wcfm( ob_get_clean() );
}

/** Liste des widgets WCFM enregistrés (pour le sélecteur de l'éditeur). */
function fand_list_wcfm_widgets() {
    global $wp_widget_factory;
    $list = array();
    if ( ! is_object( $wp_widget_factory ) ) return $list;
    foreach ( $wp_widget_factory->widgets as $class => $widget ) {
        if ( 0 === strpos( $class, 'WCFM' ) || 0 === strpos( $class, 'Wcfm' ) ) {
            $list[] = array( 'value' => $class, 'label' => $widget->name . ' (' . $class . ')' );
        }
    }
    return $list;
}

/** Transmet la liste des widgets à l'éditeur (bloc "Widget au choix"). */
add_action( 'enqueue_block_editor_assets', function() {
    wp_add_inline_script(
        'fand-storefront-blocks-js',
        'window.fandStorefront = ' . wp_json_encode( array( 'widgets' => fand_list_wcfm_widgets() ) ) . ';',
        'before'
    );
} );
