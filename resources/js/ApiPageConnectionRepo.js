/**
 * MediaWiki API specific, visjs agnostic
 */
module.ApiPageConnectionRepo = ( function ( $, mw, ApiConnectionsBuilder ) {
	"use strict"

	let ApiPageConnectionRepo = function() {
		this._addedPages = [];
	};

	/**
	 * @param {string[]} pageNames
	 */
	ApiPageConnectionRepo.prototype.addConnections = function(pageNames) {
		let deferred = $.Deferred();

		let pagesToAdd =  pageNames.filter(p => !this._addedPages.includes(p));

		if (pagesToAdd.length === 0) {
			deferred.resolve({pages: [], links: []});
		} else {
			this._addedPages.concat(pagesToAdd);

			this._queryLinks(pagesToAdd).done(
				apiResponse => this._apiResponseToPagesAndLinks(apiResponse)
					.done(connections => deferred.resolve(connections))
			);
		}

		return deferred.promise();
	};

	ApiPageConnectionRepo.prototype._apiResponseToPagesAndLinks = function(linkQueryResponse) {
		let deferred = $.Deferred();

		let connections = (new ApiConnectionsBuilder()).connectionsFromApiResponses(linkQueryResponse)

		this._queryPageNodeInfo(connections.pages).done(function(pageInfoResponse) {
			let missingPages = Object.values(pageInfoResponse.query.pages)
				.filter(p => p.missing === '')
				.map(p => p.title);

			connections.pages.forEach(function(page) {
				if(missingPages.includes(page.title)) {
					page.isMissing = true;
				}
			});

			deferred.resolve(connections);
		});

		return deferred;
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

	ApiPageConnectionRepo.prototype._queryPageNodeInfo = function(pageNodes) {
		return new mw.Api().get({
			action: 'query',
			titles: pageNodes.map(page => page.title),

			format: 'json',
			redirects: 'true'
		});
	};

	return ApiPageConnectionRepo;

}( window.jQuery, window.mediaWiki, module.ApiConnectionsBuilder ) );
