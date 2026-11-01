<?php
/**
 * Title: Questions and answers
 * Slug: tudopress/questions
 * Categories: tudopress-sections, text
 * Description: Questions that open to show their answer. Add or remove questions with the + button.
 * Viewport Width: 1000
 *
 * @package TudoPress
 */

?>
<!-- wp:group {"metadata":{"name":"Questions and answers"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Questions people ask', 'tudopress' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:details -->
<details class="wp-block-details"><summary><?php esc_html_e( 'A question someone asked you last week?', 'tudopress' ); ?></summary><!-- wp:paragraph -->
<p><?php esc_html_e( 'The answer, in a sentence or two.', 'tudopress' ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary><?php esc_html_e( 'How much does it cost?', 'tudopress' ); ?></summary><!-- wp:paragraph -->
<p><?php esc_html_e( 'The answer, in a sentence or two.', 'tudopress' ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary><?php esc_html_e( 'Where are you, and when are you open?', 'tudopress' ); ?></summary><!-- wp:paragraph -->
<p><?php esc_html_e( 'The answer, in a sentence or two.', 'tudopress' ); ?></p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div>
<!-- /wp:group -->
