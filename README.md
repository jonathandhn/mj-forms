# MJ Forms

Manage your Mailjet subscribers with Elementor forms.

![Build Status](https://github.com/jonathandhn/mj-forms/actions/workflows/release.yml/badge.svg)

## Description

This plugin adds a "Mailjet" action to Elementor Pro forms, allowing you to automatically subscribe users to a Mailjet contact list upon form submission. It supports mapping fields for Email, First Name, Last Name, Phone, and a custom field.

## Installation

1. Upload the `mj-forms` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Add your Mailjet API credentials to your `wp-config.php` file:

```php
define( 'MAILJET_API', 'your_api_key' );
define( 'MAILJET_SECRET', 'your_api_secret' );
```

## Usage

1. Edit a page with Elementor.
2. Add or edit a Form widget.
3. In the **Actions After Submit** section, select **Mailjet Subscribe**.
4. A new section **Mailjet** will appear.
5. Enter your **Mailjet List ID** (mandatory).
6. Map your form fields to the Mailjet fields:
    - **Mailjet FirstName field**: e.g., `firstname`
    - **Mailjet LastName field**: e.g., `name`
    - **Mailjet Phone field**: e.g., `phone`
    - **Mailjet Custom field**: your custom property name

> **Note**: Ensure your form field IDs match the values mapped in the Mailjet section, or use the mapped names in your Mailjet account.

## Requirements

- PHP 7.4 or higher
- Elementor Pro
- Valid Mailjet Account via `wp-config.php`