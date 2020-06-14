
( function ( mw ) {
	"use strict"

	mw.hook( 'wikipage.content' ).add( function( $content ) {
		$content.find( 'div.network-visualization' ).each( function() {
			let network = new module.Network(
				$( this ).attr('id'),
				new module.ApiPageConnectionRepo(mw.config.get( 'wgPageName' ))
			);

			network.show();
		} );
	} );

}( window.mediaWiki ) );
