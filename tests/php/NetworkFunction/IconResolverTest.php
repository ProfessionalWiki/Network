<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\Tests\NetworkFunction;

use MediaWiki\Extension\Network\NetworkFunction\IconResolver;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\Network\NetworkFunction\IconResolver
 */
class IconResolverTest extends TestCase {

	private const FIXTURE_ICONS = [
		'cdxIconArticle' => [
			'ltr' => '<path d="M5 1h10z"/>',
			'shouldFlip' => true,
		],
		'cdxIconLinkExternal' => [
			'ltr' => '<path d="M1 2h2z"/>',
		],
		// Plain-string shape (155 of MW's bundled icons use this form, e.g. cdxIconAdd).
		'cdxIconAdd' => '<path d="M11 9V4z"/>',
		// {default, langCodeMap} shape (e.g. cdxIconBold).
		'cdxIconBold' => [
			'default' => '<path d="M3 19h7z"/>',
			'langCodeMap' => [ 'en' => '<path d="M-default-replaced/>' ],
		],
	];

	private string $fixturePath;

	protected function setUp(): void {
		$this->fixturePath = tempnam( sys_get_temp_dir(), 'codex-icons-' ) . '.json';
		file_put_contents( $this->fixturePath, json_encode( self::FIXTURE_ICONS ) );
	}

	protected function tearDown(): void {
		if ( file_exists( $this->fixturePath ) ) {
			unlink( $this->fixturePath );
		}
	}

	private function newResolver( string $scriptPath = '/w' ): IconResolver {
		return new IconResolver( $this->fixturePath, $scriptPath );
	}

