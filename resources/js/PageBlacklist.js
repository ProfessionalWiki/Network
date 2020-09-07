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

		return title === null
			|| (this.excludeTalk && this._isTalkPage(title))
			|| this.namespaces.includes(title.getNamespaceId());
	};

	PageBlacklist.prototype._isTalkPage = function(title) {
		// Can replace this function with title.isTalkPage() on MW 1.35+
		let namespaceId = title.getNamespaceId();
		return !!(namespaceId > 0 && namespaceId % 2);
	};

	return PageBlacklist;

}( window.mediaWiki ) );
