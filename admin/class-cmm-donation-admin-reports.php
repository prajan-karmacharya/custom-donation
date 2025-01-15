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
class Cmm_Donation_Admin_Repots
{
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

    private $screen;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->screen = 'cmm-donation_page_cmm-donation-reports';

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        $currentScreen = get_current_screen();
        if($currentScreen->id == $this->screen) {

            wp_enqueue_style($this->plugin_name . '-datatable', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css', array(), $this->version, 'all');

            wp_enqueue_style($this->plugin_name . '-bootstrap-datatable', 'https://cdn.datatables.net/1.13.3/css/dataTables.bootstrap5.min.css', array(), $this->version, 'all');

            wp_enqueue_style($this->plugin_name . '-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css', array(), $this->version, 'all');

            wp_enqueue_style($this->plugin_name . '-daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', array(), $this->version, 'all');

        }

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        $currentScreen = get_current_screen();
        if($currentScreen->id == $this->screen) {

            wp_enqueue_script($this->plugin_name . '-datatable-js', 'https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false);

            wp_enqueue_script($this->plugin_name .'-bootstrap-datatable-js', 'https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js', array( 'jquery' ), $this->version, false);

            wp_enqueue_script($this->plugin_name .'-validation-js', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js', array( 'jquery' ), $this->version, false);

            wp_enqueue_script($this->plugin_name .'-moment-js', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array( 'jquery' ), $this->version, false);

            wp_enqueue_script($this->plugin_name .'-daterangepicker-js', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array( 'jquery' ), $this->version, false);

        }

    }

    public function cmm_donation_add_report_page()
    {
        add_submenu_page(
            'edit.php?post_type=cmm-donation',
            'CMM Donation', //page title
            'Reports', //menu title
            'edit_themes', //capability,
            'cmm-donation-reports',//menu slug
            array( $this, 'cmm_donation_reports_page' ) //callback function
        );
    }

    public function cmm_donation_reports_page()
    {
        $current_url = $_SERVER['REQUEST_URI']; 
        if( isset($_GET['campaign']) ){
            $id = $_GET['campaign'];
            $this->cmm_donation_campaign_detail_list($id);
        } else{
        ?>
		<div class="wrap cmm-donation-report-page-wrap">
			<h1>Donation Reports</h1>
			<div class="spinner-border" role="status">
				<span class="sr-only">Loading...</span>
			</div>
			<p></p>
			<?php
            $get_all_campaigns = $this->get_all_campaigns();
            if($get_all_campaigns) {
            ?>
                <div class="alert alert-danger" role="alert" style="display:none;"></div>
                <table id="cmm-donation-reports" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Campaign ID</th>    
                            <th>Campaign Name</th>
                            <th>Total Collected Amount</th>
                            <th>Total Donor</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($get_all_campaigns as $campaign) {
                            $indv_donation_page = home_url() . '' . $current_url . '&campaign=' . $campaign->ID;
                            ?>
                        <tr data-id="<?php echo $campaign->ID; ?>">
                            <td><?php echo $campaign->ID; ?></td>
                            <td><a href="<?php echo $indv_donation_page;?>"><?php echo stripslashes($campaign->post_title); ?></a></td>
                            <td><?php echo '$' . $this->get_total_donation_amount_by_campaign($campaign->ID); ?></td> 
                            <td><?php echo $this->get_total_donor_by_campaign($campaign->ID); ?></td>                        
                            <td><a href="#" class="badge badge-primary all-export-cmm-donation">Export</a> <a href="#" class="badge badge-warning date-filter-cmm-donation">Date filters</a></td>
                        </tr>
                        <?php } ?>                                
                    </tbody>                
                </table>
            <?php
            } else {
            ?>
                <div class="alert alert-danger" role="alert">
                    Campaign not found. <a href="edit.php?post_type=cmm-donation" class="alert-link">Please create Campaign</a>. 
                </div>
            <?php
            }
            ?>
		</div>
		<?php
        }
        $plugin_admin = new Cmm_Donation_Admin( $this->plugin_name, $this->version );
        $plugin_admin->add_daily_schedules_function();
    }

    public function cmm_donation_campaign_detail_list($id){
        ?>
            <div class="wrap cmm-donation-report-page-wrap">
                <h1>Donation Reports -  Campaign: <?php echo get_the_title($id);?></h1>
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p></p>
                <?php
                $get_campaign_detail = $this->export_campaign_data_by_id_function_main($id);
                // echo '<pre>';
                // print_r($get_campaign_detail);
                // echo '</pre>';
                // die('11***');
                $current_url = $_SERVER['REQUEST_URI']; 
                
                if($get_campaign_detail) {
                ?>
                    <div class="alert alert-danger" role="alert" style="display:none;"></div>
                    <table id="cmm-donation-reports" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>    
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Donation Amount</th>
                                <th>Donation Frequency</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($get_campaign_detail as $campaign) {
                            $indv_donation_page = home_url() . '' . $current_url . '&campaign=' . $campaign->ID;
                            if($campaign->frequency){
                                $frequency = ucfirst($campaign->frequency);
                            } else{
                                $frequency = 'One off';
                            }
    
                            if($campaign->billing_id == 'billing_id') {
                                $status = 'PAYMENT FAILED';
                            } else{
                                $status = 'Success';
                            }
                            if( $campaign->invoice_file  ){
                                $invoice = $campaign->invoice_file;
                            } else{
                                // $date = date('Y-m-d H:i:s');
                                $date = $campaign->date;
                                $data['id'] = $campaign->ID;
                                $data['type'] = $campaign->donation_type;
                                $data['amount'] = $campaign->amount;
                                $data['firstname'] = $campaign->firstname;
                                $data['lastname'] = $campaign->lastname;
                                $data['company'] = $campaign->company;
                                $data['address_1'] = $campaign->address_1;
                                $data['address_2'] = $campaign->address_2;
                                $data['city'] = $campaign->city;
                                $data['suburb'] = $campaign->suburb;
                                $data['state'] = $campaign->state;
                                $data['postcode'] = $campaign->postcode;
                                $data['country'] = $campaign->country;
                                $data['phone'] = $campaign->phone;
                                $data['email'] = $campaign->email;
    
                                $plugin_public = new Cmm_Donation_Public( $this->plugin_name, $this->version );
    
                                $create_invoice = $plugin_public->create_invoice($campaign->ID, $data, $date);
                                if($create_invoice){
                                    $invoice_suffix = explode('wp-content', $create_invoice);
                                    $invoice_file_url = home_url().'/wp-content'. $invoice_suffix[1];
                                }
    
                                global $table_prefix, $wpdb;
    
                                $billingTable = $table_prefix . 'cmm_donation_billing';
    
                                $update = $wpdb->update(
                                    $billingTable,
                                    array(
                                        'invoice_file' => $invoice_file_url,
                                    ),
                                    array( 'ID' => $campaign->ID )
                                );
                                $invoice = $invoice_file_url;
                            }
                            ?>
                            <tr data-id="<?php echo $campaign->ID; ?>">
                                <td><?php echo $campaign->ID; ?></td>
                                <td><?php echo $campaign->firstname; ?></td>
                                <td><?php echo $campaign->lastname; ?></td>
                                <td><?php echo $campaign->email; ?></td>
                                <td><?php echo $campaign->phone; ?></td>
                                <td><?php echo '$'.$campaign->amount; ?></td>
                                <td><?php echo ucfirst($frequency); ?></td> 
                                <td><?php echo $status; ?></td> 
                                <td><?php echo  date('d M Y', strtotime($campaign->date) ); ?></td>                        
                                <td><a href="<?php echo $invoice;?>" target="_blank" class="badge badge-primary">Download invoice</a> </td>
                            </tr>
                            <?php } ?>                                
                        </tbody>                
                    </table>
                <?php
                } else {
                    ?>
                        <div class="alert alert-danger" role="alert">
                            Campaign not found. <a href="edit.php?post_type=cmm-donation" class="alert-link">Please create Campaign</a>. 
                        </div>
                    <?php
                }
                ?>
            </div>
        <?php
        }

    public function get_all_campaigns()
    {
        $args = array(
            'post_type' => 'cmm-donation',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        $the_query = new WP_Query($args);
        return $the_query->posts;
    }

    public function get_total_donation_amount_by_campaign($id)
    {

        global $table_prefix, $wpdb;

        $total_amount = 0;

        $billingTable = $table_prefix . 'cmm_donation_billing';
        $transactionTable = $table_prefix . 'cmm_donation_transaction';

        $get_data = $wpdb->get_results(" SELECT SUM(t1.`amount`) as total_amount FROM $billingTable t1 INNER JOIN $transactionTable t2 ON t1.`ID` = t2.billing_id WHERE t1.`campaign_id` = $id; ");

        if($get_data) {
            foreach($get_data as $data) {
                if($data->total_amount) {
                    return $data->total_amount;
                } else {
                    return $total_amount;
                }
            }
        } else {
            return $total_amount;
        }

    }

    public function get_total_donor_by_campaign($id)
    {

        global $table_prefix, $wpdb;

        $total_donor = 0;

        $billingTable = $table_prefix . 'cmm_donation_billing';
        $transactionTable = $table_prefix . 'cmm_donation_transaction';

        $get_data = $wpdb->get_results(" SELECT count(t1.`ID`) as total_donor FROM $billingTable t1 INNER JOIN $transactionTable t2 ON t1.`ID` = t2.billing_id WHERE t1.`campaign_id` = $id; ");

        if($get_data) {
            foreach($get_data as $data) {
                if($data->total_donor) {
                    return $data->total_donor;
                } else {
                    return $total_donor;
                }
            }
        } else {
            return $total_donor;
        }

    }

    public function export_campaign_data_by_id_function()
    {

        $id = $_POST['campaignID'];
        
        $get_export_data = $this->export_campaign_data_by_id_function_main($id);

        $get_export_data = array_reverse($get_export_data);

        if($get_export_data) {
			$return = $this->get_csv_data($get_export_data);
        }

        return cmm_outputCsv($return);

    }

    public function export_campaign_data_by_id_function_main($id ){
        global $table_prefix, $wpdb;

        $billingTable = $table_prefix . 'cmm_donation_billing';
        $transactionTable = $table_prefix . 'cmm_donation_transaction';

        $return = array();

        $get_export_data = $wpdb->get_results(" 
            (
                SELECT * FROM
                    (
                    SELECT t1.`ID`, t1.`campaign_id`, t1.`donation_type`, t1.`amount`, t1.`frequency`, t1.`firstname`, t1.`lastname`, t1.`email`, t1.`phone`, t1.`company`, t1.`country`, t1.`address_1`, t1.`address_2`, t1.`city`, t1.`suburb`, t1.`state`, t1.`postcode`, t1.`date`, t1.`invoice_file`, t2.`billing_id`, t2.`sp_customerCode`, t2.`sp_referenceNumber`, t2.`sp_token`, t2.`sp_ip`, t2.`sp_orderId`, t2.`sp_bankTransactionId`, t2.`sp_currency`, t2.`sp_gatewayResponseCode`, t2.`sp_gatewayResponseMessage`, t2.`sp_status`, t2.`scheduleId`, t2.`schedulingDetails_paymentIntervalType`, t2.`schedulingDetails_startDate`
                    FROM $billingTable t1 INNER JOIN $transactionTable t2 ON t1.`ID` = t2.`billing_id` WHERE t1.`campaign_id` = $id 
                    ) AS A
            )
            UNION
            ( 
                SELECT * FROM (
                    (
                    SELECT t1.`ID`, t1.`campaign_id`, t1.`donation_type`, t1.`amount`, t1.`frequency`, t1.`firstname`, t1.`lastname`, t1.`email`, t1.`phone`, t1.`company`, t1.`country`, t1.`address_1`, t1.`address_2`, t1.`city`, t1.`suburb`, t1.`state`, t1.`postcode`, t1.`date`, t1.`invoice_file`, 'billing_id', 'sp_customerCode', 'sp_referenceNumber', 'sp_token', 'sp_ip', 'sp_orderId', 'sp_bankTransactionId', 'sp_currency', 'sp_gatewayResponseCode', 'sp_gatewayResponseMessage', 'sp_status', 'scheduleId', 'schedulingDetails_paymentIntervalType', 'schedulingDetails_startDate' FROM $billingTable t1
                    WHERE NOT EXISTS( SELECT NULL FROM $transactionTable t2 WHERE t2.billing_id = t1.`ID` ) AND t1.`campaign_id` = $id ORDER BY t1.`ID` DESC
                )
                ) AS B
            ); 
        ");
            // print_r($wpdb);
            // die('*-*');
        return $get_export_data;
   }

	public function export_campaign_data_by_id_date_function()
    {
        $id = $_POST['campaignID'];
		$start_date = $_POST['startDate'];
        $end_date = $_POST['endDate'];

        $return =  $this->export_campaign_data_by_id_date_function_main($id, $start_date, $end_date );
        return cmm_outputCsv($return);

    }

    public function export_campaign_data_by_id_date_function_main($id, $start_date, $end_date ){
        global $table_prefix, $wpdb;

        $billingTable = $table_prefix . 'cmm_donation_billing';
        $transactionTable = $table_prefix . 'cmm_donation_transaction';

        $return = array();
       
        $get_export_data = $wpdb->get_results(" 
			(
				SELECT * FROM
					(
					SELECT t1.`ID`, t1.`campaign_id`, t1.`donation_type`, t1.`amount`, t1.`frequency`, t1.`firstname`, t1.`lastname`, t1.`email`, t1.`phone`, t1.`company`, t1.`country`, t1.`address_1`, t1.`address_2`, t1.`city`, t1.`suburb`, t1.`state`, t1.`postcode`, t1.`date`, t2.`billing_id`, t2.`sp_customerCode`, t2.`sp_referenceNumber`, t2.`sp_token`, t2.`sp_ip`, t2.`sp_orderId`, t2.`sp_bankTransactionId`, t2.`sp_currency`, t2.`sp_gatewayResponseCode`, t2.`sp_gatewayResponseMessage`, t2.`sp_status`, t2.`scheduleId`, t2.`schedulingDetails_paymentIntervalType`, t2.`schedulingDetails_startDate`
					FROM $billingTable t1 INNER JOIN $transactionTable t2 ON t1.`ID` = t2.`billing_id` WHERE t1.`campaign_id` = $id AND (t1.`date` BETWEEN '$start_date' AND '$end_date')
					) AS A
			)
			UNION
			( 
				SELECT * FROM (
					(
					SELECT t1.`ID`, t1.`campaign_id`, t1.`donation_type`, t1.`amount`, t1.`frequency`, t1.`firstname`, t1.`lastname`, t1.`email`, t1.`phone`, t1.`company`, t1.`country`, t1.`address_1`, t1.`address_2`, t1.`city`, t1.`suburb`, t1.`state`, t1.`postcode`, t1.`date`, 'billing_id', 'sp_customerCode', 'sp_referenceNumber', 'sp_token', 'sp_ip', 'sp_orderId', 'sp_bankTransactionId', 'sp_currency', 'sp_gatewayResponseCode', 'sp_gatewayResponseMessage', 'sp_status', 'scheduleId', 'schedulingDetails_paymentIntervalType', 'schedulingDetails_startDate' FROM $billingTable t1
					WHERE NOT EXISTS( SELECT NULL FROM $transactionTable t2 WHERE t2.billing_id = t1.`ID` ) AND t1.`campaign_id` = $id AND (t1.`date` BETWEEN '$start_date' AND '$end_date')
				)
				) AS B
			); 
		");

        $get_export_data = array_reverse($get_export_data);

        if($get_export_data) {
			$return = $this->get_csv_data($get_export_data);
            
        }
        return $return;
    }

	public function get_csv_data($get_export_data){
		$return = array();
		if($get_export_data) {

            foreach($get_export_data as $k=>$data) {
				
				$return[$k]['Campaign ID'] = $data->campaign_id;
				$return[$k]['Donation Type'] = $data->donation_type;
				$return[$k]['Amount'] = $data->amount;
				$return[$k]['Frequency'] = $data->frequency;
				$return[$k]['First name'] = $data->firstname;
				$return[$k]['lastname'] = $data->lastname;
				$return[$k]['Email'] = $data->email;
				$return[$k]['Phone'] = $data->phone;
				$return[$k]['Company'] = $data->company;
				$return[$k]['Country'] = $data->country;
				$return[$k]['Address 1'] = $data->address_1;
				$return[$k]['Address 2'] = $data->address_2;
                $return[$k]['City'] = $data->city;
				$return[$k]['Suburb'] = $data->suburb;
				$return[$k]['State'] = $data->state;
				$return[$k]['Postcode'] = $data->postcode;
				$return[$k]['date'] = date('d M Y', strtotime($data->date) );

				$billing_id = ($data->billing_id != 'billing_id' ) ? $data->billing_id : '' ;
				$sp_customerCode = ($data->sp_customerCode != 'sp_customerCode' ) ? $data->sp_customerCode : '';
				$sp_referenceNumber = ($data->sp_referenceNumber != 'sp_referenceNumber' ) ? $data->sp_referenceNumber : '';
				$sp_token = ($data->sp_token != 'sp_token' ) ? $data->sp_token : '';
				$sp_ip = ($data->sp_ip != 'sp_ip' ) ? $data->sp_ip : '';
				$sp_orderId = ($data->sp_orderId != 'sp_orderId' ) ? $data->sp_orderId : '';
				$sp_bankTransactionId = ($data->sp_bankTransactionId != 'sp_bankTransactionId' ) ? $data->sp_bankTransactionId : '';
				$sp_currency = ($data->sp_currency != 'sp_currency' ) ? $data->sp_currency : '';
				$sp_gatewayResponseCode = ($data->sp_gatewayResponseCode != 'sp_gatewayResponseCode' ) ? $data->sp_gatewayResponseCode : '';
				$sp_gatewayResponseMessage = ($data->sp_gatewayResponseMessage != 'sp_gatewayResponseMessage' ) ? $data->sp_gatewayResponseMessage : '';
				$sp_status = ($data->sp_status != 'sp_status' ) ? $data->sp_status : '';
				$scheduleId = ($data->scheduleId != 'scheduleId' ) ? $data->scheduleId : '';
				$schedulingDetails_paymentIntervalType = ($data->schedulingDetails_paymentIntervalType != 'schedulingDetails_paymentIntervalType' ) ? $data->schedulingDetails_paymentIntervalType : '';
				$schedulingDetails_startDate = ($data->schedulingDetails_startDate != 'schedulingDetails_startDate' ) ? $data->schedulingDetails_startDate : '';

				if($data->billing_id == 'billing_id') {
					$sp_status = 'PAYMENT FAILED';
				}
				$return[$k]['Secure Pay - Status'] = $sp_status;					
				$return[$k]['Billing ID'] = $billing_id;
				$return[$k]['Secure Pay - Customer Code'] = $sp_customerCode;
				$return[$k]['Secure Pay - Reference Number'] = $sp_referenceNumber;
				$return[$k]['Secure Pay - Token'] = $sp_token;
				$return[$k]['Secure Pay - User IP'] = $sp_ip;
				$return[$k]['Secure Pay - Order ID'] = $sp_orderId;
				$return[$k]['Secure Pay - Bank Transaction ID'] = $sp_bankTransactionId;
				$return[$k]['Secure Pay - Currency'] = $sp_currency;
				$return[$k]['Secure Pay - Gateway Response Code'] = $sp_gatewayResponseCode;
				$return[$k]['Secure Pay - Gateway Response Message'] = $sp_gatewayResponseMessage;
				$return[$k]['Secure Pay - Recurring - Schedule ID'] = $scheduleId;
				$return[$k]['Secure Pay - Recurring - Payment Interval Type'] = $schedulingDetails_paymentIntervalType;
				$return[$k]['Secure Pay - Recurring - Start Date'] = $schedulingDetails_startDate;
				
            }

        }
		return $return;
	}

}
