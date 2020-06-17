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
		let network = this._newNetwork();

		this._bindEvents(network);

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
		return {};
	};

	Network.prototype._bindEvents = function(network) {
		let self = this;

		network.on(
			'doubleClick',
			function(event) {
				if (event.nodes.length === 1) {
					let node = self._data.nodes.get(event.nodes[0]);

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
