<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class SpecialNetworkPresenter extends AbstractNetworkPresenter {

	public function buildGraph( ResponseModel $viewModel ): void {
		$this->setHtml( $viewModel );
	}

	public function getReturnValue(): string {
		return $this->html;
	}

}
