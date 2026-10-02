<?php
/**
 * Plugin Name:       HM Social Links
 * Plugin URI:        https://github.com/humanmade/hm-social-share-links
 * Description:       Wires the core Social Links block up to dynamic, per-page share-intent URLs (Facebook, X, LinkedIn, WhatsApp, Reddit, Pinterest, email) via the Block Bindings API, plus a ready-made pattern to insert them.
 * Version:           0.3.0
 * Requires at least: 6.9
 * Requires PHP:      7.4
 * Author:            Human Made
 * Author URI:        https://humanmade.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hm-social-links
 *
 * @package hm-social-links
 */

namespace HM\SocialLinks;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( __NAMESPACE__ . '\VERSION', '0.3.0' );
define( __NAMESPACE__ . '\PLUGIN_FILE', __FILE__ );
define( __NAMESPACE__ . '\PLUGIN_DIR', __DIR__ );

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/patterns.php';

add_action( 'init', __NAMESPACE__ . '\register_block_bindings' );
add_action( 'init', __NAMESPACE__ . '\register_patterns' );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_editor_assets' );
add_filter( 'block_bindings_supported_attributes_core/social-link', __NAMESPACE__ . '\allow_social_link_url_binding' );
