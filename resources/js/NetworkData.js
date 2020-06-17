module.NetworkData = ( function ( viz, mw ) {
	"use strict"

	let NetworkData = function() {
		this.nodes = new vis.DataSet();
		this.edges = new vis.DataSet();
	};

	NetworkData.prototype.addNodes = function(nodes) {
		nodes.forEach((node) => {
			if ( this.nodes.get(node.id) === null ) {
				this.nodes.add([node]);
			}
		});
	}

	NetworkData.prototype.addEdges = function(edges) {
		this.edges.add(edges);
	}

	return NetworkData;

}( window.vis, window.mediaWiki ) );
