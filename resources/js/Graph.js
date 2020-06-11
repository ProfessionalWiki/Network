module.Graph = ( function (d3) {

	let Graph = function() {
	};

	Graph.prototype.createSimulation = function(container, connections) {
		let simulation = d3.forceSimulation()
			.force("link", d3.forceLink().id(function(page) { return page.title; }))
			.force("charge", d3.forceManyBody())
			.force("center", d3.forceCenter(500, 300));

		let linkGroup = createLinkGroup(container, connections.links);
		let nodeGroup = createNodeGroup(container, connections.pages);
		let nodeLabels = createNodeLabels(container, connections.pages);

		simulation.nodes(connections.pages).on(
			"tick",
			function ticked() {
				updateNodeGroup(nodeGroup);
				updateLinkGroup(linkGroup);

				updateNodeGroup(nodeLabels);
			}
		);

		simulation.force("link").links(connections.links);
	};

	function createLinkGroup(container, links) {
		return container.append("g").attr("class", "links")
			.selectAll("line")
			.data(links)
			.enter()
			.append("line")
			.attr("stroke", "#aaa")
			.attr("stroke-width", "1px");
	}

	function createNodeGroup(container, nodes) {
		let color = d3.scaleOrdinal(d3.schemeCategory10);

		let nodeGroup = container.append("g").attr("class", "nodes")
			.selectAll("g")
			.data(nodes)
			.enter()
			.append("circle")
			.attr("r", 5)
			.attr("fill", function(page) { return color(page.ns); });

		nodeGroup.append("title").text(function(page) { return page.title; });

		return nodeGroup;
	}

	function createNodeLabels(container, nodes) {
		return container.append("g").attr("class", "labelNodes")
			.selectAll("text")
			.data(nodes)
			.enter()
			.append("text")
			.text(function(page, i) { return page.title; })
			.style("fill", "#555")
			.style("font-family", "Arial")
			.style("font-size", 12)
			.style("pointer-events", "none")
			.attr('x', 9)
			.attr('y', 4);
	}

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

	return Graph;

}(window.d3) );
