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
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Cmm_Donation_Admin {

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

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cmm-donation-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cmm-donation-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'cmm_donation_ajax', array(
			'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
		) );

	}

	public function cmm_donation_create_post_type(){
		register_post_type('cmm-donation', array(
			'labels' => array(
				'name' => __("CMM Donation"),
				'singular_name' => __('Tool Content Page'),
				'search_items' =>  __('Search Campaign'),
				'all_items' => __('All Campaigns'),
				'edit_item' => __('Edit Campaign'),
				'add_new_item' => __('Add New Campaign'),
			),
			'menu_icon' => 'dashicons-money-alt',
			'public' => false,
			'publicly_queryable' => false,
			'show_in_rest' => true,
			'has_archive' => false,
			'hierarchical' => false,
			'show_ui' => true,
			'supports' => array('title'),
		));
	}

	public function cmm_donation_meta_boxes() {
		add_meta_box( 
			'cmm-donation-meta',
			__( 'Campaign Setting' ),
			array( $this, 'render_cmm_donation_meta_box' ),
			'cmm-donation',
			'advanced',
			'high'
		);
	}
	public function render_cmm_donation_meta_box() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cmm-donation-admin-meta-box-html.php';
	}

	public function cmm_donation_setting_save_metabox( $post_id ) {
		
		if ( ! isset( $_POST['cmm_donation_meta_box_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['cmm_donation_meta_box_nonce'], 'cmm_donation_meta_box' ) ) {
			// return;
		}
	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		if ( isset( $_POST['post_type'] ) && 'cmm-donation' === $_POST['post_type'] ) {
			
			//one-off donation
			$single_title = sanitize_text_field( $_POST['cmm-donation-single-title'] );
			$single_sub_title = sanitize_text_field( $_POST['cmm-donation-single-subtitle'] );
			$single_heading = sanitize_text_field( $_POST['cmm-donation-single-heading'] );
			$single_other_amt = sanitize_text_field( $_POST['cmm-donation-single-other-amt'] );
			$single_other_amt_text = sanitize_text_field( $_POST['cmm-donation-single-other-amt-txt'] );
			$single_other_amt_desc = $_POST['cmm-donation-single-other-amt-desc'];
			$single_button = sanitize_text_field( $_POST['cmm-donation-single-btn-txt'] );
			$single_layout = sanitize_text_field( $_POST['cmm-donation-single-layout'] );
			$single_amount = $_POST['cmm-donation-single-amt'];

			update_post_meta( $post_id, '_cmm_donation_single_title', $single_title );
			update_post_meta( $post_id, '_cmm_donation_single_sub_title', $single_sub_title );
			update_post_meta( $post_id, '_cmm_donation_single_heading', $single_heading );
			update_post_meta( $post_id, '_cmm_donation_single_other_amt', $single_other_amt );
			update_post_meta( $post_id, '_cmm_donation_single_other_amt_text', $single_other_amt_text );
			update_post_meta( $post_id, '_cmm_donation_single_other_amt_desc', $single_other_amt_desc );
			update_post_meta( $post_id, '_cmm_donation_single_button', $single_button );
			update_post_meta( $post_id, '_cmm_donation_single_layout', $single_layout );
			update_post_meta( $post_id, '_cmm_donation_single_amount', $single_amount );

			// recurring donation
			$recurring_title = sanitize_text_field( $_POST['cmm-donation-recurring-title'] );
			$recurring_sub_title = sanitize_text_field( $_POST['cmm-donation-recurring-subtitle'] );
			$recurring_heading = sanitize_text_field( $_POST['cmm-donation-recurring-heading'] );
			$recurring_other_amt = sanitize_text_field( $_POST['cmm-donation-recurring-other-amt'] );
			$recurring_other_amt_text = sanitize_text_field( $_POST['cmm-donation-recurring-other-amt-txt'] );
			$recurring_other_amt_desc = $_POST['cmm-donation-recurring-other-amt-desc'];
			$recurring_frequency = sanitize_text_field( $_POST['cmm-donation-recurring-frequency'] );
			$recurring_button = sanitize_text_field( $_POST['cmm-donation-recurring-btn-txt'] );
			$recurring_layout = sanitize_text_field( $_POST['cmm-donation-recurring-layout'] );
			$recurring_amount = $_POST['cmm-donation-recurring-amt'];

			update_post_meta( $post_id, '_cmm_donation_recurring_title', $recurring_title );
			update_post_meta( $post_id, '_cmm_donation_recurring_sub_title', $recurring_sub_title );
			update_post_meta( $post_id, '_cmm_donation_recurring_heading', $recurring_heading );
			update_post_meta( $post_id, '_cmm_donation_recurring_other_amt', $recurring_other_amt );
			update_post_meta( $post_id, '_cmm_donation_recurring_other_amt_text', $recurring_other_amt_text );
			update_post_meta( $post_id, '_cmm_donation_recurring_other_amt_desc', $recurring_other_amt_desc );
			update_post_meta( $post_id, '_cmm_donation_recurring_frequency', $recurring_frequency );
			update_post_meta( $post_id, '_cmm_donation_recurring_button', $recurring_button );
			update_post_meta( $post_id, '_cmm_donation_recurring_layout', $recurring_layout );
			update_post_meta( $post_id, '_cmm_donation_recurring_amount', $recurring_amount );
	
		}
		
	}

	public function manage_cmm_donation_columns($columns)
    {

        $filtered_columns = array();

        foreach ($columns as $key => $column) {			
			if( $key == 'title' ){
				$filtered_columns[$key] = __('Campaign', 'cmm-donation');
			} else if( $key == 'date' ){
				$filtered_columns['shortcode'] = __('Shortcode', 'cmm-donation');
				$filtered_columns[ $key ] = $column;
			} else{
				$filtered_columns[ $key ] = $column;
			}
		}

        return $filtered_columns;
    }

 
    public function manage_cmm_donation_custom_column($column_name, $post_id )
    {
        switch ($column_name) {
            case 'shortcode':
				?>
				<textarea spellcheck="false" id="cmm-donation-campaign-shortcode-<?php esc_attr_e($post_id); ?>" class="cmm_donation_shortcode_col">[cmm_donation id="<?php esc_attr_e($post_id); ?>"]</textarea>
				<a href="javascript:void(0);" onclick="copyToClip('cmm-donation-campaign-shortcode-<?php esc_attr_e($post_id); ?>')"><span class="dashicons dashicons-admin-page"></span></a>
				<span class="cmm-donation-shortcode-copy-success">Copied!!"</span>
				<?php
			break;
        }
    }


	public function add_daily_schedules( $schedules ) {
		$schedules['everyday'] = array(
				// 'interval'  => 60 * 5,
				'interval'  => 86400,
				'display'   => __( 'Every day')
		);
		return $schedules;
	}
	public function cronstarter_activation() {
		if( !wp_next_scheduled( 'add_daily_schedules' ) ) {  
		   wp_schedule_event( time(), 'everyday', 'add_daily_schedules' );  
		}
	}
	public function add_daily_schedules_function(){

		$plugin_admin_reports = new Cmm_Donation_Admin_Repots( $this->plugin_name, $this->version );
		$plugin_public = new Cmm_Donation_Public( $this->plugin_name, $this->version );

		$get_all_campaigns = $plugin_admin_reports->get_all_campaigns();

		if( $get_all_campaigns ){

			$start_date = date('Y-m-d');

			$datetime = new DateTime($start_date);
			$datetime->modify('+1 day');
			$end_date =  $datetime->format('Y-m-d');

			foreach( $get_all_campaigns as $campaign ){


				$csv_data = $plugin_admin_reports->export_campaign_data_by_id_date_function_main($campaign->ID, $start_date, $end_date );
				if( $csv_data ){

					$file = $this->create_daily_csv_file($csv_data, $campaign->ID, $start_date);
		
					$to_name = 'Masterbuilders Donation';
					$to = 'accounts@masterbuilders.com.au';
					$sender_email = 'enquiries@masterbuilders.com.au';
					$subject = 'Daily Donation Report';
					$message = 'Please find the attachment for the daily donation report';
					

					$mail = new PHPMailer(true);
			
					try {
						$blog_name = get_bloginfo('name');
						//Recipients
						$mail->setFrom($sender_email, $blog_name);
						$mail->addAddress($to, $to_name);     //Add a recipient
			
						//Attachments
						$mail->addAttachment($file);         //Add attachments
			
						//Content
						$mail->isHTML(true);	//Set email format to HTML
						$mail->Subject = $subject;
						$mail->Body    = $message;
			
						$mail->send();
						// return 'Message has been sent';
						$plugin_public->write_log('Cron email sent.  '.$file);
					} catch (Exception $e) {
						// return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
					}
					

				}
			}

		}


	}

	public function create_daily_csv_file($data, $campaign_id, $start_date){

		$upload_dir = wp_upload_dir();
		$file_name = 'cron-'.$campaign_id.'-'. $start_date.'.csv';
		$path =  $upload_dir['path'].'/'. $file_name;
		$fp = fopen( $path, 'w' );
		fputcsv( $fp, array_keys( reset($data) ) );

		foreach ( $data AS $values ){
			fputcsv( $fp, $values );
		}

		fclose( $fp );

		return $path;

	}

}
