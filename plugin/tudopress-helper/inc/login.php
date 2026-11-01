<?php
/**
 * The login screen shows the site's own icon and name, so people know they're in the right place.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'login_headerurl', fn() => home_url( '/' ) );
add_filter( 'login_headertext', fn() => get_bloginfo( 'name' ) );

add_action(
	'login_enqueue_scripts',
	function () {
		$icon = get_site_icon_url( 192 );
		if ( ! $icon ) {
			return;
		}
		wp_add_inline_style( 'login', '#login h1 a{background-image:url(' . esc_url( $icon ) . ');background-size:contain;border-radius:16px;width:96px;height:96px}' );
	}
);
