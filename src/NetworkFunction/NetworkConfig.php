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
	private $defaultEnableDisplayTitle;

	/**
	 * @var int
	 */
	private $defaultLabelMaxLength;

	public function __construct() {
		$this->options = $GLOBALS['wgPageNetworkOptions'];
		$this->excludeTalkPages = (bool)$GLOBALS['wgPageNetworkExcludeTalkPages'];
		$this->excludedNamespaces = $GLOBALS['wgPageNetworkExcludedNamespaces'];
		$this->defaultEnableDisplayTitle = (bool)$GLOBALS['wgPageNetworkDefaultEnableDisplayTitle'];
		$this->defaultLabelMaxLength = (int)$GLOBALS['wgPageNetworkDefaultLabelMaxLength'];
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

	public function getDefaultEnableDisplayTitle(): bool {
		return $this->defaultEnableDisplayTitle;
	}

	public function getDefaultLabelMaxLength(): int {
		return $this->defaultLabelMaxLength;
	}
}
