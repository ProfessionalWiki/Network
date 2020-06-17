module.NetworkData = ( function ( viz, mw ) {
	"use strict"

	let NetworkData = function() {
		this.nodes = new vis.DataSet();
		this.edges = new vis.DataSet();
	};

	NetworkData.prototype.addNodes = function(nodes) {
		this.nodes.update(nodes.map(
			node => Object.assign(node, {id: node.label})
		));
	}

	NetworkData.prototype.addEdges = function(edges) {
		this.edges.update(edges.map(
			edge => Object.assign(edge, {id: edge.from + '|' + edge.to})
		));
	}

	return NetworkData;

}( window.vis, window.mediaWiki ) );
