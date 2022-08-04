<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class ResponseModel {

	public /* string[] */ $pageNames;
	public /* string */ $cssClass;
	public /* string[] */ $excludedPages;
	public /* int[] */ $excludedNamespaces;
	public /* bool */ $enableDisplayTitle;
	public /* int */ $labelMaxLength;
	public /* array */ $visJsOptions;
	public  /* bool */ $AllowOnlyLinksToPages;
	public  /* bool */ $AllowLinkExpansion;

}
