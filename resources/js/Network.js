module.Network = ( function (vis, mw ) {
	"use strict"

	/**
	 * @param {string} divId
	 * @param pageConnectionRepo
	 * @param {string[]} pageNames
	 */
	let Network = function(divId, pageConnectionRepo, pageNames) {
		this._divId = divId;
		this._pageConnectionRepo = pageConnectionRepo;
		this._initialPageNames = pageNames;

		this._data = new module.NetworkData();
	};

	Network.prototype.show = function() {
		this._network = this._newNetwork();

		this._bindEvents();

		this._initialPageNames.forEach(Network.prototype._addPage.bind(this));
	};

	Network.prototype._addPage = function(pageName) {
		this._pageConnectionRepo.addConnections(this._data, pageName);
	};

	Network.prototype._newNetwork = function() {
		return new vis.Network(
			document.getElementById(this._divId),
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
			let node = this._data.nodes.get(event.nodes[0]);

			window.open(
				mw.Title.newFromText(node.pageTitle, node.pageNs).getUrl(),
				"_self"
			);
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
			let targetNodeId = event.edges[0].split('|')[1];
			this._network.selectNodes([targetNodeId]);
		}
	};

	return Network;

}( window.vis, window.mediaWiki ) );
