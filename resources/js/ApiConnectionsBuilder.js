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

	ApiConnectionsBuilder.prototype.connectionsFromApiResponses = function(responses) {
		//console.log(JSON.stringify(responses, null, 4));

		let outgoingLinks = this._getOutgoingLinksFromResponse(responses);

		return {
			pages: this._buildPageList(responses.backLinks[0], outgoingLinks),
			links: this._buildLinksList(responses, outgoingLinks)
		};
	}

	ApiConnectionsBuilder.prototype._getOutgoingLinksFromResponse = function(responses) {
		let response = responses.outgoingLinks[0];
		return response.query.pages[Object.keys(response.query.pages)[0]].links || [];
	}

	ApiConnectionsBuilder.prototype._buildPageList = function(backLinks, outgoingLinks) {
		return Object.entries(this._buildPageMap(backLinks, outgoingLinks))
			.map(function([_, page]) {
				return {
					title: page.title,
				};
			});
	};

	ApiConnectionsBuilder.prototype._buildPageMap = function(backLinks, outgoingLinks) {
		let pages = {};
		pages[this._pageName] = {
			title: this._pageName
		};

		backLinks.query.backlinks.forEach(
			page => { pages[page.title] = page; }
		);

		outgoingLinks.forEach(
			page => { pages[page.title] = page; }
		);

		return pages;
	}

	ApiConnectionsBuilder.prototype._buildLinksList = function(responses, outgoingLinks) {
		return this._buildBackLinks(responses.backLinks[0]).concat(this._buildOutgoingLinks(outgoingLinks));
	}

	ApiConnectionsBuilder.prototype._buildBackLinks = function(response) {
		return response.query.backlinks.map(
			link => {
				return {
					from: link.title,
					to: this._pageName
				};
			}
		);
	};

	ApiConnectionsBuilder.prototype._buildOutgoingLinks = function(outgoingLinks) {
		return outgoingLinks.map(
			link => {
				return {
					from: this._pageName,
					to: link.title
				};
			}
		);
	};

	return ApiConnectionsBuilder;

}() );
