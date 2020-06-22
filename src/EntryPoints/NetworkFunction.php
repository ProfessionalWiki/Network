<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use MediaWiki\Extension\Network\Extension;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use Parser;

class NetworkFunction {

	public static function onParserFirstCallInit( Parser $parser ): void {
		$parser->setHook(
			'network',
			function() {
				return ( new self() )->handleParserFunctionCall( ...func_get_args() );
			}
		);
	}

	/**
	 * @param string $text
	 * @param Parser $parser
	 * @param string[] ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( string $text, array $arguments, Parser $parser ) {
		$requestModel = new RequestModel();
		$requestModel->functionArguments = $arguments;

		/**
		 * @psalm-suppress PossiblyNullReference
		 */
		$requestModel->renderingPageName = $parser->getTitle()->getFullText();
		$presenter = $this->newPresenterFromJsonOptions( $text );

		$this->newUseCase( $presenter )->run( $requestModel );

		$parser->getOutput()->addModules( $presenter->getResourceModules() );
		return $presenter->getParserFunctionReturnValue();
	}

	private function newPresenterFromJsonOptions( string $jsonOptions ): NetworkPresenter {
		return Extension::getFactory()->newNetworkPresenter(
			json_decode( $jsonOptions, true ) ?? []
		);
	}

	private function newUseCase( NetworkPresenter $presenter ): NetworkUseCase {
		return Extension::getFactory()->newNetworkFunction( $presenter );
	}

}
