<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

class NetworkUseCase {

	private NetworkPresenter $presenter;
	private array $visJsOptions;

	public function __construct(
		NetworkPresenter $presenter,
		array $visJsOptions
	) {
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
		$response->excludedNamespaces = $this->getExcludedNamespaces( $keyValuePairs, $request->excludedNamespaces );
		$response->enableDisplayTitle = $this->getEnableDisplayTitle( $keyValuePairs, $request->enableDisplayTitle );
		$response->labelMaxLength = $this->getLabelMaxLength( $keyValuePairs, $request->labelMaxLength );
		$response->visJsOptions = $this->getVisJsOptions( $keyValuePairs );

		$this->presenter->buildGraph( $response );
	}

	/**
	 * @param string[] $arguments
	 * @return string[]
	 */
	private function parserArgumentsToKeyValuePairs( array $arguments ): array {
		$pairs = [];

		foreach ( $arguments as $argument ) {
			[ $key, $value ] = $this->argumentStringToKeyValue( $argument );

			if ( $key !== null ) {
				$pairs[$key] = $value;
			}
		}

		return $pairs;
	}

	private function argumentStringToKeyValue( string $argument ): array {
		if ( strpos( $argument, '=' ) === false ) {
			return [ null, $argument ];
		}

		[ $key, $value ] = explode( '=', $argument );
		return [ trim( $key ), trim( $value ) ];
	}

	private function getVisJsOptions( array $arguments ): array {
		$visJsOptions = array_replace_recursive(
			$this->visJsOptions,
			json_decode( $arguments['options'] ?? '{}', true ) ?? []
		);
		return $visJsOptions;
	}

	/**
	 * @param RequestModel $request
	 * @return string[]
	 */
	private function getPageNames( RequestModel $request ): array {
		$pageNames = [];

		foreach ( $request->functionArguments as $argument ) {
			[ $key, $value ] = $this->argumentStringToKeyValue( $argument );

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
		return $key === null || in_array( $key, [ 'page', 'pages' ] );
	}

	/**
	 * @param string $pages
	 * @param non-empty-string $delimiter
	 * @return string[]
	 */
	private function pagesStringToArray( string $pages, string $delimiter ): array {
		return array_values(
			array_filter(
				array_map(
					'trim',
					explode( $delimiter, $pages )
				),
				static function ( string $pageName ): bool {
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
	 * @param int[] $excludedNamespaces
	 * @return int[]
	 */
	private function getExcludedNamespaces( array $arguments, array $excludedNamespaces ): array {
		if ( !isset( $arguments['excludedNamespaces'] ) ) {
			return $excludedNamespaces;
		}
		$namespaces = explode( ',', $arguments['excludedNamespaces'] );
		array_walk( $namespaces, 'intval' );
		return $namespaces;
	}

	/**
	 * @param string[] $arguments
	 * @param bool $enableDisplayTitle
	 * @return bool
	 */
	private function getEnableDisplayTitle( array $arguments, bool $enableDisplayTitle ): bool {
		return isset( $arguments['enableDisplayTitle'] )
			? filter_var( $arguments['enableDisplayTitle'], FILTER_VALIDATE_BOOLEAN )
			: $enableDisplayTitle;
	}

	/**
	 * @param string[] $arguments
	 * @param int $labelMaxLength
	 * @return int
	 */
	private function getLabelMaxLength( array $arguments, int $labelMaxLength ): int {
		return isset( $arguments['labelMaxLength'] ) ? (int)$arguments['labelMaxLength'] : $labelMaxLength;
	}
}
