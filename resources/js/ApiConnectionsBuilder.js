module.ApiConnectionsBuilder = ( function ( $, mw ) {
	"use strict"

	let ApiConnectionsBuilder = function(pageName) {
		this._pageName = pageName;
	};

	ApiConnectionsBuilder.prototype.connectionsFromApiResponses = function(responses) {
		//console.log(JSON.stringify(responses, null, 4));

		return {
			nodes: this._getNodesFromResponse(responses.backLinks[0], responses.outgoingLinks[0]),
			edges: this._buildBackLinks(responses.backLinks[0]).concat(this._buildOutgoingLinks(responses.outgoingLinks[0]))
		};
	}

	ApiConnectionsBuilder.prototype._getNodesFromResponse = function(backLinks, outgoingLinks) {
		let pages = {};
		pages[this._pageName] = {
			title: this._pageName,
			ns: 0,
		};

		$.each(
			backLinks.query.backlinks,
			function(_, page) {
				pages[page.title] = page;
			}
		);

		$.each(
			outgoingLinks.query.pages[Object.keys(outgoingLinks.query.pages)[0]].links,
			function(_, page) {
				pages[page.title] = page;
			}
		);

		return Object.entries(pages).map(function([_, page]) {
			return {
				id: page.title,
				label: page.title,
				pageName: page.title,
				pageNs: page.ns,
			};
		});
	};

	ApiConnectionsBuilder.prototype._buildBackLinks = function(response) {
		return response.query.backlinks.map(
			link => {
				return {
					from: link.title,
					to: this._pageName,
					arrows: 'to'
				};
			}
		);
	};

	ApiConnectionsBuilder.prototype._buildOutgoingLinks = function(response) {
		let page = response.query.pages[Object.keys(response.query.pages)[0]];

		return (page.links || []).map(
			link => {
				return {
					from: this._pageName,
					to: link.title,
					arrows: 'to'
				};
			}
		);
	};

	return ApiConnectionsBuilder;

}( window.jQuery, window.mediaWiki ) );
