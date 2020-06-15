
( function ( mw ) {
	"use strict"

	mw.hook( 'wikipage.content' ).add( function( $content ) {
		$content.find( 'div.network-visualization' ).each( function() {
			let $this = $( this );
			
			let network = new module.Network(
				$this.attr('id'),
				new module.ApiPageConnectionRepo($this.data('page'))
			);

			network.show();
		} );
	} );

}( window.mediaWiki ) );
