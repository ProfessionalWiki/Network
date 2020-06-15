<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	public function __construct() {
	}

	public function run( NetworkArguments $arguments ): NetworkResponse {
		$response = new NetworkResponse();

		$functionArguments = $this->parserArgumentsToKeyValuePairs( $arguments->functionArguments );

		$response->pageName = $functionArguments['page'] ?? $arguments->renderingPageName;
		$response->cssClass = 'network-visualization ' . ( $functionArguments['class'] ?? '' );

		return $response;
	}

	private function parserArgumentsToKeyValuePairs( array $arguments ): array {
		$pairs = [];

		foreach ( $arguments as $argument ) {
			if ( false !== strpos( $argument, '=' ) ) {
				[$key, $value] = explode( '=', $argument );
				$pairs[$key] = $value;
			}
		}

		return $pairs;
	}

}
