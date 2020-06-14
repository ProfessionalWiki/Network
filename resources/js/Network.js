module.Network = ( function (vis, mw, GraphElements, InteractiveGraph ) {
	"use strict"

	let Network = function(divId, pageConnectionRepo) {
		this._divId = divId;
		this._pageConnectionRepo = pageConnectionRepo;
	};

	Network.prototype.show = function() {
		let container = document.getElementById(this._divId);
		let options = {};

		this._pageConnectionRepo.getConnections().done(function(pageConnections) {
			let network = new vis.Network(
				container,
				{
					nodes: new vis.DataSet(pageConnections.nodes),
					edges: new vis.DataSet(pageConnections.edges),
				},
				options
			);
		});
	};

	return Network;

}( window.vis, window.mediaWiki ) );
