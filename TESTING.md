# WooCommerce Registration Notifier - Testing Guide

## Pre-Testing Setup

### 1. Plugin Activation
1. Go to **WordPress Admin Dashboard**
2. Navigate to **Plugins** → **Installed Plugins**
3. Find "WooCommerce Registration Notifier" (v4.0.0)
4. Click **Activate**
   - ✅ Expected: Settings migration runs automatically
   - ✅ Check: Option `wc_reg_notifier_migrated` should be set to `1`

### 2. Verify Settings Migration
Run in WordPress command line or database:
```sql
SELECT * FROM wp_options WHERE option_name = 'wc_reg_notifier_settings';
```
✅ Expected: Settings array with keys: `notification_email`, `from_email`, `from_name`, `smtp_host`, `smtp_port`, `smtp_secure`, `smtp_username`, `smtp_password`

---

## Frontend Testing

### Test 1: Registration Form Custom Fields
**Steps:**
1. Go to WooCommerce Registration page (typically `/my-account/register/`)
2. Observe the registration form

**✅ Expected Results:**
- ✅ "Nome completo" (Full Name) field appears
- ✅ "Telefone com DDD" (Phone with Area Code) field appears
- ✅ Both fields marked as required with asterisk (*)
- ✅ Phone field has pattern validation hint: "Formato: (99) 99999-9999"

### Test 2: Registration Form Validation - Invalid Phone
**Steps:**
1. Fill form:
   - Email: `test@example.com`
   - Password: `TestPass123!`
   - Nome completo: `João Silva`
   - Telefone: `123` (invalid format)
2. Click Register

**✅ Expected Results:**
- ❌ Form submission fails
- ❌ Error message appears: "Formato de telefone inválido! Use: (99) 99999-9999"

### Test 3: Registration Form Validation - Empty Required Fields
**Steps:**
1. Fill only email and password
2. Leave Nome completo and Telefone empty
3. Click Register

**✅ Expected Results:**
- ❌ Form submission fails
- ❌ Error: "Nome é obrigatório!"
- ❌ Error: "Telefone é obrigatório!"

### Test 4: Successful Registration
**Steps:**
1. Fill form completely:
   - Email: `newuser@example.com`
   - Password: `TestPass123!`
   - Nome completo: `João Silva`
   - Telefone: `(11) 99999-8888`
2. Click Register

**✅ Expected Results:**
- ✅ Account created successfully
- ✅ User receives WordPress confirmation email
- ✅ Check WordPress database: User metadata saved
  ```sql
  SELECT * FROM wp_usermeta WHERE user_id = [NEW_USER_ID] AND meta_key IN ('first_name', 'billing_first_name', 'billing_phone');
  ```
  Expected rows:
  - `first_name` → "João Silva"
  - `billing_first_name` → "João Silva"
  - `billing_phone` → "(11) 99999-8888"

### Test 5: Registration Notification Email
**Action:** Complete Test 4 (successful registration)

**✅ Expected Results:**
- ✅ Admin receives notification email
- ✅ Email subject: `[Site Name] Novo cadastro: newuser@example.com`
- ✅ Email contains:
  - Nome Completo: João Silva
  - E-mail: newuser@example.com
  - Telefone: (11) 99999-8888
  - Data do Cadastro: [current date/time]
  - Link to user profile

---

## Admin Settings Testing

### Test 6: Access Settings Page
**Steps:**
1. Go to **WordPress Admin**
2. Navigate to **Settings** → **Notificação de Registros** (in left menu)

**✅ Expected Results:**
- ✅ Settings page loads without errors
- ✅ Page title: "Configurações de Notificação de Registros"
- ✅ All sections visible

### Test 7: Settings Form Fields
**Steps:**
1. On Settings page, observe all form fields

**✅ Expected Results:**
- ✅ "E-mail de Notificação" - email input field
- ✅ "E-mail do Remetente" - email input field
- ✅ "Nome do Remetente" - text input field
- ✅ SMTP Settings section with:
  - ✅ Servidor SMTP (text field)
  - ✅ Porta SMTP (number field, default 587)
  - ✅ Encriptação (select: Nenhuma, TLS, SSL)
  - ✅ Usuário SMTP (text field)
  - ✅ Senha SMTP (password field)
