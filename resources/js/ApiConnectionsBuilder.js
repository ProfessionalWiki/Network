module.ApiConnectionsBuilder = ( function () {
	"use strict"

	let ApiConnectionsBuilder = function(pageName) {
		this._pageName = pageName;
	};

	ApiConnectionsBuilder.prototype.connectionsFromApiResponses = function(responses) {
		//console.log(JSON.stringify(responses, null, 4));

		let outgoingLinks = this._getOutgoingLinksFromResponse(responses);

		return {
			nodes: this._buildNodeList(responses.backLinks[0], outgoingLinks),
			edges: this._buildBackLinks(responses.backLinks[0]).concat(this._buildOutgoingLinks(outgoingLinks))
		};
	}

	ApiConnectionsBuilder.prototype._getOutgoingLinksFromResponse = function(responses) {
		let response = responses.outgoingLinks[0];
		return response.query.pages[Object.keys(response.query.pages)[0]].links || [];
	}

	ApiConnectionsBuilder.prototype._buildNodeList = function(backLinks, outgoingLinks) {
		let pages = {};
		pages[this._pageName] = {
			title: this._pageName,
			ns: 0,
		};

		backLinks.query.backlinks.forEach(
			page => { pages[page.title] = page; }
		);

		outgoingLinks.forEach(
			page => { pages[page.title] = page; }
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

	ApiConnectionsBuilder.prototype._buildOutgoingLinks = function(outgoingLinks) {
		return outgoingLinks.map(
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

}() );
