<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class SpecialNetworkPresenter extends AbstractNetworkPresenter {

	public function buildGraph( ResponseModel $viewModel ): void {
		$this->setHtml( $viewModel );
	}

	/**
	 * @return string
	 */
	public function getReturnValue() {
		return $this->html;
	}

}
