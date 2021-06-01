module.Network = (function (vis, NetworkData) {
	"use strict"

	/**
	 * @param {string} divId
	 * @param {module.ApiPageConnectionRepo} pageConnectionRepo
	 * @param {module.PageExclusionManager} pageExclusionManager
	 * @param {object} options
	 * @param {int} labelMaxLength
	 */
	let Network = function(
		divId,
		pageConnectionRepo,
		pageExclusionManager,
		options,
		labelMaxLength
	) {
		this._pageConnectionRepo = pageConnectionRepo;
		this._data = new NetworkData(pageExclusionManager, labelMaxLength);
		this._options = options;
		this._network = this._newNetwork(divId);
		this._lastZoomPosition = {x:0, y:0}

		this._bindEvents();
	};

	/**
	 * @param {string[]} pageNames
	 * @return {Promise}
	 */
	Network.prototype.showPages = function(pageNames) {
		let promise = this._pageConnectionRepo.addConnections(pageNames);

		promise.then(
			connections => {
				this._data.addPages(connections.pages);
				this._data.addLinks(connections.links);
			}
		);

		return promise;
	};

	Network.prototype._addPage = function(pageName) {
		return this.showPages([pageName]);
	};

	Network.prototype._newNetwork = function(divId) {
		return new vis.Network(
			document.getElementById(divId),
			{
				nodes: this._data.nodes,
				edges: this._data.edges,
			},
			this._options
		);
	};

	Network.prototype._bindEvents = function() {
		this._network.on('doubleClick', this._onDoubleClick.bind(this));
		this._network.on('hold', this._onHold.bind(this));
		this._network.on('select', this._onSelect.bind(this));
		this._network.on('zoom', this._onZoom.bind(this));
		this._network.on('dragEnd', this._onDragEnd.bind(this));
	};

	Network.prototype._onDoubleClick = function(event) {
		if (event.nodes.length === 1) {
			let url = this._data.nodes.get(event.nodes[0]).getUrl();

			window.open( url, "_self" );
		}
	};

	Network.prototype._onHold = function(event) {
		if (event.nodes.length === 1) {
			let node = this._data.nodes.get(event.nodes[0]);

			this._addPage(node.label).then(() => this._network.selectNodes([event.nodes[0]]));
		}
	};

	Network.prototype._onSelect = function(event) {
		if (event.nodes.length === 0 && event.edges.length === 1) {
			let targetNodeId = this._data.edges.get(event.edges[0]).to;
			this._network.selectNodes([targetNodeId]);
		}
	};

	Network.prototype._onZoom = function(event) {
		let MIN_ZOOM = 0.25
		let MAX_ZOOM = 3.0
		let scale = this._network.getScale()
		if (scale <= MIN_ZOOM) {
			this._network.moveTo({
				position: this._lastZoomPosition,
				scale: MIN_ZOOM
			});
		}
		else if (scale >= MAX_ZOOM) {
			this._network.moveTo({
				position: this._lastZoomPosition,
				scale: MAX_ZOOM,
			});
		}
		else {
			this._lastZoomPosition = this._network.getViewPosition()
		}
	}

	Network.prototype._onDragEnd = function(event) {
		this._lastZoomPosition = this._network.getViewPosition()
	}

	return Network;

}(window.vis, module.NetworkData));
