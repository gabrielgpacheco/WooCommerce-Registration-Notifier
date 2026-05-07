<?php
/**
 * Test Email Handler
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier\Admin;

use WC_Reg_Notifier\Interfaces\Hookable;
use WC_Reg_Notifier\Email\Mailer;
use WC_Reg_Notifier\Email\Template;

/**
 * Handles SMTP test email functionality.
 *
 * @since 4.0.0
 */
class TestEmail implements Hookable {

	/**
	 * Register WordPress hooks for this component.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_post_wc_reg_notifier_test_email', array( $this, 'handle_test_email' ) );
	}

	/**
	 * Handle test email submission.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function handle_test_email() {
		// Verify capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Acesso negado!', 'woocommerce-notificacao-de-registro' ) );
		}

		// Verify nonce.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verification doesn't require sanitization.
		if ( ! isset( $_POST['wc_reg_notifier_nonce'] ) || ! wp_verify_nonce( $_POST['wc_reg_notifier_nonce'], 'wc_reg_notifier_test_email' ) ) {
			wp_die( esc_html__( 'Verificação de segurança falhou!', 'woocommerce-notificacao-de-registro' ) );
		}

		// Get and sanitize email.
		$test_email = isset( $_POST['test_email'] ) ? sanitize_email( $_POST['test_email'] ) : '';

		if ( empty( $test_email ) ) {
			add_settings_error(
				'wc_reg_notifier_messages',
				'wc_reg_notifier_message',
				__( 'E-mail de teste não fornecido!', 'woocommerce-notificacao-de-registro' ),
				'error'
			);
		} else {
			// Build test email.
			$subject = sprintf(
				/* translators: %s is the site name */
				__( 'Teste de configuração SMTP - %s', 'woocommerce-notificacao-de-registro' ),
				get_bloginfo( 'name' )
			);
			$message = Template::build_test_email();

			// Send test email.
			$sent = Mailer::send( $test_email, $subject, $message );

			if ( $sent ) {
				add_settings_error(
					'wc_reg_notifier_messages',
					'wc_reg_notifier_message',
					sprintf(
						/* translators: %s is the email address */
						__( 'E-mail de teste enviado com sucesso para %s', 'woocommerce-notificacao-de-registro' ),
						esc_html( $test_email )
					),
					'success'
				);
			} else {
				add_settings_error(
					'wc_reg_notifier_messages',
					'wc_reg_notifier_message',
					__( 'Falha ao enviar e-mail de teste. Verifique os logs de erro.', 'woocommerce-notificacao-de-registro' ),
					'error'
				);
			}
		}

		// Store errors in transient for display on redirect.
		set_transient( 'settings_errors', get_settings_errors(), 30 );

		// Redirect back to settings page.
		wp_redirect( esc_url_raw( admin_url( 'options-general.php?page=wc-reg-notifier&settings-updated=1' ) ) );
		exit;
	}
}
