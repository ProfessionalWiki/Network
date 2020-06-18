module.ApiPageConnectionRepo = ( function ( $, mw ) {
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

		if ( this._addedPages.includes(pageName) ) {
			deferred.resolve();
		} else {
			this._addedPages.push(pageName);
			this._runQueries(networkData, pageName, deferred);
		}

		return deferred.promise();
	};

	ApiPageConnectionRepo.prototype._runQueries = function(networkData, pageName, deferred) {
		$.when(
			this._queryBackLinks(pageName),
			this._queryOutgoingLinks(pageName)
		).done(function(backLinkResult, outgoingLinkResult) {
			let connectionsBuilder = new module.ApiConnectionsBuilder(pageName);

			let connections = connectionsBuilder.connectionsFromApiResponses({
				backLinks: backLinkResult,
				outgoingLinks: outgoingLinkResult
			})

			networkData.addPages(connections.pages);
			networkData.addLinks(connections.links);

			deferred.resolve();
		});
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
