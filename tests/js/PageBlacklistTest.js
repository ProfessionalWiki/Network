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

	QUnit.test( 'Talk pages are allowed by default', function ( assert ) {
		let blacklist = new PageBlacklist( [], [], false );

		assert.strictEqual(
			blacklist.isBlacklisted( 'Talk:Kittens' ),
			false
		);
	} );

	QUnit.test( 'Talk pages are not allowed when blacklisted', function ( assert ) {
		let blacklist = new PageBlacklist( [], [], true );

		assert.strictEqual(
			blacklist.isBlacklisted( 'Talk:Kittens' ),
			true
		);
	} );

	QUnit.test( 'Namespace blacklisting', function ( assert ) {
		let blacklist = new PageBlacklist( [], [ 0 ], false );

		assert.strictEqual(
			blacklist.isBlacklisted( 'Kittens' ),
			true
		);
	} );

	QUnit.test( 'Invalid titles are blacklisted', function ( assert ) {
		let blacklist = new PageBlacklist( [], [], false );

		assert.strictEqual(
			blacklist.isBlacklisted( '|' ),
			true
		);
	} );

}() );
