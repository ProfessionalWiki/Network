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

				let self = this;
				this._queryPageNodeInfo(connections.pages).done(function(pageInfoResponse) {
					let missingPages = Object.values(pageInfoResponse.query.pages)
						.filter(p => p.missing === '')
						.map(p => p.title);

					let displayTitles = [];
					let titleIcons = [];
					let fileIcons = [];
					let fileSearch = [];
					var index;
					for ( index in pageInfoResponse.query.pages ) {
						let page = pageInfoResponse.query.pages[index];
						if ( page.pageprops ) {
							if (page.pageprops.displaytitle) {
								displayTitles[page.title] = page.pageprops.displaytitle;
							} else {
								displayTitles[page.title] = page.title;
							}
							if ( page.pageprops.titleicons ) {
								try {
									let icons = JSON.parse(page.pageprops.titleicons);
									var icon;
									for ( icon in icons ) {
										if ( icons[icon].type === 'ooui' ) {
											titleIcons[page.title] = 'resources/lib/ooui/themes/wikimediaui/images/icons/' + icons[icon].icon;
											break;
										} else if ( icons[icon].type === 'file' ) {
											fileIcons[page.title] = icons[icon].icon;
											fileSearch[icons[icon].icon] = true;
											break;
										}
									}
								} catch (e) {
									// do nothing
								}
							}
						} else {
							displayTitles[page.title] = page.title;
						}
					}

					var titles = [];
					for ( index in fileSearch ) {
						titles.push( index );
					}
					var fileUrls = []
					self._queryFileUrls(titles).done(function(imageInfoResponse) {
						var pages = imageInfoResponse.query.pages;
						for ( index in pages ) {
							fileUrls[pages[index].title] = pages[index].imageinfo[0].url;
						}

						for ( index in fileIcons ) {
							titleIcons[index] = fileUrls[fileIcons[index]];
						}

						connections.pages.forEach(function(page) {
							if ( page.isExternal ) {
								page.displayTitle = page.title;
							} else {
								page.displayTitle = displayTitles[page.title];
							}
							if ( missingPages.includes( page.title ) ) {
								page.isMissing = true;
							}
							if ( titleIcons[page.title] !== undefined ) {
								page.image = titleIcons[page.title];
							}
						});

						resolve(connections);
					});
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

	ApiPageConnectionRepo.prototype._queryFileUrls = function(fileNames) {
		return new mw.Api().get({
			action: 'query',
			titles: fileNames,

			prop: ['imageinfo'],
			iiprop: 'url',

			format: 'json'
		});
	};

	return ApiPageConnectionRepo;

}( window.mediaWiki, module.ApiConnectionsBuilder ) );
