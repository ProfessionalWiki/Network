module.ApiPageConnectionRepo = ( function ( $, mw ) {
	"use strict"

	let ApiPageConnectionRepo = function(pageName) {
		this._pageName = pageName;
	};

	ApiPageConnectionRepo.prototype.getConnections = function() {
		let deferred = $.Deferred();
		let self = this;

		$.when(
			this._queryBackLinks(),
			this._queryOutgoingLinks()
		).done(function(backLinkResult, outgoingLinkResult) {
			deferred.resolve(
				{
					"nodes": self._getNodesFromResponse(backLinkResult[0], outgoingLinkResult[0]),
					"edges": self._buildBackLinks(backLinkResult[0]).concat(self._buildOutgoingLinks(outgoingLinkResult[0]))
				}
			);
		})

		return deferred.promise();
	};

	ApiPageConnectionRepo.prototype._queryBackLinks = function() {
		return new mw.Api().get({
			action: 'query',
			list: 'backlinks',
			bltitle: this._pageName,
			bllimit: 'max',
			format: 'json',
			redirects: 'true'
		});
	};

	ApiPageConnectionRepo.prototype._queryOutgoingLinks = function() {
		return new mw.Api().get({
			action: 'query',
			prop: 'links',
			titles: this._pageName,
			pllimit: 'max',
			format: 'json',
			redirects: 'true'
		});
	};

	ApiPageConnectionRepo.prototype._getNodesFromResponse = function( backLinks, outgoingLinks) {
		let pages = {};
		pages[this._pageName] = {};

		$.each(
			backLinks.query.backlinks,
			function(_, page) {
				pages[page.title] = {};
			}
		);

		$.each(
			outgoingLinks.query.pages[1].links,
			function(_, page) {
				pages[page.title] = {};
			}
		);

		return Object.entries(pages).map(function([pageName, page]) {
			return {
				id: pageName,
				label: pageName,
			};
		});
	};

	ApiPageConnectionRepo.prototype._newPage = function(apiPage) {
		return new Page(apiPage.title);
	};

	ApiPageConnectionRepo.prototype._buildBackLinks = function(response) {
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

	ApiPageConnectionRepo.prototype._buildOutgoingLinks = function(response) {
		return response.query.pages[1].links.map(
			link => {
				return {
					from: this._pageName,
					to: link.title,
					arrows: 'to'
				};
			}
		);
	};

	return ApiPageConnectionRepo;

}( window.jQuery, window.mediaWiki ) );
