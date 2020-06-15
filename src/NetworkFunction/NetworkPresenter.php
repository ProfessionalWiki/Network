<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

use Parser;

class NetworkPresenter {

	private static $idCounter = 1;

	private $parser;

	public function __construct( Parser $parser ) {
		$this->parser = $parser;
	}

	public function render( ResponseModel $viewModel ): array {
		$this->parser->getOutput()->addModules( 'ext.network' );

		return [
			\Html::element(
				'div',
				[
					'id' => 'network-viz-' . (string)self::$idCounter++,
					'class' => $viewModel->cssClass,
					'data-page' => $viewModel->pageName,
				]
			),
			'noparse' => true,
			'isHTML' => true,
		];
	}

}
