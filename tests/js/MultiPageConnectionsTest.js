( function (ApiConnectionsBuilder) {
	QUnit.module( 'ext.network.MultiPageConnectionsTest' );


	QUnit.test( 'Pages/nodes integration test', function ( assert ) {
		let builder = new ApiConnectionsBuilder();
		let connections = builder.connectionsFromApiResponses(module.stub.MultiPage);

		// console.log(JSON.stringify(connections.pages, null, 4));

		assert.deepEqual(
			connections.pages,
			[
				{
					"title": "404"
				},
				{
					"title": "Main Page"
				},
				{
					"title": "Cats"
				},
				{
					"title": "Talk:Main Page"
				},
				{
					"title": "Filtered/One"
				},
				{
					"title": "Filtered"
				},
				{
					"title": "Kittens"
				},
				{
					"title": "Fluffy kittens"
				},
				{
					"title": "Fluff"
				}
			]
		);
	} );

	QUnit.test( 'Links/edges integration test', function ( assert ) {
		let builder = new ApiConnectionsBuilder();
		let connections = builder.connectionsFromApiResponses(module.stub.MultiPage);

		// console.log(JSON.stringify(connections.links, null, 4));

		assert.deepEqual(
			connections.links,
			[
				{
					"from": "Main Page",
					"to": "404"
				},
				{
					"from": "Main Page",
					"to": "Cats"
				},
				{
					"from": "Main Page",
					"to": "Talk:Main Page"
				},
				{
					"from": "Filtered/One",
					"to": "Main Page"
				},
				{
					"from": "Filtered",
					"to": "Main Page"
				},
				{
					"from": "Cats",
					"to": "Main Page"
				},
				{
					"from": "Kittens",
					"to": "Fluffy kittens"
				},
				{
					"from": "Cats",
					"to": "Kittens"
				},
				{
					"from": "Fluff",
					"to": "Kittens"
				},
				{
					"from": "Talk:Main Page",
					"to": "Kittens"
				}
			]
		);
	} );

}(window.NetworkExtension.ApiConnectionsBuilder) );
