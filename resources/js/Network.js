module.Network = ( function (d3, mw, GraphElements, InteractiveGraph ) {
	"use strict"

	let Network = function(pageConnectionRepo, divId) {
		this._pageConnectionRepo = pageConnectionRepo;
		this._divId = divId;
	};

	Network.prototype.show = function() {
		let div = d3.select('#' + this._divId);
		let svg = div.append("svg");

		svg.attr("width", div.style("width"));
		svg.attr("height", div.style("height"));

		let container = svg.append("g");

		svg.call(
			d3.zoom()
				.scaleExtent([.1, 4])
				.on("zoom", function() { container.attr("transform", d3.event.transform); })
		);

		this._createGraph(container);
	};

	Network.prototype._createGraph = function(container) {
		this._pageConnectionRepo.getConnections().done(function(connections) {
			let graphElements = new GraphElements(container, connections)
			graphElements.createElements();

			(new InteractiveGraph(graphElements, connections)).runSimulation();
		});
	};

	return Network;

}( window.d3, window.mediaWiki, module.GraphElements, module.InteractiveGraph ) );
