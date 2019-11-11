( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-element/
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-i18n/
	 */
	var __ = wp.i18n.__;

	/**
	 * Text tools
	 */
	var TextControl = wp.components.TextControl;
	var RichText = wp.editor.RichText;

	/**
	 * Literally just for a fancy dashicon
	 * @see https://github.com/WordPress/gutenberg/blob/master/packages/components/src/dashicon/README.md
	 */
	var dashicon = wp.components.Dashicon;

	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	registerBlockType( 'citylimits/citylimits-custom-donate', {
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'Citylimits Custom Donations', 'citylimits' ),

		/**
		 * An icon property should be specified to make it easier to identify a block.
		 * These can be any of WordPress’ Dashicons, or a custom svg element.
		 */
		icon: 'money',

		/**
		 * Make it easier to discover a block with keyword aliases.
		 * These can be localised so your keywords work across locales.
		 */
		keywords: [ __( 'donation' ), __( 'donate' ) ],

		/**
		 * Blocks are grouped into categories to help users browse and discover them.
		 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
		 */
		category: 'widgets',

		/**
		 * Optional block extended support features.
		 */
		supports: {
			// Removes support for an HTML mode.
			html: false,
			alignWide: false,
			align: ['left', 'right', 'none', 'center'],
		},

		/**
		 * The block attributes
		 *
		 *
		 */
		attributes: {
			align: {
				type: 'string',
				default: 'left',
			},
			headline: {
				type: 'string',
				source: 'text',
				selector: 'h3.widgettitle',
			},
			cta: {
				type: 'string',
				source: 'html',
				selector: 'div.cta',
			},
			donate_text: {
				type: 'string',
				source: 'text',
				selector: 'a.btn',
			},
			donate_url: {
				type: 'string',
				source: 'attribute',
				attribute: 'href',
				selector: 'a.btn',
			}
		},

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function( props ) {
			return el(
				'div',
				{ className: props.className },
				el(
					TextControl,
					{
						label: [
							el(
								dashicon,
								{
									icon: 'money'
								},
							),
							__( 'Headline' )
						],
						value: props.attributes.headline,
						placeholder: __( 'Title of Donation CTA' ),
						onChange: ( value ) => { props.setAttributes( { headline: value } ); },
					}
				),
				el(
					RichText,
					{
						tagName: 'div',
						className: 'cta',
						value: props.attributes.cta,
						onChange: ( value ) => { props.setAttributes( { cta: value } ); },
						placeholder: __( 'Message goes here.' ),
						multiline: true,
					}
				),
				el(
					TextControl,
					{
						label: __( 'Button Text' ),
						value: props.attributes.donate_text,
						placeholder: __( 'Donate' ),
						onChange: ( value ) => { props.setAttributes( { donate_text: value } ); },
					}
				),
				el(
					TextControl,
					{
						label: [
							el(
								dashicon,
								{
									icon: 'admin-links'
								},
							),
							__( 'Donate Link' )
						],
						value: props.attributes.donate_url,
						placeholder: 'https://citylimits.org/donate/',
						default: 'https://citylimits.org/donate/',
						onChange: ( value ) => { props.setAttributes( { donate_url: value } ); },
					}
				)
			);
		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#save
		 *
		 * @return {Element}       Element to render.
		 */
		save: function() {
			return el(
				'div',
				{},
				__( 'Hello from the saved content!', 'citylimits' )
			);
		}
	} );
} )(
	window.wp
);
