<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	private $presenter;
	private $visJsOptions;

	public function __construct( NetworkPresenter $presenter, array $visJsOptions ) {
		$this->presenter = $presenter;
		$this->visJsOptions = $visJsOptions;
	}

	public function run( RequestModel $request ): void {
		$response = new ResponseModel();
		$response->pageNames = $this->getPageNames( $request );

		if ( count( $response->pageNames ) > 100 ) {
			$this->presenter->showTooManyPagesError();
			return;
		}

		$keyValuePairs = $this->parserArgumentsToKeyValuePairs( $request->functionArguments );

		$response->cssClass = $this->getCssClass( $keyValuePairs );
		$response->excludedPages = $this->getExcludedPages( $keyValuePairs );
		$response->visJsOptions = $this->getVisJsOptions( $keyValuePairs );

		$this->presenter->showGraph( $response );
	}


	/**
	 * @param string[] $arguments
	 * @return string[]
	 */
	private function parserArgumentsToKeyValuePairs( array $arguments ): array {
		$pairs = [];

		foreach ( $arguments as $argument ) {
			[$key, $value] = $this->argumentStringToKeyValue( $argument );

			if ( !is_null( $key ) ) {
				$pairs[$key] = $value;
			}
		}

		return $pairs;
	}

	private function argumentStringToKeyValue( string $argument ): array {
		if ( false === strpos( $argument, '=' ) ) {
			return [null, $argument];
		}

		[$key, $value] = explode( '=', $argument );
		return [trim($key), trim($value)];
	}

	private function getVisJsOptions( array $arguments ): array {
		return array_replace_recursive(
			[
				'layout' => [
					'randomSeed' => 42
				]
			],
			$this->visJsOptions,
			json_decode( $arguments['options'] ?? '{}', true ) ?? []
		);
	}

	/**
	 * @param RequestModel $request
	 * @return string[]
	 */
	private function getPageNames( RequestModel $request ): array {
		$pageNames = [];

		foreach ( $request->functionArguments as $argument ) {
			[$key, $value] = $this->argumentStringToKeyValue( $argument );

			if ( $value !== '' && ( is_null( $key ) || $key === 'page' ) ) {
				$pageNames[] = $value;
			}
		}

		if ( $pageNames === [] ) {
			$pageNames[] = $request->renderingPageName;
		}

		return $pageNames;
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
					explode( ';', $pages )
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
