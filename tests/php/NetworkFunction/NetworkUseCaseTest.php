<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\Tests\NetworkFunction;

use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase
 */
class NetworkUseCaseTest extends TestCase {

	private const RENDERING_PAGE_NAME = 'MyPage';

	public function testDefaultPageName() {
		$this->assertSame(
			[ self::RENDERING_PAGE_NAME ],
			$this->runAndReturnPresenter( $this->newBasicRequestModel() )->getResponseModel()->pageNames
		);
	}

	private function runAndReturnPresenter( RequestModel $requestModel ): SpyNetworkPresenter {
		$presenter = new SpyNetworkPresenter();

		( new NetworkUseCase( $presenter ) )->run( $requestModel );

		return $presenter;
	}

	private function newBasicRequestModel(): RequestModel {
		$request = new RequestModel();

		$request->renderingPageName = 'MyPage';
		$request->functionArguments = [ '' ];

		return $request;
	}

	public function testSpecifiedPageName() {
		$request = $this->newBasicRequestModel();
		$request->functionArguments = [ 'page = Kittens' ];

		$this->assertSame(
			[ 'Kittens' ],
			$this->runAndReturnPresenter( $request )->getResponseModel()->pageNames
		);
	}

	public function testDefaultCssClass() {
		$request = $this->newBasicRequestModel();

		$this->assertSame(
			'network-visualization',
			$this->runAndReturnPresenter( $request )->getResponseModel()->cssClass
		);
	}

	public function testSpecifiedCssClass() {
		$request = $this->newBasicRequestModel();
		$request->functionArguments = [ 'class = col-lg-3 mt-2 ' ];

		$this->assertSame(
			'network-visualization col-lg-3 mt-2',
			$this->runAndReturnPresenter( $request )->getResponseModel()->cssClass
		);
	}

	public function testMultiplePageNames() {
		$request = $this->newBasicRequestModel();
		$request->functionArguments = [ 'Kittens', 'Cats', 'page = Tigers', 'page=Bobcats ' ];

		$this->assertSame(
			[ 'Kittens', 'Cats', 'Tigers', 'Bobcats' ],
			$this->runAndReturnPresenter( $request )->getResponseModel()->pageNames
		);
	}

	public function testNothingExcludedByDefault() {
		$this->assertSame(
			[],
			$this->runAndReturnPresenter( $this->newBasicRequestModel() )->getResponseModel()->excludedPages
		);
	}

	public function testExclude() {
		$request = $this->newBasicRequestModel();
		$request->functionArguments = [ 'Kittens', 'exclude = Foo ; Bar ; Baz:bah' ];

		$this->assertSame(
			[ 'Foo', 'Bar', 'Baz:bah' ],
			$this->runAndReturnPresenter( $request )->getResponseModel()->excludedPages
		);
	}

	public function testCanUseMaxPages() {
		$request = $this->newBasicRequestModel();
		$request->functionArguments = $this->getPageNames( 100 );

		$this->assertSame(
			$this->getPageNames( 100 ),
			$this->runAndReturnPresenter( $request )->getResponseModel()->pageNames
		);
	}

	private function getPageNames( int $count ): array {
		$pageNames = [];

		for ( $i = 0 ; $i < $count ; $i++ ) {
			$pageNames[] = 'Page' . (string)$i;
		}

		return $pageNames;
	}

	public function testMoreThanMaxPagesResultsInError() {
		$request = $this->newBasicRequestModel();
		$request->functionArguments = $this->getPageNames( 101 );

		$this->assertSame(
			[ 'too many pages' ],
			$this->runAndReturnPresenter( $request )->getErrors()
		);

		$this->assertSame(
			[],
			$this->runAndReturnPresenter( $request )->getResourceModules()
		);
	}

}
