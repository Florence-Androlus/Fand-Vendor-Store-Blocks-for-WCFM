/**
 * Onglets de la boutique : navigation normale sur les liens d'onglets (comme la page WCFM).
 */
jQuery( function ( $ ) {
	$( '#tab_links_area' ).find( 'a' ).each( function () {
		$( this ).off( 'click' ).on( 'click', function () {
			window.location.href = $( this ).attr( 'href' );
		} );
	} );
} );
