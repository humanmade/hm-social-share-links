<?php
/**
 * Title: Share Links
 * Slug: hm-social-links/share-links
 * Description: Social icons wired to dynamic, per-page share-intent URLs via block bindings.
 * Categories: hm-social-links
 * Keywords: share, social, facebook, twitter, x, linkedin, whatsapp
 * Block Types: core/social-links
 */
?>
<!-- wp:social-links {"iconColor":"foreground","iconColorValue":"#000000","openInNewTab":true,"align":"left"} -->
<ul class="wp-block-social-links has-icon-color alignleft">
	<!-- wp:social-link {"service":"facebook","label":"Share on Facebook","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"network":"facebook"}}}}} /-->

	<!-- wp:social-link {"service":"x","label":"Share on X","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"network":"x"}}}}} /-->

	<!-- wp:social-link {"service":"linkedin","label":"Share on LinkedIn","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"network":"linkedin"}}}}} /-->

	<!-- wp:social-link {"service":"whatsapp","label":"Share on WhatsApp","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"network":"whatsapp"}}}}} /-->

	<!-- wp:social-link {"service":"mail","label":"Share by email","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"network":"mail"}}}}} /-->
</ul>
<!-- /wp:social-links -->
