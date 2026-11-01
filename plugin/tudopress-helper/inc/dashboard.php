<?php
/**
 * The dashboard: a "Start here" panel first, the launch checklist for builders, and less noise.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

/**
 * The tasks people come to the dashboard for, in their words.
 *
 * @return array[] Each: [ label, url, dashicon ].
 */
function hwh_start_tasks(): array {
	$tasks = array();
	$front = (int) get_option( 'page_on_front' );

	if ( 'page' === get_option( 'show_on_front' ) && $front && current_user_can( 'edit_post', $front ) ) {
		$tasks[] = array( __( 'Edit the home page', 'tudopress-helper' ), get_edit_post_link( $front, 'raw' ), 'dashicons-admin-home' );
	}
	if ( current_user_can( 'edit_posts' ) ) {
		$tasks[] = array( __( 'Write a news post', 'tudopress-helper' ), admin_url( 'post-new.php' ), 'dashicons-edit' );
	}
	if ( current_user_can( 'edit_pages' ) ) {
		$tasks[] = array( __( 'Add a page', 'tudopress-helper' ), admin_url( 'post-new.php?post_type=page' ), 'dashicons-media-default' );
		$tasks[] = array( __( 'All pages', 'tudopress-helper' ), admin_url( 'edit.php?post_type=page' ), 'dashicons-admin-page' );
	}
	if ( current_user_can( 'upload_files' ) ) {
		$tasks[] = array( __( 'Photos and files', 'tudopress-helper' ), admin_url( 'upload.php' ), 'dashicons-format-image' );
	}
	if ( current_user_can( 'edit_theme_options' ) && wp_is_block_theme() ) {
		$tasks[] = array( __( 'Change the menu', 'tudopress-helper' ), admin_url( 'site-editor.php?p=' . rawurlencode( '/navigation' ) ), 'dashicons-menu' );
		$tasks[] = array( __( 'Colours and fonts', 'tudopress-helper' ), admin_url( 'site-editor.php?p=' . rawurlencode( '/styles' ) ), 'dashicons-art' );
	}
	$tasks[] = array( __( 'See the site', 'tudopress-helper' ), home_url( '/' ), 'dashicons-visibility' );

	/**
	 * Filters the Start here tasks, e.g. to add "Add a recipe" for a custom post type.
	 *
	 * @param array[] $tasks Each: [ label, url, dashicon ].
	 */
	return apply_filters( 'hwh_start_tasks', $tasks );
}

/**
 * The help line: the site guide and the builder's email, when set.
 *
 * @return string Escaped HTML, or ''.
 */
function hwh_help_links(): string {
	$parts = array();
	$guide = (int) hwh_option( 'guide_page' );
	if ( $guide && get_post( $guide ) ) {
		$parts[] = '<a href="' . esc_url( get_permalink( $guide ) ) . '">' . esc_html__( 'How this site works', 'tudopress-helper' ) . '</a>';
	}
	$email = hwh_option( 'builder_email' );
	if ( $email ) {
		$name    = hwh_option( 'builder_name' );
		$parts[] = '<a href="' . esc_url( 'mailto:' . $email ) . '">' . esc_html(
			$name
				/* translators: %s: the builder's name. */
				? sprintf( __( 'Ask %s', 'tudopress-helper' ), $name )
				: __( 'Ask for help', 'tudopress-helper' )
		) . '</a>';
	}
	return implode( ' · ', $parts );
}

/**
 * The Start here panel.
 */
function hwh_start_widget(): void {
	$user = wp_get_current_user();
	echo '<p class="hwh-start__hello">' . esc_html(
		/* translators: %s: the user's first name or display name. */
		sprintf( __( 'Hi %s. What would you like to do?', 'tudopress-helper' ), $user->first_name ? $user->first_name : $user->display_name )
	) . '</p>';
	echo '<ul class="hwh-start">';
	foreach ( hwh_start_tasks() as $task ) {
		echo '<li><a class="hwh-start__task" href="' . esc_url( $task[1] ) . '"><span class="dashicons ' . esc_attr( $task[2] ) . '" aria-hidden="true"></span>' . esc_html( $task[0] ) . '</a></li>';
	}
	echo '</ul>';
	$help = hwh_help_links();
	if ( $help ) {
		echo '<p class="hwh-start__help">' . $help . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in hwh_help_links().
	}
}

/**
 * The launch checklist for builders (the same checks as Site Health).
 */
function hwh_launch_widget(): void {
	$checks = hwh_launch_checks();
	$done   = count( array_filter( wp_list_pluck( $checks, 'pass' ) ) );
	echo '<p>' . esc_html(
		/* translators: 1: checks passed, 2: all checks. */
		sprintf( __( '%1$d of %2$d ready.', 'tudopress-helper' ), $done, count( $checks ) )
	) . '</p><ul class="hwh-checks">';
	foreach ( $checks as $check ) {
		echo '<li class="' . ( $check['pass'] ? 'is-pass' : 'is-todo' ) . '"><span class="dashicons ' . ( $check['pass'] ? 'dashicons-yes-alt' : 'dashicons-marker' ) . '" aria-hidden="true"></span> ';
		echo '<span class="screen-reader-text">' . ( $check['pass'] ? esc_html__( 'Done:', 'tudopress-helper' ) : esc_html__( 'To do:', 'tudopress-helper' ) ) . '</span>';
		echo $check['pass'] || ! $check['fix'] ? esc_html( $check['label'] ) : '<a href="' . esc_url( $check['fix'] ) . '">' . esc_html( $check['label'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inline.
		echo '</li>';
	}
	echo '</ul>';
}

add_action(
	'wp_dashboard_setup',
	function () {
		wp_add_dashboard_widget( 'hwh_start', __( 'Start here', 'tudopress-helper' ), 'hwh_start_widget' );
		if ( hwh_is_builder() ) {
			wp_add_dashboard_widget( 'hwh_launch', __( 'Ready to launch?', 'tudopress-helper' ), 'hwh_launch_widget', null, null, 'side' );
		}

		if ( hwh_is_simple() ) {
			remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
			remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
			remove_action( 'welcome_panel', 'wp_welcome_panel' );
		}

		// Start here goes first, above anything core or other plugins add.
		global $wp_meta_boxes;
		$normal = $wp_meta_boxes['dashboard']['normal']['core'] ?? array();
		if ( isset( $normal['hwh_start'] ) ) {
			$wp_meta_boxes['dashboard']['normal']['core'] = array( 'hwh_start' => $normal['hwh_start'] ) + $normal; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- reordering widgets is the documented way.
		}
	}
);

add_filter(
	'admin_footer_text',
	function ( $text ) {
		$help = hwh_help_links();
		return $help ? '<span class="hwh-footer-help">' . esc_html__( 'Need help?', 'tudopress-helper' ) . ' ' . $help . '</span>' : $text;
	}
);
