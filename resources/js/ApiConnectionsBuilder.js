module.ApiConnectionsBuilder = ( function ( $, mw, PageNode ) {
	"use strict"

	let ApiConnectionsBuilder = function(pageNames) {
		this._pageNames = pageNames;
	};

	ApiConnectionsBuilder.prototype.connectionsFromApiResponses = function(responses) {
		//console.log(JSON.stringify(responses, null, 4));

		let connections = {
			nodes: this._getNodesFromResponse(responses.backLinks[0], responses.outgoingLinks[0]),
			edges: this._buildBackLinks(responses.backLinks[0]).concat(this._buildOutgoingLinks(responses.outgoingLinks[0]))
		}

		// console.log(connections);

		return connections;
	}

	ApiConnectionsBuilder.prototype._getNodesFromResponse = function(backLinks, outgoingLinks) {
		let pages = {};
		pages[this._pageNames] = {
			title: this._pageNames[0],
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
					to: this._pageNames[0],
					arrows: 'to'
				};
			}
		);
	};

	ApiConnectionsBuilder.prototype._buildOutgoingLinks = function(response) {
		return response.query.pages[Object.keys(response.query.pages)[0]].links.map(
			link => {
				return {
					from: this._pageNames[0],
					to: link.title,
					arrows: 'to'
				};
			}
		);
	};

	return ApiConnectionsBuilder;

}( window.jQuery, window.mediaWiki ) );
