module.GraphElements = ( function (d3) {
	"use strict"

	let GraphElements = function(container, connections) {
		this._container = container;
		this._connections = connections;
	};

	GraphElements.prototype.createElements = function() {
		this._createArrowHeads();
		this.linkGroup = this._createLinkGroup();
		this.nodeGroup = this._createNodeGroup();
	};

	GraphElements.prototype._createArrowHeads = function() {
		this._container.append('defs').append('marker')
			.attr("id",'arrowhead')
			.attr('viewBox','-0 -5 10 10') //the bound of the SVG viewport for the current SVG fragment. defines a coordinate system 10 wide and 10 high starting on (0,-5)
			.attr('refX',13) // x coordinate for the reference point of the marker. If circle is bigger, this need to be bigger.
			.attr('refY',0)
			.attr('orient','auto')
			.attr('markerWidth',10)
			.attr('markerHeight',10)
			.attr('xoverflow','visible')
			.append('svg:path')
			.attr('d', 'M 0,-5 L 10 ,0 L 0,5')
			.attr('fill', '#999')
			.style('stroke','none');
	};

	GraphElements.prototype._createLinkGroup = function () {
		return this._container.append("g")
			.attr("class", "links")
			.selectAll("line")
			.data(this._connections.links)
			.enter()
			.append("line")
			.attr("stroke", "#aaa")
			.attr("stroke-width", "1px")
			.attr('marker-end','url(#arrowhead)');
	}

	GraphElements.prototype._createNodeGroup = function() {
		let color = d3.scaleOrdinal(d3.schemeCategory10);

		let nodeGroup = this._container
			.append("g")
			.attr("class", "nodes")
			.selectAll("g")
			.data(this._connections.pages)
			.enter()
			.append("text")
			.attr("text-anchor", "middle")
			.text(function(page, i) { return page.title; })
			.style("fill", "#555")
			.style("font-family", "Arial")
			.style("font-size", 12)
			.style("pointer-events", "none");

		nodeGroup.append("title").text(function(page) { return page.title; });

		return nodeGroup;
	}

	return GraphElements;

}(window.d3) );
