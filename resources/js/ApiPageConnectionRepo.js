/**
 * MediaWiki API specific, visjs agnostic
 */
module.ApiPageConnectionRepo = ( function ( $, mw, ApiConnectionsBuilder ) {
	"use strict"

	let ApiPageConnectionRepo = function() {
		this._addedPages = [];
	};

	/**
	 * @param {NetworkData} networkData
	 * @param {string} pageName
	 */
	ApiPageConnectionRepo.prototype.addConnections = function(networkData, pageName) {
		let deferred = $.Deferred();

		if (this._addedPages.includes(pageName)) {
			deferred.resolve();
		} else {
			this._addedPages.push(pageName);
			this._runQueries(networkData, pageName, deferred);
		}

		return deferred.promise();
	};

	ApiPageConnectionRepo.prototype._runQueries = function(networkData, pageName, deferred) {
		this._queryLinks(pageName).done(function(apiResponse) {
			let connectionsBuilder = new ApiConnectionsBuilder(pageName);

			let connections = connectionsBuilder.connectionsFromApiResponses(apiResponse)

			networkData.addPages(connections.pages);
			networkData.addLinks(connections.links);

			deferred.resolve();
		});
	};

	ApiPageConnectionRepo.prototype._queryLinks = function(pageName) {
		return new mw.Api().get({
			action: 'query',
			titles: pageName,

			prop: ['links', 'linkshere', 'extlinks'],
			pllimit: 'max',
			lhlimit: 'max',
			ellimit: 'max',

			format: 'json',
			redirects: 'true'
		});
	};

	return ApiPageConnectionRepo;

}( window.jQuery, window.mediaWiki, module.ApiConnectionsBuilder ) );
