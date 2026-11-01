<?php
/**
 * Plugin Name: TudoPress Helper
 * Description: Makes the dashboard friendly for the people who run the site day to day: a "Start here" panel, a simpler menu they can switch off, a curated block inserter, a pre-publish checklist (alt text, headings, link text), a launch checklist in Site Health, and quiet privacy defaults. Settings → TudoPress.
 * Version: 0.1.0
 * Requires at least: 6.8
 * Requires PHP: 8.0
 * Author: João Miranda
 * License: GPL-2.0-or-later
 * Text Domain: tudopress-helper
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

define( 'HWH_VERSION', '0.1.0' );
define( 'HWH_FILE', __FILE__ );
define( 'HWH_URL', plugin_dir_url( __FILE__ ) );
define( 'HWH_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Every setting with its default. One option, `tudopress`, holds them all.
 *
 * @return array
 */
function hwh_defaults(): array {
	return array(
		'builder_name'      => '',
		'builder_email'     => '',
		'builders'          => array(),
		'guide_page'        => 0,
		'simple_mode'       => 1,
		'curated_inserter'  => 1,
		'publish_checklist' => 1,
		'no_openverse'      => 1,
		'close_comments'    => 1,
		'private_archives'  => 1,
		'youtube_nocookie'  => 1,
	);
}

/**
 * Reads one setting.
 *
 * @param string $key Setting name from hwh_defaults().
 * @return mixed
 */
function hwh_option( string $key ) {
	$saved = get_option( 'tudopress', array() );
	$all   = array_merge( hwh_defaults(), is_array( $saved ) ? $saved : array() );
	return $all[ $key ] ?? null;
}

/**
 * Whether a user is one of the people who built the site. Builders always see the full admin.
 *
 * @param int $user_id User ID; the current user when 0.
 * @return bool
 */
function hwh_is_builder( int $user_id = 0 ): bool {
	$user_id = $user_id ? $user_id : get_current_user_id();
	return in_array( $user_id, array_map( 'intval', (array) hwh_option( 'builders' ) ), true );
}

/**
 * Whether the current user gets the simple admin: on for everyone except builders,
 * and anyone can switch it off for themselves in their profile.
 *
 * @return bool
 */
function hwh_is_simple(): bool {
	if ( ! hwh_option( 'simple_mode' ) || ! is_user_logged_in() || hwh_is_builder() ) {
		return false;
	}
	return '1' !== get_user_meta( get_current_user_id(), 'hwh_full_admin', true );
}

register_activation_hook(
	__FILE__,
	function () {
		if ( false === get_option( 'tudopress', false ) ) {
			$user     = wp_get_current_user();
			$defaults = hwh_defaults();

			$defaults['builders']      = $user->ID ? array( $user->ID ) : array();
			$defaults['builder_name']  = $user->ID ? $user->display_name : '';
			$defaults['builder_email'] = $user->ID ? $user->user_email : '';
			add_option( 'tudopress', $defaults );
		}
	}
);

foreach ( array( 'help-tips', 'settings', 'dashboard', 'simple-mode', 'editor', 'site-defaults', 'launch-checks', 'login' ) as $hwh_file ) {
	require HWH_DIR . 'inc/' . $hwh_file . '.php';
}
unset( $hwh_file );
