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
		$requestModel = new RequestModel();
		$requestModel->functionArguments = $arguments;
		$requestModel->renderingPageName = $parser->getTitle()->getFullText();

		$presenter = Extension::getFactory()->newNetworkPresenter();
		Extension::getFactory()->newNetworkFunction( $presenter )->run( $requestModel );

		$parser->getOutput()->addModules( $presenter->getResourceModules() );
		return $presenter->getParserFunctionReturnValue();
	}

}
