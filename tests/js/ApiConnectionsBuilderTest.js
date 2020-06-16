( function (ApiConnectionsBuilder) {
	QUnit.module( 'ext.network.ApiConnectionsBuilder' );


	QUnit.test( 'nodes integration test', function ( assert ) {
		let builder = new ApiConnectionsBuilder(['Cats']);
		let connections = builder.connectionsFromApiResponses(module.stub.Cats);

		// console.log(JSON.stringify(connections.nodes, null, 4));

		assert.deepEqual(
			connections.nodes,
			[
				{
					"id": "Cats",
					"label": "Cats",
					"pageName": "Cats",
					"pageNs": 0
				},
				{
					"id": "Main Page",
					"label": "Main Page",
					"pageName": "Main Page",
					"pageNs": 0
				},
				{
					"id": "Kittens",
					"label": "Kittens",
					"pageName": "Kittens",
					"pageNs": 0
				},
				{
					"id": "Looong Cat",
					"label": "Looong Cat",
					"pageName": "Looong Cat",
					"pageNs": 0
				},
				{
					"id": "Nyan Cat",
					"label": "Nyan Cat",
					"pageName": "Nyan Cat",
					"pageNs": 0
				}
			]
		);
	} );

	QUnit.test( 'edges integration test', function ( assert ) {
		let builder = new ApiConnectionsBuilder(['Cats']);
		let connections = builder.connectionsFromApiResponses(module.stub.Cats);

		// console.log(JSON.stringify(connections.edges, null, 4));

		assert.deepEqual(
			connections.edges,
			[
				{
					"from": "Main Page",
					"to": "Cats",
					"arrows": "to"
				},
				{
					"from": "Cats",
					"to": "Kittens",
					"arrows": "to"
				},
				{
					"from": "Cats",
					"to": "Looong Cat",
					"arrows": "to"
				},
				{
					"from": "Cats",
					"to": "Main Page",
					"arrows": "to"
				},
				{
					"from": "Cats",
					"to": "Nyan Cat",
					"arrows": "to"
				}
			]
		);
	} );

}(window.NetworkExtension.ApiConnectionsBuilder) );
