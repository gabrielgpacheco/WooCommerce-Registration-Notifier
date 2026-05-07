<?php
/**
 * Settings Page Handler
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier\Admin;

use WC_Reg_Notifier\Interfaces\Hookable;

/**
 * Handles admin settings page and menu registration.
 *
 * @since 4.0.0
 */
class SettingsPage implements Hookable {

	/**
	 * Register WordPress hooks for this component.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_filter( 'plugin_action_links_woocommerce-notificacao-de-registro/woocommerce-notificacao-de-registro.php', array( $this, 'add_action_links' ) );
	}

	/**
	 * Register admin menu page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Configurações de Notificação', 'woocommerce-notificacao-de-registro' ),
			__( 'Notificação de Registros', 'woocommerce-notificacao-de-registro' ),
			'manage_options',
			'wc-reg-notifier',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function render_page() {
		// Verify capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Acesso negado!', 'woocommerce-notificacao-de-registro' ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- sanitize_text_field sanitizes the input.
		$settings_updated = isset( $_GET['settings-updated'] ) ? sanitize_text_field( $_GET['settings-updated'] ) : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Configurações de Notificação de Registros', 'woocommerce-notificacao-de-registro' ); ?></h1>

			<?php if ( $settings_updated ) : ?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e( 'Configurações salvas com sucesso!', 'woocommerce-notificacao-de-registro' ); ?></p>
				</div>
			<?php endif; ?>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'wc_reg_notifier' );
				do_settings_sections( 'wc_reg_notifier' );
				submit_button( __( 'Salvar Configurações', 'woocommerce-notificacao-de-registro' ) );
				?>
			</form>

			<div class="card">
				<h2><?php esc_html_e( 'Testar Configuração SMTP', 'woocommerce-notificacao-de-registro' ); ?></h2>
				<p><?php esc_html_e( 'Envie um e-mail de teste para verificar se as configurações estão corretas.', 'woocommerce-notificacao-de-registro' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'wc_reg_notifier_test_email', 'wc_reg_notifier_nonce' ); ?>
					<input type="hidden" name="action" value="wc_reg_notifier_test_email" />
					<table class="form-table">
						<tr>
							<th><label for="test_email"><?php esc_html_e( 'E-mail de teste', 'woocommerce-notificacao-de-registro' ); ?></label></th>
							<td>
								<input
									type="email"
									name="test_email"
									id="test_email"
									value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>"
									class="regular-text" />
								<?php submit_button( __( 'Enviar E-mail de Teste', 'woocommerce-notificacao-de-registro' ), 'secondary', 'send_test', false ); ?>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Add quick links to plugin action links.
	 *
	 * @since 4.0.0
	 *
	 * @param array $links Plugin action links.
	 *
	 * @return array Modified plugin action links.
	 */
	public function add_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=wc-reg-notifier' ) ) . '">'
			. esc_html__( 'Configurações', 'woocommerce-notificacao-de-registro' )
			. '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}
}
