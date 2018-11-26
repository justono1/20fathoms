<?php
/**
 * WooCommerce Intuit Payments
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit Payments to newer
 * versions in the future. If you wish to customize WooCommerce Intuit Payments for your
 * needs please refer to http://docs.woothemes.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Intuit;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_3_0 as Framework;

/**
 * The Intuit plugin lifecycle handler.
 *
 * @since 2.3.3
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Handles installation tasks.
	 *
	 * @since 2.3.3
	 */
	protected function install() {

		// handle upgrades from pre v2.0.0 versions, as the plugin ID changed then
		// and the upgrade routine won't be triggered automatically
		if ( $old_version = get_option( 'wc_intuit_qbms_version' ) ) {

			$this->upgrade( $old_version );

		} else {

			update_option( 'wc_intuit_payments_active_integration', \WC_Intuit_Payments::PLUGIN_ID );
		}
	}


	/**
	 * Handles upgrades.
	 *
	 * @since 2.3.3
	 *
	 * @param string $installed_version the currently installed version
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to v2.0.0
		if ( version_compare( $installed_version, '2.0.0', '<' ) ) {

			global $wpdb;

			$this->get_plugin()->log( 'Starting upgrade to v2.0.0' );

			/** Update order payment method meta ******************************/

			$this->get_plugin()->log( 'Starting order meta upgrade.' );

			// meta key: _payment_method
			// old value: intuit_qbms
			// new value: intuit_qbms_credit_card
			$rows = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'intuit_qbms_credit_card' ), array( 'meta_key' => '_payment_method', 'meta_value' => 'intuit_qbms' ) );

			$this->get_plugin()->log( sprintf( '%d orders updated for payment method meta', $rows ) );

			// meta key: _recurring_payment_method
			// old value: intuit_qbms
			// new value: intuit_qbms_credit_card
			$rows = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => 'intuit_qbms_credit_card' ), array( 'meta_key' => '_recurring_payment_method', 'meta_value' => 'intuit_qbms' ) );

			$this->get_plugin()->log( sprintf( '%d orders updated for recurring payment method meta', $rows ) );

			$order_meta_keys = array(
				'trans_id',
				'capture_trans_id',
				'trans_date',
				'txn_authorization_stamp',
				'payment_grouping_code',
				'recon_batch_id',
				'merchant_account_number',
				'client_trans_id',
				'capture_client_trans_id',
				'card_type',
				'card_expiry_date',
				'charge_captured',
				'authorization_code',
				'capture_authorization_code',
				'account_four',
				'payment_token',
				'customer_id',
				'environment',
				'retry_count',
			);

			foreach ( $order_meta_keys as $key ) {

				// old key: _wc_intuit_qbms_*
				// new key: _wc_intuit_qbms_credit_card_*
				$wpdb->update( $wpdb->postmeta, array( 'meta_key' => '_wc_intuit_qbms_credit_card_' . $key ), array( 'meta_key' => '_wc_intuit_qbms_' . $key ) );
			}

			/** Update user token method meta *********************************/

			$this->get_plugin()->log( 'Starting legacy token upgrade.' );

			// old key: _wc_intuit_qbms_payment_tokens_test
			// new key: _wc_intuit_qbms_credit_card_payment_tokens_test
			$rows = $wpdb->update( $wpdb->usermeta, array( 'meta_key' => '_wc_intuit_qbms_credit_card_payment_tokens_test' ), array( 'meta_key' => '_wc_intuit_qbms_payment_tokens_test' ) );

			// old key: _wc_intuit_qbms_payment_tokens
			// new key: _wc_intuit_qbms_credit_card_payment_tokens
			$rows = $wpdb->update( $wpdb->usermeta, array( 'meta_key' => '_wc_intuit_qbms_credit_card_payment_tokens' ), array( 'meta_key' => '_wc_intuit_qbms_payment_tokens' ) );

			/** Update the QBMS settings **************************************/

			if ( $settings = get_option( 'woocommerce_intuit_qbms_settings' ) ) {

				$this->get_plugin()->log( 'Starting legacy settings upgrade.' );

				// update switcher option
				update_option( 'wc_intuit_payments_active_integration', \WC_Intuit_Payments::QBMS_PLUGIN_ID );

				// store the settings under the new option name
				update_option( 'woocommerce_intuit_qbms_credit_card_settings', $settings );

				// remove the old option
				delete_option( 'woocommerce_intuit_qbms_settings' );
			}

			delete_option( 'wc_intuit_qbms_version' );

			$this->get_plugin()->log( 'Completed upgrade for v2.0.0' );
		}

		// upgrade to v2.1.0
		if ( version_compare( $installed_version, '2.1.0', '<' ) ) {

			$this->get_plugin()->log( 'Starting upgrade to v2.1.0' );

			update_option( 'wc_' . \WC_Intuit_Payments::PLUGIN_ID . '_oauth_version', '1.0' );

			$this->get_plugin()->log( 'Completed upgrade for v2.1.0' );
		}

		// upgrade to v2.3.0
		if ( version_compare( $installed_version, '2.3.0', '<' ) ) {

			$this->get_plugin()->log( 'Starting upgrade to v2.3.0' );

			// all of the possible OAuth 1.0 credential option names
			$credential_options = array(
				'wc_intuit_payments_credit_card_oauth_token',
				'wc_intuit_payments_credit_card_sandbox_oauth_token',
				'wc_intuit_payments_credit_card_oauth_token_secret',
				'wc_intuit_payments_credit_card_sandbox_oauth_token_secret',
				'wc_intuit_payments_echeck_oauth_token',
				'wc_intuit_payments_echeck_sandbox_oauth_token',
				'wc_intuit_payments_echeck_oauth_token_secret',
				'wc_intuit_payments_echeck_sandbox_oauth_token_secret',
			);

			foreach ( $credential_options as $option_name ) {

				if ( $value = get_option( $option_name, false ) ) {

					$value = $this->get_plugin()->decrypt_credential_legacy( $value );

					update_option( $option_name, $this->get_plugin()->encrypt_credential( $value ) );
				}
			}

			$this->get_plugin()->log( 'Completed upgrade for v2.3.0' );
		}
	}


}
