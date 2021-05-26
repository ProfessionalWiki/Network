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

	public static function addMediaWiki131compat(): void {
		// mediawiki.api.edit is present in 1.31 but not 1.32
		// Once Maps requires MW 1.32+, this can be removed after replacing usage of mediawiki.api.edit
		if ( version_compare( $GLOBALS['wgVersion'], '1.32', '>=' ) ) {
			$GLOBALS['wgResourceModules']['mediawiki.api.edit'] = [
				'dependencies' => [
					'mediawiki.api'
				],
				'targets' => [ 'desktop', 'mobile' ]
			];
		}
	}

}
