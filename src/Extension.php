<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network;

use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use Parser;

class Extension {

	public static function getFactory(): self {
		return new self();
	}

	public function newNetworkFunction(): NetworkUseCase {
		return new NetworkUseCase();
	}

	public function newNetworkPresenter( Parser $parser ): NetworkPresenter {
		return new NetworkPresenter( $parser );
	}

}
