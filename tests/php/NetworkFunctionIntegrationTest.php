<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\Tests;

use MediaWiki\MediaWikiServices;
use PHPUnit\Framework\TestCase;

class NetworkFunctionIntegrationTest extends TestCase {

	private const PAGE_TITLE = 'ContextPageTitle';

	private function parse( string $textToParse ): string {
		return MediaWikiServices::getInstance()->getParser()
			->parse( $textToParse, \Title::newFromText( self::PAGE_TITLE ), new \ParserOptions() )->getText();
	}

	public function testWhenThereAreNoParameters_contextPageIsUsed() {
		$this->assertContains(
			'data-pages="[&quot;ContextPageTitle&quot;]"',
			$this->parse( '{{#network:}}' )
		);
	}

	public function testOptionsParameters() {
		$this->assertContains(
			'&quot;shape&quot;:&quot;tomato&quot;',
			$this->parse( '{{#network:options={"nodes": {"shape": "tomato"} } }}' )
		);
	}

}
