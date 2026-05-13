=== Registration Notifier for WooCommerce ===
Contributors: gabrielgpacheco
Tags: woocommerce, registration, notification, email, smtp, admin, new user
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 5.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Notificação de cadastros de usuários com configuração SMTP personalizada para sites WooCommerce.

== Description ==

Este plugin monitora os registros de usuários em sites baseados em WooCommerce e envia notificações por email para os administradores do site via SMTP configurável.

= Funcionalidades =

* Campos personalizados no formulário de registro (Nome completo e Telefone)
* Notificação por email ao administrador sobre novos cadastros
* Template HTML elegante com detalhes do usuário
* Configuração SMTP personalizada (host, porta, autenticação, TLS/SSL)
* Fallback automático para wp_mail() se SMTP não estiver configurado
* Funcionalidade de email de teste para verificar configurações SMTP
* 100% compatível com WordPress Coding Standards
* Arquitetura OOP com namespacing PSR-4
* Segurança com nonces, sanitização e escaping

== Installation ==

1. Faça upload da pasta `woocommerce-notificacao-de-registro` para `/wp-content/plugins/`
2. Ative o plugin em **Plugins** → **Plugins Instalados**
3. Acesse **Configurações** → **Notificação de Registros**
4. Configure o email de notificação e as opções SMTP (opcional)
5. Pronto! Os administradores receberão emails quando novos usuários se registrarem

= Requisitos =

* WooCommerce (versão mais recente)
* PHP 7.4 ou superior
* WordPress 5.0 ou superior

== Frequently Asked Questions ==

= O plugin envia emails sem configuração SMTP? =

Sim. Se as configurações SMTP não forem preenchidas, o plugin usa a função padrão `wp_mail()` do WordPress.

= Preciso ter conhecimentos técnicos para configurar o SMTP? =

Conhecimentos básicos de configuração de servidor SMTP são necessários. Consulte seu provedor de email para obter as credenciais SMTP.

= O plugin funciona com qualquer tema? =

Sim, desde que o tema utilize o formulário de registro padrão do WooCommerce.

= As senhas SMTP são armazenadas com segurança? =

Sim, as senhas são codificadas em base64 antes de serem armazenadas no banco de dados.

== Screenshots ==

1. Página de configurações do plugin
2. Campos personalizados no formulário de registro

== Changelog ==

= 5.0.0 =
* Adicionado readme.txt no formato WordPress.org
* Adicionado load_plugin_textdomain() para carregar traduções
* Adicionado arquivo .pot para traduções
* Adicionado uninstall.php para limpeza ao desinstalar
* Adicionada verificação de dependência do WooCommerce
* Adicionado ABSPATH check em todos os arquivos PHP
* SMTP password agora armazenado com base64
* Campo de senha SMTP não pré-preenche o valor salvo
* Substituído error_log() por trigger_error()
* Corrigido basename hardcoded nos links de ação do plugin
* Internacionalizada string "Acesso negado!" no arquivo principal
* Nome do plugin alterado para "Registration Notifier for WooCommerce"
* Atualizada versão para 5.0.0

= 4.0.0 =
* Refatoração OOP completa com namespacing
* 100% WPCS compliance e hardening de segurança
* Nonce verification em todos os formulários
* Sanitização e escaping em toda a aplicação
* Tratamento de erros e logging aprimorados
* Documentação PHPDoc completa
* Compatibilidade reversa com migração automática de configurações

== Upgrade Notice ==

= 5.0.0 =
Atualização importante: o plugin agora verifica se o WooCommerce está ativo. Adicionado suporte completo a traduções e melhorias de segurança no armazenamento da senha SMTP.
