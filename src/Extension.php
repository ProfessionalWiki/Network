<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network;

use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\ParserFunctionNetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\SpecialNetworkPresenter;

class Extension {

	public static function getFactory(): self {
		return new self();
	}

	public function newNetworkFunction( NetworkPresenter $presenter, NetworkConfig $config ): NetworkUseCase {
		return new NetworkUseCase( $presenter, $config->getOptions() );
	}

	public function newParserFunctionNetworkPresenter(): ParserFunctionNetworkPresenter {
		return new ParserFunctionNetworkPresenter();
	}

	public function newSpecialNetworkPresenter(): SpecialNetworkPresenter {
		return new SpecialNetworkPresenter();
	}

}
