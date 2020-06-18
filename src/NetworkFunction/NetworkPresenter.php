<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkPresenter {

	private static $idCounter = 1;

	private $resourceModules;
	private $parserFunctionReturnValue;

	public function showGraph( ResponseModel $viewModel ) {
		$this->resourceModules = [ 'ext.network' ];

		$this->parserFunctionReturnValue = [
			\Html::element(
				'div',
				[
					'id' => 'network-viz-' . (string)self::$idCounter++,
					'class' => $viewModel->cssClass,
					'data-pages' => json_encode( $viewModel->pageNames ),
					'data-exclude' => json_encode( $viewModel->excludedPages ),
				]
			),
			'noparse' => true,
			'isHTML' => true,
		];
	}

	public function getResourceModules(): array {
		return $this->resourceModules;
	}

	public function getParserFunctionReturnValue(): array {
		return $this->parserFunctionReturnValue;
	}

}
