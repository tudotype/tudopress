<?php
/**
 * Title: Kind words
 * Slug: tudopress/kind-words
 * Categories: tudopress-sections, testimonials
 * Description: A quote from a client or a review, with the person's name.
 * Viewport Width: 1000
 *
 * @package TudoPress
 */

?>
<!-- wp:group {"metadata":{"name":"Kind words"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"accent-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-accent-5-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:quote -->
<blockquote class="wp-block-quote"><!-- wp:paragraph -->
<p><?php esc_html_e( 'What someone said about you, in their words. Ask them first, and keep it short.', 'tudopress' ); ?></p>
<!-- /wp:paragraph --><cite><?php esc_html_e( 'Their name, what they do', 'tudopress' ); ?></cite></blockquote>
<!-- /wp:quote --></div>
<!-- /wp:group -->
