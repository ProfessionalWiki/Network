
( function ( mw, netw ) {
	"use strict"

	mw.hook('wikipage.content').add(function($content) {
		$content.find('div.network-visualization').each(function() {
			let $this = $(this);

			let network = new netw.Network(
				$this.attr('id'),
				new netw.ApiPageConnectionRepo($this.data('enabledisplaytitle')),
				new netw.PageExclusionManager(
					$this.data('excludedpages'),
					$this.data('excludednamespaces'),
					mw.config.get('networkExcludeTalkPages')
				),
				$this.data('options'),
				$this.data('labelmaxlength'),
				$this.data('pages'),
				$this.data('allowonlylinkstopages'),
				$this.data('allowlinkexpansion')
			);

			network.showPages($this.data('pages'),$this.data('allowonlylinkstopages')).then(function() {
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
