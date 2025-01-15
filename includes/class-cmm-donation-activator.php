<?php

/**
 * Fired during plugin activation
 *
 * @link       https://mno.xyz
 * @since      1.0.0
 *
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cmm_Donation
 * @subpackage Cmm_Donation/includes
 * @author     Hemant Lama <hemantlama55@gmail.com>
 */
class Cmm_Donation_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $table_prefix, $wpdb;
		$sql = "";
		
		$donationBillingTable = $table_prefix . 'cmm_donation_billing';
		// Create Table if not exist
		if( $wpdb->get_var( "show tables like '$donationBillingTable'" ) != $donationBillingTable ) {

			$sql = "CREATE TABLE `$donationBillingTable` (";
			$sql .= " `ID` int(11) NOT NULL auto_increment, ";
			$sql .= " `campaign_id` int(11) NOT NULL, ";
			$sql .= " `donation_type` varchar(255) NOT NULL, ";
			$sql .= " `amount` varchar(255) NOT NULL, ";
			$sql .= " `frequency` varchar(255) NOT NULL, ";
			$sql .= " `firstname` varchar(255) NOT NULL, ";
			$sql .= " `lastname` varchar(255) NOT NULL, ";
			$sql .= " `email` varchar(255) NOT NULL, ";
			$sql .= " `phone` varchar(255) NOT NULL, ";
			$sql .= " `company` varchar(255) NOT NULL, ";
			$sql .= " `company_abn` varchar(255) NOT NULL, ";
			$sql .= " `country` varchar(255) NOT NULL, ";
			$sql .= " `address_1` varchar(255) NOT NULL, ";
			$sql .= " `address_2` varchar(255) NOT NULL, ";
			$sql .= " `city` varchar(255) NOT NULL, ";
			$sql .= " `suburb` varchar(255) NOT NULL, ";
			$sql .= " `state` varchar(255) NOT NULL, ";
			$sql .= " `postcode` varchar(255) NOT NULL, ";
			$sql .= " `date` datetime NOT NULL, ";
			$sql .= " `invoice_file` text NOT NULL, ";
			$sql .= " PRIMARY KEY `ID` (`ID`) ";
			$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
		}

		$donationTransactionTable = $table_prefix . 'cmm_donation_transaction';
		// Create Table if not exist
		if( $wpdb->get_var( "show tables like '$donationTransactionTable'" ) != $donationTransactionTable ) {

			$sql .= "CREATE TABLE `$donationTransactionTable` (";
			$sql .= "`ID` int(11) NOT NULL auto_increment,";
			$sql .= "`billing_id` int(11) NOT NULL,";
			$sql .= " `sp_customerCode` varchar(255) NOT NULL, ";
			$sql .= " `sp_referenceNumber` varchar(255) NOT NULL, ";
			$sql .= " `sp_token` varchar(255) NOT NULL, ";
			$sql .= " `sp_ip` varchar(255) NOT NULL, ";
			$sql .= " `sp_orderId` varchar(255) NOT NULL, ";
			$sql .= " `sp_bankTransactionId` varchar(255) NOT NULL, ";
			$sql .= " `sp_currency` varchar(255) NOT NULL, ";
			$sql .= " `sp_gatewayResponseCode` varchar(255) NOT NULL, ";
			$sql .= " `sp_gatewayResponseMessage` varchar(255) NOT NULL, ";
			$sql .= " `sp_status` varchar(255) NOT NULL, ";

			$sql .= " `scheduleId` varchar(255) NOT NULL, ";
			$sql .= " `schedulingDetails_paymentIntervalType` varchar(255) NOT NULL, ";
			$sql .= " `schedulingDetails_startDate` varchar(255) NOT NULL, ";

			$sql .= "`date` datetime NOT NULL,";
			$sql .= " PRIMARY KEY `ID` (`ID`) ";
			$sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ";

		}

		if( $sql){
			// Include Upgrade Script
			require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

			// Create Table
			dbDelta( $sql );
		}

	}

}
