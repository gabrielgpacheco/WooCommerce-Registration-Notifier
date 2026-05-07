# WooCommerce Registration Notifier - v4.0.0

A modern, WPCS-compliant WordPress plugin that captures custom registration fields (name, phone) in WooCommerce registration and sends notification emails with configurable SMTP.

## Features

✅ **Custom Registration Fields**
- Full name (required)
- Phone number with area code (required, validated)

✅ **Email Notifications**
- Admin receives notification on new user registration
- HTML email template with user details
- Configurable recipient email

✅ **SMTP Configuration**
- Custom SMTP server settings (host, port, authentication)
- Encryption support (TLS/SSL)
- Graceful fallback to WordPress default mail
- Test email functionality

✅ **Code Quality**
- 100% WPCS compliance
- OOP architecture with proper namespacing
- Security hardening (nonces, sanitization, escaping)
- Full PHPDoc documentation
- Internationalization support (i18n)

## Requirements

- **PHP**: 7.4 or higher
- **WordPress**: 5.0 or higher
- **WooCommerce**: Latest version
- **PHPMailer**: Bundled with WordPress (for SMTP)

## Installation

1. Download/upload plugin folder to `/wp-content/plugins/`
2. Go to **Plugins** → **Installed Plugins**
3. Find "WooCommerce Registration Notifier"
4. Click **Activate**
5. Configure settings in **Settings** → **Notificação de Registros**

## File Structure

```
woocommerce-notificacao-de-registro/
├── woocommerce-notificacao-de-registro.php    # Main plugin file
├── src/
│   ├── Plugin.php                              # Bootstrap class
│   ├── Interfaces/
│   │   └── Hookable.php                       # Hook interface
│   ├── Admin/
│   │   ├── Settings.php                       # Settings registration
│   │   ├── SettingsPage.php                   # Admin page rendering
│   │   └── TestEmail.php                      # Test email handler
│   ├── Frontend/
│   │   └── RegistrationForm.php               # Form fields & validation
│   ├── Email/
│   │   ├── Mailer.php                         # SMTP & email dispatch
│   │   └── Template.php                       # HTML email templates
│   └── Utils/
│       ├── Sanitizer.php                      # Input sanitization
│       └── Validator.php                      # Field validation
├── includes/
│   └── Migration.php                           # Settings migration
├── README.md                                   # This file
└── TESTING.md                                  # Testing guide
```

## Architecture

### Design Patterns

**Hookable Interface Pattern**
All components implement `Hookable` interface to ensure consistent hook registration:

```php
interface Hookable {
    public function register_hooks();
}
```

**Dependency Injection**
Components receive dependencies through constructor and method parameters rather than using globals.

**Service Locator**
The `Plugin` class acts as orchestrator, managing all components and their initialization.

### Component Breakdown

| Component | Responsibility |
|-----------|-----------------|
| `Plugin` | Bootstrap, activation, component orchestration |
| `Interfaces\Hookable` | Contract for hook-aware components |
| `Admin\Settings` | Register settings with sanitization callbacks |
| `Admin\SettingsPage` | Render admin UI, manage settings page |
| `Admin\TestEmail` | Handle test email form submission |
| `Frontend\RegistrationForm` | Registration form fields, validation, user data storage |
| `Email\Mailer` | Send emails via SMTP or wp_mail fallback |
| `Email\Template` | Generate HTML email templates |
| `Utils\Sanitizer` | Centralized input sanitization |
| `Utils\Validator` | Field validation rules |
| `Migration` | Old → new settings format conversion |

## Configuration

### Settings Page

Navigate to **Settings** → **Notificação de Registros**

**Email Configuration:**
- **E-mail de Notificação**: Email address that receives registration notifications
- **E-mail do Remetente**: Sender email address (appears in "From" field)
- **Nome do Remetente**: Sender name (appears in "From" field)

**SMTP Configuration:**
- **Servidor SMTP**: SMTP server address (e.g., smtp.gmail.com)
- **Porta SMTP**: SMTP port (usually 587 for TLS, 465 for SSL)
- **Encriptação**: Encryption type (TLS, SSL, or None)
- **Usuário SMTP**: SMTP authentication username
- **Senha SMTP**: SMTP authentication password

**Notes:**
- If SMTP is not configured, plugin falls back to WordPress default mail
- Leave SMTP fields empty to use WordPress built-in mail function
- Test email feature helps verify SMTP configuration

