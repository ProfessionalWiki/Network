( function () {
	QUnit.module( 'ext.network.PageBlacklistTest' );

	let PageBlacklist = window.NetworkExtension.PageBlacklist;

	QUnit.test( 'Page is allowed when blacklist is empty', function ( assert ) {
		let blacklist = new PageBlacklist( [], [], false );

		assert.strictEqual(
			blacklist.isBlacklisted( 'Kittens' ),
			false
		);
	} );

	QUnit.test( 'Page is allowed when blacklist contains other pages', function ( assert ) {
		let blacklist = new PageBlacklist( [ 'Cats', 'Category:Kittens' ], [], false );

		assert.strictEqual(
			blacklist.isBlacklisted( 'Kittens' ),
			false
		);
	} );

	QUnit.test( 'Page is not allowed when in blacklist', function ( assert ) {
		let blacklist = new PageBlacklist( [ 'Cats', 'Category:Kittens' ], [], false );

		assert.strictEqual(
			blacklist.isBlacklisted( 'Category:Kittens' ),
			true
		);
	} );

}() );
