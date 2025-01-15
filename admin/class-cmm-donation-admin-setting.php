<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://mno.xyz
 * @since      1.0.0
 *
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/admin
 * @author     Hemant Lama <hemantlama55@gmail.com>
 */
class Cmm_Donation_Admin_Setting {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $page_slug;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->page_slug = 'cmm-donation-setting';

	}

	public function cmm_donation_add_setting_page(){
		add_submenu_page(
            'edit.php?post_type=cmm-donation',
            'CMM Donation', //page title
            'Setting', //menu title
            'edit_themes', //capability,
            'cmm-donation-setting',//menu slug
            array( $this, 'cmm_donation_setting_page' ) //callback function
        );
	}

	public function cmm_donation_setting_page()
    {
        ?>
		<div class="wrap cmm-donation-setting-page-wrap">
			<h1>Donation Setting</h1>
			<p></p>
			<?php settings_errors(); ?>
			<!-- <h2>Secure Pay</h2> -->
			<form method="post" action="options.php">
				<?php
				settings_fields('cmm_donation_setting');
				do_settings_sections($this->page_slug);
				submit_button();
				?>
			</form>
		</div>
		<?php 
	}

	public function cmm_donation_setting_page_init()
    {


        register_setting('cmm_donation_setting', 'cmm_donation_setting', array($this, 'cmm_donation_admin_setting_field_validate' ));

		add_settings_section('secure_pay_section', 'Secure Pay', array($this, 'secure_pay_section_text_fn'), $this->page_slug);

		add_settings_field('marchant_code', 'Merchant Code', array( $this, 'marchant_code_view_fn'), $this->page_slug, 'secure_pay_section');
		add_settings_field('client_id', 'Client ID', array( $this, 'client_id_view_fn'), $this->page_slug, 'secure_pay_section');
		add_settings_field('client_secret', 'Client Secret', array( $this, 'client_secret_view_fn'), $this->page_slug, 'secure_pay_section');
		add_settings_field('test_mode', 'Test Mode', array( $this, 'test_mode_view_fn'), $this->page_slug, 'secure_pay_section');

		add_settings_section('general_section', 'General Setting', array($this, 'general_section_text_fn'), $this->page_slug);
		add_settings_field('page_url', 'Checkout Page URL', array( $this, 'checkout_page_view_fn'), $this->page_slug, 'general_section');
		add_settings_field('thankyou_url', 'Thankyou Page URL', array( $this, 'thankyou_page_view_fn'), $this->page_slug, 'general_section');

		add_settings_section('email_section', 'Email Setting', array($this, 'email_section_text_fn'), $this->page_slug);
		add_settings_field('donor_subject', 'Donor Subject', array( $this, 'donor_subject_view_fn'), $this->page_slug, 'email_section');
		add_settings_field('donor_message', 'Donor Message', array( $this, 'donor_message_view_fn'), $this->page_slug, 'email_section');
		add_settings_field('admin_email', 'Admin Email', array( $this, 'admin_email_view_fn'), $this->page_slug, 'email_section');
		add_settings_field('admin_subject', 'Admin Subject', array( $this, 'admin_subject_view_fn'), $this->page_slug, 'email_section');
		add_settings_field('admin_message', 'admin Message', array( $this, 'admin_message_view_fn'), $this->page_slug, 'email_section');

    }

	/*
	* Secure Pay Setting
	*/
	public function secure_pay_section_text_fn() {
		echo '<p>Australia Post SecurePay Payment Gateway.</p>';		
	}

	public function marchant_code_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('marchant_code', $get_options )){
			$value = $get_options['marchant_code'];
		}
		echo "<input id='marchant_code' class='large' name='cmm_donation_setting[marchant_code]' type='text' value='{$value}' />";
	}

	public function client_id_view_fn() {
		
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('client_id', $get_options)){
			$value = $get_options['client_id'];
		}
		echo "<input id='client_id' class='large' name='cmm_donation_setting[client_id]' type='text' value='{$value}' />";
	}

	public function client_secret_view_fn() {
		
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('client_secret', $get_options)){
			$value = $get_options['client_secret'];
		}
		echo "<input id='client_secret' class='large' name='cmm_donation_setting[client_secret]' type='text' value='{$value}' />";
	}

	function test_mode_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$checked = '';
		if($get_options && array_key_exists('test_mode', $get_options) && $get_options['test_mode']) { 
			$checked = ' checked="checked" '; 
		}
		echo "<input ".$checked." id='test_mode' name='cmm_donation_setting[test_mode]' type='checkbox' /> Enable Test Mode";
	}
	
	
	public function cmm_donation_admin_setting_field_validate($input) {
		// Check our textbox option field contains no HTML tags - if so strip them out
		$input['marchant_code'] =  wp_filter_nohtml_kses($input['marchant_code']);	
		$input['client_id'] =  wp_filter_nohtml_kses($input['client_id']);	

		return $input; // return validated input
	}
	
	/*
	* General setting
	*/
	public function general_section_text_fn() {
		echo '<p>Use <strong>[cmm-donation-checkout-form]</strong> on the checkout page</p>';
	}
	public function checkout_page_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('page_url', $get_options )){
			$value = $get_options['page_url'];
		}
		echo "<input id='page_url' class='large' name='cmm_donation_setting[page_url]' type='text' value='{$value}' />";
	}
	public function thankyou_page_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('thankyou_url', $get_options )){
			$value = $get_options['thankyou_url'];
		}
		echo "<input id='thankyou_url' class='large' name='cmm_donation_setting[thankyou_url]' type='text' value='{$value}' />";
	}

	/*
	* Email setting
	*/
	public function email_section_text_fn() {
		echo '<p>Use below shortcodes to render dynamic data on Email Template:</p>';
		echo '<p>Donation Amount: [[donation-amount]]</p>';
		echo '<p>Donation Type: [[donation-type]]</p>';
		echo '<p>Donor Email: [[donor-email]]</p>';
		echo '<p>Donor Firstname: [[donor-firstname]]</p>';
		echo '<p>Donor Lastname: [[donor-lastname]]</p>';
	}
	public function donor_subject_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('donor_subject', $get_options )){
			$value = $get_options['donor_subject'];
		}
		echo "<input id='donor_subject' class='large' name='cmm_donation_setting[donor_subject]' type='text' value='{$value}' />";
	}
	public function donor_message_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('donor_message', $get_options )){
			$value = $get_options['donor_message'];
		}
		echo "<textarea id='donor_message' class='large' name='cmm_donation_setting[donor_message]'>{$value}</textarea>";
	}

	public function admin_email_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('admin_email', $get_options )){
			$value = $get_options['admin_email'];
		}
		echo "<input id='admin_email' class='large' name='cmm_donation_setting[admin_email]' type='text' value='{$value}' />";
	}

	public function admin_subject_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('admin_subject', $get_options )){
			$value = $get_options['admin_subject'];
		}
		echo "<input id='admin_subject' class='large' name='cmm_donation_setting[admin_subject]' type='text' value='{$value}' />";
	}

	public function admin_message_view_fn() {
		$get_options = get_option('cmm_donation_setting');
		$value = '';
		if($get_options && array_key_exists('admin_message', $get_options )){
			$value = $get_options['admin_message'];
		}
		echo "<textarea id='admin_message' class='large' name='cmm_donation_setting[admin_message]'>{$value}</textarea>";
	}

}
