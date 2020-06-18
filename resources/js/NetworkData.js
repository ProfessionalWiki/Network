/**
 * Visjs specific
 */
module.NetworkData = ( function ( vis ) {
	"use strict"

	let NetworkData = function(pageBlacklist) {
		this.nodes = new vis.DataSet();
		this.edges = new vis.DataSet();
		this._pageBlacklist = pageBlacklist;
	};

	NetworkData.prototype.addPages = function(pages) {
		this.nodes.update(
			pages
				.filter(page => this._pageTitleIsAllowed(page.title))
				.map(function(page) {
					return {
						id: page.title,
						label: page.title,

						// shape: page.title === 'Main Page' ? 'box': 'ellipse',

						getUrl: function() {
							return window.mediaWiki.Title.newFromText(page.title, page.ns).getUrl();
						}
					}
				})
		);
	}

	NetworkData.prototype._pageTitleIsAllowed = function(pageTitle) {
		return !this._pageBlacklist.isBlacklisted(pageTitle);
	}

	NetworkData.prototype.addLinks = function(links) {
		this.edges.update(
			links
				.filter(link => this._pageTitleIsAllowed(link.from) && this._pageTitleIsAllowed(link.to))
				.map(function(link) {
					return {
						id: link.from + '|' + link.to,
						from: link.from,
						to: link.to,

						arrows: 'to'
					}
				})
		);
	}

	return NetworkData;

}( window.vis ) );
