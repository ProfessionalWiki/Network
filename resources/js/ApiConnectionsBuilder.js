/**
 * MediaWiki API specific, visjs agnostic
 */
module.ApiConnectionsBuilder = ( function () {
	"use strict"

	let ApiConnectionsBuilder = function() {
	};

	ApiConnectionsBuilder.prototype.connectionsFromApiResponses = function(apiResponse) {
		// console.log(JSON.stringify(apiResponse, null, 4));

		let centralPages = this._centralPagesFromApiResponse(apiResponse);

		return {
			pages: this._buildPageList(centralPages),
			links: this._buildLinksList(centralPages)
		};
	}

	ApiConnectionsBuilder.prototype._centralPagesFromApiResponse = function( apiResponse) {
		return Object.entries(apiResponse.query.pages)
			.map(function([_, page]) {
				return {
					outgoingLinks: page.links || [],
					incomingLinks: page.linkshere || [],
					externalLinks: page.extlinks || [],
					title: page.title,
				};
			});
	};

	ApiConnectionsBuilder.prototype._buildPageList = function(centralPages) {
		return Object.entries(this._buildPageMap(centralPages))
			.map(function([_, page]) {
				return {
					title: page.title,
				};
			});
	};

	ApiConnectionsBuilder.prototype._buildPageMap = function(centralPages) {
		let pages = {};

		centralPages.forEach(function(centralPage) {
			pages[centralPage.title] = {
				title: centralPage.title
			};

			centralPage.outgoingLinks.forEach(
				page => { pages[page.title] = page; }
			);

			centralPage.incomingLinks.forEach(
				page => { pages[page.title] = page; }
			);
		});

		return pages;
	}

	ApiConnectionsBuilder.prototype._buildLinksList = function(centralPages) {
		let a = centralPages.map(
			centralPage => {
				return this._buildOutgoingLinks(centralPage.title, centralPage.outgoingLinks)
					.concat(this._buildIncomingLinks(centralPage.title, centralPage.incomingLinks));
			}
		);

		return a.flat();
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
