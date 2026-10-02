<?php
/**
 * Block bindings source: resolves dynamic share-intent URLs per service.
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
 * Enqueue the editor script that resolves bindings client-side.
 *
 * The service map is handed to JS so the editor and the front end build
 * their URLs from the same templates.
 *
 * @return void
 */
function enqueue_editor_assets(): void {
	wp_enqueue_script(
		'hm-social-links-editor',
		plugins_url( 'assets/editor.js', PLUGIN_FILE ),
		[ 'wp-blocks', 'wp-data', 'wp-core-data' ],
		VERSION,
		true
	);

	wp_add_inline_script(
		'hm-social-links-editor',
		'window.hmSocialLinks = ' . wp_json_encode( [ 'services' => get_services() ] ) . ';',
		'before'
	);
}

/**
 * Block bindings `get_value_callback`: resolve the share URL for a service.
 *
 * Returns null rather than an empty string for an unresolvable service, so
 * the block keeps its own `url` attribute instead of rendering `href=""`.
 *
 * @param array     $source_args Binding args, expects a `service` key.
 * @param \WP_Block $block       Block instance, used for post context.
 * @return string|null
 */
function get_share_link_url( array $source_args, $block ): ?string {
	$service  = $source_args['service'] ?? '';
	$post_id  = $block->context['postId'] ?? get_the_ID();
	$services = get_services();

	if ( ! $post_id || ! isset( $services[ $service ]['template'] ) ) {
		return null;
	}

	// Decode first: an encoded entity in the title would otherwise survive into the share text.
	$title = wp_strip_all_tags( html_entity_decode( get_the_title( $post_id ), ENT_QUOTES, 'UTF-8' ) );

	return strtr(
		$services[ $service ]['template'],
		[
			'{url}'   => rawurlencode( get_permalink( $post_id ) ),
			'{title}' => rawurlencode( $title ),
		]
	);
}

/**
 * Build the map of supported services.
 *
 * Keyed by the `core/social-link` `service` slug, so one key drives both the
 * icon and the bound URL.
 *
 * @return array<string, array{label: string, template: string}>
 */
function get_services(): array {
	$services = [
		'facebook'  => [
			'label'    => __( 'Share on Facebook', 'hm-social-links' ),
			'template' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
		],
		'x'         => [
			'label'    => __( 'Share on X', 'hm-social-links' ),
			'template' => 'https://x.com/intent/post?url={url}&text={title}',
		],
		'linkedin'  => [
			'label'    => __( 'Share on LinkedIn', 'hm-social-links' ),
			'template' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
		],
		'whatsapp'  => [
			'label'    => __( 'Share on WhatsApp', 'hm-social-links' ),
			'template' => 'https://api.whatsapp.com/send?text={title}%20{url}',
		],
		'reddit'    => [
			'label'    => __( 'Share on Reddit', 'hm-social-links' ),
			'template' => 'https://www.reddit.com/submit?url={url}&title={title}',
		],
		'pinterest' => [
			'label'    => __( 'Share on Pinterest', 'hm-social-links' ),
			'template' => 'https://pinterest.com/pin/create/button/?url={url}&description={title}',
		],
		'mail'      => [
			'label'    => __( 'Share by email', 'hm-social-links' ),
			'template' => 'mailto:?subject={title}&body={url}',
		],
	];

	/**
	 * Filter the available share services.
	 *
	 * Add or remove services here rather than editing the plugin. Each entry
	 * is keyed by service slug and holds a `label` and a `template`, where the
	 * template is a share-intent URL containing the literal placeholders
	 * `{url}` and `{title}` — both replaced with rawurlencoded values. Slugs
	 * should match a `core/social-link` `service` slug so the icon and the
	 * bound URL stay in sync. The editor reads the same map, so a template
	 * added here works in both places.
	 *
	 * @param array<string, array{label: string, template: string}> $services Map of service slug to label and URL template.
	 */
	return apply_filters( 'hm_social_links_services', $services );
}
