module.ApiPageConnectionRepo = ( function ( $, mw ) {
	"use strict"

	let ApiPageConnectionRepo = function(pageName) {
		this._pageName = pageName;
	};

	ApiPageConnectionRepo.prototype.getConnections = function() {
		let deferred = $.Deferred();
		let self = this;

		let queries = $.when(this._queryBackLinks());

		queries.done(function(response) {
			console.log(response);

			let r = {
				"pages": self._getPagesFromResponse(response),
				"links": self._getLinksFromResponse(response)
			};

			console.log(r);

			deferred.resolve(r);
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

	ApiPageConnectionRepo.prototype._getPagesFromResponse = function(response) {
		let pages = [
			{ "title": this._pageName, "ns": -1 }
		];

		$.each(
			response.query.backlinks,
			function(_, page) {
				pages.push(page);
			}
		);

		return pages;
	};

	ApiPageConnectionRepo.prototype._getLinksFromResponse = function(response) {
		return response.query.backlinks.map(
			link => {
				return {
					"source": link.title,
					"target": this._pageName
				};
			}
		);
	};

	return ApiPageConnectionRepo;

}( window.jQuery, window.mediaWiki ) );
