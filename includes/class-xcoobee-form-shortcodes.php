<?php
/**
 * The XcooBee_Form_Shortcodes class
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
 * @since 1.0.0
 */
class XcooBee_Form_Shortcodes {
	/**
	 * The constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_shortcode( 'xcoobee_form', [ $this, 'form' ] );
	}

	/**
	 * Generates the HTML output for `[form]`.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function form( $atts ) {
		$atts = shortcode_atts( array(
			'campaign_reference' => get_option( 'xbee_form_default_campaign_reference', '' ),
			'button_size'        => 'medium',
			'approve_redirect'   => '',
			'decline_redirect'   => '',
			'form_id'            => '',
			'text'               => __( 'Safe Form', 'xcoobee' ),
			'alt_text'           => __( 'Submit data securely via Xcoobee!', 'xcoobee' ),
			'class'              => '',
			'id'                 => '',
		), $atts );

		// Button size.
		switch( $atts['button_size'] ) {
			case 'large': $btn_size = 'xbee-btn-lg'; break;
			case 'small': $btn_size = 'xbee-btn-sm'; break;
			default: $btn_size = 'xbee-btn-md';
		}

		// HTML attributes.
		$class = 'xbee-form xbee-btn ' . $btn_size . xbee_add_css_class( ! empty( $atts['class'] ), $atts['class'], true, false );
		$html_atts['class'] = $class;
		$html_atts['id'] = $atts['id'];
		$html_atts['data-campaign-reference'] = $atts['campaign_reference'];
		$html_atts['data-form-id'] = $atts['form_id'];
		$html_atts['data-approve-redirect'] = $atts['approve_redirect'];
		$html_atts['data-decline-redirect'] = $atts['decline_redirect'];

		ob_start();
		?>
		<div <?php xbee_generate_html_tag_atts( $html_atts, false, false, true ); ?>>
			<span class="xbee-btn-title"><?php echo $atts['text']; ?></span>
			<span class="xbee-btn-alt-title"><?php echo $atts['alt_text']?></span>
		</div>

		<?php
		$output = ob_get_contents();
		ob_clean();

		return $output;
	}
}

new XcooBee_Form_Shortcodes;