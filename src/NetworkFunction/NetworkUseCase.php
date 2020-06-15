<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	public function __construct() {
	}

	public function run( NetworkArguments $arguments ): NetworkResponse {
		$response = new NetworkResponse();

		$response->pageName = $arguments->pageName;
		$response->cssClass = 'network-visualization ' . trim( $arguments->cssClass );

		return $response;
	}

}
