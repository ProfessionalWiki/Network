module.Network = ( function (d3, mw, Graph ) {

	let Network = function(dataSource, divId) {
		this._dataSource = dataSource;
		this._divId = divId;
	};

	Network.prototype.show = function() {
		let div = d3.select(this._divId);
		let svg = div.append("svg");

		svg.attr("width", div.style("width"));
		svg.attr("height", div.style("height"));

		let container = svg.append("g");

		this._createSimulation(container);

		svg.call(
			d3.zoom()
				.scaleExtent([.1, 4])
				.on("zoom", function() { container.attr("transform", d3.event.transform); })
		);
	};

	Network.prototype._getGraph = function() {
		return this._dataSource.getGraph();
	};

	Network.prototype._createSimulation = function(container) {
		let g = new Graph();
		g.createSimulation(container, this._getGraph());
	};

	return Network;

}( window.d3, window.mediaWiki, module.Graph ) );
