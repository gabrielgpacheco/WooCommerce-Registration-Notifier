<?php
/**
 * Settings Registration
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

defined( 'ABSPATH' ) || exit;

namespace WC_Reg_Notifier\Admin;

use WC_Reg_Notifier\Interfaces\Hookable;
use WC_Reg_Notifier\Utils\Sanitizer;

/**
 * Handles settings registration and initialization.
 *
 * Registers the plugin settings option with sanitization callbacks
 * and initializes the admin interface.
 *
 * @since 4.0.0
 */
class Settings implements Hookable {

	/**
	 * Register WordPress hooks for this component.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Register plugin settings and settings fields.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_settings() {
		// Register the main settings option with sanitization callback.
		register_setting(
			'wc_reg_notifier',
			'wc_reg_notifier_settings',
			array(
				'sanitize_callback' => array( Sanitizer::class, 'sanitize_settings' ),
				'type'               => 'array',
				'default'            => $this->get_default_settings(),
			)
		);

		// Add settings section.
		add_settings_section(
			'wc_reg_notifier_section',
			__( 'Configurações de Notificação', 'woocommerce-notificacao-de-registro' ),
			array( $this, 'render_section_description' ),
			'wc_reg_notifier'
		);

		// Add settings fields.
		$this->add_settings_fields();
	}

	/**
	 * Get default settings values.
	 *
	 * @since 4.0.0
	 *
	 * @return array Default settings.
	 */
	private function get_default_settings() {
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

	/**
	 * Render settings section description.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_section_description() {
		esc_html_e( 'Configure os e-mails de notificação para novos cadastros', 'woocommerce-notificacao-de-registro' );
	}

	/**
	 * Add individual settings fields.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function add_settings_fields() {
		// Notification email field.
		add_settings_field(
			'notification_email',
			__( 'E-mail de Notificação', 'woocommerce-notificacao-de-registro' ),
			array( $this, 'render_notification_email_field' ),
			'wc_reg_notifier',
			'wc_reg_notifier_section'
		);

		// From email field.
		add_settings_field(
			'from_email',
			__( 'E-mail do Remetente', 'woocommerce-notificacao-de-registro' ),
			array( $this, 'render_from_email_field' ),
			'wc_reg_notifier',
			'wc_reg_notifier_section'
		);

		// From name field.
		add_settings_field(
			'from_name',
			__( 'Nome do Remetente', 'woocommerce-notificacao-de-registro' ),
			array( $this, 'render_from_name_field' ),
			'wc_reg_notifier',
			'wc_reg_notifier_section'
		);

		// SMTP settings field.
		add_settings_field(
			'smtp_settings',
			__( 'Configurações SMTP', 'woocommerce-notificacao-de-registro' ),
			array( $this, 'render_smtp_settings_fields' ),
			'wc_reg_notifier',
			'wc_reg_notifier_section'
		);
	}

	/**
	 * Render notification email field.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_notification_email_field() {
		$options = get_option( 'wc_reg_notifier_settings', $this->get_default_settings() );
		$email   = $options['notification_email'] ?? '';
		?>
		<input
			type="email"
			name="wc_reg_notifier_settings[notification_email]"
			value="<?php echo esc_attr( $email ); ?>"
			class="regular-text"
			required />
		<p class="description">
			<?php esc_html_e( 'E-mail que receberá as notificações de novos cadastros', 'woocommerce-notificacao-de-registro' ); ?>
		</p>
		<?php
	}

	/**
	 * Render from email field.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_from_email_field() {
		$options = get_option( 'wc_reg_notifier_settings', $this->get_default_settings() );
		$email   = $options['from_email'] ?? get_option( 'admin_email' );
		?>
		<input
			type="email"
			name="wc_reg_notifier_settings[from_email]"
			value="<?php echo esc_attr( $email ); ?>"
			class="regular-text"
			required />
		<p class="description">
			<?php esc_html_e( 'E-mail que aparecerá como remetente', 'woocommerce-notificacao-de-registro' ); ?>
		</p>
		<?php
	}

	/**
	 * Render from name field.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_from_name_field() {
		$options = get_option( 'wc_reg_notifier_settings', $this->get_default_settings() );
		$name    = $options['from_name'] ?? get_bloginfo( 'name' );
		?>
		<input
			type="text"
			name="wc_reg_notifier_settings[from_name]"
			value="<?php echo esc_attr( $name ); ?>"
			class="regular-text"
			required />
		<p class="description">
			<?php esc_html_e( 'Nome que aparecerá como remetente', 'woocommerce-notificacao-de-registro' ); ?>
		</p>
		<?php
	}

	/**
	 * Render SMTP settings fields.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_smtp_settings_fields() {
		$options = get_option( 'wc_reg_notifier_settings', $this->get_default_settings() );
		?>
		<table class="form-table">
			<tr>
				<th><label for="smtp_host"><?php esc_html_e( 'Servidor SMTP', 'woocommerce-notificacao-de-registro' ); ?></label></th>
				<td>
					<input
						type="text"
						name="wc_reg_notifier_settings[smtp_host]"
						value="<?php echo esc_attr( $options['smtp_host'] ?? '' ); ?>"
						class="regular-text" />
					<p class="description"><?php esc_html_e( 'Ex: smtp.seudominio.com', 'woocommerce-notificacao-de-registro' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="smtp_port"><?php esc_html_e( 'Porta SMTP', 'woocommerce-notificacao-de-registro' ); ?></label></th>
				<td>
					<input
						type="number"
						name="wc_reg_notifier_settings[smtp_port]"
						value="<?php echo esc_attr( $options['smtp_port'] ?? '587' ); ?>"
						class="small-text" />
					<p class="description"><?php esc_html_e( 'Normalmente 587 (TLS) ou 465 (SSL)', 'woocommerce-notificacao-de-registro' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="smtp_secure"><?php esc_html_e( 'Encriptação', 'woocommerce-notificacao-de-registro' ); ?></label></th>
				<td>
					<select name="wc_reg_notifier_settings[smtp_secure]">
						<option value=""><?php esc_html_e( 'Nenhuma', 'woocommerce-notificacao-de-registro' ); ?></option>
						<option value="tls" <?php selected( $options['smtp_secure'] ?? '', 'tls' ); ?>>TLS</option>
						<option value="ssl" <?php selected( $options['smtp_secure'] ?? '', 'ssl' ); ?>>SSL</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="smtp_username"><?php esc_html_e( 'Usuário SMTP', 'woocommerce-notificacao-de-registro' ); ?></label></th>
				<td>
					<input
						type="text"
						name="wc_reg_notifier_settings[smtp_username]"
						value="<?php echo esc_attr( $options['smtp_username'] ?? '' ); ?>"
						class="regular-text" />
					<p class="description"><?php esc_html_e( 'Normalmente seu e-mail completo', 'woocommerce-notificacao-de-registro' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="smtp_password"><?php esc_html_e( 'Senha SMTP', 'woocommerce-notificacao-de-registro' ); ?></label></th>
				<td>
					<input
						type="password"
						name="wc_reg_notifier_settings[smtp_password]"
						value="<?php echo esc_attr( ! empty( $options['smtp_password'] ) ? '********' : '' ); ?>"
						class="regular-text"
						placeholder="<?php esc_attr_e( 'Deixe em branco para manter a atual', 'woocommerce-notificacao-de-registro' ); ?>" />
				</td>
			</tr>
		</table>
		<?php
	}
}
