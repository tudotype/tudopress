<?php
/**
 * Help tips and labelled fields for any admin screen.
 *
 * The lesson from tudotype.com: every field a person fills in says, in one or two plain
 * sentences, where its value shows up on the site and what happens when it's empty.
 * Use these helpers in your own meta boxes and settings screens.
 *
 * @package TudoPress_Helper
 */

defined( 'ABSPATH' ) || exit;

/**
 * A "?" beside a label. The explanation opens on hover and on keyboard focus, and screen
 * readers get it as the field's description.
 *
 * @param string $id   The field's id; the tip gets `<id>-help`.
 * @param string $text Plain text: where it shows, what empty means, an example.
 * @return string Escaped HTML.
 */
function hwh_help_tip( string $id, string $text ): string {
	return '<span class="hwh-help" tabindex="0" role="button" aria-label="' . esc_attr__( 'Help', 'tudopress-helper' ) . '" aria-describedby="' . esc_attr( $id . '-help' ) . '">?'
		. '<span class="hwh-help__tip" role="tooltip" id="' . esc_attr( $id . '-help' ) . '">' . esc_html( $text ) . '</span></span>';
}

/**
 * One labelled field: label, help tip, input, and a visible example under it.
 *
 * @param array $args {
 *     @type string $id          Input id and name.
 *     @type string $label       What the field is, in the reader's words.
 *     @type string $help        Where it shows up and what empty means.
 *     @type string $type        text | email | url | textarea | checkbox | select.
 *     @type mixed  $value       Current value.
 *     @type string $placeholder An example value.
 *     @type array  $options     For select: value => label.
 *     @type string $name        Input name, when it differs from the id.
 * }
 */
function hwh_field( array $args ): void {
	$a = wp_parse_args(
		$args,
		array(
			'id'          => '',
			'label'       => '',
			'help'        => '',
			'type'        => 'text',
			'value'       => '',
			'placeholder' => '',
			'options'     => array(),
			'name'        => '',
		)
	);

	$name = $a['name'] ? $a['name'] : $a['id'];
	$tip  = $a['help'] ? hwh_help_tip( $a['id'], $a['help'] ) : '';
	$desc = $a['help'] ? ' aria-describedby="' . esc_attr( $a['id'] . '-help' ) . '"' : '';

	echo '<div class="hwh-field">';
	if ( 'checkbox' === $a['type'] ) {
		echo '<label><input type="checkbox" id="' . esc_attr( $a['id'] ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( (bool) $a['value'], true, false ) . $desc . '> ' . esc_html( $a['label'] ) . '</label>' . $tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above and in hwh_help_tip().
		echo '</div>';
		return;
	}

	echo '<label class="hwh-field__label" for="' . esc_attr( $a['id'] ) . '">' . esc_html( $a['label'] ) . '</label>' . $tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in hwh_help_tip().

	switch ( $a['type'] ) {
		case 'textarea':
			echo '<textarea class="large-text" rows="4" id="' . esc_attr( $a['id'] ) . '" name="' . esc_attr( $name ) . '" placeholder="' . esc_attr( $a['placeholder'] ) . '"' . $desc . '>' . esc_textarea( (string) $a['value'] ) . '</textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $desc is escaped.
			break;
		case 'select':
			echo '<select id="' . esc_attr( $a['id'] ) . '" name="' . esc_attr( $name ) . '"' . $desc . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $desc is escaped.
			foreach ( $a['options'] as $value => $label ) {
				echo '<option value="' . esc_attr( (string) $value ) . '"' . selected( (string) $a['value'], (string) $value, false ) . '>' . esc_html( $label ) . '</option>';
			}
			echo '</select>';
			break;
		default:
			echo '<input class="regular-text" type="' . esc_attr( $a['type'] ) . '" id="' . esc_attr( $a['id'] ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $a['value'] ) . '" placeholder="' . esc_attr( $a['placeholder'] ) . '"' . $desc . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $desc is escaped.
	}
	echo '</div>';
}

add_action(
	'admin_enqueue_scripts',
	function () {
		wp_enqueue_style( 'hwh-admin', HWH_URL . 'assets/admin.css', array(), HWH_VERSION );
	}
);
