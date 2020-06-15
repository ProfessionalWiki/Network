module.Network = ( function (vis, mw, GraphElements, InteractiveGraph ) {
	"use strict"

	let Network = function(divId, pageConnectionRepo) {
		this._divId = divId;
		this._pageConnectionRepo = pageConnectionRepo;
	};

	Network.prototype.show = function() {
		this._pageConnectionRepo.getConnections().done(this._initialize.bind(this));
	};

	Network.prototype._initialize = function(pageConnections) {
		let nodes = new vis.DataSet(pageConnections.nodes);
		let network = this._newNetwork(nodes, pageConnections);

		this._bindEvents(network, nodes);
	};

	Network.prototype._newNetwork = function(nodes, pageConnections) {
		return new vis.Network(
			document.getElementById(this._divId),
			{
				nodes: nodes,
				edges: new vis.DataSet(pageConnections.edges),
			},
			this._getOptions()
		);
	};

	Network.prototype._getOptions = function() {
		return {};
	};

	Network.prototype._bindEvents = function(network, nodes) {
		network.on(
			'doubleClick',
			function(event) {
				if (event.nodes.length === 1) {
					let node = nodes.get(event.nodes[0]);

					window.open(
						mw.Title.newFromText(node.pageName, node.pageNs).getUrl(),
						"_self"
					);
				}
			}
		);
	};

	return Network;

}( window.vis, window.mediaWiki ) );
