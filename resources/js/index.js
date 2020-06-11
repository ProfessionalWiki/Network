
( function ( mw ) {

	mw.hook( 'wikipage.content' ).add( function( $content ) {
		$content.find( 'div.network-visualization' ).each( function() {
			let pageName = mw.config.get( 'wgPageName' );

			// let dataSource = new module.StubPageConnectionRepo();
			let dataSource = new module.ApiPageConnectionRepo(pageName);

			let network = new module.Network(dataSource, $( this ).attr('id'));

			network.show();
		} );
	} );

}( window.mediaWiki ) );
