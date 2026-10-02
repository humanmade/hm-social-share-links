<?php
/**
 * Title: Share Links
 * Slug: hm-social-links/share-links
 * Description: Social icons wired to dynamic, per-page share-intent URLs via block bindings.
 * Categories: hm-social-links
 * Keywords: share, social, facebook, twitter, x, linkedin, whatsapp
 */
?>
<!-- wp:social-links {"iconColor":"foreground","iconColorValue":"#000000","openInNewTab":true,"align":"left"} -->
<ul class="wp-block-social-links has-icon-color alignleft">
	<!-- wp:social-link {"service":"facebook","label":"Share on Facebook","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"service":"facebook"}}}}} /-->

	<!-- wp:social-link {"service":"x","label":"Share on X","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"service":"x"}}}}} /-->

	<!-- wp:social-link {"service":"linkedin","label":"Share on LinkedIn","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"service":"linkedin"}}}}} /-->

	<!-- wp:social-link {"service":"whatsapp","label":"Share on WhatsApp","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"service":"whatsapp"}}}}} /-->

	<!-- wp:social-link {"service":"mail","label":"Share by email","metadata":{"bindings":{"url":{"source":"hm-social-links/share-link","args":{"service":"mail"}}}}} /-->
</ul>
<!-- /wp:social-links -->
