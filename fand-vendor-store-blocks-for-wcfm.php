<?php
/**
 * Plugin Name: Fand Vendor Store Blocks for WCFM
 * Description: Native, atomic Gutenberg blocks to customize WCFM vendor store pages.
 * Version: 1.0.0
 * Requires at least:  7.1
 * Requires PHP:       8.2
 * Requires Plugins:   woocommerce,wc-frontend-manager
 * Author: Fan-develop
 * Text Domain: fand-vendor-store-blocks-for-wcfm
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * */
 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'FAND_STOREFRONT_VERSION', '1.0.0' );
define( 'FAND_STOREFRONT_PATH', plugin_dir_path( __FILE__ ) );
define( 'FAND_STOREFRONT_URL', plugin_dir_url( __FILE__ ) );

// Catégorie de blocs
add_filter( 'block_categories_all', function( $categories ) {
    array_unshift( $categories, array(
        'slug'  => 'fand-storefront',
        'title' => 'Fand Vendor Store (WCFM)',
        'icon'  => 'store',
    ) );
    return $categories;
}, 10, 1 );

// Script de l'éditeur (dépendances complètes : wp.blockEditor / wp.components)
add_action( 'init', function() {
    wp_register_script(
        'fand-storefront-blocks-js',
        FAND_STOREFRONT_URL . 'assets/js/fand-storefront-blocks.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
        FAND_STOREFRONT_VERSION,
        true
    );
    // Traductions JS : fichiers JSON du dossier languages (ou paquets de langue de WordPress.org)
    wp_set_script_translations( 'fand-storefront-blocks-js', 'fand-vendor-store-blocks-for-wcfm', FAND_STOREFRONT_PATH . 'languages' );
} );

// Feuille de style (front + éditeur)
add_action( 'enqueue_block_assets', function() {
    wp_enqueue_style(
        'fand-storefront-blocks-css',
        FAND_STOREFRONT_URL . 'assets/css/fand-storefront-blocks.css',
        array(),
        FAND_STOREFRONT_VERSION
    );
} );

// Blocs : un fichier par bloc dans includes/Blocks/ (les fichiers communs sont chargés en premier)
foreach ( array( 'store-sections.php', 'store-widget-helpers.php' ) as $fand_common ) {
    require_once FAND_STOREFRONT_PATH . 'includes/Blocks/' . $fand_common;
}
foreach ( glob( FAND_STOREFRONT_PATH . 'includes/Blocks/*.php' ) as $fand_block_file ) {
    require_once $fand_block_file;
}
unset( $fand_common, $fand_block_file );

// Utilitaires (contexte, icônes, SVG)
require_once FAND_STOREFRONT_PATH . 'includes/fand-storefront-blocks-Helper.php';

// Surcharge du template de la boutique
require_once FAND_STOREFRONT_PATH . 'includes/fand-storefront-blocks-override.php';

// Supports natifs Gutenberg (couleurs, typo, marges, bordures, mise en page) appliqués à tous les blocs fand-storefront/*
require_once FAND_STOREFRONT_PATH . 'includes/fand-storefront-blocks-supports.php';

// Contexte "route" : pages d'add-ons (branches, événements) servies par la page modèle
require_once FAND_STOREFRONT_PATH . 'includes/fand-storefront-blocks-context-routing.php';
