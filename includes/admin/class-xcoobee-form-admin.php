<?php
/**
 * The XcooBee_Form_Admin class.
 *
 * @package XcooBee/Form/Admin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controls form settings.
 *
 * @since 1.0.0
 */
class XcooBee_Form_Admin {
	/**
	 * The constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_init', [ $this, 'settings' ] );
	}

	/**
	 * Registers form setting page.
	 *
	 * @since 1.0.0
	 */
	public function add_page() {
		add_submenu_page(
			'xcoobee',
			__( 'Form', 'xcoobee' ),
			__( 'Form', 'xcoobee' ),
			'manage_options',
			'admin.php?page=xcoobee&tab=form'
		);
	}

	/**
	 * Registers the form setting fields.
	 *
	 * @since 1.0.0
	 */
	public function settings() {
		// Form settings.
		register_setting( 'xbee_form', 'xbee_form_default_campaign_reference' );

	}
}

new XcooBee_Form_Admin;