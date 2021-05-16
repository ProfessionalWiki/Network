<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

use Html;

class ParserFunctionNetworkPresenter implements NetworkPresenter {

	private static $idCounter = 1;

	/**
	 * @var mixed[]|string
	 */
	private $parserFunctionReturnValue = '';

	public function showGraph( ResponseModel $viewModel ): void {
		$this->parserFunctionReturnValue = [
			Html::element(
				'div',
				[
					'id' => 'network-viz-' . (string)self::$idCounter++,
					'class' => $viewModel->cssClass,
					'data-pages' => json_encode( $viewModel->pageNames ),
					'data-exclude' => json_encode( $viewModel->excludedPages ),
					'data-options' => json_encode( $viewModel->visJsOptions ),
					'data-enabledisplaytitle' => json_encode( $viewModel->enableDisplayTitle ),
					'data-labelmaxlength' => json_encode( $viewModel->labelMaxLength ),
				]
			),
			'noparse' => true,
			'isHTML' => true,
		];
	}

	/**
	 * @return mixed[]|string
	 */
	public function getParserFunctionReturnValue() {
		return $this->parserFunctionReturnValue;
	}

	public function showTooManyPagesError(): void {
		// TODO: i18n
		$this->parserFunctionReturnValue = 'Too many pages. Can only show connections for up to 100 pages.';
	}

}
