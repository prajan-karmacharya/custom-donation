<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://mno.xyz
 * @since      1.0.0
 *
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/includes
 * @author     Hemant Lama <hemantlama55@gmail.com>
 */
class Cmm_Donation {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cmm_Donation_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CMM_DONATION_VERSION' ) ) {
			$this->version = CMM_DONATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cmm-donation';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cmm_Donation_Loader. Orchestrates the hooks of the plugin.
	 * - Cmm_Donation_i18n. Defines internationalization functionality.
	 * - Cmm_Donation_Admin. Defines all hooks for the admin area.
	 * - Cmm_Donation_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cmm-donation-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cmm-donation-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cmm-donation-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the admin setting area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cmm-donation-admin-setting.php';

		/**
		 * The class responsible for defining all actions that occur in the admin report area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cmm-donation-admin-reports.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cmm-donation-public.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

		$this->loader = new Cmm_Donation_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cmm_Donation_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cmm_Donation_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cmm_Donation_Admin( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_setting = new Cmm_Donation_Admin_Setting( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin_reports = new Cmm_Donation_Admin_Repots( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'init', $plugin_admin, 'cmm_donation_create_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin,'cmm_donation_meta_boxes', 10, 2 );
		$this->loader->add_action( 'save_post', $plugin_admin,'cmm_donation_setting_save_metabox' );
		$this->loader->add_filter( 'manage_cmm-donation_posts_columns', $plugin_admin, 'manage_cmm_donation_columns' );
        $this->loader->add_action( 'manage_cmm-donation_posts_custom_column', $plugin_admin, 'manage_cmm_donation_custom_column', 10, 2) ;

		$this->loader->add_action( 'admin_menu', $plugin_admin_setting, 'cmm_donation_add_setting_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin_setting, 'cmm_donation_setting_page_init' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_reports, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_reports, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin_reports, 'cmm_donation_add_report_page');
		$this->loader->add_action( 'wp_ajax_export_campaign_data_by_id', $plugin_admin_reports, 'export_campaign_data_by_id_function' );
		$this->loader->add_action( 'wp_ajax_export_campaign_data_by_id_date', $plugin_admin_reports, 'export_campaign_data_by_id_date_function' );

		//crons functions
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'add_daily_schedules' );
		$this->loader->add_action('wp', $plugin_admin, 'cronstarter_activation');
		$this->loader->add_action( 'add_daily_schedules', $plugin_admin, 'add_daily_schedules_function' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cmm_Donation_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_ajax_cmm_donation_process', $plugin_public, 'cmm_donation_process_function' );
    	$this->loader->add_action( 'wp_ajax_nopriv_cmm_donation_process', $plugin_public, 'cmm_donation_process_function' );
		
		add_shortcode( 'cmm_donation', array( $plugin_public, 'cmm_donation_shortcode_function' ) );
		add_shortcode( 'cmm-donation-checkout-form', array( $plugin_public, 'cmm_donation_checkout_shortcode_function' ) );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cmm_Donation_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
