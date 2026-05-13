<?php
/**
 * WooCommerce Registration Notifier
 *
 * @package WC_Reg_Notifier
 * @version 5.0.0
 *
 * Plugin Name: Registration Notifier for WooCommerce
 * Description: Notificação de cadastros com configuração SMTP personalizada
 * Version: 5.0.0
 * Author: Gabriel Pacheco
 * Text Domain: woocommerce-notificacao-de-registro
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.0
 * Tested up to: 6.7
 *
 * @author Gabriel Pacheco
 * @license GPLv2 or later
 * @link https://github.com/gabrielgpacheco/woocommerce-registration-notifier
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

// phpcs:disable WordPress.Files.FileName.InvalidClassFileName

// Security: Prevent direct access.
defined( 'ABSPATH' ) || die( esc_html__( 'Acesso negado!', 'woocommerce-notificacao-de-registro' ) );

/**
 * Load plugin files and initialize.
 *
 * Autoloader for plugin classes using PSR-4 standard.
 *
 * @param string $class The class name being loaded.
 *
 * @return void
 */
spl_autoload_register(
	function ( $class ) {
		// Project-specific namespace.
		$prefix = 'WC_Reg_Notifier\\';

		// Base directory for the namespace.
		$base_dir = plugin_dir_path( __FILE__ );

		// Does the class use the namespace prefix?
		$len = strlen( $prefix );
		if ( 0 !== strncmp( $prefix, $class, $len ) ) {
			return;
		}

		// Get the relative class name.
		$relative_class = substr( $class, $len );

		// Replace namespace separators with directory separators.
		$file = $base_dir . 'src/' . str_replace( '\\', '/', $relative_class ) . '.php';

		// If the file exists, require it.
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

// Load migration and initialize plugin.
require_once plugin_dir_path( __FILE__ ) . 'includes/Migration.php';

// Initialize the plugin.
$plugin = new WC_Reg_Notifier\Plugin( __FILE__ );
$plugin->init();