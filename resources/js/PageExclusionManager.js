/**
 * Visjs agnostic
 */
module.PageExclusionManager = ( function (mw ) {
	"use strict"

	/**
	 * @param {string[]} excludedPageNames
	 * @param {int[]} excludedNamespaces
	 * @param {boolean} excludeTalkPages
	 */
	let PageExclusionManager = function(excludedPageNames, excludedNamespaces, excludeTalkPages) {
		this.pages = excludedPageNames;
		this.namespaces = excludedNamespaces;
		this.excludeTalk = excludeTalkPages;
	};

	/**
	 * @param {string} pageName
	 */
	PageExclusionManager.prototype.isExcluded = function(pageName) {
		if (this.pages.includes(pageName)) {
			return true;
		}

		let title = mw.Title.newFromText(pageName);

		return title === null
			|| (this.excludeTalk && this._isTalkPage(title))
			|| this.namespaces.includes(title.getNamespaceId().toString());
	};

	PageExclusionManager.prototype._isTalkPage = function(title) {
		// Can replace this function with title.isTalkPage() on MW 1.35+
		let namespaceId = title.getNamespaceId();
		return !!(namespaceId > 0 && namespaceId % 2);
	};

	return PageExclusionManager;

}( window.mediaWiki ) );
