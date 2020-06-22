module.Network = ( function (vis, mw, NetworkData ) {
	"use strict"

	/**
	 * @param {string} divId
	 * @param {module.ApiPageConnectionRepo} pageConnectionRepo
	 * @param {module.PageBlacklist} pageBlacklist
	 * @param {object} options
	 */
	let Network = function(divId, pageConnectionRepo, pageBlacklist, options) {
		this._pageConnectionRepo = pageConnectionRepo;

		this._options = options;
		this._data = new NetworkData(pageBlacklist);
		this._network = this._newNetwork(divId);

		this._bindEvents();
	};

	/**
	 * @param {string[]} pageNames
	 * @return {Promise}
	 */
	Network.prototype.showPages = function(pageNames) {
		let promise = this._pageConnectionRepo.addConnections(pageNames);

		promise.then(
			connections => {
				this._data.addPages(connections.pages);
				this._data.addLinks(connections.links);
			}
		);

		return promise;
	};

	Network.prototype._addPage = function(pageName) {
		return this.showPages([pageName]);
	};

	Network.prototype._newNetwork = function(divId) {
		return new vis.Network(
			document.getElementById(divId),
			{
				nodes: this._data.nodes,
				edges: this._data.edges,
			},
			this._options
		);
	};

	Network.prototype._bindEvents = function() {
		this._network.on('doubleClick', this._onDoubleClick.bind(this));
		this._network.on('hold', this._onHold.bind(this));
		this._network.on('select', this._onSelect.bind(this));
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

			this._addPage(node.label).then(() => this._network.selectNodes([event.nodes[0]]));
		}
	};

	Network.prototype._onSelect = function(event) {
		if (event.nodes.length === 0 && event.edges.length === 1) {
			let targetNodeId = this._data.edges.get(event.edges[0]).to;
			this._network.selectNodes([targetNodeId]);
		}
	};

	return Network;

}( window.vis, window.mediaWiki, module.NetworkData ) );
