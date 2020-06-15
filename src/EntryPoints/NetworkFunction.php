<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use MediaWiki\Extension\Network\Extension;
use MediaWiki\Extension\Network\NetworkFunction\NetworkArguments;
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
		return Extension::getFactory()->newNetworkPresenter( $parser )->render(
			Extension::getFactory()->newNetworkFunction()->run(
				$this->parserToNetworkArguments( $arguments )
			)
		);
	}

	private function parserToNetworkArguments( array $parserArguments ): NetworkArguments {
		$args = new NetworkArguments();

		$arguments = $this->parserArgumentsToKeyValuePairs( $parserArguments );
		$args->pageName = $arguments['page'] ?? '';
		$args->cssClass = $arguments['class'] ?? '';

		return $args;
	}

	private function parserArgumentsToKeyValuePairs( array $arguments ): array {
		$pairs = [];

		foreach ( $arguments as $argument ) {
			[$key, $value] = explode( '=', $argument );
			$pairs[$key] = $value;
		}

		return $pairs;
	}

}
