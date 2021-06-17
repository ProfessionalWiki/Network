/**
 * Visjs specific
 */
module.NetworkData = ( function ( vis, mw ) {
	"use strict"

	let NetworkData = function(pageExclusionManager, labelMaxLength) {
		this.nodes = new vis.DataSet();
		this.edges = new vis.DataSet();
		this._pageExclusionManager = pageExclusionManager;
		this._labelMaxLength = labelMaxLength;
	};

	NetworkData.prototype.addPages = function(pages) {
		var maxlength = this._labelMaxLength;
		this.nodes.update(
			pages
				.filter(page => this._pageTitleIsAllowed(page.title))
				.map(function(page) {
					if (maxlength > 0 && page.displayTitle.length > maxlength) {
						page.label = page.displayTitle.slice(0, maxlength) + '\u2026';
					} else {
						page.label = page.displayTitle;
					}
					if (page.title !== page.displayTitle) {
						page.tooltip = page.displayTitle + ' <i>(' + page.title + ')</i>';
					} else {
						page.tooltip = page.displayTitle;
					}
					return page;
				})
				.map(function(page) {
					let node = {
						id: page.title,
						label: page.label,
						title: page.tooltip,

						getUrl: function() {
							if (page.isExternal) {
								return page.title;
							}
							let title = mw.Title.newFromText(page.title, page.ns);
							return  title === null ? '' : title.getUrl()
						}
					}

					if (page.isMissing) {
						node.group = 'redlink'
					} else if (page.isExternal) {
						node.group = 'externallink'
					} else {
						node.group = 'bluelink'
					}

					if (page.image !== undefined) {
						node.image = page.image;
						node.shape = 'image'
					}

					if (page.text !== undefined) {
						let txt = document.createElement("textarea");
						txt.innerHTML = page.text + '&nbsp;' + node.label;
						node.label = txt.value;
						node.shape = 'text'
					}

					return node
				})
		);
	}

	NetworkData.prototype._pageTitleIsAllowed = function(pageTitle) {
		return !this._pageExclusionManager.isExcluded(pageTitle);
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
