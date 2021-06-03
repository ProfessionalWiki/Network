<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkConfig {
	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var bool
	 */
	private $excludeTalkPages;

	/**
	 * @var int[]
	 */
	private $excludedNamespaces;

	/**
	 * @var bool
	 */
	private $enableDisplayTitle;

	/**
	 * @var int
	 */
	private $labelMaxLength;

	public function __construct() {
		$this->options = $GLOBALS['wgPageNetworkOptions'];
		$this->excludeTalkPages = (bool)$GLOBALS['wgPageNetworkExcludeTalkPages'];
		$this->excludedNamespaces = array_map( 'strval', $GLOBALS['wgPageNetworkExcludedNamespaces'] );
		$this->enableDisplayTitle = (bool)$GLOBALS['wgPageNetworkEnableDisplayTitle'];
		$this->labelMaxLength = (int)$GLOBALS['wgPageNetworkLabelMaxLength'];
	}

	public function getOptions(): array {
		return $this->options;
	}

	public function getExcludeTalkPages(): bool {
		return $this->excludeTalkPages;
	}

	public function getExcludedNamespaces(): array {
		return $this->excludedNamespaces;
	}

	public function getEnableDisplayTitle(): bool {
		return $this->enableDisplayTitle;
	}

	public function getLabelMaxLength(): int {
		return $this->labelMaxLength;
	}
}
