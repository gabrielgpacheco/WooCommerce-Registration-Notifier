<?php
/**
 * Registration Form Handler
 *
 * @package WC_Reg_Notifier
 * @since 4.0.0
 */

namespace WC_Reg_Notifier\Frontend;

use WC_Reg_Notifier\Interfaces\Hookable;
use WC_Reg_Notifier\Utils\Sanitizer;
use WC_Reg_Notifier\Utils\Validator;
use WC_Reg_Notifier\Email\Mailer;
use WC_Reg_Notifier\Email\Template;

/**
 * Handles WooCommerce registration form customization.
 *
 * Adds custom fields (name, phone), validates them, saves user metadata,
 * and triggers notification email on successful registration.
 *
 * @since 4.0.0
 */
class RegistrationForm implements Hookable {

	/**
	 * Register WordPress hooks for this component.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'woocommerce_register_form', array( $this, 'add_custom_fields' ) );
		add_action( 'woocommerce_register_post', array( $this, 'validate_fields' ), 10, 3 );
		add_action( 'woocommerce_created_customer', array( $this, 'process_registration' ) );
	}

	/**
	 * Add custom fields to WooCommerce registration form.
	 *
	 * Displays full name and phone number fields with proper labels and validation.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_custom_fields() {
		?>
		<p class="form-row form-row-wide">
			<label for="reg_first_name">
				<?php esc_html_e( 'Nome completo', 'woocommerce-notificacao-de-registro' ); ?>
				<span class="required">*</span>
			</label>
			<input
				type="text"
				class="input-text"
				name="first_name"
				id="reg_first_name"
				required
				value="<?php echo esc_attr( $_POST['first_name'] ?? '' ); ?>" />
		</p>

		<p class="form-row form-row-wide">
			<label for="reg_phone">
				<?php esc_html_e( 'Telefone com DDD', 'woocommerce-notificacao-de-registro' ); ?>
				<span class="required">*</span>
			</label>
			<input
				type="tel"
				class="input-text"
				name="phone"
				id="reg_phone"
				required
				pattern="\(?\d{2}\)?\s?\d{4,5}\-?\d{4}"
				title="<?php esc_attr_e( 'Formato: (99) 99999-9999', 'woocommerce-notificacao-de-registro' ); ?>"
				value="<?php echo esc_attr( $_POST['phone'] ?? '' ); ?>" />
		</p>
		<?php
	}

	/**
	 * Validate registration form fields.
	 *
	 * @since 4.0.0
	 *
	 * @param string           $username           The submitted username.
	 * @param string           $email              The submitted email.
	 * @param \WP_Error|object $validation_errors  WP_Error object for adding validation errors.
	 *
	 * @return void
	 */
	public function validate_fields( $username, $email, $validation_errors ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce handles nonce verification.

		$first_name = $_POST['first_name'] ?? '';
		$phone      = $_POST['phone'] ?? '';

		// Validate first name.
		$name_errors = Validator::validate_first_name( $first_name );
		foreach ( $name_errors as $error ) {
			$validation_errors->add( 'first_name_error', $error );
		}

		// Validate phone.
		$phone_errors = Validator::validate_phone( $phone );
		foreach ( $phone_errors as $error ) {
			$validation_errors->add( 'phone_error', $error );
		}
	}

	/**
	 * Process registration: save user metadata and send notification email.
	 *
	 * @since 4.0.0
	 *
	 * @param int $user_id The ID of the newly created user.
	 *
	 * @return void
	 */
	public function process_registration( $user_id ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- WooCommerce handles nonce verification.

		// Save first name.
		$first_name = isset( $_POST['first_name'] ) ? Sanitizer::sanitize_name( $_POST['first_name'] ) : '';
		if ( ! empty( $first_name ) ) {
			update_user_meta( $user_id, 'first_name', $first_name );
			update_user_meta( $user_id, 'billing_first_name', $first_name );
		}

		// Save phone.
		$phone = isset( $_POST['phone'] ) ? Sanitizer::sanitize_phone( $_POST['phone'] ) : '';
		if ( ! empty( $phone ) ) {
			update_user_meta( $user_id, 'billing_phone', $phone );
		}

		// Send notification email.
		$user = get_userdata( $user_id );
		if ( $user ) {
			$this->send_notification_email( $user );
		}
	}

	/**
	 * Send registration notification email to admin.
	 *
	 * @since 4.0.0
	 *
	 * @param \WP_User $user The registered user object.
	 *
	 * @return void
	 */
	private function send_notification_email( $user ) {
		$options = get_option( 'wc_reg_notifier_settings' );

		$to = ! empty( $options['notification_email'] )
			? sanitize_email( $options['notification_email'] )
			: sanitize_email( get_option( 'admin_email' ) );

		$subject = sprintf(
			/* translators: %1$s is the site name, %2$s is the user email */
			__( '[%1$s] Novo cadastro: %2$s', 'woocommerce-notificacao-de-registro' ),
			get_bloginfo( 'name' ),
			$user->user_email
		);

		$message = Template::build_registration_email( $user );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		Mailer::send( $to, $subject, $message, $headers );
	}
}
