<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mno.xyz
 * @since      1.0.0
 *
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/public
 * @author     Hemant Lama <hemantlama55@gmail.com>
 */
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Cmm_Donation_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cmm-donation-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$get_options = get_option('cmm_donation_setting');
		if($get_options && array_key_exists('test_mode', $get_options) && $get_options['test_mode']) { 
			wp_enqueue_script( 'securepay-ui-js', 'https://payments-stest.npe.auspost.zone/v3/ui/client/securepay-ui.min.js', array(), $this->version, false );
		} else{
			wp_enqueue_script( 'securepay-ui-js', 'https://payments.auspost.net.au/v3/ui/client/securepay-ui.min.js', array(), $this->version, false );
		}

		wp_enqueue_script( $this->plugin_name.'-jquery-validation', 'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js', array('jquery'), $this->version, false );

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cmm-donation-public.js', array( 'jquery' ), $this->version, false );

	}

	public function cmm_donation_shortcode_function( $atts ){
		$id = $atts['id'];
		$get_options = get_option('cmm_donation_setting');
		$checkout_page = '';
		if($get_options && array_key_exists('page_url', $get_options )){
			$checkout_page = $get_options['page_url'];
		}

		$html = '';
		$html .= '<div class="cmm-donation-wrap">
			<div class="cmm-donation-content cmm-donation-left cmm-donation-recurring-wrap">
				' . $this->get_recurring_donation_block($id, $checkout_page) . '
			</div>
			<div class="cmm-donation-content cmm-donation-right cmm-donation-single-wrap">
			' . $this->get_single_donation_block($id, $checkout_page) . '
			</div>
			
		</div>';
		return $html;
	}

	public function get_recurring_donation_block($id, $checkout_page){

		$html = '';
		$recurring_title = get_post_meta( $id, '_cmm_donation_recurring_title', true );
		$recurring_sub_title = get_post_meta( $id, '_cmm_donation_recurring_sub_title', true );
		$recurring_heading = get_post_meta( $id, '_cmm_donation_recurring_heading', true );
		$recurring_other_amt = get_post_meta( $id, '_cmm_donation_recurring_other_amt', true );
		$recurring_other_amt_text = get_post_meta( $id, '_cmm_donation_recurring_other_amt_text', true );
		$recurring_other_amt_desc = get_post_meta( $id, '_cmm_donation_recurring_other_amt_desc', true );
		$recurring_frequency = get_post_meta( $id, '_cmm_donation_recurring_frequency', true );
		$recurring_button = get_post_meta( $id, '_cmm_donation_recurring_button', true );
		$recurring_layout = get_post_meta( $id, '_cmm_donation_recurring_layout', true );
		$recurring_amount = get_post_meta( $id, '_cmm_donation_recurring_amount', true );

		if( $recurring_title ){
			$recurring_title = '<div class="donation-title">'. $recurring_title .'</div>';
		}
		if( $recurring_sub_title ){
			$recurring_sub_title = '<div class="donation-sub-title">'. $recurring_sub_title .'</div>';
		}
		if( $recurring_heading ){
			$recurring_heading = '<div class="donation-heading">'. $recurring_heading .'</div>';
		}
		$html .='<form action="'. $checkout_page .'" method="post">
			'.
			$recurring_title . 
			$recurring_sub_title . 
			$recurring_heading . 
			$this->get_donation_amount('recurring', $recurring_layout, $recurring_amount, $recurring_other_amt, $recurring_other_amt_text, $recurring_other_amt_desc )
			.'
			<div class="donation-btn-wrap">
				<input type="hidden" name="campaign-id" value="'. $id .'" />
				<input type="hidden" name="frequency" value="'. $recurring_frequency .'" />
				<input type="hidden" name="donation-type" value="recurring" />
				<button type="submit" value="Submit" onclick="fbq("track", "Donate");">'. $recurring_button .'</button>
			</div>
		</form>';
		return $html;
	}

	public function get_single_donation_block($id, $checkout_page){

		$html = '';
		$single_title = get_post_meta( $id, '_cmm_donation_single_title', true );
		$single_sub_title = get_post_meta( $id, '_cmm_donation_single_sub_title', true );
		$single_heading = get_post_meta( $id, '_cmm_donation_single_heading', true );
		$single_other_amt = get_post_meta( $id, '_cmm_donation_single_other_amt', true );
		$single_other_amt_text = get_post_meta( $id, '_cmm_donation_single_other_amt_text', true );
		$single_other_amt_desc = get_post_meta( $id, '_cmm_donation_single_other_amt_desc', true );
		$single_button = get_post_meta( $id, '_cmm_donation_single_button', true );
		$single_layout = get_post_meta( $id, '_cmm_donation_single_layout', true );
		$single_amount = get_post_meta( $id, '_cmm_donation_single_amount', true );

		if( $single_title ){
			$single_title = '<div class="donation-title">'. $single_title .'</div>';
		}
		if( $single_sub_title ){
			$single_sub_title = '<div class="donation-sub-title">'. $single_sub_title .'</div>';
		}
		if( $single_heading ){
			$single_heading = '<div class="donation-heading">'. $single_heading .'</div>';
		}
		$html .='<form action="'. $checkout_page .'" method="post">
			'.
			$single_title . 
			$single_sub_title . 
			$single_heading . 
			$this->get_donation_amount('single',  $single_layout, $single_amount, $single_other_amt, $single_other_amt_text, $single_other_amt_desc )
			.'						
			<div class="donation-btn-wrap">
				<input type="hidden" name="campaign-id" value="'. $id .'" />
				<input type="hidden" name="donation-type" value="single" />
				<button type="submit" value="Submit" onclick="fbq("track", "Donate");">'. $single_button .'</button>
			</div>
		</form>';
		return $html;
	}

	public function get_donation_amount($type, $layout, $amount, $other_amt, $other_amt_text, $other_amt_desc){
		$html = '';
		if( $layout == 'list' ){
			$html = $this->get_donation_amount_list_layout($type, $amount, $other_amt, $other_amt_text, $other_amt_desc);
		} else{			
			$html = $this->get_donation_amount_grid_layout($type, $amount, $other_amt, $other_amt_text, $other_amt_desc);		
		}
		return $html;
	}

	public function get_donation_amount_list_layout($type, $amount, $other_amt, $other_amt_text, $other_amt_desc){
		$html = '';
		$html .= '<div class="donation-amount-wrap type-list">';
		if( $amount ){
			foreach( $amount as $k=>$data ){
				$label = '';
				$desc = '';
				if( $data['label'] ){
					$label = '<div class="donation-item-title">'. $data['label'] .'</div>';
				}
				if( $data['desc'] ){
					$desc = '<div class="donation-item-desc">'. $data['desc'] .'</div>';
				}
				$html .='<div class="donation-item">
					<input type="radio" class="donation-radio" name="amount-'.$type.'" id="amount-'. $type.'-'.$k .'" value="'. $data['amount'] .'" required>
					<label for="amount-'. $type.'-'.$k .'">'. $label . $desc .'</label>					
				</div>';
			}
		}
		if( $other_amt == 1 ){
			$label = '';
			$desc = '';
			if( $other_amt_text ){
				$label = '<div class="donation-item-title">'. $other_amt_text .'</div>';
			}
			if( $other_amt_desc ){
				$desc = '<div class="donation-item-desc">'. $other_amt_desc .'</div>';
			}

			$html .='			
			<div class="donation-item custom-amount">					
				<input type="radio" class="donation-radio" name="amount-'.$type.'" id="other-'.$type.'" value="custom" required>
				<label for="other-'.$type.'">
					'. $label .'
					<div class="custom-amount custom-amount-input-wrap hide">					
						<input type="number" class="donation-custom-amount" name="custom-amount-'.$type.'">
					</div>
					'. $desc .'
				</label>
			</div>';
		}		
		$html .='</div>';
		return $html;
	}

	public function get_donation_amount_grid_layout($type, $amount, $other_amt, $other_amt_text, $other_amt_desc){
		$html = '';
		$html .= '<div class="donation-amount-wrap type-grid">';
		if( $amount ){
			foreach( $amount as $k=>$data ){
				$label = '';
				$desc = '';
				if( $data['label'] ){
					$label = '<div class="donation-item-title">'. $data['label'] .'</div>';
				}
				
				$html .='<div class="donation-item">
					<input type="radio" class="donation-radio" name="amount-'.$type.'" id="amount-'. $type.'-'.$k .'" value="'. $data['amount'] .'" required>
					<label for="amount-'. $type.'-'.$k .'">'. $label .'</label>					
				</div>';
			}
		}
		if( $other_amt == 1 ){
			$label = '';
			$desc = '';
			if( $other_amt_text ){
				$label = '<div class="donation-item-title">'. $other_amt_text .'</div>';
			}
			if( $other_amt_desc ){
				$desc = '<div class="donation-item-desc">'. $other_amt_desc .'</div>';
			}

			$html .='			
			<div class="donation-item custom-amount">					
				<input type="radio" class="donation-radio" name="amount-'.$type.'" id="other-'.$type.'" value="custom" required>
				<label for="other-'.$type.'">
					'. $label .'
				</label>
			</div>';
		}		
		$html .='</div>';
		if($other_amt == 1) {
			$html .='			
			<div class="custom-amount custom-amount-input-wrap hide">					
				<input type="number" class="donation-custom-amount" name="custom-amount-'.$type.'">
			</div>';
		}

		return $html;
	}

	public function validate_cmm_donation(){
		$error_data = array(
			'status' => 'invalid',
			'msg'	=> '<div class="donation-invalid"><p>Invalid form submission. <a href="javascript:history.back()">Go Back</a> </p></div>'
		);
		
		if( $_SERVER['REQUEST_METHOD'] != 'POST' && empty( $_POST )){
			$error_data['type'] = 'post';
			return $error_data;
		}

		$valid_frequency = array('WEEKLY', 'FORTNIGHTLY', 'MONTHLY', 'QUARTERLY', 'HALF_YEARLY', 'ANNUALLY');
		$frequency = '';

		$id = (array_key_exists('campaign-id', $_POST) ) ? $_POST['campaign-id'] : '' ;
		$type = (array_key_exists('donation-type', $_POST) ) ? $_POST['donation-type'] : '' ;
		if( $type == 'single' ){
			$amount = (array_key_exists('amount-single', $_POST) ) ? $_POST['amount-single'] : '' ;
			if( $amount == 'custom' ){
				$amount = (array_key_exists('custom-amount-single', $_POST) ) ? $_POST['custom-amount-single'] : '' ;
			}
		} elseif( $type == 'recurring' ){
			$frequency = (array_key_exists('frequency', $_POST) ) ? $_POST['frequency'] : '' ;
			$amount = (array_key_exists('amount-recurring', $_POST) ) ? $_POST['amount-recurring'] : '' ;
			if( $amount == 'custom' ){
				$amount = (array_key_exists('custom-amount-recurring', $_POST) ) ? $_POST['custom-amount-recurring'] : '' ;
			}
		}

		if( get_post_type($id) != 'cmm-donation' ){
			$error_data['type'] = 'id';
			return $error_data;
		}elseif ( ! is_numeric($amount)) {
			$error_data['type'] = 'amount';
			return $error_data;
		}elseif ( $type != 'single' && $type != 'recurring' ) {
			$error_data['type'] = 'type';
			return $error_data;
		}elseif ( $type == 'recurring' && ! in_array($frequency, $valid_frequency) ) {
			$error_data['type'] = 'frequency';
			return $error_data;
		}else{
			$get_options = get_option('cmm_donation_setting');
			$merchantCode = '';
			if($get_options && array_key_exists('marchant_code', $get_options )){
				$merchantCode = $get_options['marchant_code'];
			}
			$clientId = '';
			if($get_options && array_key_exists('client_id', $get_options )){
				$clientId = $get_options['client_id'];
			}
			$data = array(
				'status'	=> 'valid',
				'id'		=> $id,
				'type'		=> $type,
				'amount'	=> $amount,
				'frequency'	=> $frequency,
				'clientId'	=> $clientId,
				'merchantCode'	=> $merchantCode,
			);
			return $data;
		}
		
	}

	public function cmm_donation_checkout_shortcode_function( $atts ){

		$data = $this->validate_cmm_donation();
		if( array_key_exists('status', $data) && $data['status'] == 'invalid'){
			return $data['msg'];
		}
		$type = '';
		if($data['type'] == 'single'){
			$type = '<div class="row">
					<div class="col">
						<label>Donation Type:</label>
						<input type="text" class="form-control" value="One-off" disabled readonly>
					</div>
				</div>';
		} elseif($data['type'] == 'recurring'){
			$type = '<div class="row">
					<div class="col">
						<label>Donation Type:</label>
						<input type="text" class="form-control" value="'.$data['frequency'].' Recurring" disabled readonly>
					</div>
				</div>';
		}
		
		$html = '';
		
		$html = '
			<div class="cmm-donation-checkout-wrap" id="cmm-donation-checkout-'.$data['id'].'">
				<form action="#" id="cmm-donation-checkout-form">
					<div class="donation-details-wrap">
						<h3>Donation Information</h3>
						<div class="donation-details-info">
							<div class="row">
								<div class="col">
									<label>Donation Amount:</label>
									<span class="currency-prefix">$</span><input type="text" class="form-control" name="amount-cmm-donation" value="'.$data['amount'].'" disabled readonly>
								</div>
							</div>'. $type .'
						</div>
					</div>
					<div class="billing-details-wrap">
						<h3>Billing Information</h3>
						<div class="billing-details-fields">						
							<div class="row">
								<div class="col">
									<label for="firstname-cmm-donation">First name <span>*</span></label>
									<input type="text" class="form-control" name="firstname-cmm-donation" id="firstname-cmm-donation" maxlength="100" placeholder="" required>
								</div>
								<div class="col">
									<label for="lastname-cmm-donation">Last name <span>*</span></label>
									<input type="text" class="form-control" name="lastname-cmm-donation" id="lastname-cmm-donation" maxlength="100" placeholder="" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label for="email-cmm-donation">Email <span>*</span></label>
									<input type="email" class="form-control" name="email-cmm-donation" id="email-cmm-donation" placeholder="" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label for="phone-cmm-donation">Phone <span>*</span></label>
									<input type="phone" class="form-control" name="phone-cmm-donation" id="phone-cmm-donation" placeholder="" required>
								</div>
							</div>	
							<div class="row">
								<div class="col">
									<label for="company-cmm-donation">Company name (optional)</label>
									<input type="text" class="form-control" name="company-cmm-donation" id="company-cmm-donation">
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label for="company-abn-cmm-donation">Company ABN <span> (mandatory if you are donating on behalf of a company)</span></label>
									<input type="text" class="form-control" name="company-abn-cmm-donation" id="company-abn-cmm-donation" placeholder="" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label for="country-cmm-donation">Country / Region <span>*</span></label>
									<input type="text" class="form-control" name="country-cmm-donation" id="country-cmm-donation" placeholder="" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label for="address-1-cmm-donation">Street address <span>*</span></label>
									<input type="text" class="form-control" name="address-1-cmm-donation" id="address-1-cmm-donation" placeholder="House number and street name" required>
									<input type="text" class="form-control" name="address-2-cmm-donation" id="address-2-cmm-donation" placeholder="Apartment, suite, unit, etc. (optional)">
								</div>
							</div>
							
							<div class="row">
								<div class="col">
									<label for="suburb-cmm-donation">Suburb <span>*</span></label>
									<input type="text" class="form-control" name="suburb-cmm-donation" id="suburb-cmm-donation" placeholder="" required>
								</div>
							</div>
							<div class="row">
								<div class="col">
									<label for="state-cmm-donation">State <span>*</span></label>
									<input type="text" class="form-control" name="state-cmm-donation" id="state-cmm-donation" placeholder="" required>
								</div>
							</div>
							<div class="row">	
								<div class="col">
									<label for="postcode-cmm-donation">Postcode <span>*</span></label>
									<input type="text" class="form-control" name="postcode-cmm-petition" id="postcode-cmm-petition" maxlength="100" placeholder="" required>
								</div>
							</div>
							<div class="row">	
								<div class="col info-wrap">
									<p>By donating, you may receive communications from Master Builders Australia and understand and agree to our <a href="https://masterbuilders.com.au/privacy-policy/">Privacy Policy</a>, <a href="https://masterbuilders.com.au/terms-and-conditions/">Terms & Conditions</a> and <a href="https://masterbuilders.com.au/legal-disclaimer/">Disclaimer</a>.</p>
								</div>
							</div>
						</div>
					</div>
					
					<div class="card-details-wrap">
						<h3>Card Information</h3>
						<div class="card-details-info">
							<div class="row">
								<div class="col">
									<div id="securepay-ui-container"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col submit-wrap">
							<input type="hidden" class="form-control" id="id" value="'.$data['id'].'">
							<input type="hidden" class="form-control" id="amount" value="'.$data['amount'].'">
							<input type="hidden" class="form-control" id="type" value="'.$data['type'].'">
							<input type="hidden" class="form-control" id="frequency" value="'.$data['frequency'].'">
							<input type="hidden" class="form-control" id="clientId" value="'.$data['clientId'].'">
							<input type="hidden" class="form-control" id="merchantCode" value="'.$data['merchantCode'].'">
							<button type="submit" id="cmm_donation_submit_button" class="btn secondary-btn">
								<span id="cmm-donation-loading" style="display:none;"></span>Donate Now</button>								
						</div>
					</div>
				</form>
				<div class="cmm-donation-message"></div>
			</div>
			';
		return $html;
	}

	public function cmm_donation_process_function(){
		
		global $table_prefix, $wpdb;
        $return = array();

		$billingTable = $table_prefix . 'cmm_donation_billing';
        $transactionTable = $table_prefix . 'cmm_donation_transaction';

        $campaign_id = $_POST['id'];
        $donation_type = $_POST['type'];
        $amount = $_POST['amount'];
        $frequency = $_POST['frequency'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $company = $_POST['company'];
		$abn = $_POST['abn'];
        $country = $_POST['country'];
        $address_1 = $_POST['address_1'];
        $address_2 = $_POST['address_2'];
        $suburb = $_POST['suburb'];
        $state = $_POST['state'];
        $postcode = $_POST['postcode'];

        $tokenisedCard = $_POST['tokenisedCard'];
        
        $date = date('Y-m-d H:i:s');

		$insert_billing = $wpdb->query("INSERT INTO $billingTable(campaign_id, donation_type, amount, frequency, firstname, lastname, email, phone, company, company_abn, country, address_1, address_2, suburb, state, postcode, date ) VALUES('$campaign_id', '$donation_type' , '$amount', '$frequency', '$firstname', '$lastname', '$email', '$phone', '$company', '$abn', '$country', '$address_1', '$address_2', '$suburb', '$state', '$postcode', '$date')"); 

		$insert_id = $wpdb->insert_id;

		if( $insert_billing == 1 ){
			$referenceNumber = 'donation-'. $campaign_id .'-'. $insert_id;

			$donation_return = $this->process_donation($referenceNumber, $tokenisedCard, $donation_type, $amount, $frequency );
			if( $donation_return == 'error' ){
				$msg = array('response' => 'error', 'message' => 'Error while submitting Donation(Gateway Error). Please try again.', 'page' => '');
			}else{

				$get_options = get_option('cmm_donation_setting');
				$thankyou_page = '';
				if($get_options && array_key_exists('thankyou_url', $get_options )){
					$thankyou_page = $get_options['thankyou_url'];
				}

				$billing_id = $insert_id;
				$sp_customerCode = $donation_return->customerCode;
				$sp_referenceNumber = $referenceNumber;
				$sp_token = $donation_return->token;
				$sp_ip = $donation_return->ip;
				$sp_orderId = $donation_return->orderId;
				$sp_bankTransactionId = $donation_return->bankTransactionId;
				$sp_currency = $donation_return->currency;
				$sp_gatewayResponseCode = $donation_return->gatewayResponseCode;
				$sp_gatewayResponseMessage = $donation_return->gatewayResponseMessage;
				$sp_status = $donation_return->status;

				$scheduleId = $donation_return->scheduleId;
				$schedulingDetails_paymentIntervalType = $donation_return->schedulingDetails->paymentIntervalType;
				$schedulingDetails_startDate = $donation_return->schedulingDetails->startDate;

				$insert_transaction = $wpdb->query("INSERT INTO $transactionTable(billing_id, sp_customerCode, sp_referenceNumber, sp_token, sp_ip, sp_orderId, sp_bankTransactionId, sp_currency, sp_gatewayResponseCode, sp_gatewayResponseMessage, sp_status, scheduleId, schedulingDetails_paymentIntervalType, schedulingDetails_startDate, date ) VALUES('$billing_id', '$sp_customerCode' , '$sp_referenceNumber', '$sp_token', '$sp_ip', '$sp_orderId', '$sp_bankTransactionId', '$sp_currency', '$sp_gatewayResponseCode', '$sp_gatewayResponseMessage', '$sp_status', '$scheduleId', '$schedulingDetails_paymentIntervalType', '$schedulingDetails_startDate', '$date')"); 
				
				$msg = array('response' => 'success', 'message' => 'Donation successfull.', 'page' => $thankyou_page, 'billing_id' => $billing_id );

				$invoice = $this->create_invoice($billing_id, $_POST, $date);
				if($invoice){
					$invoice_suffix = explode('wp-content', $invoice);
					$invoice_file_url = home_url().'/wp-content'. $invoice_suffix[1];
				}
				$update = $wpdb->update(
					$billingTable,
					array(
						'invoice_file' => $invoice_file_url,
					),
					array( 'ID' => $billing_id )
				);
		
		
				$this->send_email($amount, $donation_type, $frequency, $email, $firstname, $lastname, $invoice);
			
			}
			 
		} else {
			$msg = array('response' => 'error', 'message' => 'Error while submitting Donation. Please try again.', 'page' => 'error while saving the data to billing table');
		}
        

        echo json_encode($msg);
        exit();
	}

	public function process_donation($referenceNumber, $tokenisedCard, $donation_type, $amount, $frequency ){
		
		$authentication = $this->donation_authentication();
		if($authentication == 'error') {
			return 'error';
		}
		$access_token = $authentication->token_type.' '.$authentication->access_token;

		$get_options = get_option('cmm_donation_setting');
		$merchantCode = '';
		if($get_options && array_key_exists('marchant_code', $get_options )){
			$merchantCode = $get_options['marchant_code'];
		}
		
		$currency = 'AUD';
		$amount = $amount*100;
		
		$ip = $this->get_ip_address();
		
		if($donation_type == 'single'){
			$data = array(
				'merchantCode'	=> $merchantCode,
				'amount'	=> $amount,
				'currency'	=> $currency,
				'token'	=> $tokenisedCard,
				'ip'	=> $ip,
			);

			$method = '/v2/payments';

		}elseif($donation_type == 'recurring'){

			$datetime = new DateTime('tomorrow');
			// $datetime->modify('+1 day');
			$customerCode = explode('-', $referenceNumber);
			$customerCode = $customerCode[1].'Donor'. $customerCode[2];

			$get_instrument_data = $this->create_payment_instrument($access_token, $customerCode, $ip, $tokenisedCard);
			if($get_instrument_data == 'error') {
				return 'error';
			}
			$data = array(
				'ip'	=> $ip,
				'referenceNumber'	=> $referenceNumber,
				'token'	=> $tokenisedCard,
				'merchantCode'	=> $merchantCode,
				'customerCode'	=> $customerCode,
				'amount'	=> $amount,
				'currency'	=> $currency,
				'recurringTransaction'	=> true,
				'scheduleDetails'	=> array(
					'paymentIntervalType'	=> $frequency,
					'startDate'	=> $datetime->format('Y-m-d'),
				),
			);

			$method = '/v2/payments/schedules/recurring';

		}
		$donation_data = $this->submit_donation($access_token, $data, $method);
		return $donation_data;
	}

	public function create_payment_instrument($access_token, $customerCode, $ip, $tokenisedCard){

		$header = array( 
			'Authorization: '. $access_token,
			'Content-Type: application/json',
			'token: '. $tokenisedCard,
			'ip: '. $ip
		);
		$method = '/v2/customers/'.$customerCode.'/payment-instruments/token';

		$url = $this->get_donation_api_url( 'payment' );
		$url = $url.$method;

		$curl_data = $this->donation_curl($url, '', $header );

		return $curl_data;

	}

	public function submit_donation($access_token, $data, $method){
		
		$header = array( 
			'Authorization: '. $access_token,
			'Content-Type: application/json'
		);

		$url = $this->get_donation_api_url( 'payment' );
		$url = $url.$method;

		$curl_data = $this->donation_curl($url, json_encode($data), $header );

		return $curl_data;

	}

	public function donation_authentication(){

		$clientSecret = '';
		$clientId = '';

		$url = $this->get_donation_api_url( 'authentication' );
		$get_options = get_option('cmm_donation_setting');
		
		if($get_options && array_key_exists('client_secret', $get_options )){
			$clientSecret = $get_options['client_secret'];
		}
		if($get_options && array_key_exists('client_id', $get_options )){
			$clientId = $get_options['client_id'];
		}
		
		$data = 'grant_type=client_credentials&audience=https%3A%2F%2Fapi.payments.auspost.com.au&client_id='. $clientId .'&client_secret='. $clientSecret;

		$header = array('Content-Type: application/x-www-form-urlencoded');

		$curl_data = $this->donation_curl($url, $data, $header );
		return $curl_data;
	}

	public function donation_curl($url, $data, $header){
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_HTTPHEADER => $header,
		));

		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		$this->write_log($response);
		
		if( $httpcode == 200 || $httpcode == 201 ){
			return json_decode($response);
		} else{
			return 'error';
		}
	}

	public function write_log($data){
		$file_path = $this->create_log_file();
		
		$content_to_write = date('M d, Y H:i:s').'  '.$data.PHP_EOL;

		file_put_contents("$file_path", $content_to_write, FILE_APPEND);
	}

	public function get_donation_api_url( $type ){
		$data = array();
		$get_options = get_option('cmm_donation_setting');
		if($get_options && array_key_exists('test_mode', $get_options) && $get_options['test_mode']) { 
			$data['authentication'] = 'https://welcome.api2.sandbox.auspost.com.au/oauth/token';
			$data['payment'] = 'https://payments-stest.npe.auspost.zone';
		} else{
			$data['authentication'] = 'https://welcome.api2.auspost.com.au/oauth/token';
			$data['payment'] = 'https://payments.auspost.net.au';
		}
		return $data[$type];
	}

	public function get_ip_address() {  
		//whether ip is from the share internet  
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
			$ip = $_SERVER['HTTP_CLIENT_IP'];  
		}  
		//whether ip is from the proxy  
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
		}  
		//whether ip is from the remote address  
		else{  
			$ip = $_SERVER['REMOTE_ADDR'];  
		}  
		return $ip;  
	} 

	public function send_email($amount, $donation_type, $frequency, $email, $firstname, $lastname, $invoice){

		$amount = number_format((float)$amount , 2, '.', '');

		$data = array(
			'amount' => $amount,
			'donation_type' => $donation_type,
			'frequency' => $frequency,
			'email' => $email,
			'firstname' => $firstname,
			'lastname' => $lastname,
		);
		$get_options = get_option('cmm_donation_setting');

		$donor_subject = '';
		$donor_message = '';
		$admin_email = '';
		$admin_subject = '';
		$admin_message = '';

		if($get_options && array_key_exists('donor_subject', $get_options )){
			$donor_subject = $get_options['donor_subject'];
		}
		if($get_options && array_key_exists('donor_message', $get_options )){
			$donor_message = $get_options['donor_message'];
		}
		if($get_options && array_key_exists('admin_email', $get_options )){
			$admin_email = $get_options['admin_email'];
		}
		if($get_options && array_key_exists('admin_subject', $get_options )){
			$admin_subject = $get_options['admin_subject'];
		}
		if($get_options && array_key_exists('admin_message', $get_options )){
			$admin_message = $get_options['admin_message'];
		}
		
		$to = $email;
		
		$donor_subject = $this->mergeTag_Convert( $donor_subject, $data);
		$donor_message = $this->mergeTag_Convert( $donor_message, $data);

		$admin_email = $this->mergeTag_Convert( $admin_email, $data);
		$admin_subject = $this->mergeTag_Convert( $admin_subject, $data);
		$admin_message = $this->mergeTag_Convert( $admin_message, $data);

		$blog_name = get_bloginfo('name');

		$donor = $this->mailer($to, $donor_subject, $donor_message, $admin_email, $invoice, $firstname);
		$admin = $this->mailer($admin_email, $admin_subject, $admin_message, $admin_email, $invoice, $blog_name);

	}

	public function mailer($to, $subject, $message, $sender_email, $invoice, $to_name){
		$mail = new PHPMailer(true);

		try {
			$blog_name = get_bloginfo('name');
			//Recipients
			$mail->setFrom($sender_email, $blog_name);
			$mail->addAddress($to, $to_name);     //Add a recipient

			//Attachments
			$mail->addAttachment($invoice);         //Add attachments

			//Content
			$mail->isHTML(true);	//Set email format to HTML
			$mail->Subject = $subject;
			$mail->Body    = $message;

			$mail->send();
			return 'Message has been sent';
		} catch (Exception $e) {
			return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
	}
	
	public function mergeTag_Convert($content, $data){

		$finalContent = $content;
		if (stripos($finalContent, "[[donation-amount]]") !== false) {
			$finalContent = str_replace("[[donation-amount]]", '$'.$data['amount'],$finalContent);
		}
		if (stripos($finalContent, "[[donation-type]]") !== false) {
			if($data['donation_type'] == 'single'){
				$finalContent = str_replace("[[donation-type]]",$data['donation_type'],$finalContent);
	
			}elseif($data['donation_type'] == 'recurring'){
				$finalContent = str_replace("[[donation-type]]",$data['donation_type'].'-'.$data['frequency'],$finalContent);
			}
		}
		if (stripos($finalContent, "[[donor-email]]") !== false) {
			$finalContent = str_replace("[[donor-email]]",$data['email'],$finalContent);
		}
		if (stripos($finalContent, "[[donor-firstname]]") !== false) {
			$finalContent = str_replace("[[donor-firstname]]",$data['firstname'],$finalContent);
		}
		if (stripos($finalContent, "[[donor-lastname]]") !== false) {
			$finalContent = str_replace("[[donor-lastname]]",$data['lastname'],$finalContent);
		}			
		return $finalContent;
	}

	public function create_invoice($billing_id, $data, $date){

		$pdf_content = $this->get_pdf_content($billing_id, $data, $date);

		$upload_dir = wp_upload_dir();

		$invoice_name = 'donation-invoice-'. $billing_id.'-'. strtotime($date);
		$invoice_file = $upload_dir['path'].'/'. $invoice_name .'.pdf';

		$dompdf = new Dompdf();
		$dompdf->set_option('defaultMediaType', 'all');
		$dompdf->set_option('isFontSubsettingEnabled', true);
		$dompdf->loadHtml($pdf_content);

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		// $dompdf->stream();
		$output = $dompdf->output();
		
    	file_put_contents($invoice_file, $output);
		return $invoice_file;
	}

	public function get_pdf_content($billing_id, $data, $date){

		$type = '';
		$address = $this->get_formatted_address($data);

		if($data['type'] == 'single'){
			$type = 'One-off';
		}elseif($data['type'] == 'recurring'){
			$type = 'Recurring / '. $data['frequency'];
		}

		$amount = number_format((float)$data['amount'] , 2, '.', '');
		
		$pdf_content = '<!DOCTYPE html>
		<html lang="en-US" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://ogp.me/ns/fb#">
		<head>
		  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		  <style>
			@page { margin: 0in; background-color: #f7f7f7; }
		  </style>
		</head>
		<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color: #f7f7f7; padding: 30px; text-align: center;" bgcolor="#f7f7f7">
			<table width="100%" id="outer_wrapper" style="background-color: #f7f7f7;" bgcolor="#f7f7f7">
				<tr>
					<td>
						<!-- Deliberately empty to support consistent sizing and layout across multiple email clients. -->
					</td>
					<td>
						<div id="wrapper" dir="ltr" style="margin: 0 auto; width: 100%;" width="100%">
							<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
								<tr>
									<td align="center" valign="top">
										<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #fff; border: 1px solid #dedede;" bgcolor="#fff">
											<tr>
												<td align="center" valign="top">
													<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #243780; color: #fff;" bgcolor="#243780">
														<tr>
															<td id="header_wrapper" style="padding: 26px 48px; display: block;">
																<h1 style="font-family: Helvetica,Roboto,Arial,sans-serif; font-size: 30px; text-align: left; color: #fff;">Invoice for donation #'. $billing_id .'</h1>
															</td>
														</tr>
													</table>
												</td>
											</tr>
											<tr>
												<td align="center" valign="top">
													<!-- Body -->
													<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
														<tr>
															<td valign="top" id="body_content" style="background-color: #fff;" bgcolor="#fff">
																<!-- Content -->
																<table border="0" cellpadding="20" cellspacing="0" width="100%">
																	<tr>
																		<td valign="top" style="padding: 48px 48px 32px;">
																			<div id="body_content_inner" style="color: #636363; font-family: Helvetica,Roboto,Arial,sans-serif; font-size: 14px; text-align: left;" align="left">
		
																				<p style="margin: 0 0 16px;">Hi '. $data['firstname'] .',</p>
		
																				<p style="margin: 0 0 16px;">
																					Here are the details of your donation placed on '. date('F d, Y', strtotime($date)) .': </p>
																					<h2 style="color: #243780; display: block; font-family: Helvetica,Roboto,Arial,sans-serif; font-size: 18px; font-weight: bold; margin: 0 0 18px; text-align: left;">TAX INVOICE</h2>
																					<p>Master Builders Australia - ABN:68 137 130 182</p>
																					<p>Level 3, 44 Sydney Avenue</p>
																					<p>FORREST, ACT, 2603</p>
		
																				<h2 style="color: #243780; display: block; font-family: Helvetica,Roboto,Arial,sans-serif; font-size: 18px; font-weight: bold; margin: 0 0 18px; text-align: left;">
																				
																					[Donation #'. $billing_id .'] ('. date('F d, Y', strtotime($date)) .')</h2>
		
																				<div style="margin-bottom: 40px;">
																					<table class="td" cellspacing="0" cellpadding="6" border="1" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; width: 100%; font-family: 
																					 Helvetica, Roboto, Arial, sans-serif;" width="100%">
																						<thead>
																							<tr>
																								<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;" align="left">Campaign</th>
																								<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;" align="left">Type/Frequency</th>
																								<th class="td" scope="col" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;" align="left">Amount</th>
																							</tr>
																						</thead>
																						<tbody>
																							<tr class="order_item">
																								<td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 
																								 Helvetica, Roboto, Arial, sans-serif; word-wrap: break-word;" align="left"> '. get_the_title($data['id']) .' </td>
																								<td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 
																								 Helvetica, Roboto, Arial, sans-serif;" align="left">'. $type .' </td>
																								<td class="td" style="color: #636363; border: 1px solid #e5e5e5; padding: 12px; text-align: left; vertical-align: middle; font-family: 
																								 Helvetica, Roboto, Arial, sans-serif;" align="left">
																									<span class="amount"><span">$</span>'. $amount .'</span>
																								</td>
																							</tr>
		
																						</tbody>
																						<tfoot>
																							<tr>
																								<th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left; border-top-width: 4px;" align="left">Subtotal:</th>
																								<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left; border-top-width: 4px;" align="left"><span class="amount"><span>$</span>'. $amount .'</span>
																								</td>
																							</tr>
																						   
																							<tr>
																								<th class="td" scope="row" colspan="2" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;" align="left">Total:</th>
																								<td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px; text-align: left;" align="left">
																								<span class="amount">
																								<span>$</span>'. $amount .'</span>
																								</td>
																							</tr>
																						</tfoot>
																					</table>
																				</div>
		
																				<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding: 0;" width="100%">
																					<tr>
																						<td valign="top" width="50%" style="text-align: left; font-family: 
																						 Helvetica, Roboto, Arial, sans-serif; border: 0; padding: 0;" align="left">
																							<h2 style="color: #243780; display: block; font-family: Helvetica,Roboto,Arial,sans-serif; font-size: 18px; font-weight: bold; line-height: 130%; margin: 0 0 18px; text-align: left;">
																							Billing address</h2>
		
																							<address class="address" style="padding: 12px; color: #636363; border: 1px solid #e5e5e5;">
																							'. $address .'
																							</address>
																						</td>
																					</tr>
																				</table>
																				<p style="margin: 0 0 16px;">Thanks for donating at '. get_bloginfo('name') .'!</p>
																			</div>
																		</td>
																	</tr>
																</table>
																<!-- End Content -->
															</td>
														</tr>
													</table>
													<!-- End Body -->
												</td>
											</tr>
										</table>
									</td>
								</tr>
		
							</table>
						</div>
					</td>
					<td>
					 
					</td>
				</tr>
			</table>
		</body>
		
		</html>';

		return $pdf_content;

	}

	public function get_formatted_address($data){
		$address = '';
		if( $data['firstname'] ){
			$address .= $data['firstname'];
		}
		if( $data['lastname'] ){
			$address .= ' '.$data['lastname'];
		}
		if( $data['company'] ){
			$address .= '<br>'.$data['company'];
		}
		if( $data['address_1'] ){
			$address .= '<br>'.$data['address_1'];
		}
		if( $data['address_2'] ){
			$address .= '<br>'.$data['address_2'];
		}
		if( $data['city'] ){
			$address .= '<br>'.$data['city'];
		}
		if( $data['suburb'] ){
			$address .= ' '.$data['suburb'];
		}
		if( $data['state'] ){
			$address .= ' '.$data['state'];
		}
		if( $data['postcode'] ){
			$address .= ' '.$data['postcode'];
		}
		if( $data['country'] ){
			$address .= ' '.$data['country'];
		}
		if( $data['phone'] ){
			$address .= '<br><a href="tel:'. $data['phone'] .'" style="color: #7f54b3; font-weight: normal; text-decoration: underline;">'. $data['phone'] .'<a>';
		}
		if( $data['email'] ){
			$address .= '<br>'.$data['email'];
		}
		return $address;
	}

	public function create_log_file(){
		
		$upload_dir = wp_upload_dir();

		$log_dir = $upload_dir['basedir'].'/cmm_donation_log';

		$file_path = $log_dir . '/log.txt';
		if( is_dir($log_dir) === false )
		{
			mkdir($log_dir);
		}
		if ( ! file_exists($file_path)) {

			$file = fopen($file_path,"w");
			
			include $file_path;
		}

		return $file_path;
		
	}

}
