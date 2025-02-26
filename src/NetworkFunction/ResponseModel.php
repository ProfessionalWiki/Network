<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class ResponseModel {

	/** @var string[] */
	public array $pageNames;

	public string $cssClass;

	/** @var string[] */
	public array $excludedPages;

	/** @var int[] */
	public array $excludedNamespaces;

	public bool $enableDisplayTitle;

	public int $labelMaxLength;

	public array $visJsOptions;

}
