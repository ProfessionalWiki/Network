<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use Html;
use IncludableSpecialPage;
use MediaWiki\Extension\Network\Extension;
use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use MediaWiki\MediaWikiServices;
use Message;
use Title;
use WebRequest;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SpecialNetwork extends IncludableSpecialPage {

	public function __construct() {
		parent::__construct( 'Network' );
	}

	public function execute( $subPage ): void {
		$this->setHeaders();

		$config = new NetworkConfig();
		$params = $this->parseParams( $this->getRequest(), $config );

		if ( $this->getRequest()->getCheck( 'pages' ) ) {
			$this->getOutput()->addHTML( $this->showGraph( $this->formatParams( $params ), $config ) );
		}

		if ( !$this->including() ) {
			$this->showForm( $params, $config );
		}
	}

	private function parseParams( WebRequest $request, NetworkConfig $config ): array {
		$params = [];

		if ( $request->getCheck( 'pages' ) ) {
			$params['pages'] = $request->getText( 'pages' );
			if ( $params['pages'] === '' ) {
				$params['pages'] = Title::newMainPage()->getPrefixedText();
			}
		} else {
			$params['pages'] = '';
		}

		$params['exclude'] = $request->getText( 'exclude', '' );

		/**
		 * @psalm-suppress PossiblyNullArgument
		 * @psalm-suppress PossiblyNullArrayAccess
		 */
		$params['excludedNamespaces'] = array_map(
			'strval',
			$request->getArray( 'excludedNamespaces', $config->getExcludedNamespaces() )
		);

		$params['class'] = $request->getText( 'class', '' );

		$params['options'] = $request->getText( 'options', json_encode( $config->getOptions(), JSON_PRETTY_PRINT ) );

		if ( $this->including() ) {
			$params['enableDisplayTitle'] =
				filter_var(
					$request->getText( 'enableDisplayTitle', strval( $config->getEnableDisplayTitle() ) ),
					FILTER_VALIDATE_BOOL,
					FILTER_NULL_ON_FAILURE
				);
		} elseif ( $request->getCheck( 'pages' ) ) {
			$params['enableDisplayTitle'] = $request->getCheck( 'enableDisplayTitle' );
		} else {
			$params['enableDisplayTitle'] = $config->getEnableDisplayTitle();
		}

		$params['labelMaxLength'] = $request->getInt( 'labelMaxLength', $config->getLabelMaxLength() );

		return $params;
	}

	/**
	 * @param array $params
	 * @return string[]
	 */
	private function formatParams( array $params ): array {
		$formattedParams = [];
		if ( $params['pages'] === '' ) {
			$formattedParams['pages'] = 'pages=' . Title::newMainPage()->getPrefixedText();
		} else {
			$formattedParams['pages'] = 'pages=' . strtr( $params['pages'], "\n", '|' );
		}
		if ( $params['exclude'] !== '' ) {
			$formattedParams['exclude'] = 'exclude=' . strtr( $params['exclude'], "\n", ';' );
		}
		if ( $params['excludedNamespaces'] !== [] ) {
			$formattedParams['excludedNamespaces'] = 'excludedNamespaces=' . implode( ',', $params['excludedNamespaces'] );
		}
		if ( $params['class'] !== '' ) {
			$formattedParams['class'] = 'class=' . $params['class'];
		}
		$formattedParams['options'] = "options=" . $params['options'];
		$formattedParams['enableDisplayTitle'] = 'enableDisplayTitle=' .
			( $params['enableDisplayTitle'] ? 'true' : 'false' );
		$formattedParams['labelMaxLength'] = 'labelMaxLength=' . strval( $params['labelMaxLength'] );
		return $formattedParams;
	}

	/**
	 * @param string[] $arguments
	 * @param NetworkConfig $config
	 * @return string
	 */
	public function showGraph( array $arguments, NetworkConfig $config ): string {
		$output = $this->getOutput();
		$output->addModules( [ 'ext.network' ] );
		$output->addJsConfigVars( 'networkExcludeTalkPages', $config->getExcludeTalkPages() );

		$requestModel = new RequestModel();
		$requestModel->functionArguments = $arguments;
		$requestModel->excludedNamespaces = $config->getExcludedNamespaces();
		$requestModel->enableDisplayTitle = $config->getEnableDisplayTitle();
		$requestModel->labelMaxLength = $config->getLabelMaxLength();

		/**
		 * @psalm-suppress PossiblyNullReference
		 */
		$requestModel->renderingPageName = $output->getTitle()->getFullText();
		$presenter = Extension::getFactory()->newSpecialNetworkPresenter();

		$this->newUseCase( $presenter, $config )->run( $requestModel );

		return $presenter->getReturnValue();
	}

	private function newUseCase( NetworkPresenter $presenter, NetworkConfig $config ): NetworkUseCase {
		return Extension::getFactory()->newNetworkFunction( $presenter, $config );
	}

	/**
	 * @param string[] $defaultValues
	 * @param NetworkConfig $config
	 */
	private function showForm( array $defaultValues, NetworkConfig $config ): void {
		$output = $this->getOutput();
		$output->addModules( [ 'ext.network.special' ] );
		$output->addHTML( Html::element(
			'div',
			[
				'class' => 'network-special-form',
				'data-defaultvalues' => json_encode( $defaultValues ),
				'data-namespaces' => json_encode( $this->getNamespaces( $config ) )
			]
		) );
	}

	private function getNamespaces( NetworkConfig $config ): array {
		$namespaces = MediaWikiServices::getInstance()->getContentLanguage()->getFormattedNamespaces();
		$namespaces[0] = ( new Message( 'blanknamespace' ) )->plain();
		return array_filter(
			$namespaces,
			static function ( int $value ) use ( $config ) {
				if ( $value < 0 ) {
					return false;
				}
				if ( $config->getExcludeTalkPages() ) {
					return !( $value % 2 );
				}
				return true;
			},
			ARRAY_FILTER_USE_KEY
		);
	}
}
