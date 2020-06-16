<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	public function __construct() {
	}

	public function run( RequestModel $request ): ResponseModel {
		$response = new ResponseModel();

		$functionArguments = $this->parserArgumentsToKeyValuePairs( $request->functionArguments );

		$response->pageName = $functionArguments['page'] ?? $request->renderingPageName;
		$response->cssClass = trim( 'network-visualization ' . ( trim( $functionArguments['class'] ?? '' ) ) );

		return $response;
	}

	private function parserArgumentsToKeyValuePairs( array $arguments ): array {
		$pairs = [];

		foreach ( $arguments as $argument ) {
			if ( false !== strpos( $argument, '=' ) ) {
				[$key, $value] = explode( '=', $argument );
				$pairs[trim( $key )] = trim( $value );
			}
		}

		return $pairs;
	}


}
