( function (ApiConnectionsBuilder) {
	QUnit.module( 'ext.network.ApiConnectionsBuilder' );


	QUnit.test( 'pages integration test', function ( assert ) {
		let builder = new ApiConnectionsBuilder('Cats');
		let connections = builder.connectionsFromApiResponses(module.stub.Cats);

		// console.log(JSON.stringify(connections.nodes, null, 4));

		assert.deepEqual(
			connections.pages,
			[
				{
					"title": "Cats",
					"ns": 0
				},
				{
					"title": "Main Page",
					"ns": 0
				},
				{
					"title": "Kittens",
					"ns": 0
				},
				{
					"title": "Looong Cat",
					"ns": 0
				},
				{
					"title": "Nyan Cat",
					"ns": 0
				}
			]
		);
	} );

	QUnit.test( 'links integration test', function ( assert ) {
		let builder = new ApiConnectionsBuilder('Cats');
		let connections = builder.connectionsFromApiResponses(module.stub.Cats);

		// console.log(JSON.stringify(connections.edges, null, 4));

		assert.deepEqual(
			connections.links,
			[
				{
					"from": "Main Page",
					"to": "Cats",
				},
				{
					"from": "Cats",
					"to": "Kittens",
				},
				{
					"from": "Cats",
					"to": "Looong Cat",
				},
				{
					"from": "Cats",
					"to": "Main Page",
				},
				{
					"from": "Cats",
					"to": "Nyan Cat",
				}
			]
		);
	} );

}(window.NetworkExtension.ApiConnectionsBuilder) );
