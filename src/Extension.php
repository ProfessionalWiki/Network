<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network;

use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\ParserFunctionNetworkPresenter;

class Extension {

	public static function getFactory(): self {
		return new self();
	}

	public function newNetworkFunction( NetworkPresenter $presenter ): NetworkUseCase {
		return new NetworkUseCase( $presenter, $GLOBALS['wgPageNetworkOptions'] );
	}

	public function newNetworkPresenter(): NetworkPresenter {
		return new ParserFunctionNetworkPresenter();
	}

}
