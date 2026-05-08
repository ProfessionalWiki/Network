<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network;

use MediaWiki\Extension\Network\NetworkFunction\IconResolver;
use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\ParserFunctionNetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\SpecialNetworkPresenter;
use MediaWiki\MainConfigNames;
use MediaWiki\MediaWikiServices;

class Extension {

	public function __construct(
		private readonly IconResolver $iconResolver
	) {
	}

	public static function getFactory(): self {
		$mainConfig = MediaWikiServices::getInstance()->getMainConfig();
		return new self(
			new IconResolver(
				MW_INSTALL_PATH . '/resources/lib/codex-icons/codex-icons.json',
				$mainConfig->get( MainConfigNames::ScriptPath )
			)
		);
	}

	public function newNetworkFunction( NetworkPresenter $presenter, NetworkConfig $config ): NetworkUseCase {
		return new NetworkUseCase( $presenter, $config->getOptions(), $this->iconResolver );
	}

	public function newParserFunctionNetworkPresenter(): ParserFunctionNetworkPresenter {
		return new ParserFunctionNetworkPresenter();
	}

	public function newSpecialNetworkPresenter(): SpecialNetworkPresenter {
		return new SpecialNetworkPresenter();
	}

}
