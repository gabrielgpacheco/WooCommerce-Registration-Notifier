<?php
/**
 * Email Mailer Class
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier\Email;

/**
 * Handles email sending with SMTP configuration and fallback.
 *
 * This class sends emails using PHPMailer with custom SMTP settings
 * or falls back to WordPress's default wp_mail() function.
 *
 * @since 4.0.0
 */
class Mailer {

	/**
	 * Send email with SMTP or fallback to wp_mail.
	 *
	 * @since 4.0.0
	 *
	 * @param string $to      Recipient email address.
	 * @param string $subject Email subject line.
	 * @param string $message HTML email message body.
	 * @param array  $headers Optional. Additional email headers.
	 *
	 * @return bool True if email was sent, false otherwise.
	 */
	public static function send( $to, $subject, $message, $headers = array() ) {
		$options = get_option( 'wc_reg_notifier_settings' );

		// If SMTP not configured, use default wp_mail.
		if ( empty( $options['smtp_host'] ) || empty( $options['smtp_username'] ) ) {
			return self::send_with_wp_mail( $to, $subject, $message, $headers );
		}

		// Attempt to send via SMTP.
		return self::send_with_smtp( $to, $subject, $message, $headers, $options );
	}

	/**
	 * Send email using WordPress's default wp_mail function.
	 *
	 * @since 4.0.0
	 *
	 * @param string $to      Recipient email address.
	 * @param string $subject Email subject line.
	 * @param string $message HTML email message body.
	 * @param array  $headers Optional. Additional email headers.
	 *
	 * @return bool True if email was sent, false otherwise.
	 */
	private static function send_with_wp_mail( $to, $subject, $message, $headers ) {
		if ( ! is_array( $headers ) ) {
			$headers = array();
		}

		// Ensure Content-Type is set for HTML emails.
		if ( ! in_array( 'Content-Type: text/html; charset=UTF-8', $headers, true ) ) {
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
		}

		return wp_mail( $to, $subject, $message, $headers );
	}

	/**
	 * Send email using PHPMailer with custom SMTP settings.
	 *
	 * @since 4.0.0
	 *
	 * @param string $to      Recipient email address.
	 * @param string $subject Email subject line.
	 * @param string $message HTML email message body.
	 * @param array  $headers Optional. Additional email headers.
	 * @param array  $options SMTP configuration options.
	 *
	 * @return bool True if email was sent, false otherwise.
	 */
	private static function send_with_smtp( $to, $subject, $message, $headers, $options ) {
		try {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

			$mail = new \PHPMailer\PHPMailer\PHPMailer( true );

			// Configure SMTP.
			$mail->isSMTP();
			$mail->Host       = sanitize_text_field( $options['smtp_host'] );
			$mail->SMTPAuth   = true;
			$mail->Username   = sanitize_text_field( $options['smtp_username'] );
			$mail->Password   = wp_kses_post( $options['smtp_password'] );
			$mail->SMTPSecure = sanitize_text_field( $options['smtp_secure'] );
			$mail->Port       = intval( $options['smtp_port'] );

			// Configure message.
			$mail->CharSet = 'UTF-8';
			$mail->setFrom(
				sanitize_email( $options['from_email'] ),
				sanitize_text_field( $options['from_name'] )
			);
			$mail->addAddress( sanitize_email( $to ) );
			$mail->isHTML( true );
			$mail->Subject = sanitize_text_field( $subject );
			$mail->Body    = wp_kses_post( $message );
			$mail->AltBody = wp_strip_all_tags( $message );

			if ( ! $mail->send() ) {
				throw new \Exception( $mail->ErrorInfo );
			}

			return true;

		} catch ( \Exception $e ) {
			// Log error and fallback to wp_mail.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( 'WC Reg Notifier SMTP Error: ' . $e->getMessage() );
			return self::send_with_wp_mail( $to, $subject, $message, $headers );
		}
	}
}
