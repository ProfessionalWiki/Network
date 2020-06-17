module.ApiPageConnectionRepo = ( function ( $, mw ) {
	"use strict"

	let ApiPageConnectionRepo = function() {
	};

	/**
	 * @param {NetworkData} networkData
	 * @param {string} pageName
	 */
	ApiPageConnectionRepo.prototype.addConnections = function(networkData, pageName) {
		let deferred = $.Deferred();
		let self = this;

		$.when(
			this._queryBackLinks(pageName),
			this._queryOutgoingLinks(pageName)
		).done(function(backLinkResult, outgoingLinkResult) {
			let connectionsBuilder = new module.ApiConnectionsBuilder(pageName);

			let connections = connectionsBuilder.connectionsFromApiResponses({
				backLinks: backLinkResult,
				outgoingLinks: outgoingLinkResult
			})

			networkData.addNodes(connections.nodes);
			networkData.addEdges(connections.edges);

			deferred.resolve();
		})

		return deferred.promise();
	};

	ApiPageConnectionRepo.prototype._queryBackLinks = function(pageName) {
		return new mw.Api().get({
			action: 'query',
			list: 'backlinks',
			bltitle: pageName,
			bllimit: 'max',
			format: 'json',
			redirects: 'true'
		});
	};

	ApiPageConnectionRepo.prototype._queryOutgoingLinks = function(pageName) {
		return new mw.Api().get({
			action: 'query',
			prop: 'links',
			titles: pageName,
			pllimit: 'max',
			format: 'json',
			redirects: 'true'
		});
	};

	return ApiPageConnectionRepo;

}( window.jQuery, window.mediaWiki ) );
