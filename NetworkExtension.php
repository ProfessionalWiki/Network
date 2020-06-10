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
				$parser->getOutput()->addModules( 'ext.network' );

				return [
					'<div id="NetworkCanvas" style="width: 1000px; height: 600px; border: 1px solid blue"></div>',
					'noparse' => true,
					'isHTML' => true,
				];
			},
			Parser::SFH_OBJECT_ARGS
		);
	}

}