- ✅ "Salvar Configurações" button
- ✅ "Testar Configuração SMTP" section with test email form

### Test 8: Save Settings
**Steps:**
1. Fill settings form:
   - E-mail de Notificação: `admin@example.com`
   - E-mail do Remetente: `sender@example.com`
   - Nome do Remetente: `Herbalife Site`
   - (SMTP fields can be left empty for fallback to wp_mail)
2. Click "Salvar Configurações"

**✅ Expected Results:**
- ✅ Page reloads with success message: "Configurações salvas com sucesso!"
- ✅ Database check - settings saved:
  ```sql
  SELECT * FROM wp_options WHERE option_name = 'wc_reg_notifier_settings';
  ```

### Test 9: Test Email Without SMTP
**Steps:**
1. Ensure SMTP fields are empty (use default wp_mail)
2. Scroll to "Testar Configuração SMTP" section
3. Enter test email: `youremail@example.com`
4. Click "Enviar E-mail de Teste"

**✅ Expected Results:**
- ✅ Success message: "E-mail de teste enviado com sucesso para youremail@example.com"
- ✅ Test email received at `youremail@example.com`
- ✅ Email contains HTML template with test message

### Test 10: Test Email With SMTP Configuration
**Steps:**
1. Fill SMTP settings (requires valid SMTP credentials):
   - Servidor SMTP: `smtp.gmail.com` (example)
   - Porta SMTP: `587`
   - Encriptação: `TLS`
   - Usuário SMTP: `your-email@gmail.com`
   - Senha SMTP: `your-app-password`
2. Save settings
3. Send test email to `youremail@example.com`

**✅ Expected Results:**
- ✅ Email sent successfully via SMTP
- ✅ Test email received
- ✅ If SMTP fails, fallback to wp_mail should handle it gracefully

### Test 11: Plugin Action Links
**Steps:**
1. Go to **Plugins** → **Installed Plugins**
2. Find "WooCommerce Registration Notifier"

**✅ Expected Results:**
- ✅ "Configurações" (Settings) link appears in plugin action row
- ✅ Clicking link takes you to plugin settings page

---

## Security Testing

### Test 12: Nonce Verification (Test Email)
**Steps:**
1. Open browser dev tools (F12)
2. Go to Settings page
3. Right-click test email form → Inspect
4. Look for nonce field

**✅ Expected Results:**
- ✅ Nonce field present: `wc_reg_notifier_nonce`
- ✅ Nonce value changes on page reload
- ✅ Submitting form without valid nonce should fail with: "Verificação de segurança falhou!"

### Test 13: Capability Check
**Steps:**
1. Create a non-admin WordPress user
2. Log in as that user
3. Try to access `/wp-admin/options-general.php?page=wc-reg-notifier`

**✅ Expected Results:**
- ❌ Access denied message: "Acesso negado!"
- ❌ Page does not load settings

### Test 14: Input Sanitization
**Steps:**
1. On Settings page, fill fields with special characters and HTML:
   - Nome do Remetente: `<script>alert('xss')</script> Herbalife`
   - Servidor SMTP: `<img src=x onerror=alert('xss')>`
2. Save settings
3. Check database for saved values

**✅ Expected Results:**
- ✅ HTML tags removed/escaped
- ✅ No script execution
- ✅ Malicious content neutralized in database

---

## WPCS Compliance Verification

### Test 15: PHP CodeSniffer Check (if available)
**Command line (requires WPCS installed):**
```bash
cd /path/to/woocommerce-notificacao-de-registro
phpcs --standard=WordPress src/ includes/ woocommerce-notificacao-de-registro.php
```

**✅ Expected Results:**
- ✅ 0 errors
- ✅ 0 critical security warnings
- ✅ Only minor style warnings acceptable

