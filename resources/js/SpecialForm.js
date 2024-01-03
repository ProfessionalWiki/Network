
( function ( mw, netw ) {
	"use strict"

	mw.hook('wikipage.content').add(function($content) {
		$content.find('div.network-special-form').each(function() {
			let $this = $(this);
			let defaultValues = $this.data('defaultvalues');
			let namespaces = $this.data('namespaces');

			let pagesInput = new OO.ui.MultilineTextInputWidget({
				name: 'pages',
				value: defaultValues['pages'],
				rows: 4
			});
			let pagesField = new OO.ui.FieldLayout(
				pagesInput,
				{
					label: mw.message('pagenetwork-pages-field-label').text(),
					align: 'top',
					help: mw.message('pagenetwork-pages-field-help').text(),
					helpInline: true
				} );

			let excludeInput = new OO.ui.MultilineTextInputWidget({
				name: 'exclude',
				value: defaultValues['exclude'],
				rows: 4
			});
			let excludeField = new OO.ui.FieldLayout(
				excludeInput,
				{
					label: mw.message('pagenetwork-exclude-field-label').text(),
					align: 'top',
					help: mw.message('pagenetwork-exclude-field-help').text(),
					helpInline: true
				} );

			let value = [];
			let options = [];
			var index;
			for (index in namespaces) {
				if (defaultValues['excludedNamespaces'].includes(index)) {
					value.push(index);
				}
				options.push({
					data: index,
					label: namespaces[index]
				});
			}
			let excludedNamespacesInput = new OO.ui.CheckboxMultiselectInputWidget( {
				name: 'excludedNamespaces[]',
				value: value,
				options: options
			} );
			let excludedNamespacesField = new OO.ui.FieldLayout(
				excludedNamespacesInput,
				{
					label: mw.message('pagenetwork-excludedNamespaces-field-label').text(),
					align: 'top',
					help: mw.message('pagenetwork-excludedNamespaces-field-help').text(),
					helpInline: true
				} );

			let classInput = new OO.ui.TextInputWidget({
				name: 'class',
				value: defaultValues['class'],
			});
			let classField = new OO.ui.FieldLayout(
				classInput,
				{
					label: mw.message('pagenetwork-class-field-label').text(),
					align: 'top',
					help: mw.message('pagenetwork-class-field-help').text(),
					helpInline: true
				} );

			let optionsInput = new OO.ui.MultilineTextInputWidget({
				name: 'options',
				value: defaultValues['options'],
				rows: 20
			});
			let optionsField = new OO.ui.FieldLayout(
				optionsInput,
				{
					label: mw.message('pagenetwork-options-field-label').text(),
					align: 'top',
					help: mw.message('pagenetwork-options-field-help').text(),
					helpInline: true
				} );

			let enableDisplayTitleInput = new OO.ui.CheckboxInputWidget({
				name: 'enableDisplayTitle',
				selected: defaultValues['enableDisplayTitle'],
			});
			let enableDisplayTitleField = new OO.ui.FieldLayout(
				enableDisplayTitleInput,
				{
					label: mw.message('pagenetwork-enableDisplayTitle-field-label').text(),
					align: 'inline',
					help: mw.message('pagenetwork-enableDisplayTitle-field-help').text(),
					helpInline: true
				} );

			let labelMaxLengthInput = new OO.ui.TextInputWidget({
				name: 'labelMaxLength',
				value: defaultValues['labelMaxLength'],
			});
			let labelMaxLengthField = new OO.ui.FieldLayout(
				labelMaxLengthInput,
				{
					label: mw.message('pagenetwork-labelMaxLength-field-label').text(),
					align: 'top',
					help: mw.message('pagenetwork-labelMaxLength-field-help').text(),
					helpInline: true
				} );

			let AllowOnlyLinksToPagesInput = new OO.ui.CheckboxInputWidget({
				name: 'AllowOnlyLinksToPages',
				selected: defaultValues['AllowOnlyLinksToPages'],
			});
			let AllowOnlyLinksToPagesField = new OO.ui.FieldLayout(
				AllowOnlyLinksToPagesInput,
				{
					label: mw.message('pagenetwork-AllowOnlyLinksToPages-field-label').text(),
					align: 'inline',
					help: mw.message('pagenetwork-AllowOnlyLinksToPages-field-help').text(),
					helpInline: true
				} );

			let AllowLinkExpansionInput = new OO.ui.CheckboxInputWidget({
				name: 'AllowLinkExpansion',
				selected: defaultValues['AllowLinkExpansion'],
			});
			let AllowLinkExpansionField = new OO.ui.FieldLayout(
				AllowLinkExpansionInput,
				{
					label: mw.message('pagenetwork-AllowLinkExpansion-field-label').text(),
					align: 'inline',
					help: mw.message('pagenetwork-AllowLinkExpansion-field-help').text(),
					helpInline: true
				} );

			let submitButton = new OO.ui.ButtonInputWidget( {
				label: mw.message('htmlform-submit').text(),
				type: 'submit',
				flags: [
					'primary',
					'progressive'
				]
			});

			let SpecialTabPanel = function TabPanel( name, config ) {
				OO.ui.TabPanelLayout.call( this, name, config );
				if ( this.$element.is( ':empty' ) ) {
					this.$element.text( this.label );
				}
			};
			OO.inheritClass( SpecialTabPanel, OO.ui.TabPanelLayout );

			let fieldset = new OO.ui.FieldLayout(
				new OO.ui.Widget( {
					content: [
						new OO.ui.PanelLayout( {
							expanded: false,
							framed: false,
							content: [
								new OO.ui.IndexLayout( {
									expanded: false,
									framed: false
								} ).addTabPanels( [
									new SpecialTabPanel( 'first', {
										expanded: false,
										label: mw.message('pagenetwork-basic-tab-label').text(),
										content: [
											pagesField,
											excludeField,
											excludedNamespacesField
										]
									} ),
									new SpecialTabPanel( 'second', {
										expanded: false,
										label: mw.message('pagenetwork-advanced-tab-label').text(),
										content: [
											classField,
											optionsField,
											enableDisplayTitleField,
											labelMaxLengthField,
											AllowOnlyLinksToPagesField,
											AllowLinkExpansionField
										]
									} ),
								] )
							]
						} )
					]
				} ),
				{
					align: 'top'
				}
			);

			var form = new OO.ui.FormLayout(  {
				items: [ fieldset, submitButton ],
				action: mw.Title.newFromText( mw.config.get( 'wgTitle' ), mw.config.get( 'wgNamespaceNumber' ) ).getUrl(),
				method: 'post'
			} );

			$this.append( form.$element );
		} );

	} );

}( window.mediaWiki, module ) );

window.NetworkExtension = module;
