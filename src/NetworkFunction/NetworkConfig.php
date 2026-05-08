<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

use MediaWiki\MediaWikiServices;

class NetworkConfig {

	private readonly array $options;
	private readonly bool $excludeTalkPages;
	/** @var string[] */
	private readonly array $excludedNamespaces;
	private readonly bool $enableDisplayTitle;
	private readonly int $labelMaxLength;

	public function __construct() {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		$this->options = $config->get( 'PageNetworkOptions' );
		$this->excludeTalkPages = (bool)$config->get( 'PageNetworkExcludeTalkPages' );
		$this->excludedNamespaces = array_map( 'strval', $config->get( 'PageNetworkExcludedNamespaces' ) );
		$this->enableDisplayTitle = (bool)$config->get( 'PageNetworkEnableDisplayTitle' );
		$this->labelMaxLength = (int)$config->get( 'PageNetworkLabelMaxLength' );
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
