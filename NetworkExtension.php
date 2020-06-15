<?php

declare( strict_types = 1 );

namespace MediaWiki\Network;

use Parser;
use PPFrame;

class NetworkExtension {

	public static function onParserFirstCallInit( Parser $parser ): void {
		static $idCounter = 1;

		$parser->setFunctionHook(
			'network',
			function( Parser $parser, PPFrame $frame, array $arguments ) use ( $idCounter ) {
				$parser->getOutput()->addModules( 'ext.network' );

				return [
					\Html::element(
						'div',
						[
							'class' => 'network-visualization',
							'id' => 'network-viz-' . (string)$idCounter++
						]
					),
					'noparse' => true,
					'isHTML' => true,
				];
			},
			Parser::SFH_OBJECT_ARGS
		);
	}

}
