<?php
/**
 * Title: Contact details
 * Slug: tudopress/contact
 * Categories: tudopress-sections, contact
 * Description: Email, phone and address in three columns, with a button that opens an email.
 * Viewport Width: 1400
 *
 * @package TudoPress
 */

?>
<!-- wp:group {"metadata":{"name":"Contact details"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Get in touch', 'tudopress' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Email', 'tudopress' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="mailto:hello@example.com">hello@example.com</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Phone', 'tudopress' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="tel:+351000000000">+351 000 000 000</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="wp-block-heading has-medium-font-size"><?php esc_html_e( 'Address', 'tudopress' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Street and number', 'tudopress' ); ?><br><?php esc_html_e( 'Postcode, town', 'tudopress' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
