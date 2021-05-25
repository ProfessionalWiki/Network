<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\EntryPoints;

use HTMLForm;
use IncludableSpecialPage;
use MediaWiki\Extension\Network\NetworkFunction\NetworkConfig;
use Title;

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
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		$config = new NetworkConfig();
		$params = [];

		$params[] =
			'pages=' .
			strtr( $request->getText( 'pages', Title::newMainPage()->getPrefixedText() ), "\n", '|' );

		$exclude = $request->getText( 'exclude' );
		if ( $exclude !== '' ) {
			$params[] = 'exclude=' . strtr( $exclude, "\n", ';' );
		}

		$class = $request->getText( 'class' );
		if ( $class !== '' ) {
			$params[] = 'class=' . $class;
		}

		$options = $request->getText( 'options', json_encode( $config->getOptions(), JSON_PRETTY_PRINT ) );
		$params[] = 'options=' . $options;

		$enableDisplayTitle = filter_var(
			$request->getText( 'enableDisplayTitle', strval( $config->getDefaultEnableDisplayTitle() ) ),
			FILTER_VALIDATE_BOOL,
			FILTER_NULL_ON_FAILURE
		);
		$params[] = 'enableDisplayTitle=' . ( $enableDisplayTitle ? 'true' : 'false' );

		$labelMaxLength = $request->getInt(
			'labelMaxLength',
			$config->getDefaultLabelMaxLength()
		);
		$params[] = 'labelMaxLength=' . strval( $labelMaxLength );

		/**
		 * @psalm-suppress PossiblyNullReference
		 */
		$html = ( new NetworkFunction( $config ) )->handleParserFunctionCall(
			$output,
			$output->getTitle()->getFullText(),
			$params
		);
		$output->addHTML( $html[0] );

		if ( !$this->including() ) {

			$formDescriptor = [
				'pagesfield' => [
					'label-message' => 'pagenetwork-pages-field-label',
					'help-message' => 'pagenetwork-pages-field-help',
					'class' => 'HTMLTextAreaField',
					'rows' => 4,
					'default' => '',
					'name' => 'pages'
				],
				'excludefield' => [
					'label-message' => 'pagenetwork-exclude-field-label',
					'help-message' => 'pagenetwork-exclude-field-help',
					'class' => 'HTMLTextAreaField',
					'rows' => 4,
					'default' => '',
					'name' => 'exclude'
				],
				'classfield' => [
					'label-message' => 'pagenetwork-class-field-label',
					'help-message' => 'pagenetwork-class-field-help',
					'class' => 'HTMLTextField',
					'default' => '',
					'name' => 'class'
				],
				'optionsfield' => [
					'label-message' => 'pagenetwork-options-field-label',
					'help-message' => 'pagenetwork-options-field-help',
					'class' => 'HTMLTextAreaField',
					'default' => $options,
					'name' => 'options'
				],
				'enableDisplayTitle' => [
					'label-message' => 'pagenetwork-enableDisplayTitle-field-label',
					'help-message' => 'pagenetwork-enableDisplayTitle-field-help',
					'class' => 'HTMLCheckField',
					'default' => $enableDisplayTitle,
					'name' => 'enableDisplayTitle'
				],
				'labelMaxLengthfield' => [
					'label-message' => 'pagenetwork-labelMaxLength-field-label',
					'help-message' => 'pagenetwork-labelMaxLength-field-help',
					'class' => 'HTMLIntField',
					'default' => $labelMaxLength,
					'name' => 'labelMaxLength'
				]
			];

			$htmlForm =
				HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );

			$htmlForm->setMethod( 'get' );

			$htmlForm->prepareForm()->displayForm( false );
		}
	}
}
