<?php
/**
 * Plugin Bootstrap Class
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier;

use WC_Reg_Notifier\Interfaces\Hookable;
use WC_Reg_Notifier\Admin\Settings;
use WC_Reg_Notifier\Admin\SettingsPage;
use WC_Reg_Notifier\Admin\TestEmail;
use WC_Reg_Notifier\Frontend\RegistrationForm;

/**
 * Main plugin bootstrap and orchestration class.
 *
 * Initializes and coordinates all plugin components through
 * the Hookable interface pattern.
 *
 * @since 4.0.0
 */
class Plugin {

	/**
	 * Plugin file path.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Array of hookable components.
	 *
	 * @since 4.0.0
	 *
	 * @var Hookable[]
	 */
	private $components = array();

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param string $plugin_file The main plugin file path.
	 */
	public function __construct( $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Initialize the plugin.
	 *
	 * Registers all plugin hooks and components.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		// Load text domain for internationalization.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Check for WooCommerce dependency.
		add_action( 'admin_init', array( $this, 'check_woocommerce_dependency' ) );

		// Run migration on activation.
		register_activation_hook( $this->plugin_file, array( $this, 'activate' ) );

		// Initialize components.
		$this->initialize_components();

		// Register hooks for all components.
		$this->register_component_hooks();
	}

	/**
	 * Load plugin text domain.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'woocommerce-notificacao-de-registro',
			false,
			dirname( plugin_basename( $this->plugin_file ) ) . '/languages'
		);
	}

	/**
	 * Check if WooCommerce is active and display admin notice if not.
	 *
	 * @since 5.0.0
	 *
	 * @return void
	 */
	public function check_woocommerce_dependency() {
		if ( ! class_exists( 'WooCommerce' ) && current_user_can( 'activate_plugins' ) ) {
			add_action(
				'admin_notices',
				function () {
					?>
					<div class="notice notice-warning is-dismissible">
						<p>
							<?php
							echo wp_kses(
								sprintf(
									/* translators: %s: plugin name */
									__( 'O plugin %s requer o WooCommerce para funcionar corretamente. Por favor, instale e ative o WooCommerce.', 'woocommerce-notificacao-de-registro' ),
									'<strong>Registration Notifier for WooCommerce</strong>'
								),
								array( 'strong' => array() )
							);
							?>
						</p>
					</div>
					<?php
				}
			);
		}
	}

	/**
	 * Plugin activation hook.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function activate() {
		Migration::migrate();
	}

	/**
	 * Initialize all plugin components.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function initialize_components() {
		// Frontend components.
		$this->components[] = new RegistrationForm();

		// Admin components.
		$this->components[] = new Settings();
		$this->components[] = new SettingsPage( $this->plugin_file );
		$this->components[] = new TestEmail();
	}

	/**
	 * Register hooks for all components.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function register_component_hooks() {
		foreach ( $this->components as $component ) {
			if ( $component instanceof Hookable ) {
				$component->register_hooks();
			}
		}
	}
}
