<?php
/**
 * Fand Vendor Store Blocks for WCFM — supports natifs Gutenberg, déclarés UNE SEULE FOIS ici
 * pour tous les blocs fand-storefront/* (gratuits ET Pro).
 *
 * Source unique de vérité : plus de `supports` dans le JS (ancienne fonction sup()), ni dans
 * chaque fichier de bloc. WordPress lit ce résultat côté PHP (rendu front : classes + styles
 * inline) et côté éditeur (panneaux Style / Mise en page / Dimensions) : les deux restent
 * toujours identiques.
 *
 * Organisation :
 *   1) fandsb_support_parts()   : catalogue de briques (1 brique = 1 support natif = ses
 *                                 contrôles individuels dans l'éditeur) ;
 *   2) fandsb_support_presets() : listes de briques (conteneur, boîte, texte, média, léger) ;
 *   3) fandsb_support_map()     : une ligne par bloc ; filtre « fandsb_support_map » pour que
 *                                 le Pro (ou tout add-on) ajoute les siens.
 *
 * Rien n'est fusionné en un style global : chaque brique garde ses contrôles propres, réglables
 * bloc par bloc. Un bloc absent de la table reçoit le préréglage « box » par défaut.
 *
 * Rappel : pour un bloc dynamique, les classes/styles natifs ne sortent sur le site que si le
 * wrapper du rendu utilise get_block_wrapper_attributes() (une seule fois, sur la racine).
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/** Affiche d'office les contrôles dans les panneaux (sans les cocher dans le menu ⋮). */
function fandsb_support_with_controls( $support, $controls = null ) {
	if ( null === $controls ) {
		$controls = $support;
	}
	// Les deux clés : __experimentalDefaultControls (WP < 6.9) et defaultControls (récent).
	$support['__experimentalDefaultControls'] = $controls;
	$support['defaultControls']               = $controls;
	return $support;
}

/** Tableau associatif (et non une simple liste) ? */
function fandsb_support_is_assoc( $array ) {
	return is_array( $array ) && array() !== $array && array_keys( $array ) !== range( 0, count( $array ) - 1 );
}

/** Fusion récursive des tableaux associatifs ; le reste (booléens, listes) est remplacé. */
function fandsb_support_merge( $base, $add ) {
	foreach ( $add as $key => $value ) {
		if ( isset( $base[ $key ] ) && fandsb_support_is_assoc( $base[ $key ] ) && fandsb_support_is_assoc( $value ) ) {
			$base[ $key ] = fandsb_support_merge( $base[ $key ], $value );
		} else {
			$base[ $key ] = $value;
		}
	}
	return $base;
}

/**
 * 1) Catalogue des briques. Chaque brique est indépendante : on peut en retirer une pour un bloc
 *    précis (clé 'without' de la table) sans toucher aux autres.
 */
