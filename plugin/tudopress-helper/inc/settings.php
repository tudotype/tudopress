<?php
/**
 * Settings → TudoPress: who built the site, where the site guide is, and each helper on or off.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

/**
 * The switches, with the label and the help each one shows.
 *
 * @return array key => [ label, help ]
 */
function hwh_switches(): array {
	return array(
		'simple_mode'       => array( __( 'Simple menu for everyone but the builders', 'tudopress-helper' ), __( 'Hides Tools, Plugins, the theme file editor and, while comments are off, Comments. Nothing is locked: anyone can bring the full menu back under Users → Profile → "Show the full admin menu".', 'tudopress-helper' ) ),
		'curated_inserter'  => array( __( 'Shorter block list', 'tudopress-helper' ), __( 'Hides blocks people rarely need (Classic, Verse, RSS, Tag Cloud, Calendar…) from the + menu, and WordPress.org\'s pattern directory from the Patterns tab, so the site\'s own sections come first. Blocks already on a page keep working. Builders still see everything.', 'tudopress-helper' ) ),
		'publish_checklist' => array( __( 'Checklist before publishing', 'tudopress-helper' ), __( 'Before Publish, lists images without a description (alt text), skipped heading levels, links that say only "click here" or show a web address, empty paragraphs used as spacing, and a missing featured image or excerpt. It advises; it never blocks.', 'tudopress-helper' ) ),
		'no_openverse'      => array( __( 'No Openverse images in the editor', 'tudopress-helper' ), __( 'Removes the free-image search from the inserter\'s Media tab. Its licences need a credit line people forget; their own photos are safer.', 'tudopress-helper' ) ),
		'close_comments'    => array( __( 'Comments off everywhere', 'tudopress-helper' ), __( 'Closes comments and pingbacks on every post and page, and removes the Discussion panel. Most small sites get only spam. Untick to let each post decide.', 'tudopress-helper' ) ),
		'private_archives'  => array( __( 'Hide author and date archives', 'tudopress-helper' ), __( 'Sends /author/… and date archives to the home page and hides the user list from visitors, so login names aren\'t public. Turn off for a magazine with several authors.', 'tudopress-helper' ) ),
		'youtube_nocookie'  => array( __( 'YouTube in privacy mode', 'tudopress-helper' ), __( 'Embedded YouTube videos load from youtube-nocookie.com: YouTube stores nothing about a visitor until they press play.', 'tudopress-helper' ) ),
	);
}

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'TudoPress', 'tudopress-helper' ),
			__( 'TudoPress', 'tudopress-helper' ),
			'manage_options',
			'tudopress',
			'hwh_settings_page'
		);
	}
);

add_action(
	'admin_init',
	function () {
		register_setting(
			'tudopress',
			'tudopress',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'hwh_sanitize',
				'default'           => hwh_defaults(),
			)
		);
	}
);

/**
 * Keeps only known settings, each in its own shape.
 *
 * @param mixed $input Posted values.
 * @return array
 */
function hwh_sanitize( $input ): array {
	$input = is_array( $input ) ? $input : array();
	$clean = array(
		'builder_name'  => sanitize_text_field( $input['builder_name'] ?? '' ),
		'builder_email' => sanitize_email( $input['builder_email'] ?? '' ),
		'builders'      => array_values( array_filter( array_map( 'absint', (array) ( $input['builders'] ?? array() ) ) ) ),
		'guide_page'    => absint( $input['guide_page'] ?? 0 ),
	);
	foreach ( array_keys( hwh_switches() ) as $key ) {
		$clean[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
	}
	return $clean;
}

/**
 * The settings screen.
 */
function hwh_settings_page(): void {
	$admins = get_users(
		array(
			'role__in' => array( 'administrator', 'editor' ),
			'orderby'  => 'display_name',
		)
	);
	$pages  = get_pages( array( 'post_status' => array( 'publish', 'private', 'draft' ) ) );
	$opts   = array( 0 => __( '— None yet —', 'tudopress-helper' ) );
	foreach ( $pages as $page ) {
		$opts[ $page->ID ] = $page->post_title . ( 'publish' !== $page->post_status ? ' (' . $page->post_status . ')' : '' );
	}
	?>
	<div class="wrap hwh-settings">
		<h1><?php esc_html_e( 'TudoPress', 'tudopress-helper' ); ?></h1>
		<p class="hwh-lede"><?php esc_html_e( 'Tools that make this dashboard easier for the people who run the site day to day. Each one can be switched off.', 'tudopress-helper' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'tudopress' ); ?>

			<h2><?php esc_html_e( 'Who to ask', 'tudopress-helper' ); ?></h2>
			<?php
			hwh_field(
				array(
					'id'          => 'hwh-builder-name',
					'name'        => 'tudopress[builder_name]',
					'label'       => __( 'Builder\'s name', 'tudopress-helper' ),
					'help'        => __( 'Shown on the Start here panel and at the foot of every admin screen as "Need help? Write to …".', 'tudopress-helper' ),
					'value'       => hwh_option( 'builder_name' ),
					'placeholder' => 'João',
				)
			);
			hwh_field(
				array(
					'id'          => 'hwh-builder-email',
					'type'        => 'email',
					'name'        => 'tudopress[builder_email]',
					'label'       => __( 'Builder\'s email', 'tudopress-helper' ),
					'help'        => __( 'Where the help links write to. Empty = no help link.', 'tudopress-helper' ),
					'value'       => hwh_option( 'builder_email' ),
					'placeholder' => 'you@example.com',
				)
			);
			hwh_field(
				array(
					'id'      => 'hwh-guide-page',
					'type'    => 'select',
					'name'    => 'tudopress[guide_page]',
					'label'   => __( 'Site guide page', 'tudopress-helper' ),
					'help'    => __( 'A private page that explains how this site works, in the owner\'s words. Linked from the Start here panel and the toolbar\'s Help menu. Make it Private so only people who log in can read it.', 'tudopress-helper' ),
					'value'   => hwh_option( 'guide_page' ),
					'options' => $opts,
				)
			);
			?>

			<h2><?php esc_html_e( 'Builders', 'tudopress-helper' ); ?></h2>
			<p><?php esc_html_e( 'Builders always see the full admin and every block, and get the launch checklist on the dashboard.', 'tudopress-helper' ); ?></p>
			<fieldset class="hwh-field">
				<legend class="screen-reader-text"><?php esc_html_e( 'Builders', 'tudopress-helper' ); ?></legend>
				<?php foreach ( $admins as $user ) : ?>
					<label class="hwh-check">
						<input type="checkbox" name="tudopress[builders][]" value="<?php echo esc_attr( (string) $user->ID ); ?>" <?php checked( hwh_is_builder( $user->ID ) ); ?>>
						<?php echo esc_html( $user->display_name . ' (' . $user->user_login . ')' ); ?>
					</label>
				<?php endforeach; ?>
			</fieldset>

			<h2><?php esc_html_e( 'Helpers', 'tudopress-helper' ); ?></h2>
			<?php
			foreach ( hwh_switches() as $key => $switch ) {
				hwh_field(
					array(
						'id'    => 'hwh-' . $key,
						'type'  => 'checkbox',
						'name'  => 'tudopress[' . $key . ']',
						'label' => $switch[0],
						'help'  => $switch[1],
						'value' => hwh_option( $key ),
					)
				);
			}
			submit_button();
			?>
		</form>
	</div>
	<?php
}
