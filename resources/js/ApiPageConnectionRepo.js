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
				let pagesToAdd = pageNames.filter(page => !this._addedPages.includes(page));

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

				this._queryPageNodeInfo(connections.pages).done(
					function(pageInfoResponse) {
						let pages = Object.values(pageInfoResponse.query.pages)

						let missingPages = this._getMissingPages(pages)

						let displayTitles = this._getDisplayTitles(pages)

						this._getTitleIcons(pages)
							.then(function(titleIcons) {

								connections.pages.forEach(function(page) {
									if (missingPages.includes(page.title)) {
										page.isMissing = true;
									}

									if (page.isExternal) {
										page.displayTitle = page.title;
									} else {
										page.displayTitle = displayTitles[page.title];
									}

									if (titleIcons.images[page.title] !== undefined) {
										page.image = titleIcons.images[page.title];
									} else if (titleIcons.text[page.title] !== undefined) {
										page.text = titleIcons.text[page.title];
									}
								});

								resolve(connections);
							})
					}.bind(this)
				)
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
		let titles = pageNodes
			.filter(page => page.isExternal !== true)
			.map(page => page.title)

		let parameters = {
			action: 'query',
			titles: titles,

			format: 'json',
			redirects: 'true'
		}
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

			format: 'json',
			redirects: 'true'
		});
	};

	ApiPageConnectionRepo.prototype._getMissingPages = function(pages) {
		return pages
			.filter(page => page.missing === '')
			.map(page => page.title);
	}

	ApiPageConnectionRepo.prototype._getDisplayTitles = function(pages) {
		let displayTitles = []
		pages.forEach(function(page) {
			if (page.pageprops && page.pageprops.displaytitle) {
				displayTitles[page.title] = page.pageprops.displaytitle
			} else {
				displayTitles[page.title] = page.title
			}
		})
		return displayTitles
	}

	ApiPageConnectionRepo.prototype._getTitleIcons = function(pages) {
		return new Promise(
			function(resolve) {
				let images = [];
				let text = [];
				let fileIcons = [];
				let fileSearch = [];
				pages.forEach(function(page) {
					if (page.pageprops && page.pageprops.titleicons) {
						try {
							let icons = JSON.parse(page.pageprops.titleicons);
							for (let index in icons) {
								let icon = icons[index]
								if (icon.type === 'ooui') {
									images[page.title] = 'resources/lib/ooui/themes/wikimediaui/images/icons/' + icon.icon;
									break
								} else if (icon.type === 'file') {
									fileIcons[page.title] = icon.icon;
									fileSearch[icon.icon] = true;
									break
								} else if (icon.type === 'unicode') {
									text[page.title] = icon.icon;
									break
								}
							}
						} catch (e) {
							// do nothing
						}
					}
				})

				let files = [];
				for (let index in fileSearch) {
					files.push(index);
				}
				this._queryFileUrls(files)
					.done(function(imageInfoResponse) {
						var filePages = Object.values(imageInfoResponse.query.pages);
						let fileUrls = [];
						filePages.forEach(function(filePage) {
							fileUrls[filePage.title] = filePage.imageinfo[0].url;
						})

						for (let page in fileIcons) {
							images[page] = fileUrls[fileIcons[page]];
						}

						resolve({'images': images, 'text': text});
					})
			}.bind(this)
		)
	}

	return ApiPageConnectionRepo;

}( window.mediaWiki, module.ApiConnectionsBuilder ) );