function fandsb_support_parts() {
	return array(
		'basics'      => array(
			'html'            => false,
			'anchor'          => true,
			'customClassName' => true,
		),
		'align'       => array( 'align' => array( 'left', 'center', 'right', 'wide', 'full' ) ),
		'align_text'  => array( 'align' => array( 'left', 'center', 'right' ) ),
		'align_wide'  => array( 'align' => array( 'wide', 'full' ) ),
		'color'       => array(
			'color' => fandsb_support_with_controls(
				array( 'text' => true, 'background' => true, 'gradients' => true, 'link' => true ),
				array( 'text' => true, 'background' => true, 'link' => true )
			),
		),
		'typography'  => array(
			// Clés stables (WP 6.1+) et anciennes clés expérimentales (WP plus anciens) : les deux
			// sont posées, WordPress lit celles qu'il connaît.
			'typography' => fandsb_support_with_controls(
				array(
					'fontSize'                     => true,
					'lineHeight'                   => true,
					'textAlign'                    => true,
					'fontFamily'                   => true,
					'fontWeight'                   => true,
					'fontStyle'                    => true,
					'letterSpacing'                => true,
					'textTransform'                => true,
					'textDecoration'               => true,
					'__experimentalFontFamily'     => true,
					'__experimentalFontWeight'     => true,
					'__experimentalFontStyle'      => true,
					'__experimentalLetterSpacing'  => true,
					'__experimentalTextTransform'  => true,
					'__experimentalTextDecoration' => true,
				),
				array(
					'fontSize'       => true,
					'fontAppearance' => true,
					'lineHeight'     => true,
					'letterSpacing'  => true,
					'textDecoration' => true,
					'textTransform'  => true,
					'fontFamily'     => true,
				)
			),
		),
		'spacing'     => array(
			'spacing' => fandsb_support_with_controls(
				array( 'margin' => true, 'padding' => true ),
				array( 'margin' => true, 'padding' => true )
			),
		),
		// Espacement entre les blocs enfants : utile uniquement pour les conteneurs.
		'gap'         => array(
			'spacing' => fandsb_support_with_controls(
				array( 'blockGap' => true ),
				array( 'blockGap' => true )
			),
		),
		'border'      => array(
			// Les DEUX clés sont déclarées : « __experimentalBorder » (clé historique, celle qui fait
			// apparaître le panneau Bordure même avec un thème classique comme Kadence) et « border »
			// (clé stable, WP 6.1+). WordPress lit celle qu'il connaît ; ne jamais en retirer une.
			'__experimentalBorder' => fandsb_support_with_controls(
				array( 'color' => true, 'radius' => true, 'style' => true, 'width' => true ),
				array( 'color' => true, 'radius' => true, 'style' => true, 'width' => true )
			),
			'border'               => fandsb_support_with_controls(
				array( 'color' => true, 'radius' => true, 'style' => true, 'width' => true ),
				array( 'color' => true, 'radius' => true, 'style' => true, 'width' => true )
			),
		),
		'shadow'      => array( 'shadow' => true ),
		'dimensions'  => array(
			'dimensions' => fandsb_support_with_controls(
				array( 'minHeight' => true, 'aspectRatio' => true ),
				array( 'minHeight' => true )
			),
		),
		'background'  => array(
			'background' => fandsb_support_with_controls(
				array( 'backgroundImage' => true, 'backgroundSize' => true ),
				array( 'backgroundImage' => true, 'backgroundSize' => true )
			),
		),
	);
}

/** 2) Préréglages : simples listes de briques. */
function fandsb_support_presets() {
	return array(
		// Conteneur (InnerBlocks) : style de boîte complet + espacement des enfants + image de fond.
		'container' => array( 'basics', 'align', 'color', 'typography', 'spacing', 'gap', 'border', 'shadow', 'dimensions', 'background' ),
		// Bloc d'affichage rendu par le serveur : style de boîte complet.
		'box'       => array( 'basics', 'align', 'color', 'typography', 'spacing', 'border', 'shadow', 'dimensions' ),
		// Bloc texte / ligne d'info : comme « box », alignement simple (gauche, centre, droite).
		'text'      => array( 'basics', 'align_text', 'color', 'typography', 'spacing', 'border', 'shadow', 'dimensions' ),
		// Média (logo) : pas de couleur ni de typo.
		'media'     => array( 'basics', 'align', 'spacing', 'border', 'shadow', 'dimensions' ),
		// Bloc léger (liens, zones brutes) : alignement, espacements, bordure.
		'light'     => array( 'basics', 'align', 'spacing', 'border' ),
	);
}

/**
 * 3) Table « bloc => réglages ».
 *   preset   : préréglage de départ ;
 *   extra    : briques à ajouter ;
 *   without  : briques à retirer pour ce bloc ;
 *   override : retouche fine d'un support (fusionnée à la fin) ;
 *   layout   : mise en page par défaut (supports.layout) pour les conteneurs.
 */
function fandsb_support_map() {
	return apply_filters( 'fandsb_support_map', array(
		// Textes et lignes d'information
		'store-name'        => array( 'preset' => 'text' ),
		'store-phone'       => array( 'preset' => 'text' ),
		'store-address'     => array( 'preset' => 'text' ),
		'store-email'       => array( 'preset' => 'text' ),
		'store-social'      => array( 'preset' => 'text' ),
		'store-badges'      => array( 'preset' => 'text' ),
		'store-description' => array( 'preset' => 'text' ),

		// Média
		'store-logo'        => array( 'preset' => 'media' ),

		// Conteneur du plugin gratuit (les autres conteneurs sont déclarés par le Pro)
		'store-banner'      => array(
			'preset'  => 'container',
			'without' => array( 'align' ),
			'extra'   => array( 'align_wide' ),
			'layout'  => array( 'type' => 'flex', 'orientation' => 'vertical', 'justifyContent' => 'center' ),
		),

		// Sections
		'store-tabs'        => array( 'preset' => 'box' ),
		'store-content'     => array( 'preset' => 'box' ),
		'store-products'    => array( 'preset' => 'box' ),
		'store-rating'      => array( 'preset' => 'box' ),
		'store-actions'     => array( 'preset' => 'box' ),

		// Widgets de la barre latérale
		'store-search'      => array( 'preset' => 'box' ),
		'store-category'    => array( 'preset' => 'box' ),
		'store-location'    => array( 'preset' => 'box' ),
		'store-hours'       => array( 'preset' => 'box' ),
	) );
}