	public function testLtrShapeCodexIconBecomesDataUri(): void {
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'bluelink' => [ 'image' => 'cdxIconArticle' ] ],
		] );

		$image = $result['groups']['bluelink']['image'];
		$this->assertStringStartsWith( 'data:image/svg+xml;base64,', $image );

		$decoded = base64_decode( substr( $image, strlen( 'data:image/svg+xml;base64,' ) ) );
		$this->assertStringContainsString( '<svg ', $decoded );
		$this->assertStringContainsString( 'viewBox="0 0 20 20"', $decoded );
		$this->assertStringContainsString( '<path d="M5 1h10z"/>', $decoded );
	}

	public function testStringShapeCodexIconBecomesDataUri(): void {
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'g' => [ 'image' => 'cdxIconAdd' ] ],
		] );

		$image = $result['groups']['g']['image'];
		$this->assertStringStartsWith( 'data:image/svg+xml;base64,', $image );

		$decoded = base64_decode( substr( $image, strlen( 'data:image/svg+xml;base64,' ) ) );
		$this->assertStringContainsString( '<path d="M11 9V4z"/>', $decoded );
	}

	public function testDefaultShapeCodexIconBecomesDataUri(): void {
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'g' => [ 'image' => 'cdxIconBold' ] ],
		] );

		$image = $result['groups']['g']['image'];
		$this->assertStringStartsWith( 'data:image/svg+xml;base64,', $image );

		$decoded = base64_decode( substr( $image, strlen( 'data:image/svg+xml;base64,' ) ) );
		$this->assertStringContainsString( '<path d="M3 19h7z"/>', $decoded );
	}

	public function testUnknownCodexIconNameLeftUnchanged(): void {
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'bluelink' => [ 'image' => 'cdxIconNonExistent' ] ],
		] );

		$this->assertSame( 'cdxIconNonExistent', $result['groups']['bluelink']['image'] );
	}

	public function testHttpsUrlLeftUnchanged(): void {
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'g' => [ 'image' => 'https://example.com/foo.svg' ] ],
		] );

		$this->assertSame( 'https://example.com/foo.svg', $result['groups']['g']['image'] );
	}

	public function testProtocolRelativeUrlLeftUnchanged(): void {
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'g' => [ 'image' => '//example.com/foo.svg' ] ],
		] );

		$this->assertSame( '//example.com/foo.svg', $result['groups']['g']['image'] );
	}

	public function testRootRelativeUrlLeftUnchanged(): void {
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'g' => [ 'image' => '/wiki/foo.svg' ] ],
		] );

		$this->assertSame( '/wiki/foo.svg', $result['groups']['g']['image'] );
	}

	public function testDataUriLeftUnchanged(): void {
		$dataUri = 'data:image/svg+xml;base64,PHN2Zy8+';
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'g' => [ 'image' => $dataUri ] ],
		] );

		$this->assertSame( $dataUri, $result['groups']['g']['image'] );
	}

	public function testRelativePathPrefixedWithScriptPath(): void {
		$result = $this->newResolver( '/w' )->resolve( [
			'groups' => [ 'g' => [ 'image' => 'resources/lib/ooui/foo.svg' ] ],
		] );

		$this->assertSame( '/w/resources/lib/ooui/foo.svg', $result['groups']['g']['image'] );
	}

	public function testEmptyScriptPathStillProducesAbsolute(): void {
		$result = $this->newResolver( '' )->resolve( [
			'groups' => [ 'g' => [ 'image' => 'resources/lib/ooui/foo.svg' ] ],
		] );

		$this->assertSame( '/resources/lib/ooui/foo.svg', $result['groups']['g']['image'] );
	}

	public function testNoGroupsKeyTolerated(): void {
		$input = [ 'nodes' => [ 'shape' => 'image' ] ];
		$this->assertSame( $input, $this->newResolver()->resolve( $input ) );
	}

	public function testGroupsNotArrayTolerated(): void {
		$input = [ 'groups' => 'not-an-array' ];
		$this->assertSame( $input, $this->newResolver()->resolve( $input ) );
	}

	public function testGroupWithoutImageTolerated(): void {
		$input = [
			'groups' => [
				'bluelink' => [ 'color' => 'blue' ],
			],
		];
		$this->assertSame( $input, $this->newResolver()->resolve( $input ) );
	}

	public function testNonStringImageTolerated(): void {
		$input = [
			'groups' => [
				'bluelink' => [ 'image' => 123 ],
			],
		];
		$this->assertSame( $input, $this->newResolver()->resolve( $input ) );
	}

	public function testMultipleGroupsResolvedIndependently(): void {
		$result = $this->newResolver( '/w' )->resolve( [
			'groups' => [
				'bluelink' => [ 'image' => 'cdxIconArticle' ],
				'redlink' => [ 'image' => 'resources/foo.svg' ],
				'externallink' => [ 'image' => 'cdxIconLinkExternal' ],
				'fallback' => [ 'image' => 'https://example.com/x.svg' ],
			],
		] );

		$this->assertStringStartsWith( 'data:image/svg+xml;base64,', $result['groups']['bluelink']['image'] );
		$this->assertSame( '/w/resources/foo.svg', $result['groups']['redlink']['image'] );
		$this->assertStringStartsWith( 'data:image/svg+xml;base64,', $result['groups']['externallink']['image'] );
		$this->assertSame( 'https://example.com/x.svg', $result['groups']['fallback']['image'] );
	}

	public function testMissingIconsJsonFileTolerated(): void {
		$resolver = new IconResolver( '/path/that/does/not/exist.json', '/w' );
		$result = $resolver->resolve( [
			'groups' => [ 'bluelink' => [ 'image' => 'cdxIconArticle' ] ],
		] );

		$this->assertSame( 'cdxIconArticle', $result['groups']['bluelink']['image'] );
	}

	public function testMalformedIconsJsonFileTolerated(): void {
		file_put_contents( $this->fixturePath, 'not valid json {{{' );
		$result = $this->newResolver()->resolve( [
			'groups' => [ 'bluelink' => [ 'image' => 'cdxIconArticle' ] ],
		] );

		$this->assertSame( 'cdxIconArticle', $result['groups']['bluelink']['image'] );
	}

}
