<?php
/**
 * The XcooBee_Save_Consent_Shortcodes class
 *
 * @package XcooBee/Document
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate plugin shortcodes.
 *
 * @since 1.2.0
 */
class XcooBee_Save_Consent_Shortcodes {
	/**
	 * The constructor.
	 */
	public function __construct() {
		add_shortcode( 'xcoobee_save_consent', [ $this, 'save_consent' ] );
	}

	/**
	 * Generates the HTML output for `[form]`.
	 *
	 * @param array  $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function save_consent( $atts ) {
		$current_user_id = get_current_user_id();

		$xbee_xid = null;
		if ( $current_user_id ) {
			$xbee_xid = get_user_meta( $current_user_id, 'xbee_xid', true );
		}

		$atts = shortcode_atts( array(
			'campaign_reference' => get_option( 'xbee_form_default_campaign_reference', '' ),
			'email_field'        => 'email',
			'xcoobee_id_field'   => 'xcoobee_id',
		), $atts );

		ob_start();
		?>
		<input type="hidden" name="xbee__action" value="save_consent">
		<input type="hidden" name="xbee__campaign_reference" value="<?php echo esc_attr( $atts['campaign_reference'] ); ?>">
		<input type="hidden" name="xbee__xcoobee_id_field" value="<?php echo esc_attr( $atts['xcoobee_id_field'] ); ?>">
		<input type="hidden" name="xbee__email_field" value="<?php echo esc_attr( $atts['email_field'] ); ?>">
		<?php if ( ! empty( $xbee_xid ) ) : ?>
			<script>
				// Pre-populate the XcooBee ID field with current user's XcooBee ID.
				(function ($) {
					var xcoobeeIdField = <?php echo json_encode( $atts['xcoobee_id_field'] ); ?>;
					var elmt = $('input[type=hidden][value=save_consent]').get(-1);
					if (elmt) {
						var xcoobeeIdInput = elmt.form.elements[xcoobeeIdField];
						if (xcoobeeIdInput && !xcoobeeIdInput.value) {
							xcoobeeIdInput.value = <?php echo json_encode( $xbee_xid ); ?>;
						}
					}
				})(jQuery);
			</script>
		<?php endif ?>
		<?php
		$output = ob_get_contents();
		ob_clean();

		return $output;
	}
}

new XcooBee_Save_Consent_Shortcodes;
