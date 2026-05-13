<?php
/**
 * Email Template Builder
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || exit;

namespace WC_Reg_Notifier\Email;

/**
 * Generates HTML email templates for registration notifications.
 *
 * @since 4.0.0
 */
class Template {

	/**
	 * Build HTML email template for new user registration.
	 *
	 * @since 4.0.0
	 *
	 * @param \WP_User $user The registered user object.
	 *
	 * @return string HTML email content.
	 */
	public static function build_registration_email( $user ) {
		$first_name = get_user_meta( $user->ID, 'first_name', true );
		$phone      = get_user_meta( $user->ID, 'billing_phone', true );

		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php esc_attr_e( 'Novo cadastro', 'woocommerce-notificacao-de-registro' ); ?></title>
		</head>
		<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
			<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
				<div style="background-color: #f8f8f8; padding: 20px; text-align: center;">
					<h2><?php esc_html_e( 'Novo usuário registrado', 'woocommerce-notificacao-de-registro' ); ?></h2>
				</div>
				<div style="padding: 20px; background-color: #fff;">
					<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
						<tr>
							<th style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd; background-color: #f8f8f8; width: 30%;">
								<?php esc_html_e( 'Nome Completo:', 'woocommerce-notificacao-de-registro' ); ?>
							</th>
							<td style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd;">
								<?php echo esc_html( $first_name ); ?>
							</td>
						</tr>
						<tr>
							<th style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd; background-color: #f8f8f8; width: 30%;">
								<?php esc_html_e( 'E-mail:', 'woocommerce-notificacao-de-registro' ); ?>
							</th>
							<td style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd;">
								<?php echo esc_html( $user->user_email ); ?>
							</td>
						</tr>
						<tr>
							<th style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd; background-color: #f8f8f8; width: 30%;">
								<?php esc_html_e( 'Telefone:', 'woocommerce-notificacao-de-registro' ); ?>
							</th>
							<td style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd;">
								<?php echo esc_html( $phone ); ?>
							</td>
						</tr>
						<tr>
							<th style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd; background-color: #f8f8f8; width: 30%;">
								<?php esc_html_e( 'Data do Cadastro:', 'woocommerce-notificacao-de-registro' ); ?>
							</th>
							<td style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd;">
								<?php echo esc_html( date_i18n( 'd/m/Y H:i' ) ); ?>
							</td>
						</tr>
						<tr>
							<th style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd; background-color: #f8f8f8; width: 30%;">
								<?php esc_html_e( 'Perfil:', 'woocommerce-notificacao-de-registro' ); ?>
							</th>
							<td style="padding: 12px; text-align: left; border-bottom: 1px solid #ddd;">
								<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $user->ID ) ); ?>" style="color: #0073aa; text-decoration: none;">
									<?php esc_html_e( 'Ver perfil completo', 'woocommerce-notificacao-de-registro' ); ?>
								</a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}

	/**
	 * Build HTML email template for SMTP test email.
	 *
	 * @since 4.0.0
	 *
	 * @return string HTML email content.
	 */
	public static function build_test_email() {
		ob_start();
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<title><?php esc_attr_e( 'Teste de configuração', 'woocommerce-notificacao-de-registro' ); ?></title>
		</head>
		<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
			<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
				<div style="background-color: #f8f8f8; padding: 20px; text-align: center;">
					<h2><?php esc_html_e( 'Teste de Configuração SMTP', 'woocommerce-notificacao-de-registro' ); ?></h2>
				</div>
				<div style="padding: 20px; background-color: #fff;">
					<p style="color: #28a745; font-weight: bold;">
						<?php esc_html_e( 'Este é um e-mail de teste enviado pelo plugin WooCommerce Registration Notifier.', 'woocommerce-notificacao-de-registro' ); ?>
					</p>
					<p>
						<?php esc_html_e( 'Se você recebeu este e-mail, suas configurações de SMTP estão funcionando corretamente.', 'woocommerce-notificacao-de-registro' ); ?>
					</p>
				</div>
			</div>
		</body>
		</html>
		<?php
		return ob_get_clean();
	}
}
