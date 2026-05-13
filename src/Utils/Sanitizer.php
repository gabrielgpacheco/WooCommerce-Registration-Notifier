<?php
/**
 * Sanitizer Utility Class
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || exit;

namespace WC_Reg_Notifier\Utils;

/**
 * Centralized input sanitization based on field type.
 *
 * This class provides consistent sanitization methods for different
 * input types according to WordPress Coding Standards.
 *
 * @since 4.0.0
 */
class Sanitizer {

	/**
	 * Validate and sanitize a phone number.
	 *
	 * Accepts formats like: (99) 99999-9999, 99 99999-9999, 9999999999, etc.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $phone The phone number to sanitize.
	 *
	 * @return string|false Sanitized phone number or false if invalid.
	 */
	public static function sanitize_phone( $phone ) {
		if ( empty( $phone ) ) {
			return false;
		}

		// Remove all non-numeric characters except parentheses, spaces, and hyphens.
		$phone = preg_replace( '/[^\d\(\)\s\-]/', '', $phone );

		// Validate phone format: must match Brazilian phone pattern or be 10-11 digits.
		if ( ! preg_match( '/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/', $phone ) ) {
			return false;
		}

		return sanitize_text_field( $phone );
	}

	/**
	 * Sanitize a name field.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $name The name to sanitize.
	 *
	 * @return string Sanitized name.
	 */
	public static function sanitize_name( $name ) {
		return sanitize_text_field( $name );
	}

	/**
	 * Sanitize an email field.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $email The email to sanitize.
	 *
	 * @return string Sanitized email.
	 */
	public static function sanitize_email_field( $email ) {
		return sanitize_email( $email );
	}

	/**
	 * Sanitize SMTP host.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $host The SMTP host to sanitize.
	 *
	 * @return string Sanitized host.
	 */
	public static function sanitize_smtp_host( $host ) {
		return sanitize_text_field( $host );
	}

	/**
	 * Sanitize SMTP port.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $port The SMTP port to sanitize.
	 *
	 * @return int Sanitized port.
	 */
	public static function sanitize_smtp_port( $port ) {
		$port = intval( $port );
		return ( $port > 0 && $port < 65536 ) ? $port : 587;
	}

	/**
	 * Sanitize SMTP security type (encryption).
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $secure The security type to sanitize.
	 *
	 * @return string Sanitized security type ('', 'tls', or 'ssl').
	 */
	public static function sanitize_smtp_secure( $secure ) {
		$secure = sanitize_text_field( $secure );
		return in_array( $secure, array( '', 'tls', 'ssl' ), true ) ? $secure : '';
	}

	/**
	 * Sanitize SMTP username.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $username The SMTP username to sanitize.
	 *
	 * @return string Sanitized username.
	 */
	public static function sanitize_smtp_username( $username ) {
		return sanitize_text_field( $username );
	}

	/**
	 * Sanitize SMTP password.
	 *
	 * Encodes the password with base64 for safe storage to preserve
	 * special characters.
	 *
	 * @since 5.0.0
	 *
	 * @param mixed $password The SMTP password to sanitize.
	 *
	 * @return string Base64-encoded password.
	 */
	public static function sanitize_smtp_password( $password ) {
		if ( '********' === $password ) {
			$options = get_option( 'wc_reg_notifier_settings', array() );
			return $options['smtp_password'] ?? '';
		}
		return base64_encode( sanitize_text_field( $password ) );
	}

	/**
	 * Sanitize notification email.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $email The notification email to sanitize.
	 *
	 * @return string Sanitized email.
	 */
	public static function sanitize_notification_email( $email ) {
		return sanitize_email( $email );
	}

	/**
	 * Sanitize sender name.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $name The sender name to sanitize.
	 *
	 * @return string Sanitized name.
	 */
	public static function sanitize_from_name( $name ) {
		return sanitize_text_field( $name );
	}

	/**
	 * Sanitize sender email.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $email The sender email to sanitize.
	 *
	 * @return string Sanitized email.
	 */
	public static function sanitize_from_email( $email ) {
		return sanitize_email( $email );
	}

	/**
	 * Sanitize settings array with individual field sanitization.
	 *
	 * @since 4.0.0
	 *
	 * @param array $settings The settings array to sanitize.
	 *
	 * @return array Sanitized settings.
	 */
	public static function sanitize_settings( $settings ) {
		if ( ! is_array( $settings ) ) {
			return array();
		}

		return array(
			'notification_email' => self::sanitize_notification_email( $settings['notification_email'] ?? '' ),
			'from_email'         => self::sanitize_from_email( $settings['from_email'] ?? '' ),
			'from_name'          => self::sanitize_from_name( $settings['from_name'] ?? '' ),
			'smtp_host'          => self::sanitize_smtp_host( $settings['smtp_host'] ?? '' ),
			'smtp_port'          => self::sanitize_smtp_port( $settings['smtp_port'] ?? 587 ),
			'smtp_secure'        => self::sanitize_smtp_secure( $settings['smtp_secure'] ?? '' ),
			'smtp_username'      => self::sanitize_smtp_username( $settings['smtp_username'] ?? '' ),
			'smtp_password'      => self::sanitize_smtp_password( $settings['smtp_password'] ?? '' ),
		);
	}
}
