/**
 * Visjs specific
 */
module.NetworkData = ( function ( vis ) {
	"use strict"

	let NetworkData = function() {
		this.nodes = new vis.DataSet();
		this.edges = new vis.DataSet();
	};

	NetworkData.prototype.addPages = function(pages) {
		this.nodes.update(pages.map(
			page => {
				return {
					id: page.title,
					label: page.title,

					getUrl: function() {
						return window.mediaWiki.Title.newFromText(page.title, page.ns).getUrl();
					}
				}
			}
		));
	}

	NetworkData.prototype.addLinks = function(links) {
		this.edges.update(links.map(
			link => {
				return {
					id: link.from + '|' + link.to,
					from: link.from,
					to: link.to,

					arrows: 'to'
				}
			}
		));
	}

	return NetworkData;

}( window.vis ) );
