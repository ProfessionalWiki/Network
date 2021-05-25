<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use MediaWiki\Extension\Network\Extension;
use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use OutputPage;
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
				$args = func_get_args();
				$parser = $args[0];
				array_shift( $args );
				return ( new self( new NetworkConfig() ) )->handleParserFunctionCall(
					$parser->getOutput(),
					$parser->getTitle()->getFullText(),
					$args
				);
			}
		);
	}

	/**
	 * @param OutputPage|ParserOutput $output
	 * @param string $renderingPageName
	 * @param string[] $arguments
	 * @return array|string
	 */
	public function handleParserFunctionCall( $output, $renderingPageName, $arguments ) {
		$output->addModules( [ 'ext.network' ] );
		$output->addJsConfigVars( 'networkExcludedNamespaces', $this->config->getExcludedNamespaces() );
		$output->addJsConfigVars( 'networkExcludeTalkPages', $this->config->getExcludeTalkPages() );

		$requestModel = new RequestModel();
		$requestModel->functionArguments = $arguments;
		$requestModel->defaultEnableDisplayTitle = $this->config->getDefaultEnableDisplayTitle();
		$requestModel->defaultLabelMaxLength = $this->config->getDefaultLabelMaxLength();

		/**
		 * @psalm-suppress PossiblyNullReference
		 */
		$requestModel->renderingPageName = $renderingPageName;
		$presenter = Extension::getFactory()->newNetworkPresenter();

		$this->newUseCase( $presenter, $this->config )->run( $requestModel );

		return $presenter->getParserFunctionReturnValue();
	}

	private function newUseCase( NetworkPresenter $presenter, NetworkConfig $config ): NetworkUseCase {
		return Extension::getFactory()->newNetworkFunction( $presenter, $config );
	}

}
