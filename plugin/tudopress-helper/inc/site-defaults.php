<?php
/**
 * Quiet defaults most small sites want: no comment spam, no public login names, privacy-friendly embeds.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

// Comments off everywhere, and the Discussion panel gone, so nobody wonders why nothing arrives.
add_action(
	'init',
	function () {
		if ( ! hwh_option( 'close_comments' ) ) {
			return;
		}
		foreach ( get_post_types_by_support( 'comments' ) as $type ) {
			remove_post_type_support( $type, 'comments' );
			remove_post_type_support( $type, 'trackbacks' );
		}
		add_filter( 'comments_open', '__return_false', 20 );
		add_filter( 'pings_open', '__return_false', 20 );
		add_filter( 'comments_array', '__return_empty_array', 20 );
	},
	100
);

// Author and date archives go home, and visitors can't list users: login names stay private.
add_action(
	'template_redirect',
	function () {
		if ( hwh_option( 'private_archives' ) && ( is_author() || is_date() ) ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);

add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return hwh_option( 'private_archives' ) && 'users' === $name ? false : $provider;
	},
	10,
	2
);

add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( hwh_option( 'private_archives' ) && ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);

// YouTube's privacy-enhanced mode: nothing stored about a visitor until they press play.
add_filter(
	'embed_oembed_html',
	function ( $html ) {
		if ( ! hwh_option( 'youtube_nocookie' ) ) {
			return $html;
		}
		return preg_replace( '#https?://(www\.)?youtube\.com/embed/#i', 'https://www.youtube-nocookie.com/embed/', $html );
	}
);
