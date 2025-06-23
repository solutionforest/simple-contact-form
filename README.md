# Simple Contact Form - FilamentPHP Plugin

A lightweight, customizable contact form plugin for FilamentPHP that provides an easy-to-use alternative to Contact Form 7. Build and manage contact forms with a simple, intuitive interface directly from your Filament admin panel.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solution-forest/simple-contact-form.svg?style=flat-square)](https://packagist.org/packages/solution-forest/simple-contact-form)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/solutionforest/simple-contact-form/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/solutionforest/simple-contact-form/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/solutionforest/simple-contact-form/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/solutionforest/simple-contact-form/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/solution-forest/simple-contact-form.svg?style=flat-square)](https://packagist.org/packages/solution-forest/simple-contact-form)


## Features

- 🚀 **Easy Installation** - Get up and running in minutes
- 📝 **Basic Form Management** - Create and manage contact forms
- 📧 **Email Notifications** - Receive form submissions via email
- 📱 **Responsive Design** - Mobile-friendly forms out of the box

## Coming Soon

- 📁 **File Upload** - Support for file attachments in forms
- 🪝 **Form Hooks** - Before/after submit hooks for custom logic
- ⚙️ **Configuration Options** - Adjustable settings for forms
- 💾 **Submission Storage** - Save and manage form submissions

## Installation

You can install the package via composer:

```bash
composer require solution-forest/simple-contact-form
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="simple-contact-form-migrations"
php artisan migrate
```

Register the plugin in your Panel provider:

```php
use SolutionForest\SimpleContactForm\SimpleContactFormPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            SimpleContactFormPlugin::make(),
        ]);
}
```
## Configuration

### Customizing Translations

If you need to modify the translations, publish the language files:

```bash
php artisan vendor:publish --tag="simple-contact-form-lang"
```

This will copy the language files to your application's `lang` directory where you can edit them.

### Email Setting

For the plugin to send emails properly, ensure your Laravel mail configuration is set up correctly in your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com 
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@example.com
MAIL_FROM_NAME="${APP_NAME}"
```
## Usage

### Creating a Form

1. Navigate to the "Contact Forms" section in your Filament admin panel
2. Click "Create Form"
3. Configure the basic form settings:
   - Form Name
   - Email Content
   - Success Message
   - Error Message
   - Email Subject
4. Add fields using the form builder:
   - Text Input
   - Email Input
   - Textarea
   - Select Dropdown
   - Radio Buttons
   - Checkboxes


### Displaying Forms

Use the Blade component with form id in your views:

```blade
<x-simple-contact-form :form="1" />
```
### Preview 
<video controls src="backend.mov" title="Title"></video> 
<video controls src="frontend.mov" title="Title"></video>
![alt text](image.png)
![alt text](image-1.png)
![alt text](image-2.png)

### Common Issues


**Emails not sending:**
- Check your mail configuration in `.env`
- Verify SMTP credentials
- Check Laravel log files
- smtp doc

**Lost style in view:**
- Make sure you're using Tailwind CSS v3 (FilamentPHP 3 only supports Tailwind v3)
- If you've upgraded to Tailwind v4, downgrade to v3 with `npm install tailwindcss@^3.0`
- Make sure your `tailwind.config.js` looks like this:
    ```js
    import preset from './vendor/filament/support/tailwind.config.preset'

    export default {
            presets: [preset],
            content: [
                    './app/Filament/**/*.php',
                    './resources/views/filament/**/*.blade.php',
                    './vendor/filament/**/*.blade.php',
            ],
    }
    ```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](.github/CONTRIBUTING.md) for details.


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [hayward](https://github.com/solutionforest)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
