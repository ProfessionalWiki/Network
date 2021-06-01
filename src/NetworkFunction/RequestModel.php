<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class RequestModel {

	public /* string[] */ $functionArguments;
	public /* string */ $renderingPageName;
	public /* int[] */ $excludedNamespaces;
	public /* bool */ $enableDisplayTitle;
	public /* int */ $labelMaxLength;

}
