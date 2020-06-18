
( function ( mw ) {
	"use strict"

	mw.hook( 'wikipage.content' ).add( function( $content ) {
		$content.find( 'div.network-visualization' ).each( function() {
			let $this = $( this );

			let network = new module.Network(
				$this.attr('id'),
				new module.ApiPageConnectionRepo()
			);

			network.showPages($this.data('pages'));
		} );
	} );

}( window.mediaWiki ) );

window.NetworkExtension = module;
