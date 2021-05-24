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

	/**
	 * @return array
	 */
	public function getOptions() : array {
		return $this->options;
	}

	/**
	 * @param array $options
	 */

	public function setOptions( $options ) {
		$this->options = $options;
	}

	/**
	 * @return bool
	 */
	public function getExcludeTalkPages() : bool {
		return $this->excludeTalkPages;
	}

	/**
	 * @param bool $excludeTalkPages
	 */
	public function setExcludeTalkPages( bool $excludeTalkPages ) {
		$this->excludeTalkPages = $excludeTalkPages;
	}

	/**
	 * @return int[]
	 */
	public function getExcludedNamespaces() {
		return $this->excludedNamespaces;
	}

	/**
	 * @param int[] $excludeTalkPages
	 */
	public function setExcludedNamespaces( $excludedNamespaces ) {
		$this->excludedNamespaces = $excludedNamespaces;
	}

	/**
	 * @return bool
	 */
	public function getDefaultEnableDisplayTitle() : bool {
		return $this->defaultEnableDisplayTitle;
	}

	/**
	 * @param bool $excludeTalkPages
	 */
	public function setDefaultEnableDisplayTitle( bool $defaultEnableDisplayTitle ) {
		$this->defaultEnableDisplayTitle = $defaultEnableDisplayTitle;
	}

	/**
	 * @return int
	 */
	public function getDefaultLabelMaxLength() : int {
		return $this->defaultLabelMaxLength;
	}

	/**
	 * @param int $defaultLabelMaxLength
	 */
	public function setDefaultLabelMaxLength( int $defaultLabelMaxLength ) {
		$this->defaultLabelMaxLength = $defaultLabelMaxLength;
	}
}
