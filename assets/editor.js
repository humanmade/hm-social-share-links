/**
 * Editor-side half of the share-link binding: resolves bound URLs against the
 * post being edited, and registers a pre-bound Social Link variation per
 * network. Plain browser JS against the `wp.*` globals — the plugin has no
 * build step.
 *
 * @package hm-social-links
 */

( function ( wp, settings ) {
	if ( ! wp || ! wp.blocks || ! wp.data || ! settings || ! settings.networks ) {
		return;
	}

	var SOURCE = 'hm-social-links/share-link';
	var networks = settings.networks;

	/**
	 * Substitute the encoded URL and title into a network's template.
	 *
	 * @param {string} network Network slug.
	 * @param {string} url     Encoded post URL.
	 * @param {string} title   Encoded post title.
	 * @return {string|undefined} Share URL, or undefined for an unknown network.
	 */
	function buildUrl( network, url, title ) {
		var config = networks[ network ];

		if ( ! config || ! config.template ) {
			return undefined;
		}

		return config.template
			.replace( /\{url\}/g, url )
			.replace( /\{title\}/g, title );
	}

	wp.blocks.registerBlockBindingsSource( {
		name: SOURCE,
		usesContext: [ 'postId', 'postType' ],
		canUserEditValue: function () {
			return false;
		},
		getValues: function ( args ) {
			var context = args.context || {};
			var values = {};
			var post;

			if ( context.postId && context.postType ) {
				post = args.select( 'core' ).getEntityRecord( 'postType', context.postType, context.postId );
			}

			if ( ! post ) {
				return values;
			}

			var url = encodeURIComponent( post.link || '' );
			var title = encodeURIComponent( ( post.title && post.title.raw ) || '' );

			Object.keys( args.bindings ).forEach( function ( attribute ) {
				var binding = args.bindings[ attribute ] || {};
				var network = ( binding.args || {} ).network;

				values[ attribute ] = buildUrl( network, url, title );
			} );

			return values;
		},
	} );

	Object.keys( networks ).forEach( function ( network ) {
		// Matching on both attributes is what makes this win over core's own
		// variation for the same service.
		wp.blocks.registerBlockVariation( 'core/social-link', {
			name: 'share-' + network,
			title: networks[ network ].label,
			attributes: {
				service: network,
				label: networks[ network ].label,
				metadata: {
					bindings: {
						url: {
							source: SOURCE,
							args: { network: network },
						},
					},
				},
			},
			isActive: [ 'service', 'metadata.bindings.url.source' ],
		} );
	} );
}( window.wp, window.hmSocialLinks ) );
