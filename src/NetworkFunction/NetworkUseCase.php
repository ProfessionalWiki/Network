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
			$this->presenter->setTooManyPagesError();
			return;
		}

		$keyValuePairs = $this->parserArgumentsToKeyValuePairs( $request->functionArguments );

		$response->cssClass = $this->getCssClass( $keyValuePairs );
		$response->excludedPages = $this->getExcludedPages( $keyValuePairs );
		$response->visJsOptions = $this->getVisJsOptions( $keyValuePairs );
		$response->enableDisplayTitle = $this->getEnableDisplayTitle( $keyValuePairs, $request->defaultEnableDisplayTitle );
		$response->labelMaxLength = $this->getLabelMaxLength( $keyValuePairs, $request->defaultLabelMaxLength );

		$this->presenter->buildGraph( $response );
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

			if ( $value !== '' && $this->isPageKey( $key ) ) {
				foreach ( $this->pagesStringToArray( $value, '|' ) as $pageName ) {
					$pageNames[] = $pageName;
				}
			}
		}

		if ( $pageNames === [] ) {
			$pageNames[] = $request->renderingPageName;
		}

		return $pageNames;
	}

	private function isPageKey( ?string $key ): bool {
		return is_null( $key ) || in_array( $key, [ 'page', 'pages' ] );
	}

	/**
	 * @param string $pages
	 * @param string $delimiter
	 * @return string[]
	 */
	private function pagesStringToArray( string $pages, string $delimiter ): array {
		return array_values(
			array_filter(
				array_map(
					'trim',
					explode( $delimiter, $pages )
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
		return $this->pagesStringToArray( $arguments['exclude'] ?? '', ';' );
	}

	/**
	 * @param string[] $arguments
	 * @param bool $defaultEnableDisplayTitle
	 * @return bool
	 */
	private function getEnableDisplayTitle(array $arguments, bool $defaultEnableDisplayTitle ): bool {
		return isset( $arguments['enableDisplayTitle'] )
			? filter_var( $arguments['enableDisplayTitle'], FILTER_VALIDATE_BOOLEAN )
			: $defaultEnableDisplayTitle;
	}

	/**
	 * @param string[] $arguments
	 * @param int $defaultLabelMaxLength
	 * @return int
	 */
	private function getLabelMaxLength(array $arguments, int $defaultLabelMaxLength ): int {
		return isset( $arguments['labelMaxLength'] ) ? (int)$arguments['labelMaxLength'] : $defaultLabelMaxLength;
	}
}
