/**
 * MediaWiki API specific, visjs agnostic
 */
module.ApiConnectionsBuilder = ( function () {
	"use strict"

	/**
	 * @param {string} pageName
	 */
	let ApiConnectionsBuilder = function(pageName) {
		this._pageName = pageName;
	};

	ApiConnectionsBuilder.prototype.connectionsFromApiResponses = function(apiResponse) {
		// console.log(JSON.stringify(apiResponse, null, 4));

		let centralPage = this._centralPageFromApiResponse(apiResponse);

		return {
			pages: this._buildPageList(centralPage),
			links: this._buildLinksList(centralPage)
		};
	}

	ApiConnectionsBuilder.prototype._centralPageFromApiResponse = function(apiResponse) {
		let page = apiResponse.query.pages[Object.keys(apiResponse.query.pages)[0]];

		return {
			outgoingLinks: page.links || [],
			incomingLinks: page.linkshere || [],
			externalLinks: page.extlinks || [],
			title: page.title,
		};
	};

	ApiConnectionsBuilder.prototype._buildPageList = function(centralPage) {
		return Object.entries(this._buildPageMap(centralPage))
			.map(function([_, page]) {
				return {
					title: page.title,
				};
			});
	};

	ApiConnectionsBuilder.prototype._buildPageMap = function(centralPage) {
		let pages = {};
		pages[centralPage.title] = {
			title: centralPage.title
		};

		centralPage.outgoingLinks.forEach(
			page => { pages[page.title] = page; }
		);

		centralPage.incomingLinks.forEach(
			page => { pages[page.title] = page; }
		);

		return pages;
	}

	ApiConnectionsBuilder.prototype._buildLinksList = function(centralPage) {
		return this._buildOutgoingLinks(centralPage.title, centralPage.outgoingLinks)
			.concat(this._buildIncomingLinks(centralPage.title, centralPage.incomingLinks));
	}

	ApiConnectionsBuilder.prototype._buildOutgoingLinks = function(sourceTitle, targetPages) {
		return targetPages.map(
			page => {
				return {
					from: sourceTitle,
					to: page.title
				};
			}
		);
	};

	ApiConnectionsBuilder.prototype._buildIncomingLinks = function(targetTitle, sourcePages) {
		return sourcePages.map(
			page => {
				return {
					from: page.title,
					to: targetTitle
				};
			}
		);
	};

	return ApiConnectionsBuilder;

}() );
