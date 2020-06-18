<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\Tests\NetworkFunction;

use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\ResponseModel;

class SpyNetworkPresenter extends NetworkPresenter {

	private $viewModel;

	public function showGraph( ResponseModel $viewModel ) {
		$this->viewModel = $viewModel;
	}

	public function getResponseModel(): ?ResponseModel {
		return $this->viewModel;
	}

}
