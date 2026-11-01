<?php
/**
 * Title: Text beside an image
 * Slug: tudopress/text-image
 * Categories: tudopress-sections, text, media
 * Description: A picture on one side, a heading, a paragraph and a link on the other. Stacks on phones.
 * Viewport Width: 1400
 *
 * @package TudoPress
 */

?>
<!-- wp:group {"metadata":{"name":"Text beside an image"},"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"large","className":"is-style-rounded"} -->
<figure class="wp-block-image size-large is-style-rounded"><img alt="" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'A heading for this part of the story', 'tudopress' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'A short paragraph. Say one thing well, and link to the page where people can read more.', 'tudopress' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="#"><?php esc_html_e( 'Read the whole story', 'tudopress' ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
