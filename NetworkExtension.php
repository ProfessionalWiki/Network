<?php

declare( strict_types = 1 );

namespace MediaWiki\Network;

use Parser;
use PPFrame;

class NetworkExtension {

	public static function onParserFirstCallInit( Parser $parser ): void {
		$parser->setFunctionHook(
			'network',
			function( Parser $parser, PPFrame $frame, array $arguments ) {
				return var_export( $arguments, true );
			},
			Parser::SFH_OBJECT_ARGS
		);
	}

}
