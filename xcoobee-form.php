<?php
/**
 * Plugin Name: XcooBee Form
 * Plugin URI:  https://wordpress.org/plugins/xcoobee-form/
 * Author URI:  https://www.xcoobee.com/
 * Description: Ask user for Data and Consent in GDPR compliant manner. Safely transfer the collected information to backend.
 * Version:     1.2.1
 * Author:      XcooBee
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Text Domain: xcoobee
 * Domain Path: /languages
 *
 * Requires at least: 4.4.0
 * Tested up to: 5.2.2
 *
 * @package XcooBee/Form
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Globals constants.
 */
define( 'XBEE_FORM_ABSPATH', plugin_dir_path( __FILE__ ) ); // With trailing slash.
define( 'XBEE_FORM_DIR_URL', plugin_dir_url( __FILE__ ) );  // With trailing slash.
define( 'XBEE_FORM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The main class.
 *
 * @since 1.0.0
 */
class XcooBee_Form {
	private static $default_email_field = 'email';
	private static $default_xcoobee_id_field = 'xcoobee_id';

	/**
	 * The singleton instance of XcooBee_Form.
	 *
	 * @since 1.0.0
	 * @var XcooBee_Form
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance of XcooBee_Form.
	 *
	 * Ensures only one instance of XcooBee_Form is/can be loaded.
	 *
	 * @since 1.0.0
	 * @return XcooBee_Form
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * The constructor.
	 *
	 * Private constructor to make sure it cannot be called directly from outside the class.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Exit if XcooBee for WordPress is not installed and active.
		if ( ! in_array( 'xcoobee/xcoobee.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			add_action( 'admin_notices', array( $this, 'xcoobee_missing_notice' ) );
			return;
		}

		// Register text strings.
		add_filter( 'xbee_text_strings', [ $this, 'register_text_strings' ], 10, 1 );

		// Include required files.
		$this->includes();

		// Register hooks.
		$this->hooks();

		/**
		 * Fires after the plugin is completely loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'xcoobee_form_loaded' );
	}

	/**
	 * XcooBee fallback notice.
	 *
	 * @since 1.0.0
	 */
	public function xcoobee_missing_notice() {
		echo '<div class="notice notice-warning"><p><strong>' . sprintf( esc_html__( 'XcooBee Form requires XcooBee for WordPress to be installed and active. You can download %s here.', 'xcoobee' ), '<a href="https://wordpress.org/plugins/xcoobee" target="_blank">XcooBee for WordPress</a>' ) . '</strong></p></div>';
	}

	/**
	 * Includes plugin files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {
		// Global includes.
		include_once XBEE_FORM_ABSPATH . 'includes/functions.php';
		include_once XBEE_FORM_ABSPATH . 'includes/class-xcoobee-form-shortcodes.php';
		include_once XBEE_FORM_ABSPATH . 'includes/class-xcoobee-save-consent-shortcodes.php';

		// Back-end includes.
		if ( is_admin() ) {
			include_once XBEE_FORM_ABSPATH . 'includes/admin/class-xcoobee-form-admin.php';
		}

		// Front-end includes.
		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			// Nothing to include for now.
		}
	}

	/**
	 * Plugin hooks.
	 *
	 * @since 1.0.0
	 */
	private function hooks() {
		add_filter( 'plugin_action_links_' . XBEE_FORM_PLUGIN_BASENAME, [ $this, 'action_links' ], 10, 1 );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'styles' ] );

		// Allows processing of our shortcodes in CF7. Without it, the embedded shortcode remains as text in the HTML.
		add_filter( 'wpcf7_form_elements', 'do_shortcode' );
		add_action( 'wpcf7_submit', [ $this, 'handle_cf7_submit' ], 10, 2 );
		// add_action( 'wpcf7_validate', [ $this, 'handle_cf7_validate' ], 10, 2 ); // TODO: Revisit.

		// Uncomment the following to test the CF7 form when the CF7 JavaScript has been disabled.
		// That is, the form submission will do a regular form submission as opposed to using AJAX.
		// add_filter( 'wpcf7_load_js', '__return_false' );
	}

	/**
	 * @since 1.2.0
	 *
	 * @param WPCF7_ContactForm $cf7form
	 * @param Array $result
	 * @param number $result['contact_form_id']
	 * @param string $result['message']
	 * @param string $result['status']
	 * @param string [$result['invalid_fields']]
	 */
	public function handle_cf7_submit( $cf7form, $result ) {
		// error_log('XcooBee_Form->handle_cf7_submit');
		// error_log(print_r($_POST, true));
		// error_log(print_r($result, true));

		if ( $cf7form->in_demo_mode() ) {
			return;
		}

		// $status may be: aborted, acceptance_missing, error, init, mail_failed, mail_sent, spam, validation_failed
		$status = $result['status'];
		if ( $status === 'mail_sent' || $status === 'mail_failed' || $status === 'init' ) {
			$targets = [];
			$xbee_action = isset( $_POST['xbee__action'] ) ? $_POST['xbee__action'] : '';
			if ( 'save_consent' === $xbee_action ) {
				$campaign_reference = isset( $_POST['xbee__campaign_reference'] ) ? $_POST['xbee__campaign_reference'] : '';
				if ( empty( $campaign_reference ) ) {
					$default_campaign_reference = get_option( 'xbee_form_default_campaign_reference', '' );
					$campaign_reference = $default_campaign_reference;
				}

				// error_log('$campaign_reference:' . print_r($campaign_reference, true));

				if ( ! empty( $campaign_reference) ) {
					$xcoobee_id_field = isset( $_POST['xbee__xcoobee_id_field'] ) ? $_POST['xbee__xcoobee_id_field'] : '';
					if ( empty( $xcoobee_id_field ) ) {
						$xcoobee_id_field = $default_xcoobee_id_field;
					}
					$xcoobee_id = isset( $_POST[$xcoobee_id_field] ) ? $_POST[$xcoobee_id_field] : '';
					// If appears to be a valid XcooBee ID, then ...
					if ( ! empty( $xcoobee_id ) && xbee_validate_xid( $xcoobee_id ) ) {
						$targets[] = array( 'target' => $xcoobee_id );
					} else {
						$email_field = isset( $_POST['xbee__email_field'] ) ? $_POST['xbee__email_field'] : '';
						if ( empty( $email_field ) ) {
							$email_field = $default_email_field;
						}
						$email = isset( $_POST[$email_field] ) ? $_POST[$email_field] : '';
						if ( ! empty( $email ) ) {
							$targets[] = array( 'target' => $email );
						}
					}

					if ( count( $targets ) > 0 ) {
						$xcoobee = XcooBee::get_xcoobee();
						$response = $xcoobee->consents->getCampaignIdByRef( $campaign_reference );

						// error_log('$response:' . print_r($response, true));
						if ( 200 === $response->code ) {
							$campaign_id = $response->result;
							// error_log('$targets:' . print_r($targets, true));
							// error_log('$campaign_id:' . print_r($campaign_id, true));
							$response = $xcoobee->consents->registerConsents( null, $targets, null, $campaign_id );
							if ( 200 !== $response->code || ! empty( $response->errors ) ) {
								error_log( 'Failed to save consent. Error Code: ' . $response->code );
								if ( ! empty( $response->errors ) ) {
									error_log( print_r( $response->errors, true ) );
								}
							}
						} else {
							error_log( 'Failed to save consent. Unable to get campaign using "' . $campaign_reference . '".' );
						}
					}
				} else {
					error_log( 'No campaign reference set. Unable to save consent.' );
				}
			}
		}
	}

	// /**
	//  * @since 1.2.0
	//  *
	//  * @param WPCF7_Validation $result
	//  * @param Array<WPCF7_FormTag>
	//  */
	// public function handle_cf7_validate( $result, $tags ) {
	// 	error_log('XcooBee_Form->handle_cf7_validate');
	// 	error_log(print_r($result, true));
	// 	error_log(print_r($tags, true));

	// 	return $result;
	// }

	/**
	 * Adds plugin action links.
	 *
	 * @since 1.1.0
	 */
	public function action_links( $links ) {
		$action_links = [
			'settings' => '<a href="' . admin_url( 'admin.php?page=xcoobee&tab=form' ) . '" aria-label="' . esc_attr__( 'View XcooBee Form settings', 'xcoobee' ) . '">' . esc_html__( 'Settings', 'xcoobee' ) . '</a>',
		];

		return array_merge( $action_links, $links );
	}

	/**
	 * Loads plugin scripts.
	 *
	 * @since 1.0.0
	 */
	public function scripts() {
		// Back-end scripts.
		if ( 'admin_enqueue_scripts' === current_action() ) {
			wp_enqueue_script( 'xbee-form-admin-scripts', XBEE_FORM_DIR_URL . 'assets/dist/js/admin/scripts.min.js', [ 'clipboard', 'jquery', 'xbee-admin-scripts' ], null, true );
			wp_localize_script( 'xbee-form-admin-scripts', 'xbeeFormAdminParams', [
				'ajaxURL' => admin_url( 'admin-ajax.php' ),
			] );
		}
		// Front-end scripts.
		else {
			wp_enqueue_script( 'xbee-form-scripts', XBEE_FORM_DIR_URL . 'assets/dist/js/scripts.min.js', [ 'jquery' ], null, false );
			wp_localize_script( 'xbee-form-scripts', 'xbeeFormParams', [
				'ajaxURL'        => admin_url( 'admin-ajax.php' ),
				'miniConsentURL' =>  'test' === xbee_get_env() ? 'https://testapp.xcoobee.net/miniConsent' : 'https://app.xcoobee.net/miniConsent',
				'images'         => [
					'loader' => XBEE_DIR_URL . '/assets/dist/images/loader.svg',
				]
			] );
		}
	}

	/**
	 * Enqueue plugin styles.
	 *
	 * @since 1.0.0
	 */
	public function styles() {
		// Back-end styles.
		if ( 'admin_enqueue_scripts' === current_action() ) {
			wp_enqueue_style( 'xbee-form-admin-styles', XBEE_FORM_DIR_URL . 'assets/dist/css/admin/main.min.css', [], false, 'all' );
		}
		// Front-end styles.
		else {
			wp_enqueue_style( 'xbee-form-styles', XBEE_FORM_DIR_URL . 'assets/dist/css/main.min.css', [], false, 'all' );
		}
	}

	/**
	 * Defines and registers text strings.
	 *
	 * Use `url_name_of_the_url` for URL keys and `message_type_the_message` for message keys.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $strings Text strings array.
	 * @return array The updated text strings array.
	 */
	public function register_text_strings( $strings ) {
		return array_merge( $strings, [] );
	}

	/**
	 * Activation hooks.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Nothing to do for now.
	}

	/**
	 * Deactivation hooks.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		// Nothing to do for now.
	}

	/**
	 * Uninstall hooks.
	 *
	 * @since 1.0.0
	 */
	public static function uninstall() {
		include_once XBEE_FORM_ABSPATH . 'uninstall.php';
	}
}

function init_xcoobee_form() {
	XcooBee_Form::get_instance();
}
add_action( 'plugins_loaded', 'init_xcoobee_form' );

// Plugin hooks.
register_activation_hook( __FILE__, [ 'XcooBee_Form', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'XcooBee_Form', 'deactivate' ] );
register_uninstall_hook( __FILE__, [ 'XcooBee_Form', 'uninstall' ] );
