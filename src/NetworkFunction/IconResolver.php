<?php

declare( strict_types = 1 );

namespace MediaWiki\Extension\Network\NetworkFunction;

use MediaWiki\Logger\LoggerFactory;

class IconResolver {

	private ?array $iconData = null;

	public function __construct(
		private readonly string $codexIconsJsonPath,
		private readonly string $scriptPath
	) {
	}

	public function resolve( array $visJsOptions ): array {
		if ( !isset( $visJsOptions['groups'] ) || !is_array( $visJsOptions['groups'] ) ) {
			return $visJsOptions;
		}
		foreach ( $visJsOptions['groups'] as $name => $group ) {
			if ( is_array( $group ) && isset( $group['image'] ) && is_string( $group['image'] ) ) {
				$visJsOptions['groups'][$name]['image'] = $this->resolveImage( $group['image'] );
			}
		}
		return $visJsOptions;
	}

	private function resolveImage( string $value ): string {
		if ( preg_match( '/^cdxIcon[A-Z]/', $value ) === 1 ) {
			$resolved = $this->codexIconToDataUri( $value );
			if ( $resolved === null ) {
				LoggerFactory::getInstance( 'Network' )->warning(
					'Unknown or unsupported Codex icon "{name}" in PageNetworkOptions; '
						. 'falling back to literal value (will likely fail to load).',
					[ 'name' => $value ]
				);
				return $value;
			}
			return $resolved;
		}
		if ( $this->isAbsoluteUrl( $value ) ) {
			return $value;
		}
		return $this->scriptPath . '/' . $value;
	}

	private function isAbsoluteUrl( string $value ): bool {
		return str_starts_with( $value, 'http://' )
			|| str_starts_with( $value, 'https://' )
			|| str_starts_with( $value, '//' )
			|| str_starts_with( $value, '/' )
			|| str_starts_with( $value, 'data:' );
	}

	private function codexIconToDataUri( string $iconName ): ?string {
		$path = $this->extractPath( $this->loadIcons()[$iconName] ?? null );
		if ( $path === null ) {
			return null;
		}
		// Codex icons are authored on a 20x20 grid.
		// See https://doc.wikimedia.org/codex/main/style-guide/icons.html
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">'
			. $path
			. '</svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * codex-icons.json stores entries in three shapes:
	 *  - a plain string of SVG path markup (e.g. cdxIconAdd)
	 *  - an associative array with an 'ltr' key (e.g. cdxIconArticle)
	 *  - an associative array with a 'default' key plus a 'langCodeMap' (e.g. cdxIconBold)
	 *
	 * Returns the SVG path markup for any of these, preferring 'ltr' when available
	 * and falling back to 'default'. Returns null for shapes we don't understand.
	 */
	private function extractPath( mixed $icon ): ?string {
		if ( is_string( $icon ) ) {
			return $icon;
		}
		if ( !is_array( $icon ) ) {
			return null;
		}
		if ( isset( $icon['ltr'] ) && is_string( $icon['ltr'] ) ) {
			return $icon['ltr'];
		}
		if ( isset( $icon['default'] ) && is_string( $icon['default'] ) ) {
			return $icon['default'];
		}
		return null;
	}

	private function loadIcons(): array {
		if ( $this->iconData === null ) {
			if ( is_readable( $this->codexIconsJsonPath ) ) {
				$json = file_get_contents( $this->codexIconsJsonPath );
				$this->iconData = ( $json !== false ? json_decode( $json, true ) : null ) ?? [];
			} else {
				$this->iconData = [];
			}
		}
		return $this->iconData;
	}

}
