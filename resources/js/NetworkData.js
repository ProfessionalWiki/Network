module.NetworkData = ( function ( viz, mw ) {
	"use strict"

	let NetworkData = function() {
		this.nodes = new vis.DataSet();
		this.edges = new vis.DataSet();
	};

	NetworkData.prototype.getNetworkData = function(responses) {

	}

	return NetworkData;

}( window.vis, window.mediaWiki ) );
