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
					"pages": self._getPagesFromResponse(backLinkResult[0], outgoingLinkResult[0]),
					"links": self._buildBackLinks(backLinkResult[0]).concat(self._buildOutgoingLinks(outgoingLinkResult[0]))
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

	ApiPageConnectionRepo.prototype._getPagesFromResponse = function(backLinks, outgoingLinks) {
		let pages = [
			{ "title": this._pageName, "ns": -1 }
		];

		$.each(
			backLinks.query.backlinks,
			function(_, page) {
				pages.push(page);
			}
		);

		$.each(
			outgoingLinks.query.pages[1].links,
			function(_, page) {
				pages.push(page);
			}
		);

		return pages;
	};

	ApiPageConnectionRepo.prototype._buildBackLinks = function(response) {
		return response.query.backlinks.map(
			link => {
				return {
					"source": link.title,
					"target": this._pageName
				};
			}
		);
	};

	ApiPageConnectionRepo.prototype._buildOutgoingLinks = function(response) {
		return response.query.pages[1].links.map(
			link => {
				return {
					"source": this._pageName,
					"target": link.title
				};
			}
		);
	};

	return ApiPageConnectionRepo;

}( window.jQuery, window.mediaWiki ) );
