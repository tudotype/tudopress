<?php
/**
 * The simple menu: fewer doors for people who only write and edit, never a locked one.
 *
 * Everything hidden here stays reachable: the profile has a switch that brings the full
 * menu back, and the hidden screens still open by address.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		if ( ! hwh_is_simple() ) {
			return;
		}
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'plugins.php' );
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
		remove_submenu_page( 'tools.php', 'theme-editor.php' );
		remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
		if ( hwh_option( 'close_comments' ) ) {
			remove_menu_page( 'edit-comments.php' );
			remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		}
	},
	999
);

add_action(
	'admin_bar_menu',
	function ( WP_Admin_Bar $bar ) {
		if ( hwh_is_simple() ) {
			$bar->remove_node( 'wp-logo' );
		}
		if ( hwh_option( 'close_comments' ) ) {
			$bar->remove_node( 'comments' );
		}

		$guide = (int) hwh_option( 'guide_page' );
		if ( is_user_logged_in() && $guide && get_post( $guide ) ) {
			$bar->add_node(
				array(
					'id'    => 'hwh-guide',
					'title' => '<span class="ab-icon dashicons dashicons-editor-help" aria-hidden="true"></span><span class="ab-label">' . esc_html__( 'Site guide', 'tudopress-helper' ) . '</span>',
					'href'  => get_permalink( $guide ),
				)
			);
		}
	},
	999
);

/**
 * The profile switch.
 *
 * @param WP_User $user The user being edited.
 */
function hwh_profile_switch( WP_User $user ): void {
	if ( ! hwh_option( 'simple_mode' ) || hwh_is_builder( $user->ID ) ) {
		return;
	}
	?>
	<h2><?php esc_html_e( 'Admin menu', 'tudopress-helper' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( 'Full menu', 'tudopress-helper' ); ?></th>
			<td>
				<?php
				hwh_field(
					array(
						'id'    => 'hwh_full_admin',
						'type'  => 'checkbox',
						'label' => __( 'Show the full admin menu', 'tudopress-helper' ),
						'help'  => __( 'The menu normally hides Tools, Plugins and the code editors, which you rarely need. Tick this to see everything; untick it to go back.', 'tudopress-helper' ),
						'value' => '1' === get_user_meta( $user->ID, 'hwh_full_admin', true ),
					)
				);
				wp_nonce_field( 'hwh_profile', 'hwh_profile_nonce' );
				?>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'hwh_profile_switch' );
add_action( 'edit_user_profile', 'hwh_profile_switch' );

/**
 * Saves the profile switch.
 *
 * @param int $user_id The user being saved.
 */
function hwh_profile_save( int $user_id ): void {
	if ( ! isset( $_POST['hwh_profile_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['hwh_profile_nonce'] ), 'hwh_profile' ) || ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}
	update_user_meta( $user_id, 'hwh_full_admin', empty( $_POST['hwh_full_admin'] ) ? '0' : '1' );
}
add_action( 'personal_options_update', 'hwh_profile_save' );
add_action( 'edit_user_profile_update', 'hwh_profile_save' );