### Test 16: Code Standards Review
**Manual checks:**
- ✅ All classes use proper namespacing
- ✅ All functions have PHPDoc comments
- ✅ All output is escaped (esc_html, esc_attr, esc_url)
- ✅ All input is sanitized (sanitize_text_field, sanitize_email, etc.)
- ✅ No global functions in global namespace (all use classes)
- ✅ All strings use proper i18n functions

---

## Error Handling Testing

### Test 17: Missing PHPMailer Classes
**Action:** If SMTP is configured with invalid host
**Steps:**
1. Set SMTP Host to invalid address (e.g., `invalid.smtp.xyz`)
2. Configure port/user/pass
3. Try to send registration email

**✅ Expected Results:**
- ✅ Error logged in `wp-content/debug.log`
- ✅ Plugin gracefully falls back to wp_mail
- ✅ Email still sent (via wp_mail fallback)
- ✅ No PHP errors on frontend

### Test 18: Missing Settings Configuration
**Action:** First time use
**Steps:**
1. Delete all `wc_reg_notifier_settings` options from database
2. Trigger a user registration

**✅ Expected Results:**
- ✅ Plugin uses default values from Settings class
- ✅ Notification email sent to admin email (fallback)
- ✅ No errors

---

## Performance Testing

### Test 19: Load Time
**Steps:**
1. Register a new user
2. Monitor registration form load time
3. Monitor admin settings page load time

**✅ Expected Results:**
- ✅ Registration form loads in < 1 second
- ✅ Settings page loads in < 1 second
- ✅ No excessive database queries

---

## Backwards Compatibility Testing (if upgrading from v3.1)

### Test 20: Settings Migration
**Steps:**
1. (Simulated) Have old v3.1 settings in database
2. Activate v4.0 plugin
3. Check settings

**✅ Expected Results:**
- ✅ Old settings preserved
- ✅ Migration flag set: `wc_reg_notifier_migrated = 1`
- ✅ All settings converted to new format
- ✅ No data loss

---

## Troubleshooting

If tests fail, check:

1. **PHP Version**: Requires PHP 7.4+
   ```php
   echo phpversion();
   ```

2. **WordPress Version**: Requires WP 5.0+
   - Check in **Dashboard** → **Updates**

3. **WooCommerce**: Plugin requires active WooCommerce
   - Verify in **Plugins** page

4. **Error Logs**: Check WordPress debug log
   ```bash
   tail -f wp-content/debug.log
   ```

5. **Database**: Verify option exists
   ```sql
   SELECT * FROM wp_options WHERE option_name LIKE 'wc_reg_notifier%';
   ```

6. **Autoloader**: Verify PSR-4 autoloader works
   - Check file paths match namespace structure

---

## Checklist Summary

- [ ] Test 1: Custom fields appear
- [ ] Test 2: Phone validation (invalid format)
- [ ] Test 3: Required field validation
- [ ] Test 4: Successful registration
- [ ] Test 5: Notification email received
- [ ] Test 6: Settings page loads
- [ ] Test 7: Form fields display
- [ ] Test 8: Settings save correctly
- [ ] Test 9: Test email (wp_mail)
- [ ] Test 10: Test email (SMTP)
- [ ] Test 11: Plugin action links
- [ ] Test 12: Nonce verification
- [ ] Test 13: Capability checks
- [ ] Test 14: Input sanitization
- [ ] Test 15: WPCS compliance
- [ ] Test 16: Code standards
- [ ] Test 17: Error handling
- [ ] Test 18: Missing settings fallback
- [ ] Test 19: Performance
- [ ] Test 20: Backwards compatibility (if upgrading)

---

## Quick Start Command Reference

```bash
# Check PHP version
php -v

# Check WordPress debug log
tail -f wp-content/debug.log

# Run WPCS check (requires phpcs installed)
phpcs --standard=WordPress src/ includes/

# Check database options
wp option get wc_reg_notifier_settings

# Check migration status
wp option get wc_reg_notifier_migrated
```

---

**Ready to test! Execute tests in the order provided for best results.**
