<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network;

use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\TagNetworkPresenter;

class Extension {

	public static function getFactory(): self {
		return new self();
	}

	public function newNetworkFunction( NetworkPresenter $presenter ): NetworkUseCase {
		return new NetworkUseCase( $presenter );
	}

	public function newNetworkPresenter(): NetworkPresenter {
		return new TagNetworkPresenter( $GLOBALS['wgPageNetworkOptions'] );
	}

}
