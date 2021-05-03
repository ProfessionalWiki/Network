/**
 * Visjs specific
 */
module.NetworkData = ( function ( vis, mw ) {
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
					let node = {
						id: page.title,
						label: page.displaytitle,

						getUrl: function() {
							let title = mw.Title.newFromText(page.title, page.ns);
							return  title === null ? '' : title.getUrl();
						}
					}

					if (page.isMissing) {
						node.color = {
							background: 'lightgrey',
							border: 'grey',
							highlight: {
								background: 'lightgrey',
								border: 'grey',
							}
						};
						node.font = {
							color: '#ba0000'
						};
					}

					return node;
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

						arrows: 'to',
						color: {
							inherit: 'to'
						}
					}
				})
		);
	}

	return NetworkData;

}( window.vis, window.mediaWiki ) );
