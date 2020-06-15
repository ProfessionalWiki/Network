<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use MediaWiki\Extension\Network\Extension;
use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use Parser;

class NetworkFunction {

	public static function onParserFirstCallInit( Parser $parser ): void {
		$parser->setFunctionHook(
			'network',
			function() {
				return ( new self() )->handleParserFunctionCall( ...func_get_args() );
			}
		);
	}

	public function handleParserFunctionCall( Parser $parser, ...$arguments ) {
		$args = new RequestModel();
		$args->functionArguments = $arguments;
		$args->renderingPageName = $parser->getTitle()->getFullText();

		return Extension::getFactory()->newNetworkPresenter( $parser )->render(
			Extension::getFactory()->newNetworkFunction()->run( $args )
		);
	}

}
