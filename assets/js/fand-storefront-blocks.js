( function( blocks, element ) {
    var el = element.createElement;
    var __ = wp.i18n.__;
    var blockEditor = wp.blockEditor;
    var InnerBlocks = blockEditor.InnerBlocks;
    var InspectorControls = blockEditor.InspectorControls;
    var useBlockProps = blockEditor.useBlockProps;
    var useInnerBlocksProps = blockEditor.useInnerBlocksProps;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var RangeControl = wp.components.RangeControl;
    var SelectControl = wp.components.SelectControl;

    // Supports natifs (couleurs, typo, espacements, bordures, mise en page...) : déclarés UNE SEULE FOIS
    // côté PHP (includes/fand-storefront-blocks-supports.php), lus à la fois par l'éditeur et le rendu.
    // Ne rien déclarer en JS : un `supports` JS remplace celui du PHP dans l'éditeur.

    // ------------------------------------------------------------------
    // APERÇU ÉDITEUR
    // Le wrapper reçoit useBlockProps() SANS style en dur : les réglages
    // natifs (couleurs, marges, bordures...) s'appliquent donc vraiment.
    // ------------------------------------------------------------------
    function makeEditBox( label, icon ) {
        var blockProps = useBlockProps();
        return el( 'div', blockProps,
            el( 'div', {
                style: {
                    padding: '8px 12px',
                    border: '1px dashed #2271b1',
                    borderRadius: '4px',
                    fontSize: '13px',
                    background: 'rgba(34,113,177,.06)'
                }
            }, icon + ' [WCFM] ' + label )
        );
    }

    // Valeurs par défaut des conteneurs, appliquées SEULEMENT si l'utilisateur
    // n'a rien réglé (sinon le style inline écraserait les réglages natifs)
    function containerDefaults( a, opts ) {
        var st = a.style || {};
        var s = { position: 'relative', outline: '1px dashed #2271b1', outlineOffset: '-1px' };
        if ( opts.bg ) {
            var hasBg = a.backgroundColor || a.gradient ||
                ( st.color && ( st.color.background || st.color.gradient ) ) ||
                ( st.background && st.background.backgroundImage );
            if ( ! hasBg ) {
                s.background = opts.bg;
                if ( ! a.textColor && ! ( st.color && st.color.text ) ) { s.color = '#fff'; }
            }
        }
        if ( ! ( st.spacing && st.spacing.padding ) ) { s.padding = opts.padding || '12px'; }
        if ( ! ( st.dimensions && st.dimensions.minHeight ) && opts.minHeight ) { s.minHeight = opts.minHeight; }
        return s;
    }

    function editorLabel( text ) {
        return el( 'div', {
            style: {
                position: 'absolute', top: '2px', left: '8px',
                fontWeight: 'bold', fontSize: '11px', opacity: '0.7',
                pointerEvents: 'none'
            }
        }, text );
    }

    // ------------------------------------------------------------------
    // ICÔNES SVG
    // ------------------------------------------------------------------
    var svgIcons = {
        'phone-solid': '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.27c1.21.49 2.53.76 3.88.76a1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.35.27 2.67.76 3.88a1 1 0 01-.27 1.11l-2.37 2.4z"/></svg>',
        'phone-outline': '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>',
        'mobile-solid': '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/></svg>',
        'map-solid': '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/></svg>',
        'map-outline': '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'mail-solid': '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>',
        'mail-outline': '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
        'building-solid': '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3h2v2h-2V6zm0 4h2v2h-2v-2zm-4-4h2v2H8V6zm0 4h2v2H8v-2zm-2 9v-2h12v2H6z"/></svg>',
        'store-solid': '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M20 4H4v2h16V4zm1 10v-2l-1-5H4l-1 5v2h1v6h10v-6h4v6h2v-6h1zm-9 4H6v-4h6v4z"/></svg>'
    };

    function FandIconPicker( props ) {
        var selectedKey = props.value || 'phone-solid';
        return el('div', { style: { marginBottom: '15px' } },
            el('label', { style: { fontWeight: 'bold', display: 'block', marginBottom: '8px', fontSize: '12px' } }, __( 'Icon:', 'fand-vendor-store-blocks-for-wcfm' )),
            el('div', { style: { display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: '8px', background: '#f0f0f0', padding: '8px', borderRadius: '6px' } },
                Object.keys(svgIcons).map( function( key ) {
                    var isSelected = ( selectedKey === key );
                    return el('div', {
                        key: key,
                        onClick: function() { props.onChange( key ); },
                        style: {
                            padding: '8px',
                            background: isSelected ? '#2271b1' : '#fff',
                            color: isSelected ? '#fff' : '#333',
                            border: '1px solid ' + ( isSelected ? '#2271b1' : '#ccc' ),
                            borderRadius: '4px',
                            cursor: 'pointer',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center'
                        },
                        dangerouslySetInnerHTML: { __html: svgIcons[key] }
                    });
                })
            )
        );
    }

    // ------------------------------------------------------------------
    // 1. Logo
    // ------------------------------------------------------------------
    blocks.registerBlockType( 'fand-storefront/store-logo', {
        title: __( 'WCFM - Vendor Logo', 'fand-vendor-store-blocks-for-wcfm' ),
        icon: 'format-image',
        category: 'fand-storefront',
        attributes: { logoSize: { type: 'number', default: 100 } },
        edit: function( props ) {
            var logoSize = props.attributes.logoSize;
            return el( element.Fragment, {},
                el( InspectorControls, {},
                    el( PanelBody, { title: __( 'Logo settings', 'fand-vendor-store-blocks-for-wcfm' ), initialOpen: true },
                        el( RangeControl, {
                            label: __( 'Logo size (px)', 'fand-vendor-store-blocks-for-wcfm' ),
                            value: logoSize,
                            onChange: function( n ) { props.setAttributes( { logoSize: n } ); },
                            min: 40, max: 300, step: 5
                        } )
                    )
                ),
                makeEditBox( __( 'Vendor Logo (', 'fand-vendor-store-blocks-for-wcfm' ) + logoSize + 'px)', '🖼️' )
            );
        },
        save: function() { return null; }
    } );

    // ------------------------------------------------------------------
    // 2. Nom Boutique
    // ------------------------------------------------------------------
    blocks.registerBlockType( 'fand-storefront/store-name', {
        title: __( 'WCFM - Store Name', 'fand-vendor-store-blocks-for-wcfm' ),
        icon: 'heading',
        category: 'fand-storefront',
        edit: function() { return makeEditBox( __( 'Store Name', 'fand-vendor-store-blocks-for-wcfm' ), '🏷️' ); },
        save: function() { return null; }
    } );

    // ------------------------------------------------------------------
    // 3, 4, 5. Téléphone / Adresse / Email (sélecteur d'icônes SVG)
    // ------------------------------------------------------------------
    function registerIconBlock( slug, title, icon, defaultIcon, panelTitle, label, emoji ) {
        blocks.registerBlockType( 'fand-storefront/' + slug, {
            title: title,
            icon: icon,
            category: 'fand-storefront',
            attributes: {
                iconKey: { type: 'string', default: defaultIcon },
                customSvg: { type: 'string', default: '' },
                iconSize: { type: 'number', default: 18 }
            },
            edit: function( props ) {
                var attrs = props.attributes;
                return el( element.Fragment, {},
                    el( InspectorControls, {},
                        el( PanelBody, { title: panelTitle, initialOpen: true },
                            el( FandIconPicker, {
                                value: attrs.iconKey,
                                onChange: function( k ) { props.setAttributes( { iconKey: k } ); }
                            } ),
                            el( RangeControl, {
                                label: __( 'Icon size (px)', 'fand-vendor-store-blocks-for-wcfm' ),
                                value: attrs.iconSize,
                                onChange: function( s ) { props.setAttributes( { iconSize: s } ); },
                                min: 12, max: 48
                            } ),
                            el( TextControl, {
                                label: __( 'Or paste your custom SVG:', 'fand-vendor-store-blocks-for-wcfm' ),
                                value: attrs.customSvg,
                                onChange: function( svg ) { props.setAttributes( { customSvg: svg } ); }
                            } )
                        )
                    ),
                    makeEditBox( label, emoji )
                );
            },
            save: function() { return null; }
        } );
    }

    registerIconBlock( 'store-phone',   __( 'WCFM - Phone', 'fand-vendor-store-blocks-for-wcfm' ), 'phone',      'phone-solid', __( 'Phone icon settings', 'fand-vendor-store-blocks-for-wcfm' ), __( 'Vendor Phone', 'fand-vendor-store-blocks-for-wcfm' ), '📞' );
    registerIconBlock( 'store-address', __( 'WCFM - Address', 'fand-vendor-store-blocks-for-wcfm' ),   'admin-home', 'map-solid',   __( 'Address icon settings', 'fand-vendor-store-blocks-for-wcfm' ),   __( 'Vendor Address', 'fand-vendor-store-blocks-for-wcfm' ),   '📍' );
    registerIconBlock( 'store-email',   __( 'WCFM - Email', 'fand-vendor-store-blocks-for-wcfm' ),     'email',      'mail-solid',  __( 'Email icon settings', 'fand-vendor-store-blocks-for-wcfm' ),     __( 'Vendor Email', 'fand-vendor-store-blocks-for-wcfm' ),     '✉️' );

    // ------------------------------------------------------------------
    // 6, 7, 8. Réseaux sociaux / Badges / Description
    // ------------------------------------------------------------------
    blocks.registerBlockType( 'fand-storefront/store-social', {
        title: __( 'WCFM - Social Networks', 'fand-vendor-store-blocks-for-wcfm' ),
        icon: 'share',
        category: 'fand-storefront',
        edit: function() { return makeEditBox( __( 'Social Networks', 'fand-vendor-store-blocks-for-wcfm' ), '🌐' ); },
        save: function() { return null; }
    } );

    blocks.registerBlockType( 'fand-storefront/store-badges', {
        title: __( 'WCFM - Vendor Badges', 'fand-vendor-store-blocks-for-wcfm' ),
        icon: 'awards',
        category: 'fand-storefront',
        edit: function() { return makeEditBox( __( 'Vendor Badges', 'fand-vendor-store-blocks-for-wcfm' ), '🏅' ); },
        save: function() { return null; }
    } );

    blocks.registerBlockType( 'fand-storefront/store-description', {
        title: __( 'WCFM - Store Description', 'fand-vendor-store-blocks-for-wcfm' ),
        icon: 'text',
        category: 'fand-storefront',
        edit: function() { return makeEditBox( __( 'Store Description', 'fand-vendor-store-blocks-for-wcfm' ), '📝' ); },
        save: function() { return null; }
    } );


    // ------------------------------------------------------------------
    // BANNIÈRE : conteneur (InnerBlocks) avec layout natif, image de fond, etc.
    // registerContainer() est exposé au plugin Pro, qui s'en sert pour ses conteneurs.
    // ------------------------------------------------------------------
    var BANNER_ALLOWED = [
        'fand-storefront/store-logo',
        'fand-storefront/store-name',
        'fand-storefront/store-phone',
        'fand-storefront/store-address',
        'fand-storefront/store-email',
        'fand-storefront/store-social',
        'fand-storefront/store-badges',
        'fand-storefront/store-description',
        'core/columns', 'core/column', 'core/group', 'core/paragraph', 'core/heading', 'core/image'
    ];

    function registerContainer( cfg ) {
        blocks.registerBlockType( 'fand-storefront/' + cfg.name, {
            title: cfg.title,
            icon: cfg.icon,
            category: 'fand-storefront',
            edit: function( props ) {
                var blockProps = useBlockProps( { style: containerDefaults( props.attributes, cfg.defaults ) } );
                var innerProps = useInnerBlocksProps( blockProps, {
                    allowedBlocks: cfg.allowed,
                    template: cfg.template,
                    templateLock: false,
                    renderAppender: InnerBlocks.ButtonBlockAppender
                } );
                return el( 'div', innerProps,
                    editorLabel( cfg.label ),
                    innerProps.children
                );
            },
            // Le wrapper (classes de layout, styles natifs) est généré par le render_callback PHP
            // via get_block_wrapper_attributes() : on ne sauvegarde donc QUE les blocs enfants,
            // sinon on obtient une double <div> et la disposition ne s'applique pas aux enfants.
            save: function() {
                return el( InnerBlocks.Content );
            }
        } );
    }

    // 9. Bannière
    registerContainer( {
        name: 'store-banner',
        title: __( 'WCFM - Vendor Banner', 'fand-vendor-store-blocks-for-wcfm' ),
        icon: 'cover-image',
        label: '🖼️ [WCFM] ' + __( 'Dynamic banner', 'fand-vendor-store-blocks-for-wcfm' ),
        allowed: BANNER_ALLOWED,
        defaults: { bg: '#2271b1', padding: '20px', minHeight: '180px' }
    } );

    // ------------------------------------------------------------------
    // Blocs "sections" (rendus côté serveur)
    // ------------------------------------------------------------------
    [
        [ 'store-tabs',     __( 'WCFM - Store Tabs', 'fand-vendor-store-blocks-for-wcfm' ),    'index-card',  '🗂️', __( 'Tabs (Products, About, Reviews…)', 'fand-vendor-store-blocks-for-wcfm' ) ],
        [ 'store-content',  __( 'WCFM - Tab Content', 'fand-vendor-store-blocks-for-wcfm' ), 'media-text',  '📄', __( 'Active tab content', 'fand-vendor-store-blocks-for-wcfm' ) ],
        [ 'store-products', __( 'WCFM - Store Products', 'fand-vendor-store-blocks-for-wcfm' ),   'products',    '🛍️', __( 'Store products', 'fand-vendor-store-blocks-for-wcfm' ) ],
        [ 'store-rating',   __( 'WCFM - Rating', 'fand-vendor-store-blocks-for-wcfm' ),                'star-filled', '⭐', __( 'Average rating', 'fand-vendor-store-blocks-for-wcfm' ) ],
        [ 'store-actions',  __( 'WCFM - Action Buttons', 'fand-vendor-store-blocks-for-wcfm' ),   'megaphone',   '💬', __( 'Ask the vendor + Follow', 'fand-vendor-store-blocks-for-wcfm' ) ]
    ].forEach( function( def ) {
        blocks.registerBlockType( 'fand-storefront/' + def[0], {
            title: def[1],
            icon: def[2],
            category: 'fand-storefront',
            edit: function() { return makeEditBox( def[4], def[3] ); },
            save: function() { return null; }
        } );
    } );

    // ------------------------------------------------------------------
    // Blocs-widgets (équivalents des widgets WCFM de base)
    // ------------------------------------------------------------------
    [
        [ 'store-search',   __( 'WCFM - Search Widget', 'fand-vendor-store-blocks-for-wcfm' ),  'search',       '🔎', __( 'Product search', 'fand-vendor-store-blocks-for-wcfm' ) ],
        [ 'store-category', __( 'WCFM - Categories Widget', 'fand-vendor-store-blocks-for-wcfm' ), 'category',     '🗃️', __( 'Store categories', 'fand-vendor-store-blocks-for-wcfm' ) ],
        [ 'store-location', __( 'WCFM - Map Widget', 'fand-vendor-store-blocks-for-wcfm' ),      'location-alt', '🗺️', __( 'Location map', 'fand-vendor-store-blocks-for-wcfm' ) ],
        [ 'store-hours',    __( 'WCFM - Hours Widget', 'fand-vendor-store-blocks-for-wcfm' ),   'clock',        '🕒', __( 'Opening hours', 'fand-vendor-store-blocks-for-wcfm' ) ]
    ].forEach( function( def ) {
        blocks.registerBlockType( 'fand-storefront/' + def[0], {
            title: def[1],
            icon: def[2],
            category: 'fand-storefront',
            attributes: { title: { type: 'string', default: '' } },
            edit: function( props ) {
                return el( element.Fragment, {},
                    el( InspectorControls, {},
                        el( PanelBody, { title: __( 'Widget settings', 'fand-vendor-store-blocks-for-wcfm' ), initialOpen: true },
                            el( TextControl, { label: __( 'Widget title (empty = none)', 'fand-vendor-store-blocks-for-wcfm' ), value: props.attributes.title, onChange: function( v ) { props.setAttributes( { title: v } ); } } )
                        )
                    ),
                    makeEditBox( def[4] + ( props.attributes.title ? ' — ' + props.attributes.title : '' ), def[3] )
                );
            },
            save: function() { return null; }
        } );
    } );

    // ------------------------------------------------------------------
    // API exposée au plugin Pro (Fand Storefront Blocks Pro, add-on de Fand Vendor Store Blocks for WCFM)
    // ------------------------------------------------------------------
    window.fandStorefrontBlocks = {
        makeEditBox: makeEditBox,
        registerContainer: registerContainer
    };

} )( window.wp.blocks, window.wp.element );