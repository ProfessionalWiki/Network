/**
 * MediaWiki API specific, visjs agnostic
 */
module.ApiPageConnectionRepo = ( function ( mw, ApiConnectionsBuilder ) {
	"use strict"

	let ApiPageConnectionRepo = function() {
		this._addedPages = [];
	};

	/**
	 * @param {string[]} pageNames
	 * @param {boolean} enableDisplayTitle
	 * @return {Promise}
	 */
	ApiPageConnectionRepo.prototype.addConnections = function(pageNames, enableDisplayTitle) {
		return new Promise(
			function(resolve) {
				let pagesToAdd =  pageNames.filter(p => !this._addedPages.includes(p));

				if (pagesToAdd.length === 0) {
					resolve({pages: [], links: []});
				} else {
					this._addedPages.concat(pagesToAdd);

					this._queryLinks(pagesToAdd).done(
						function(apiResponse) {
							this._apiResponseToPagesAndLinks(apiResponse, enableDisplayTitle).then(connections => resolve(connections))
						}.bind(this)
					);
				}
			}.bind(this)
		);
	};

	ApiPageConnectionRepo.prototype._apiResponseToPagesAndLinks = function(linkQueryResponse, enableDisplayTitle) {
		return new Promise(
			function(resolve) {
				let connections = (new ApiConnectionsBuilder()).connectionsFromApiResponses(linkQueryResponse)

				this._queryPageNodeInfo(connections.pages, enableDisplayTitle).done(function(pageInfoResponse) {
					let missingPages = Object.values(pageInfoResponse.query.pages)
						.filter(p => p.missing === '')
						.map(p => p.title);

					let displaytitles = [];
					pageInfoResponse.query.pages.forEach(function(page) {
						if ( page.pageprops && page.pageprops.displaytitle) {
							displaytitles[page.title] = page.pageprops.displaytitle;
						} else {
							displaytitles[page.title] = page.title;
						}
					});

					connections.pages.forEach(function(page) {
						page.displaytitle = displaytitles[page.title];
						if (missingPages.includes(page.title)) {
							page.isMissing = true;
						}
					});

					resolve(connections);
				});
			}.bind(this)
		);
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

	ApiPageConnectionRepo.prototype._queryPageNodeInfo = function(pageNodes, enableDisplayTitle) {
		let parameters = {
			action: 'query',
			titles: pageNodes.map(page => page.title),
			format: 'json',
			redirects: 'true'
		};
		if (enableDisplayTitle) {
			parameters.prop = [ 'pageprops' ];
		}
		return new mw.Api().get(parameters);
	};

	return ApiPageConnectionRepo;

}( window.mediaWiki, module.ApiConnectionsBuilder ) );
