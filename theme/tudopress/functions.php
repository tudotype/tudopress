<?php
/**
 * TudoPress theme functions.
 *
 * The theme stays small on purpose: design lives in theme.json, content lives in pages,
 * and everything about the dashboard lives in the helper plugin, so a friend can
 * change theme later without losing their admin tools.
 *
 * @package TudoPress
 */

if ( ! function_exists( 'tudopress_setup' ) ) :
	/**
	 * Loads style.css in the editors too, so what people edit looks like the site.
	 */
	function tudopress_setup() {
		add_editor_style( array( 'style.css', 'assets/css/editor-style.css' ) );
	}
endif;
add_action( 'after_setup_theme', 'tudopress_setup' );

if ( ! function_exists( 'tudopress_enqueue_styles' ) ) :
	/**
	 * Block themes don't load style.css by themselves.
	 */
	function tudopress_enqueue_styles() {
		wp_enqueue_style(
			'tudopress-style',
			get_stylesheet_uri(),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'tudopress_enqueue_styles' );

if ( ! function_exists( 'tudopress_pattern_categories' ) ) :
	/**
	 * Groups the theme's patterns where people look for them: whole pages first, then sections.
	 */
	function tudopress_pattern_categories() {
		register_block_pattern_category(
			'tudopress-pages',
			array(
				'label'       => _x( 'Page layouts', 'Block pattern category', 'tudopress' ),
				'description' => __( 'Whole pages to start from. Offered when you create a new page.', 'tudopress' ),
			)
		);
		register_block_pattern_category(
			'tudopress-sections',
			array(
				'label'       => _x( 'Sections', 'Block pattern category', 'tudopress' ),
				'description' => __( 'Pieces of a page: a hero, text beside an image, questions and answers…', 'tudopress' ),
			)
		);
	}
endif;
add_action( 'init', 'tudopress_pattern_categories' );
