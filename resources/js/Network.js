module.Network = ( function (vis, mw ) {
	"use strict"

	/**
	 * @param {string} divId
	 * @param pageConnectionRepo
	 */
	let Network = function(divId, pageConnectionRepo) {
		this._pageConnectionRepo = pageConnectionRepo;

		this._data = new module.NetworkData();
		this._network = this._newNetwork(divId);
		this._bindEvents();
	};

	/**
	 * @param {string[]} pageNames
	 */
	Network.prototype.showPages = function(pageNames) {
		pageNames.forEach(Network.prototype._addPage.bind(this));
	};

	Network.prototype._addPage = function(pageName) {
		this._pageConnectionRepo.addConnections(this._data, pageName);
	};

	Network.prototype._newNetwork = function(divId) {
		return new vis.Network(
			document.getElementById(divId),
			{
				nodes: this._data.nodes,
				edges: this._data.edges,
			},
			this._getOptions()
		);
	};

	Network.prototype._getOptions = function() {
		return {
			layout: {
				randomSeed: 42
			}
		};
	};

	Network.prototype._bindEvents = function() {
		this._network.on('doubleClick', this._onDoubleClick.bind(this));
		this._network.on('hold', this._onHold.bind(this));
		this._network.on('selectEdge', this._onSelectEdge.bind(this));
	};

	Network.prototype._onDoubleClick = function(event) {
		if (event.nodes.length === 1) {
			let url = this._data.nodes.get(event.nodes[0]).getUrl();

			window.open( url, "_self" );
		}
	};

	Network.prototype._onHold = function(event) {
		if (event.nodes.length === 1) {
			let node = this._data.nodes.get(event.nodes[0]);

			this._addPage(node.label);
		}
	};

	Network.prototype._onSelectEdge = function(event) {
		if (event.nodes.length === 0 && event.edges.length === 1) {
			let targetNodeId = this._data.edges.get(event.edges[0]).to;
			this._network.selectNodes([targetNodeId]);
		}
	};

	return Network;

}( window.vis, window.mediaWiki ) );
