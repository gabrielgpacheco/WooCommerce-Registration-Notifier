<?php
/**
 * Uninstall routines for WooCommerce Registration Notifier.
 *
 * @package WC_Reg_Notifier
 * @since 5.0.0
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin options.
delete_option( 'wc_reg_notifier_settings' );
delete_option( 'wc_reg_notifier_migrated' );
