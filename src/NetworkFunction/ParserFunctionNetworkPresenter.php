<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class ParserFunctionNetworkPresenter extends AbstractNetworkPresenter {

	/**
	 * @var array
	 */
	private $parserFunctionReturnValue = [];

	public function buildGraph( ResponseModel $viewModel ): void {
		$this->setHtml( $viewModel );
		$this->parserFunctionReturnValue = [
			$this->html,
			'noparse' => true,
			'isHTML' => true,
		];
	}

	/**
	 * @return array
	 */
	public function getReturnValue() : array {
		return $this->parserFunctionReturnValue;
	}

}
