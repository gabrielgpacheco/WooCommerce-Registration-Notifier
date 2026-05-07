<?php
/**
 * Settings Migration
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier;

/**
 * Migrates old plugin settings format to new OOP format.
 *
 * Handles backwards compatibility by converting old-style
 * flat settings to the new array-based format.
 *
 * @since 4.0.0
 */
class Migration {

	/**
	 * Migrate old settings format to new format.
	 *
	 * This is called on plugin activation to ensure settings
	 * are in the correct format for the new OOP-based plugin.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function migrate() {
		// Check if already migrated.
		if ( get_option( 'wc_reg_notifier_migrated' ) ) {
			return;
		}

		// Get old settings if they exist.
		$old_settings = get_option( 'wc_reg_notifier_settings' );

		// If settings exist and are not already migrated, standardize the format.
		if ( $old_settings && is_array( $old_settings ) ) {
			// Ensure all required keys exist with defaults.
			$migrated_settings = array(
				'notification_email' => isset( $old_settings['notification_email'] ) 
					? sanitize_email( $old_settings['notification_email'] )
					: sanitize_email( get_option( 'admin_email' ) ),
				'from_email'         => isset( $old_settings['from_email'] )
					? sanitize_email( $old_settings['from_email'] )
					: sanitize_email( get_option( 'admin_email' ) ),
				'from_name'          => isset( $old_settings['from_name'] )
					? sanitize_text_field( $old_settings['from_name'] )
					: sanitize_text_field( get_bloginfo( 'name' ) ),
				'smtp_host'          => isset( $old_settings['smtp_host'] )
					? sanitize_text_field( $old_settings['smtp_host'] )
					: '',
				'smtp_port'          => isset( $old_settings['smtp_port'] )
					? intval( $old_settings['smtp_port'] )
					: 587,
				'smtp_secure'        => isset( $old_settings['smtp_secure'] )
					? sanitize_text_field( $old_settings['smtp_secure'] )
					: 'tls',
				'smtp_username'      => isset( $old_settings['smtp_username'] )
					? sanitize_text_field( $old_settings['smtp_username'] )
					: '',
				'smtp_password'      => isset( $old_settings['smtp_password'] )
					? wp_kses_post( $old_settings['smtp_password'] )
					: '',
			);

			// Save migrated settings.
			update_option( 'wc_reg_notifier_settings', $migrated_settings );
		} else {
			// No old settings found, set defaults.
			update_option( 'wc_reg_notifier_settings', self::get_default_settings() );
		}

		// Mark as migrated.
		update_option( 'wc_reg_notifier_migrated', 1 );
	}

	/**
	 * Get default settings.
	 *
	 * @since 4.0.0
	 *
	 * @return array Default settings.
	 */
	private static function get_default_settings() {
		return array(
			'notification_email' => get_option( 'admin_email' ),
			'from_email'         => get_option( 'admin_email' ),
			'from_name'          => get_bloginfo( 'name' ),
			'smtp_host'          => '',
			'smtp_port'          => 587,
			'smtp_secure'        => 'tls',
			'smtp_username'      => '',
			'smtp_password'      => '',
		);
	}
}
