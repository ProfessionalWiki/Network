<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\Tests;

use MediaWiki\MediaWikiServices;
use MediaWiki\Parser\ParserOptions;
use MediaWiki\Title\Title;
use MediaWiki\User\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\Network\EntryPoints\NetworkFunction
 */
class NetworkFunctionIntegrationTest extends TestCase {

	private const PAGE_TITLE = 'ContextPageTitle';

	private function parse( string $textToParse ): string {
		$parserOptions = new ParserOptions( User::newSystemUser( 'TestUser' ) );
		return MediaWikiServices::getInstance()->getParser()
			->parse( $textToParse, Title::newFromText( self::PAGE_TITLE ), $parserOptions )
			->runOutputPipeline( $parserOptions )
			->getContentHolderText();
	}

	public function testWhenThereAreNoParameters_contextPageIsUsed() {
		$this->assertStringContainsString(
			'data-pages="[&quot;ContextPageTitle&quot;]"',
			$this->parse( '{{#network:}}' )
		);
	}

	public function testOptionsParameters() {
		$this->assertStringContainsString(
			'&quot;shape&quot;:&quot;tomato&quot;',
			$this->parse( '{{#network:options={"nodes": {"shape": "tomato"} } }}' )
		);
	}

}
