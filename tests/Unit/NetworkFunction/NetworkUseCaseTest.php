<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\Tests\Unit\NetworkFunction;

use MediaWiki\Extension\Network\NetworkFunction\RequestModel;
use MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\Network\NetworkFunction\NetworkUseCase
 */
class NetworkUseCaseTest extends TestCase {

	private const RENDERING_PAGE_NAME = 'MyPage';

	public function testDefaultPageName() {
		$this->assertSame(
			[ self::RENDERING_PAGE_NAME ],
			( new NetworkUseCase() )->run( $this->newBasicRequestModel() )->pageNames
		);
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
			( new NetworkUseCase() )->run( $request )->pageNames
		);
	}

	public function testDefaultCssClass() {
		$request = $this->newBasicRequestModel();

		$this->assertSame(
			'network-visualization',
			( new NetworkUseCase() )->run( $request )->cssClass
		);
	}

	public function testSpecifiedCssClass() {
		$request = $this->newBasicRequestModel();
		$request->functionArguments = [ 'class = col-lg-3 mt-2 ' ];

		$this->assertSame(
			'network-visualization col-lg-3 mt-2',
			( new NetworkUseCase() )->run( $request )->cssClass
		);
	}

}
