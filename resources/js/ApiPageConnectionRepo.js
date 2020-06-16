module.ApiPageConnectionRepo = ( function ( $, mw, ApiConnectionsBuilder ) {
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
			let connectionsBuilder = new ApiConnectionsBuilder(self._pageName);

			deferred.resolve(
				connectionsBuilder.connectionsFromApiResponses({
					backLinks: backLinkResult,
					outgoingLinks: outgoingLinkResult
				})
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

	return ApiPageConnectionRepo;

}( window.jQuery, window.mediaWiki, module.ApiConnectionsBuilder ) );
