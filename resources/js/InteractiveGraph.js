module.InteractiveGraph = ( function (d3) {
	"use strict"

	let InteractiveGraph = function(graphElements, connections) {
		this._graphElements = graphElements;
		this._connections = connections;
	};

	InteractiveGraph.prototype.runSimulation = function() {
		let self = this;

		this._newSimulation().on(
			"tick",
			function ticked() {
				updateNodeGroup(self._graphElements.nodeGroup);
				updateLinkGroup(self._graphElements.linkGroup);
				updateNodeGroup(self._graphElements.labelGroup);
			}
		);
	};

	InteractiveGraph.prototype._newSimulation = function() {
		let simulation = d3.forceSimulation(this._connections.pages);

		simulation.force("charge", d3.forceManyBody())
		simulation.force("center", d3.forceCenter(500, 300));
		simulation.force("link", this._newLinkForce());

		return simulation;
	};

	InteractiveGraph.prototype._newLinkForce = function() {
		let linkForce = d3.forceLink(this._connections.links);

		linkForce.id(function(page) { return page.title; });
		linkForce.distance(200);

		return linkForce;
	};

	function updateLinkGroup(link) {
		link.attr("x1", function(d) { return fixna(d.source.x); })
			.attr("y1", function(d) { return fixna(d.source.y); })
			.attr("x2", function(d) { return fixna(d.target.x); })
			.attr("y2", function(d) { return fixna(d.target.y); });
	}

	function updateNodeGroup(node) {
		node.attr("transform", function(d) {
			return "translate(" + fixna(d.x) + "," + fixna(d.y) + ")";
		});
	}

	function fixna(n) {
		if (isFinite(n)) return n;
		return 0;
	}

	return InteractiveGraph;

}(window.d3) );
