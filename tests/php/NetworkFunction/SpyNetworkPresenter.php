<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\Tests\NetworkFunction;

use MediaWiki\Extension\Network\NetworkFunction\NetworkPresenter;
use MediaWiki\Extension\Network\NetworkFunction\ResponseModel;

class SpyNetworkPresenter implements NetworkPresenter {

	private $viewModel;
	private $errors = [];

	public function showGraph( ResponseModel $viewModel ): void {
		$this->viewModel = $viewModel;
	}

	public function getResponseModel(): ?ResponseModel {
		return $this->viewModel;
	}

	public function showTooManyPagesError(): void {
		$this->errors[] = 'too many pages';
	}

	public function getErrors(): array {
		return $this->errors;
	}

	public function getResourceModules(): array {
		return [];
	}

	public function getParserFunctionReturnValue() {
		return [];
	}

}
