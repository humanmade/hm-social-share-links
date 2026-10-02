# HM Social Links

[![Try in WordPress Playground](https://img.shields.io/badge/Try_it_now-WordPress_Playground-3858E9?logo=wordpress&logoColor=white)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/humanmade/hm-social-share-links/main/blueprint-demo.json)

Wires the core Social Links block up to dynamic, per-page share-intent URLs
(Facebook, X, LinkedIn, WhatsApp, Reddit, Pinterest, email) using the Block
Bindings API — no custom block, no build step.

## How it works

- Registers a block bindings source, `hm-social-links/share-link`, whose
  `get_value_callback` builds a share URL for the current post from a
  `service` arg (`inc/namespace.php`).
- Adds `core/social-link`'s `url` attribute to the editor's supported
  binding attributes (WP 6.9+), so the block shows the native
  connected/locked state instead of a plain editable field.
- Ships a pattern (`patterns/share-links.php`) — a `core/social-links` block
  with icons already bound to services — registered under the "Social
  Links" pattern category.
- Registers the same binding source client-side (`assets/editor.js`), so
  bound URLs resolve against the post being edited rather than showing an
  empty connected field, and adds a Social Link variation per service to the
  inserter. It's plain browser JS against the `wp.*` globals — no build step.

Front-end resolution works from WP 6.5 (block bindings apply at render time
regardless of editor support); the 6.9 attribute filter only affects the
editor's UI/locking behaviour.

## Install

Not on Packagist, so add the repository alongside the `require`:

```json
{
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/humanmade/hm-social-share-links"
		}
	]
}
```

```bash
composer require humanmade/hm-social-share-links
```

## Usage

Activate the plugin, then insert the "Share Links" pattern from the block
inserter. Add, remove, or reorder icons as normal — each `core/social-link`
block just needs a `service` matching one of the service keys below, with a
`metadata.bindings.url` pointing at `hm-social-links/share-link` and that
same key as the `service` arg.

## Local testing (WordPress Playground)

No local PHP/MySQL/Docker needed — [WordPress Playground](https://playground.wordpress.net/)
runs WordPress in-process via WASM.

```bash
npm install
npm run playground:start
```

This mounts the working directory as an active plugin (`--auto-mount`), on
WP `latest`, landing straight in the post editor so you can insert the
pattern and check the bound URLs. Config lives in `blueprint.json`.

The README badge above uses a second file, `blueprint-demo.json` — it can't
use `--auto-mount` (there's no local filesystem in a browser), so it
installs the plugin from a zip of this repo's `main` branch instead. It only
works once this repo is pushed to `humanmade/hm-social-share-links` and
`blueprint-demo.json` is present on `main`, since Playground fetches it live
from `raw.githubusercontent.com`.

## Extending

Add, remove, or override services with the `hm_social_links_services`
filter rather than editing the plugin:

```php
add_filter( 'hm_social_links_services', function ( array $services ) {
	$services['telegram'] = [
		'label'    => __( 'Share on Telegram', 'my-theme' ),
		'template' => 'https://t.me/share/url?url={url}&text={title}',
	];
	unset( $services['pinterest'] );
	return $services;
} );
```

`{url}` and `{title}` are replaced with the rawurlencoded post permalink and
title. The same template drives the front end and the editor, so the two
can't disagree — and a service added here gets an inserter variation too.

Each key is the `core/social-link` `service` slug it applies to, which is
what drives both the icon and the bound URL.

## Requirements

- WordPress 6.9+ (for the editor-side connected/locked UI; render-time
  binding resolution works from 6.5)
- PHP 7.4+