## Security

### WPCS Compliance

The plugin follows all WordPress Coding Standards:

- ✅ **Input Sanitization**: All `$_POST` / `$_GET` data sanitized before use
- ✅ **Output Escaping**: All output escaped based on context (HTML, attribute, URL)
- ✅ **Nonce Verification**: CSRF protection on all form submissions
- ✅ **Capability Checks**: Admin operations verify `manage_options` capability
- ✅ **Proper Namespacing**: Classes use `WC_Reg_Notifier\` namespace to avoid conflicts
- ✅ **Error Handling**: Graceful degradation and logging for failures

### Sanitization Examples

```php
// Phone validation/sanitization
$phone = Sanitizer::sanitize_phone($_POST['phone']);

// Email sanitization
$email = Sanitizer::sanitize_email_field($_POST['email']);

// SMTP settings sanitization
$settings = Sanitizer::sanitize_settings($_POST['wc_reg_notifier_settings']);
```

### Nonce Protection

Test email form includes nonce verification:
```php
wp_nonce_field('wc_reg_notifier_test_email', 'wc_reg_notifier_nonce');
```

## Hooks & Filters

### WordPress Hooks Used

**Actions:**
- `woocommerce_register_form` - Add custom fields to registration form
- `woocommerce_register_post` - Validate registration fields
- `woocommerce_created_customer` - Process registration and send email
- `admin_menu` - Register settings page
- `admin_init` - Register settings fields
- `admin_post_wc_reg_notifier_test_email` - Handle test email submission

**Filters:**
- `plugin_action_links_*` - Add settings link to plugin action row

### Custom Hooks

None added (user request: WPCS compliance only)

## Backwards Compatibility

### Settings Migration

When plugin is activated (v4.0.0), migration automatically runs:

1. Checks if old settings exist
2. Converts to new array format
3. Preserves all values
4. Sets migration flag

No manual action required. Existing installations upgrade seamlessly.

## Internationalization

All user-facing strings use WordPress i18n functions:

- `__()` - Generic translation
- `esc_html__()` - Escaped HTML translation
- `_e()` - Echo translation
- `esc_html_e()` - Echo escaped HTML translation

**Text Domain**: `woocommerce-notificacao-de-registro`

To create translations:
1. Extract strings: `wp-cli i18n make-pot .`
2. Create `.po` files from `.pot`
3. Place in `/languages/` folder

## Troubleshooting

### Plugin won't activate
- Check PHP version (require 7.4+)
- Verify WooCommerce is active
- Check error log: `wp-content/debug.log`

### Emails not sending
- Verify SMTP settings are correct
- Check WordPress mail logs
- Enable debug mode: `define('WP_DEBUG', true);`
- Try test email function on settings page

### Settings not saving
- Check file permissions on `/wp-content/`
- Verify database user has UPDATE privileges
- Check for PHP errors in debug log

### Fields not appearing on registration form
- Verify WooCommerce is active
- Check plugin is activated
- Verify WooCommerce registration page exists
- Try clearing WordPress cache

## Development

### Code Standards

All code follows WordPress Coding Standards. To check:

```bash
phpcs --standard=WordPress src/ includes/ woocommerce-notificacao-de-registro.php
```

### Adding New Features

1. Create new class in appropriate namespace
2. Implement `Hookable` interface if using hooks
3. Add unit tests (future enhancement)
4. Run WPCS check
5. Update documentation

### Testing

See [TESTING.md](TESTING.md) for comprehensive testing guide.

## Changelog

### Version 4.0.0 (Current)
- ✅ Complete OOP refactor with proper namespacing
- ✅ Full WPCS compliance and security hardening
- ✅ Nonce verification on all forms
- ✅ Proper sanitization and escaping throughout
- ✅ Enhanced error handling and logging
- ✅ Full PHPDoc documentation
- ✅ Backwards compatibility with automatic settings migration

### Version 3.1 (Legacy)
- Basic procedural implementation
- Minimal security features

## Support & Issues

For issues or feature requests, please refer to the original repository.

## License

GPL v2 or later - See LICENSE file in plugin directory

## Author

Gabriel Pacheco

## Credits

- WordPress Plugin Security Standards
- PHPMailer for SMTP functionality
- WooCommerce community

---

**Last Updated**: May 6, 2026  
**Plugin Version**: 4.0.0  
**WPCS Status**: ✅ Compliant
