<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

use MediaWiki\MediaWikiServices;

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
	 * @var string[]
	 */
	private $excludedNamespaces;

	/**
	 * @var bool
	 */
	private $enableDisplayTitle;
	private $AllowOnlyLinksToPages;
	private $AllowLinkExpansion;

	/**
	 * @var int
	 */
	private $labelMaxLength;

	public function __construct() {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$this->options = $config->get( 'PageNetworkOptions' );
		$this->excludeTalkPages = (bool)$config->get( 'PageNetworkExcludeTalkPages' );
		$this->excludedNamespaces = array_map( 'strval', $config->get( 'PageNetworkExcludedNamespaces' ) );
		$this->enableDisplayTitle = (bool)$config->get( 'PageNetworkEnableDisplayTitle' );
		$this->labelMaxLength = (int)$config->get( 'PageNetworkLabelMaxLength' );
		$this->AllowOnlyLinksToPages = (bool)$config->get( 'PageNetworkAllowOnlyLinksToPages');
		$this->AllowLinkExpansion = (bool)$config->get( 'PageNetworkAllowLinkExpansion');
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

	public function getAllowOnlyLinksToPages(): bool {
		return $this->AllowOnlyLinksToPages;
	}

	public function getAllowLinkExpansion(): bool {
		return $this->AllowLinkExpansion;
	}
}
