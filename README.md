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
- 💾 **Database Storage** - Store form submissions in your database
- 🔒 **Spam Protection** - Built-in honeypot functionality
- 📊 **Submission Viewing** - Review form submissions in Filament
- 📱 **Responsive Design** - Mobile-friendly forms out of the box

## Coming Soon

- 🎨 **Advanced Form Builder** - Drag-and-drop form field management
- 🌐 **Multi-language Support** - Translatable form fields and messages
- 🔧 **Developer API** - Extensible with hooks and events
- 📋 **Custom Field Types** - Create your own field types
- 🔀 **Conditional Logic** - Show/hide fields based on user input
- 📑 **Multi-step Forms** - Create forms with multiple pages
- 🔍 **Advanced Spam Protection** - reCAPTCHA integration

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

Use the Blade component in your views:

```blade
<x-simple-contact-form :form="contact" />
```


### Common Issues


**Emails not sending:**
- Check your mail configuration in `.env`
- Verify SMTP credentials
- Check Laravel log files

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
