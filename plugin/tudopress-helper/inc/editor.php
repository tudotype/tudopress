<?php
/**
 * The block editor: a shorter inserter, the site's own patterns first, and a checklist before publishing.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

/**
 * Blocks hidden from the inserter for people in the simple admin. They keep working
 * wherever they're already used; they just aren't offered.
 *
 * @return string[]
 */
function hwh_hidden_blocks(): array {
	return apply_filters(
		'hwh_hidden_blocks',
		array(
			'core/freeform',
			'core/verse',
			'core/preformatted',
			'core/code',
			'core/rss',
			'core/calendar',
			'core/tag-cloud',
			'core/archives',
			'core/categories',
			'core/latest-comments',
			'core/legacy-widget',
			'core/widget-group',
			'core/loginout',
			'core/nextpage',
			'core/more',
			'core/avatar',
			'core/term-description',
			'core/query-title',
			'core/page-list',
			'core/home-link',
			'core/read-more',
		)
	);
}

// WordPress.org's pattern directory and core's generic patterns crowd out the site's own.
add_filter(
	'should_load_remote_block_patterns',
	function ( $load ) {
		return hwh_option( 'curated_inserter' ) ? false : $load;
	}
);

add_action(
	'after_setup_theme',
	function () {
		if ( hwh_option( 'curated_inserter' ) ) {
			remove_theme_support( 'core-block-patterns' );
		}
	},
	20
);

add_filter(
	'block_editor_settings_all',
	function ( $settings ) {
		if ( hwh_option( 'no_openverse' ) ) {
			$settings['enableOpenverseMediaCategory'] = false;
		}
		if ( hwh_is_simple() ) {
			$settings['codeEditingEnabled'] = false;
		}
		return $settings;
	}
);

add_action(
	'enqueue_block_editor_assets',
	function () {
		if ( hwh_option( 'curated_inserter' ) && hwh_is_simple() ) {
			// Runs before the editor registers its blocks, so the filter sees every one.
			wp_enqueue_script( 'hwh-inserter', HWH_URL . 'assets/inserter.js', array( 'wp-hooks' ), HWH_VERSION, false );
			wp_add_inline_script( 'hwh-inserter', 'window.hwhHiddenBlocks = ' . wp_json_encode( array_values( hwh_hidden_blocks() ) ) . ';', 'before' );
		}

		if ( hwh_option( 'publish_checklist' ) ) {
			wp_enqueue_script(
				'hwh-checklist',
				HWH_URL . 'assets/publish-checklist.js',
				array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-data', 'wp-i18n', 'wp-components', 'wp-block-editor' ),
				HWH_VERSION,
				true
			);
			wp_set_script_translations( 'hwh-checklist', 'tudopress-helper' );
		}
	}
);
