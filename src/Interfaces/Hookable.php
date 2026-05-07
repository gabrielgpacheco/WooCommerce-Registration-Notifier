<?php
/**
 * Hookable Interface
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier\Interfaces;

/**
 * Interface for classes that register WordPress hooks.
 *
 * Classes implementing this interface are responsible for registering
 * their own WordPress action and filter hooks during initialization.
 *
 * @since 4.0.0
 */
interface Hookable {

	/**
	 * Register WordPress hooks for this component.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_hooks();
}
