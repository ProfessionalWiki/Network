<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	private $presenter;

	public function __construct( NetworkPresenter $presenter ) {
		$this->presenter = $presenter;
	}

	public function run( RequestModel $request ): void {
		$response = new ResponseModel();
		$response->pageNames = $this->getPageNames( $request );

		if ( count( $response->pageNames ) > 100 ) {
			$this->presenter->showTooManyPagesError();
			return;
		}

		$response->cssClass = $this->getCssClass( $request->functionArguments );
		$response->excludedPages = $this->getExcludedPages( $request->functionArguments );

		$this->presenter->showGraph( $response );
	}

	/**
	 * @param RequestModel $request
	 * @return string[]
	 */
	private function getPageNames( RequestModel $request ): array {
		return $this->pagesStringToArray(
			$request->functionArguments['pages'] ?? $request->functionArguments['page'] ?? $request->renderingPageName
		);
	}

	/**
	 * @param string $pages
	 * @return string[]
	 */
	private function pagesStringToArray( string $pages ): array {
		return array_values(
			array_filter(
				array_map(
					'trim',
					explode( '|', $pages )
				),
				function( string $pageName ): bool {
					return $pageName !== '';
				}
			)
		);
	}

	private function getCssClass( array $arguments ): string {
		return trim( 'network-visualization ' . ( trim( $arguments['class'] ?? '' ) ) );
	}

	/**
	 * @param string[] $arguments
	 * @return string[]
	 */
	private function getExcludedPages( array $arguments ): array {
		return $this->pagesStringToArray( $arguments['exclude'] ?? '' );
	}

}
