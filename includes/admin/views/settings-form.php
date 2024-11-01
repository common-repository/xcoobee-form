<?php
/**
 * The cookie tab
 *
 * @package XcooBee/Form/Admin/Views
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_campaign_reference = get_option( 'xbee_form_default_campaign_reference', '' );
?>

<?php settings_fields( 'xbee_form' ); ?>
<div class="intro">
	<div class="right">
		<h2><?php _e( 'XcooBee Form Addon', 'xcoobee' ); ?></h2>
		<p><?php _e( 'Secure and privacy regulation (GDPR) compliant data gathering via XcooBee Forms. You receive consent for every-data point and it’s specific use. XcooBee Forms are highly secure forms that you can use instead or alongside of webforms. Users interacting with secure forms automatically grant you consent you need to be compliant and your company can manage consent via the XcooBee Privacy Network.', 'xcoobee' ); ?></p>
	</div>
	<div class="left">
		<img src="<?php echo XBEE_DIR_URL . 'assets/dist/images/icon-xcoobee.svg'; ?>" />
	</div>
</div>

<!-- Section: Settings -->
<div class="section">
	<h2 class="headline"><?php _e( 'Form Settings', 'xcoobee' ); ?></h2>
	<table class="form-table">
		<tr>
			<th scope="row"><label for="xbee_form_default_campaign_reference"><?php _e( 'Default Campaign Reference', 'xcoobee' ); ?></label></th>
			<td>
				<input name="xbee_form_default_campaign_reference" type="text" id="xbee_form_default_campaign_reference" value="<?php echo esc_attr( $default_campaign_reference ); ?>" class="regular-text" />
				<p class="description"><?php _e( 'The default campaign reference.', 'xcoobee' ); ?></p>
			</td>
		</tr>
	</table>
</div>
<!-- End Section: Settings -->

<!-- Section: Shortcodes -->
<div class="section shortcodes">
	<h2 class="headline"><?php _e( 'Shortcodes', 'xcoobee' ); ?></h2>
	<p class="message"><?php _e( 'Use the following shortcode to display safe form buttons on your site.', 'xcoobee' ); ?></p>
	<div class="tabs">
		<nav class="tabs-nav">
			<a class="nav active" data-nav="xcoobee-form"><code>[xcoobee_form]</code><span><?php _e( 'Renders a safe form button.', 'xcoobee' ); ?></span></a>
			<a class="nav" data-nav="xcoobee_save_consent"><code>[xcoobee_save_consent]</code><span><?php _e( 'This shortcode can be embedded in Contact Form 7. It will register corresponding consent for using of data with XcooBee. Users can then manage consent directly.', 'xcoobee' ); ?></span></a>
		</nav>
		<div class="tabs-content">
			<div class="content active" data-nav="xcoobee-form">
				<table class="shortcode-info">
					<thead>
						<tr>
							<th><?php _e( 'Attribute', 'xcoobee' ); ?></th>
							<th><?php _e( 'Description', 'xcoobee' ); ?></th>
							<th><?php _e( 'Default', 'xcoobee' ); ?></th>
							<th><?php _e( 'Example', 'xcoobee' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><code>campaign_reference</code></td>
							<td><?php _e( 'Campaign reference.', 'xcoobee' ); ?></td>
							<td>Default Campaign Reference</td>
							<td><code>xxx.xxxxxxxxxx</code></td>
						</tr>
						<tr>
							<td><code>button_size</code></td>
							<td><?php _e( 'Size of the button (<code>small</code>, <code>medium</code> or <code>large</code>).', 'xcoobee' ); ?></td>
							<td><code>medium</code></td>
							<td><code>small</code></td>
						</tr>
						<tr>
							<td><code>approve_redirect</code></td>
							<td><?php _e( 'URL to redirect to when request is approved.', 'xcoobee' ); ?></td>
							<td>&nbsp;</td>
							<td><code>https://example.com/thank-you/</code></td>
						</tr>
						<tr>
							<td><code>decline_redirect</code></td>
							<td><?php _e( 'URL to redirect to when request is declined.', 'xcoobee' ); ?></td>
							<td>&nbsp;</td>
							<td><code>https://example.com/oops/</code></td>
						</tr>
						<tr>
							<td><code>form_id</code></td>
							<td><?php _e( 'The <code>id</code> of the form. Leave this empty if the shortcode is placed inside the form.', 'xcoobee' ); ?></td>
							<td>&nbsp;</td>
							<td><code>my-form</code></td>
						</tr>
						<tr>
							<td><code>text</code></td>
							<td><?php _e( 'Alternate text for the button.', 'xcoobee' ); ?></td>
							<td><code><?php _e( 'Safe Form', 'xcoobee' ); ?></code></td>
							<td><code><?php _e( 'Your text', 'xcoobee' ); ?></code></td>
						</tr>
						<tr>
							<td><code>alt_text</code></td>
							<td><?php _e( 'Alternate text for the button on hover.', 'xcoobee' ); ?></td>
							<td><code><?php _e( 'Submit data securley via XcooBee!', 'xcoobee' ); ?></code></td>
							<td><code><?php _e( 'Your text', 'xcoobee' ); ?></code></td>
						</tr>
						<tr>
							<td><code>class</code></td>
							<td><?php _e( 'Additional CSS classes to button.', 'xcoobee'); ?></td>
							<td>&nbsp;</td>
							<td><code>my-class my-second-class</code></td>
						</tr>
						<tr>
							<td><code>id</code></td>
							<td><?php _e( 'Custom HTML Id for the button.', 'xcoobee' ); ?></td>
							<td>&nbsp;</td>
							<td><code>my-safe-form-button</code></td>
						</tr>
					</tbody>
				</table>
				<div class="example">
					<span class="xbee-copy-text xbee-tooltip" data-clipboard-target="#xcoobee_form_all_attrs" data-tooltip="<?php esc_attr_e('Copy', 'xcoobee'); ?>"></span>
					<span class="headline"><?php _e( 'XcooBee Form Example', 'xcoobee' ); ?></span>
					<code id="xcoobee_form_all_attrs">[xcoobee_form campaign_reference=&quot;xxx.xxxxxxxxxxx&quot; button_size=&quot;medium&quot; approve_redirect=&quot;https://example.com/thank-you/&quot; decline_redirect=&quot;https://example.com/oops/&quot; text=&quot;Use Safe Form&quot; alt_text=&quot;Use the XcooBee secure network!&quot; class=&quot;my-safe-form&quot; id=&quot;xbee-safe-form&quot;][/xcoobee_form]</code>
				</div>
			</div>
			<div class="content" data-nav="xcoobee_save_consent">
				<table class="shortcode-info">
					<thead>
						<tr>
							<th><?php _e( 'Attribute', 'xcoobee' ); ?></th>
							<th><?php _e( 'Description', 'xcoobee' ); ?></th>
							<th><?php _e( 'Default', 'xcoobee' ); ?></th>
							<th><?php _e( 'Example', 'xcoobee' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><code>campaign_reference</code></td>
							<td><?php _e( 'This is the XcooBee Campaign Reference. You can get this from your XcooBee campaign wizard screen.', 'xcoobee' ); ?></td>
							<td>Default Campaign Reference</td>
							<td><code>xxx.xxxxxxxxxx</code></td>
						</tr>
						<tr>
							<td><code>xcoobee_id_field</code></td>
							<td><?php _e( "This should be the name or ID of the form field of the form's field that contains the user's XcooBee ID. If both an email and XcooBee ID are present, we will process using XcooBee ID first.", 'xcoobee' ); ?></td>
							<td>xcoobee_id</td>
							<td><code>your-xcoobeeid</code></td>
						</tr>
						<tr>
							<td><code>email_field</code></td>
							<td><?php _e( "This should be the name or ID of the form field of the form's field that contains the user's email.", 'xcoobee' ); ?></td>
							<td>email</td>
							<td><code>your-email</code></td>
						</tr>
					</tbody>
				</table>
				<div class="example">
					<span class="xbee-copy-text xbee-tooltip" data-clipboard-target="#xcoobee_save_consent_all_defaults" data-tooltip="<?php esc_attr_e('Copy', 'xcoobee'); ?>"></span>
					<span class="headline"><?php _e( 'XcooBee Save Consent', 'xcoobee' ); ?></span>
					<code id="xcoobee_save_consent_all_defaults">[xcoobee_save_consent]</code>
				</div>
				<div class="example">
					<span class="xbee-copy-text xbee-tooltip" data-clipboard-target="#xcoobee_save_consent_no_defaults" data-tooltip="<?php esc_attr_e('Copy', 'xcoobee'); ?>"></span>
					<span class="headline"><?php _e( 'XcooBee Save Consent', 'xcoobee' ); ?></span>
					<code id="xcoobee_save_consent_no_defaults">[xcoobee_save_consent campaign_reference=&quot;xxx.xxxxxxxxxx&quot; xcoobee_id_field=&quot;xcoobeeid-field&quot; email_field=&quot;email-field&quot;]</code>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Section: Shortcodes -->

<p class="actions"><?php submit_button( 'Save Changes', 'primary', 'submit', false ); ?></p>

<script>
	(function ($) {
		$(document).ready(function ($) {
			new ClipboardJS('.xbee-copy-text');
		});
	})(jQuery);
</script>

<?php
unset( $default_campaign_reference );
