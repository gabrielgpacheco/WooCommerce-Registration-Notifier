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
		// Run migration on activation.
		register_activation_hook( $this->plugin_file, array( $this, 'activate' ) );

		// Initialize components.
		$this->initialize_components();

		// Register hooks for all components.
		$this->register_component_hooks();
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
		$this->components[] = new SettingsPage();
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
