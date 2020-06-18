/**
 * Visjs agnostic
 */
module.PageBlacklist = ( function () {
	"use strict"

	/**
	 * @param {string[]} blacklistedPageNames
	 */
	let PageBlacklist = function(blacklistedPageNames) {
		this.pages = blacklistedPageNames;
	};

	/**
	 * @param {string} pageName
	 */
	PageBlacklist.prototype.isBlacklisted = function(pageName) {
		return this.pages.includes(pageName);
	}

	return PageBlacklist;

}() );
