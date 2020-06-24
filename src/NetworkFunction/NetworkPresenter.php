<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

interface NetworkPresenter {

	public function showGraph( ResponseModel $viewModel ): void;

	public function showTooManyPagesError(): void;

	/**
	 * @return mixed[]|string
	 */
	public function getParserFunctionReturnValue();

}
