<?php
/**
 * Registers the bundled block patterns from the /patterns directory, using
 * the same header-comment format WordPress core reads from a theme's
 * `patterns/` directory. There's no public core API for a plugin to get
 * this for free, so this replicates that loader.
 *
 * @package hm-social-links
 */

namespace HM\SocialLinks;

/**
 * Register the pattern category and any patterns bundled with the plugin.
 *
 * @return void
 */
function register_patterns(): void {
	register_block_pattern_category(
		'hm-social-links',
		[ 'label' => __( 'Social Links', 'hm-social-links' ) ]
	);

	foreach ( glob( PLUGIN_DIR . '/patterns/*.php' ) as $pattern_file ) {
		register_pattern_from_file( $pattern_file );
	}
}

/**
 * Register a single pattern from a file.
 *
 * @param string $file Absolute path to the pattern file.
 * @return void
 */
function register_pattern_from_file( string $file ): void {
	$headers = get_file_data(
		$file,
		[
			'title'       => 'Title',
			'slug'        => 'Slug',
			'description' => 'Description',
			'categories'  => 'Categories',
			'keywords'    => 'Keywords',
			'blockTypes'  => 'Block Types',
		]
	);

	if ( ! $headers['slug'] || ! $headers['title'] ) {
		return;
	}

	foreach ( [ 'categories', 'keywords', 'blockTypes' ] as $list_field ) {
		if ( '' === $headers[ $list_field ] ) {
			unset( $headers[ $list_field ] );
			continue;
		}
		$headers[ $list_field ] = array_map( 'trim', explode( ',', $headers[ $list_field ] ) );
	}

	ob_start();
	include $file;
	$content = ob_get_clean();

	// Strip the leading PHP header-comment block, leaving just the markup.
	$content = preg_replace( '/^<\?php.*?\?>\s*/s', '', $content );

	$slug = $headers['slug'];
	unset( $headers['slug'] );
	$headers['content'] = $content;

	register_block_pattern( $slug, $headers );
}
