<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class RequestModel {

	/** @var string[] */
	public array $functionArguments;

	public string $renderingPageName;

	/** @var int[] */
	public array $excludedNamespaces;

	public bool $enableDisplayTitle;

	public int $labelMaxLength;

}
