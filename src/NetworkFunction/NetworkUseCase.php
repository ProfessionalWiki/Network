<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	public function __construct() {
	}

	public function run( RequestModel $request ): ResponseModel {
		$response = new ResponseModel();

		$functionArguments = $this->parserArgumentsToKeyValuePairs( $request->functionArguments );

		$response->pageNames = $this->getPageNames( $request, $functionArguments );
		$response->cssClass = $this->getCssClass( $functionArguments );

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

	private function getPageNames( RequestModel $request, array $arguments ): array {
		if ( !array_key_exists( 'page', $arguments ) ) {
			return [ $request->renderingPageName ];
		}

		return explode( ';', $arguments['page'] );
	}

	private function getCssClass( array $arguments ): string {
		return trim( 'network-visualization ' . ( trim( $arguments['class'] ?? '' ) ) );
	}

}
