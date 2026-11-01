<?php
/**
 * Title: Three highlights
 * Slug: tudopress/highlights
 * Categories: tudopress-sections, text, featured
 * Description: Three short points side by side: services, opening hours, what makes you different.
 * Viewport Width: 1400
 *
 * @package TudoPress
 */

?>
<!-- wp:group {"metadata":{"name":"Three highlights"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e( 'What we offer', 'tudopress' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)"><!-- wp:column {"className":"is-style-card"} -->
<div class="wp-block-column is-style-card"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'First thing', 'tudopress' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Two lines about it. Keep the three cards about the same length.', 'tudopress' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-card"} -->
<div class="wp-block-column is-style-card"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Second thing', 'tudopress' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Two lines about it. Keep the three cards about the same length.', 'tudopress' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"className":"is-style-card"} -->
<div class="wp-block-column is-style-card"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Third thing', 'tudopress' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Two lines about it. Keep the three cards about the same length.', 'tudopress' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
