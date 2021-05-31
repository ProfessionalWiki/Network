<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use HTMLForm;
use IncludableSpecialPage;
use MediaWiki\Extension\Network\Extension;
use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use MediaWiki\MediaWikiServices;
use Title;
use WebRequest;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class SpecialNetwork extends IncludableSpecialPage {

	public function __construct() {
		parent::__construct( 'Network' );
	}

	/**
	 * @param string|null $subPage
	 */
	public function execute( $subPage ) : void {
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

	/**
	 * @param WebRequest $request
	 * @param NetworkConfig $config
	 * @return array $params
	 */
	private function parseParams( WebRequest $request, NetworkConfig $config ) : array {
		$params = [];
		if ( $request->getCheck( 'pages' ) ) {
			$params['pages'] = $request->getText( 'pages' );
			if ( $params['pages'] === '' ) {
				$params['pages'] = Title::newMainPage()->getPrefixedText();
			}
			$params['enableDisplayTitle'] =
				filter_var(
					$request->getText( 'enableDisplayTitle', 'false' ),
					FILTER_VALIDATE_BOOL,
					FILTER_NULL_ON_FAILURE
				);
		} else {
			$params['pages'] = '';
			$params['enableDisplayTitle'] =
				filter_var(
					$request->getText( 'enableDisplayTitle', strval( $config->getEnableDisplayTitle() ) ),
					FILTER_VALIDATE_BOOL,
					FILTER_NULL_ON_FAILURE
				);
		}
		$params['exclude'] = $request->getText( 'exclude', '' );
		$params['excludedNamespaces'] = $request->getArray( 'excludedNamespaces', $config->getExcludedNamespaces() );
		$params['class'] = $request->getText( 'class', '' );
		$params['options'] = $request->getText( 'options', json_encode( $config->getOptions(), JSON_PRETTY_PRINT ) );
		$params['labelMaxLength'] = $request->getInt( 'labelMaxLength', $config->getLabelMaxLength() );
		return $params;
	}

	/**
	 * @param string[] $params
	 * @return string[]
	 */
	private function formatParams( $params ) : array {
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
	public function showGraph( array $arguments, NetworkConfig $config) : string {
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
	private function showForm( $defaultValues, NetworkConfig $config ): void {
		$namespaces = MediaWikiServices::getInstance()->getContentLanguage()->getFormattedNamespaces();
		$namespaces[0] = wfMessage( 'blanknamespace' )->plain();
		$namespaces = array_filter(
			array_flip( $namespaces ),
			function( $value ) use( $config ){
				if ( $value < 0 ) {
					return false;
				}
				if ( $config->getExcludeTalkPages() ) {
					return !( $value % 2 );
				}
				return true;
			}
		);
		$formDescriptor = [
			'pagesfield' => [
				'label-message' => 'pagenetwork-pages-field-label',
				'help-message' => 'pagenetwork-pages-field-help',
				'class' => 'HTMLTextAreaField',
				'rows' => 4,
				'default' => $defaultValues['pages'],
				'nodata' => true,
				'name' => 'pages'
			],
			'excludefield' => [
				'label-message' => 'pagenetwork-exclude-field-label',
				'help-message' => 'pagenetwork-exclude-field-help',
				'class' => 'HTMLTextAreaField',
				'rows' => 4,
				'default' => $defaultValues['exclude'],
				'nodata' => true,
				'name' => 'exclude'
			],
			'excludednamespacesfield' => [
				'label-message' => 'pagenetwork-excludedNamespaces-field-label',
				'help-message' => 'pagenetwork-excludedNamespaces-field-help',
				'class' => 'HTMLMultiSelectField',
				'default' => $defaultValues['excludedNamespaces'],
				'options' => $namespaces,
				'nodata' => true,
				'name' => 'excludedNamespaces'
			],
			'classfield' => [
				'label-message' => 'pagenetwork-class-field-label',
				'help-message' => 'pagenetwork-class-field-help',
				'class' => 'HTMLTextField',
				'default' => $defaultValues['class'],
				'nodata' => true,
				'name' => 'class'
			],
			'optionsfield' => [
				'label-message' => 'pagenetwork-options-field-label',
				'help-message' => 'pagenetwork-options-field-help',
				'class' => 'HTMLTextAreaField',
				'default' => $defaultValues['options'],
				'nodata' => true,
				'name' => 'options'
			],
			'enableDisplayTitle' => [
				'label-message' => 'pagenetwork-enableDisplayTitle-field-label',
				'help-message' => 'pagenetwork-enableDisplayTitle-field-help',
				'class' => 'HTMLCheckField',
				'default' => $defaultValues['enableDisplayTitle'],
				'nodata' => true,
				'name' => 'enableDisplayTitle'
			],
			'labelMaxLengthfield' => [
				'label-message' => 'pagenetwork-labelMaxLength-field-label',
				'help-message' => 'pagenetwork-labelMaxLength-field-help',
				'class' => 'HTMLIntField',
				'default' => $defaultValues['labelMaxLength'],
				'nodata' => true,
				'name' => 'labelMaxLength'
			]
		];

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm->setMethod( 'get' );
		$htmlForm->prepareForm()->displayForm( false );
	}
}
