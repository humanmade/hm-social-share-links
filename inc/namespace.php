<?php
/**
 * Block bindings source: resolves dynamic share-intent URLs per network.
 *
 * @package hm-social-links
 */

namespace HM\SocialLinks;

const BINDING_SOURCE = 'hm-social-links/share-link';

/**
 * Register the block bindings source.
 *
 * @return void
 */
function register_block_bindings(): void {
	register_block_bindings_source(
		BINDING_SOURCE,
		[
			'label'              => __( 'Social share link', 'hm-social-links' ),
			'get_value_callback' => __NAMESPACE__ . '\get_share_link_url',
			'uses_context'       => [ 'postId' ],
		]
	);
}

/**
 * Add `url` to the Social Link block's editor-supported binding attributes.
 *
 * Without this the binding still resolves correctly on the front end (block
 * bindings always apply at render time), but the editor shows the URL field
 * as freely editable instead of the connected/locked state. Available since
 * WP 6.9.
 *
 * @param string[] $attributes Supported attribute names.
 * @return string[]
 */
function allow_social_link_url_binding( array $attributes ): array {
	$attributes[] = 'url';
	return $attributes;
}

/**
 * Block bindings `get_value_callback`: resolve the share URL for a network.
 *
 * @param array     $source_args Binding args, expects a `network` key.
 * @param \WP_Block $block       Block instance, used for post context.
 * @return string
 */
function get_share_link_url( array $source_args, $block ): string {
	$network = $source_args['network'] ?? '';
	$post_id = $block->context['postId'] ?? get_the_ID();

	if ( ! $network || ! $post_id ) {
		return '';
	}

	$url      = get_permalink( $post_id );
	$title    = get_the_title( $post_id );
	$networks = get_share_networks( $url, $title, $post_id );

	return $networks[ $network ] ?? '';
}

/**
 * Build the map of network slug => share-intent URL.
 *
 * Network slugs are expected to match a `core/social-link` `service` value
 * so the same key drives both the icon and the bound URL.
 *
 * @param string $url     Page URL being shared.
 * @param string $title   Page title being shared.
 * @param int    $post_id Post ID being shared.
 * @return array<string, string>
 */
function get_share_networks( string $url, string $title, int $post_id ): array {
	$encoded_url   = rawurlencode( $url );
	$encoded_title = rawurlencode( $title );

	$networks = [
		'facebook'  => "https://www.facebook.com/sharer/sharer.php?u={$encoded_url}",
		'x'         => "https://twitter.com/intent/tweet?url={$encoded_url}&text={$encoded_title}",
		'linkedin'  => "https://www.linkedin.com/sharing/share-offsite/?url={$encoded_url}",
		'whatsapp'  => "https://api.whatsapp.com/send?text={$encoded_title}%20{$encoded_url}",
		'reddit'    => "https://www.reddit.com/submit?url={$encoded_url}&title={$encoded_title}",
		'pinterest' => "https://pinterest.com/pin/create/button/?url={$encoded_url}&description={$encoded_title}",
		'mail'      => "mailto:?subject={$encoded_title}&body={$encoded_url}",
	];

	/**
	 * Filter the available share networks and their URL templates.
	 *
	 * Add or remove networks here rather than editing the plugin. Keys
	 * should match a `core/social-link` `service` slug so the pattern's
	 * icon and the bound URL stay in sync.
	 *
	 * @param array<string, string> $networks Map of network slug to share URL.
	 * @param string                $url      Raw page URL being shared.
	 * @param string                $title    Raw page title being shared.
	 * @param int                   $post_id  Post ID being shared.
	 */
	return apply_filters( 'hm_social_links_networks', $networks, $url, $title, $post_id );
}
