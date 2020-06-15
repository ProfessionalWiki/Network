<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkFunction {

	public function __construct() {
	}

	public function run( NetworkArguments $arguments ): NetworkResponse {
		return new NetworkResponse();
	}

}
