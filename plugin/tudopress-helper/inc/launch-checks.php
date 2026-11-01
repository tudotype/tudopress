<?php
/**
 * The launch checklist: what tends to be forgotten before a small site goes live.
 * Shown to builders on the dashboard, and to everyone in Tools → Site Health.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

/**
 * Runs every check.
 *
 * @return array[] Each: id, label, pass (bool), fix (URL or ''), why.
 */
function hwh_launch_checks(): array {
	$title   = trim( (string) get_option( 'blogname' ) );
	$tagline = trim( (string) get_option( 'blogdescription' ) );
	$front   = (int) get_option( 'page_on_front' );
	$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
	$guide   = (int) hwh_option( 'guide_page' );
	// WordPress saves a fallback menu (a plain page list) the first time a page shows the
	// navigation block, so only a menu with links someone chose counts.
	$menus   = array_filter(
		get_posts(
			array(
				'post_type'   => 'wp_navigation',
				'post_status' => 'publish',
				'numberposts' => 20,
			)
		),
		fn( $menu ) => str_contains( $menu->post_content, '<!-- wp:navigation-link' ) || str_contains( $menu->post_content, '<!-- wp:navigation-submenu' )
	);
	$guide_text = $guide ? trim( wp_strip_all_tags( (string) get_post_field( 'post_content', $guide ) ) ) : '';
	$samples = array_filter(
		array(
			get_page_by_path( 'hello-world', OBJECT, 'post' ),
			get_page_by_path( 'sample-page', OBJECT, 'page' ),
		),
		fn( $p ) => $p && 'publish' === $p->post_status
	);

	$checks = array(
		array(
			'id'    => 'title',
			'label' => __( 'The site has its own name', 'tudopress-helper' ),
			'pass'  => '' !== $title && ! in_array( strtolower( $title ), array( 'my wordpress website', 'my blog', 'site title', 'my wordpress site' ), true ),
			'fix'   => admin_url( 'options-general.php' ),
			'why'   => __( 'The name shows in the header, in browser tabs and in search results.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'tagline',
			'label' => __( 'The tagline says what the site is about', 'tudopress-helper' ),
			'pass'  => '' !== $tagline && 'Just another WordPress site' !== $tagline,
			'fix'   => admin_url( 'options-general.php' ),
			'why'   => __( 'The tagline is the site\'s description in search results and link previews.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'icon',
			'label' => __( 'The site has an icon', 'tudopress-helper' ),
			'pass'  => has_site_icon(),
			'fix'   => admin_url( 'options-general.php' ),
			'why'   => __( 'The small square in browser tabs and on phone home screens. Settings → General → Site Icon.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'front',
			'label' => __( 'A page is set as the home page', 'tudopress-helper' ),
			'pass'  => 'page' === get_option( 'show_on_front' ) && $front && 'publish' === get_post_status( $front ),
			'fix'   => admin_url( 'options-reading.php' ),
			'why'   => __( 'A page the owner can edit like any other, instead of a template only a builder can change.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'menu',
			'label' => __( 'The site has a menu', 'tudopress-helper' ),
			'pass'  => ! empty( $menus ),
			'fix'   => wp_is_block_theme() ? admin_url( 'site-editor.php?p=' . rawurlencode( '/navigation' ) ) : admin_url( 'nav-menus.php' ),
			'why'   => __( 'Without a saved menu, the navigation block lists every published page, in no chosen order.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'samples',
			'label' => __( 'The sample post and page are gone', 'tudopress-helper' ),
			'pass'  => empty( $samples ),
			'fix'   => admin_url( 'edit.php' ),
			'why'   => __( '"Hello world!" and "Sample Page" come with every new WordPress.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'privacy',
			'label' => __( 'A privacy policy is published', 'tudopress-helper' ),
			'pass'  => $privacy && 'publish' === get_post_status( $privacy ),
			'fix'   => admin_url( 'options-privacy.php' ),
			'why'   => __( 'Needed in the EU as soon as the site has a form, a newsletter or statistics.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'timezone',
			'label' => __( 'The time zone is a city', 'tudopress-helper' ),
			'pass'  => '' !== (string) get_option( 'timezone_string' ),
			'fix'   => admin_url( 'options-general.php' ),
			'why'   => __( 'A city follows summer time; a UTC offset doesn\'t, so scheduled posts drift by an hour.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'permalinks',
			'label' => __( 'Addresses are readable', 'tudopress-helper' ),
			'pass'  => '' !== (string) get_option( 'permalink_structure' ),
			'fix'   => admin_url( 'options-permalink.php' ),
			'why'   => __( 'example.com/about/ rather than example.com/?p=12.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'guide',
			'label' => __( 'The site guide is written', 'tudopress-helper' ),
			'pass'  => strlen( $guide_text ) > 200,
			'fix'   => admin_url( 'options-general.php?page=tudopress' ),
			'why'   => __( 'A private page in the owner\'s words: how to add a post, change the menu, who to ask.', 'tudopress-helper' ),
		),
		array(
			'id'    => 'public',
			'label' => __( 'Search engines may list the site', 'tudopress-helper' ),
			'pass'  => '1' === (string) get_option( 'blog_public' ),
			'fix'   => admin_url( 'options-reading.php' ),
			'why'   => __( 'Off while building is fine. Turn it on on launch day (Settings → Reading).', 'tudopress-helper' ),
		),
	);

	/**
	 * Filters the launch checks, e.g. to add one for a shop or a booking form.
	 *
	 * @param array[] $checks Each: id, label, pass, fix, why.
	 */
	return apply_filters( 'hwh_launch_checks', $checks );
}

add_filter(
	'site_status_tests',
	function ( $tests ) {
		foreach ( hwh_launch_checks() as $check ) {
			$tests['direct'][ 'hwh_' . $check['id'] ] = array(
				'label' => $check['label'],
				'test'  => function () use ( $check ) {
					return array(
						'label'       => $check['label'],
						'status'      => $check['pass'] ? 'good' : 'recommended',
						'badge'       => array(
							'label' => __( 'Launch', 'tudopress-helper' ),
							'color' => 'blue',
						),
						'description' => '<p>' . esc_html( $check['why'] ) . '</p>',
						'actions'     => $check['pass'] || ! $check['fix'] ? '' : '<p><a href="' . esc_url( $check['fix'] ) . '">' . esc_html__( 'Fix it', 'tudopress-helper' ) . '</a></p>',
						'test'        => 'hwh_' . $check['id'],
					);
				},
			);
		}
		return $tests;
	}
);
