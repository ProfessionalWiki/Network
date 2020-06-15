<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network;

use MediaWiki\Extension\Network\NetworkFunction\NetworkArguments;
use MediaWiki\Extension\Network\NetworkFunction\NetworkFunction;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use Parser;
use PPFrame;

class Extension {

	public static function onParserFirstCallInit( Parser $parser ): void {
		$parser->setFunctionHook(
			'network',
			function( Parser $parser, PPFrame $frame, array $arguments ) {
				$parser->getOutput()->addModules( 'ext.network' );

				return self::getFactory()->newNetworkPresenter()->render(
					self::getFactory()->newNetworkFunction()->run(
						new NetworkArguments()
					)
				);
			},
			Parser::SFH_OBJECT_ARGS
		);
	}

	public static function getFactory(): self {
		return new self();
	}

	public function __construct() {

	}

	public function newNetworkFunction(): NetworkFunction {
		return new NetworkFunction();
	}

	public function newNetworkPresenter(): NetworkPresenter {
		return new NetworkPresenter();
	}



}
