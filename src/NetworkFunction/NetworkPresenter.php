<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

interface NetworkPresenter {

	public function buildGraph( ResponseModel $viewModel ): void;

	public function setTooManyPagesError(): void;

}
