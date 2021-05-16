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
		$parser->setFunctionHook(
			'network',
			function() {
				return ( new self() )->handleParserFunctionCall( ...func_get_args() );
			}
		);
	}

	/**
	 * @param Parser $parser
	 * @param string[] ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( Parser $parser, ...$arguments ) {
		$parser->getOutput()->addModules( [ 'ext.network' ] );
		$parser->getOutput()->addJsConfigVars( 'networkExcludedNamespaces', $GLOBALS['wgPageNetworkExcludedNamespaces'] );
		$parser->getOutput()->addJsConfigVars( 'networkExcludeTalkPages', $GLOBALS['wgPageNetworkExcludeTalkPages'] );

		$requestModel = new RequestModel();
		$requestModel->functionArguments = $arguments;
		$requestModel->defaultEnableDisplayTitle = (bool)$GLOBALS['wgPageNetworkDefaultEnableDisplayTitle'];
		$requestModel->defaultLabelMaxLength = (int)$GLOBALS['wgPageNetworkDefaultLabelMaxLength'];

		/**
		 * @psalm-suppress PossiblyNullReference
		 */
		$requestModel->renderingPageName = $parser->getTitle()->getFullText();
		$presenter = Extension::getFactory()->newNetworkPresenter();

		$this->newUseCase( $presenter )->run( $requestModel );

		return $presenter->getParserFunctionReturnValue();
	}

	private function newUseCase( NetworkPresenter $presenter ): NetworkUseCase {
		return Extension::getFactory()->newNetworkFunction( $presenter );
	}

}
