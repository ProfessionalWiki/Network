<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use MediaWiki\Extension\Network\Extension;
use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use Parser;

class NetworkFunction {

	/**
	 * @var NetworkConfig
	 */
	private $config;

	public function __construct( NetworkConfig $config ) {
		$this->config = $config;
	}

	public static function onParserFirstCallInit( Parser $parser ): void {
		$parser->setFunctionHook(
			'network',
			function() {
				return ( new self( new NetworkConfig() ) )->handleParserFunctionCall( ...func_get_args() );
			}
		);
	}

	/**
	 * @param Parser $parser
	 * @param string ...$arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( Parser $parser, string ...$arguments ) {
		$parser->getOutput()->addModules( [ 'ext.network' ] );
		$parser->getOutput()->addJsConfigVars( 'networkExcludeTalkPages', $this->config->getExcludeTalkPages() );

		$requestModel = new RequestModel();
		$requestModel->functionArguments = $arguments;
		$requestModel->excludedNamespaces = $this->config->getExcludedNamespaces();
		$requestModel->enableDisplayTitle = $this->config->getEnableDisplayTitle();
		$requestModel->labelMaxLength = $this->config->getLabelMaxLength();
		$requestModel->AllowOnlyLinksToPages = $this->config->getAllowOnlyLinksToPages();
		$requestModel->AllowLinkExpansion = $this->config->getAllowLinkExpansion();

		/**
		 * @psalm-suppress PossiblyNullReference
		 */
		$requestModel->renderingPageName = $parser->getTitle()->getFullText();
		$presenter = Extension::getFactory()->newParserFunctionNetworkPresenter();

		$this->newUseCase( $presenter, $this->config )->run( $requestModel );

		return $presenter->getReturnValue();
	}

	private function newUseCase( NetworkPresenter $presenter, NetworkConfig $config ): NetworkUseCase {
		return Extension::getFactory()->newNetworkFunction( $presenter, $config );
	}

}
