<?php
/**
 * Validator Utility Class
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier\Utils;

/**
 * Form field validation logic.
 *
 * This class provides validation methods for registration form fields
 * according to plugin requirements.
 *
 * @since 4.0.0
 */
class Validator {

	/**
	 * Phone number pattern for validation.
	 *
	 * Matches formats like: (99) 99999-9999, 99 99999-9999, 9999999999, etc.
	 *
	 * @since 4.0.0
	 *
	 * @var string
	 */
	const PHONE_PATTERN = '/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/';

	/**
	 * Validate first name (full name).
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return array Error messages (empty if valid).
	 */
	public static function validate_first_name( $value ) {
		$errors = array();

		if ( empty( $value ) ) {
			$errors[] = __( 'Nome é obrigatório!', 'woocommerce-notificacao-de-registro' );
		} elseif ( strlen( $value ) > 100 ) {
			$errors[] = __( 'Nome muito longo!', 'woocommerce-notificacao-de-registro' );
		}

		return $errors;
	}

	/**
	 * Validate phone number.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return array Error messages (empty if valid).
	 */
	public static function validate_phone( $value ) {
		$errors = array();

		if ( empty( $value ) ) {
			$errors[] = __( 'Telefone é obrigatório!', 'woocommerce-notificacao-de-registro' );
		} elseif ( ! preg_match( self::PHONE_PATTERN, $value ) ) {
			$errors[] = __( 'Formato de telefone inválido! Use: (99) 99999-9999', 'woocommerce-notificacao-de-registro' );
		}

		return $errors;
	}

	/**
	 * Validate email address.
	 *
	 * @since 4.0.0
	 *
	 * @param mixed $value The value to validate.
	 *
	 * @return array Error messages (empty if valid).
	 */
	public static function validate_email( $value ) {
		$errors = array();

		if ( empty( $value ) ) {
			$errors[] = __( 'E-mail é obrigatório!', 'woocommerce-notificacao-de-registro' );
		} elseif ( ! is_email( $value ) ) {
			$errors[] = __( 'E-mail inválido!', 'woocommerce-notificacao-de-registro' );
		}

		return $errors;
	}
}