/** Construit le tableau `supports` d'un bloc à partir de sa ligne de la table. */
function fandsb_support_build( $rule, $existing ) {
	$parts   = fandsb_support_parts();
	$presets = fandsb_support_presets();

	$names = isset( $presets[ $rule['preset'] ] ) ? $presets[ $rule['preset'] ] : $presets['box'];
	if ( ! empty( $rule['extra'] ) ) {
		$names = array_merge( $names, (array) $rule['extra'] );
	}
	if ( ! empty( $rule['without'] ) ) {
		$names = array_diff( $names, (array) $rule['without'] );
	}

	$supports = is_array( $existing ) ? $existing : array();
	foreach ( array_unique( $names ) as $name ) {
		if ( isset( $parts[ $name ] ) ) {
			$supports = fandsb_support_merge( $supports, $parts[ $name ] );
		}
	}

	if ( ! empty( $rule['layout'] ) ) {
		$supports['layout'] = array(
			'allowSwitching'  => true,
			'allowInheriting' => false,
			'allowEditing'    => true,
			'default'         => $rule['layout'],
		);
	}

	if ( ! empty( $rule['override'] ) ) {
		$supports = fandsb_support_merge( $supports, $rule['override'] );
	}

	return $supports;
}

/** Application sur tous les blocs fand-storefront/*. */
add_filter( 'register_block_type_args', function ( $args, $name ) {
	if ( 0 !== strpos( $name, 'fand-storefront/' ) ) {
		return $args;
	}

	$slug = substr( $name, strlen( 'fand-storefront/' ) );
	$map  = fandsb_support_map();
	$rule = isset( $map[ $slug ] ) ? $map[ $slug ] : array();
	$rule = wp_parse_args( $rule, array( 'preset' => 'box' ) );

	$args['supports'] = fandsb_support_build( $rule, isset( $args['supports'] ) ? $args['supports'] : array() );
	return $args;
}, 10, 2 );

/**
 * Panneaux Bordure et Ombre visibles sur TOUS les blocs fand-storefront/*, quel que soit le thème.
 *
 * Un thème classique (Kadence, etc.) masque ces panneaux par défaut, même si le bloc déclare les
 * supports « border » et « shadow ». On les active donc bloc par bloc dans les réglages globaux
 * (settings.blocks.<bloc>), sans toucher aux réglages du reste du site. Les réglages d'un bloc
 * l'emportent sur ceux de la racine : un thème qui désactive les bordures « partout » ne touche
 * pas à nos blocs. Appliqué à la fois sur la couche « défaut » et sur la couche « thème », pour
 * rester actif même si un thème redéfinit ses propres réglages.
 */
function fandsb_enable_block_ui( $theme_json ) {
	if ( ! class_exists( 'WP_Theme_JSON' ) || ! is_object( $theme_json ) || ! method_exists( $theme_json, 'update_with' ) ) {
		return $theme_json;
	}

	$names = array();
	foreach ( array_keys( fandsb_support_map() ) as $slug ) {
		$names[] = 'fand-storefront/' . $slug;
	}
	foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $type ) {
		if ( 0 === strpos( $name, 'fand-storefront/' ) ) {
			$names[] = $name;
		}
	}

	$blocks = array();
	foreach ( array_unique( $names ) as $name ) {
		$blocks[ $name ] = array(
			'border' => array( 'color' => true, 'radius' => true, 'style' => true, 'width' => true ),
			'shadow' => array( 'defaultPresets' => true ),
		);
	}

	return $theme_json->update_with( array(
		'version'  => WP_Theme_JSON::LATEST_SCHEMA,
		'settings' => array( 'blocks' => $blocks ),
	) );
}
add_filter( 'wp_theme_json_data_default', 'fandsb_enable_block_ui' );
add_filter( 'wp_theme_json_data_theme', 'fandsb_enable_block_ui' );
