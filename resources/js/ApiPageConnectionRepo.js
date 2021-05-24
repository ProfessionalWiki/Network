/**
 * MediaWiki API specific, visjs agnostic
 */
module.ApiPageConnectionRepo = ( function ( mw, ApiConnectionsBuilder ) {
	"use strict"

	/**
	 * @param {boolean} enableDisplayTitle
	 * @constructor
	 */
	let ApiPageConnectionRepo = function(enableDisplayTitle) {
		this._addedPages = [];
		this._enableDisplayTitle = enableDisplayTitle;
	};

	/**
	 * @param {string[]} pageNames
	 * @return {Promise}
	 */
	ApiPageConnectionRepo.prototype.addConnections = function(pageNames) {
		return new Promise(
			function(resolve) {
				let pagesToAdd =  pageNames.filter(p => !this._addedPages.includes(p));

				if (pagesToAdd.length === 0) {
					resolve({pages: [], links: []});
				} else {
					this._addedPages.concat(pagesToAdd);

					this._queryLinks(pagesToAdd).done(
						function(apiResponse) {
							this._apiResponseToPagesAndLinks(apiResponse).then(connections => resolve(connections))
						}.bind(this)
					);
				}
			}.bind(this)
		);
	};

	ApiPageConnectionRepo.prototype._apiResponseToPagesAndLinks = function(linkQueryResponse) {
		return new Promise(
			function(resolve) {
				let connections = (new ApiConnectionsBuilder()).connectionsFromApiResponses(linkQueryResponse)

				this._queryPageNodeInfo(connections.pages).done(function(pageInfoResponse) {
					let missingPages = Object.values(pageInfoResponse.query.pages)
						.filter(p => p.missing === '')
						.map(p => p.title);

					let displayTitles = [];
					var index;
					for ( index in pageInfoResponse.query.pages ) {
						let page = pageInfoResponse.query.pages[index];
						if ( page.pageprops && page.pageprops.displaytitle) {
							displayTitles[page.title] = page.pageprops.displaytitle;
						} else {
							displayTitles[page.title] = page.title;
						}
					};

					connections.pages.forEach(function(page) {
						if ( page.isExternal ) {
							page.displayTitle = page.title;
						} else {
							page.displayTitle = displayTitles[page.title];
						}
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

	ApiPageConnectionRepo.prototype._queryPageNodeInfo = function(pageNodes) {
		let parameters = {
			action: 'query',
			titles: pageNodes.filter(page => page.isExternal !== true).map(page => page.title),
			format: 'json',
			redirects: 'true'
		};
		if (this._enableDisplayTitle) {
			parameters.prop = [ 'pageprops' ];
		}
		return new mw.Api().get(parameters);
	};

	return ApiPageConnectionRepo;

}( window.mediaWiki, module.ApiConnectionsBuilder ) );
