/**
 * Visjs agnostic
 */
module.PageBlacklist = ( function ( mw ) {
	"use strict"

	/**
	 * @param {string[]} blacklistedPageNames
	 * @param {int[]} excludedNamespaces
	 * @param {boolean} excludeTalkPages
	 */
	let PageBlacklist = function(blacklistedPageNames, excludedNamespaces, excludeTalkPages) {
		this.pages = blacklistedPageNames;
		this.namespaces = excludedNamespaces;
		this.excludeTalk = excludeTalkPages;
	};

	/**
	 * @param {string} pageName
	 */
	PageBlacklist.prototype.isBlacklisted = function(pageName) {
		if (this.pages.includes(pageName)) {
			return true;
		}

		let title = mw.Title.newFromText(pageName);

		return (this.excludeTalk && title.isTalkPage())
			|| this.namespaces.includes(title.getNamespaceId());
	}

	return PageBlacklist;

}( window.mediaWiki ) );
