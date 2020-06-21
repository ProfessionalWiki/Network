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
	 * @param {string[]} pageNames
	 */
	ApiPageConnectionRepo.prototype.addConnections = function(networkData, pageNames) {
		let deferred = $.Deferred();

		let pagesToAdd =  pageNames.filter(p => !this._addedPages.includes(p));

		if (pagesToAdd.length === 0) {
			deferred.resolve();
		} else {
			this._addedPages.concat(pagesToAdd);
			this._runQueries(networkData, pagesToAdd, deferred);
		}

		return deferred.promise();
	};

	ApiPageConnectionRepo.prototype._runQueries = function(networkData, pageNames, deferred) {
		this._queryLinks(pageNames).done(function(apiResponse) {
			let connectionsBuilder = new ApiConnectionsBuilder();

			let connections = connectionsBuilder.connectionsFromApiResponses(apiResponse)

			networkData.addPages(connections.pages);
			networkData.addLinks(connections.links);

			deferred.resolve();
		});
	};

	ApiPageConnectionRepo.prototype._queryLinks = function(pageNames) {
		return new mw.Api().get({
			action: 'query',
			titles: pageNames,

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
