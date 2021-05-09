
( function ( mw, netw ) {
	"use strict"

	mw.hook('wikipage.content').add(function($content) {
		$content.find('div.network-visualization').each(function() {
			let $this = $(this);

			let network = new netw.Network(
				$this.attr('id'),
				new netw.ApiPageConnectionRepo(),
				new netw.PageBlacklist(
					$this.data('exclude'),
					mw.config.get('networkExcludedNamespaces'),
					mw.config.get('networkExcludeTalkPages')
				),
				$this.data('options'),
				$this.data('labelmaxlength')
			);

			network.showPages($this.data('pages')).then(function() {
				$this.find('canvas:first').attr(
					'aria-label',
					mw.message(
						'network-aria',
						$this.data('pages').length,
						$this.data('pages').join(', ')
					).parse()
				);
			});
		} );
	} );

}( window.mediaWiki, module ) );

window.NetworkExtension = module;
