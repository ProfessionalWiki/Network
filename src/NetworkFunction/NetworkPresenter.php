<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkPresenter {

	private static $idCounter = 1;

	public function render( NetworkResponse $viewModel ): array {
		return [
			\Html::element(
				'div',
				[
					'class' => 'network-visualization',
					'id' => 'network-viz-' . (string)self::$idCounter++
				]
			),
			'noparse' => true,
			'isHTML' => true,
		];
	}

}
