![Simple-Contact-Form](https://github.com/user-attachments/assets/302127a9-fada-404c-ade9-d7658a3bfa8c)

# Simple Contact Form - FilamentPHP Plugin

A lightweight, customizable contact form plugin for FilamentPHP that provides an easy-to-use alternative to Contact Form 7. Build and manage contact forms with a simple, intuitive interface directly from your Filament admin panel.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solution-forest/simple-contact-form.svg?style=flat-square)](https://packagist.org/packages/solution-forest/simple-contact-form)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/solutionforest/simple-contact-form/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/solutionforest/simple-contact-form/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/solutionforest/simple-contact-form/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/solutionforest/simple-contact-form/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/solution-forest/simple-contact-form.svg?style=flat-square)](https://packagist.org/packages/solution-forest/simple-contact-form)

## Features

-   🚀 **Easy Installation** - Get up and running in minutes
-   📝 **Basic Form Management** - Create and manage contact forms
-   📧 **Email Notifications** - Receive form submissions via email
-   📱 **Responsive Design** - Mobile-friendly forms out of the box

## [Pro Version (Click me)](https://filamentphp.com/plugins/solution-forest-simple-contact-form-pro) 

-   📁 **File Upload** - Support for file attachments in forms
-   🪝 **Form Hooks** - Before/after submit hooks for custom logic
-   ⚙️ **Configuration Options** - Adjustable settings for forms
-   💾 **Submission Storage** - Save and manage form submissions

## Try Pro Version Now

     https://checkout.anystack.sh/simple-contact-form-pro

### Supported Filament versions

| Filament Version | Plugin Version |
| ---------------- | -------------- |
| v3               | 0.0.6          |
| v4               | 2.0.2          |

## Installation

You can install the package via composer:

```bash
composer require solution-forest/simple-contact-form
```

For Filament v4, use the v2.0.1 version:

```bash
composer require solution-forest/simple-contact-form:^2.0.2
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

Simple Contact Form provides a Filament form that can be used outside the Filament panel, but it requires Filament styles. There are several ways to set this up depending on your environment:

1. **Filament v3 with Tailwind v3:**  
   Follow the official Filament installation instructions: [https://filamentphp.com/docs/3.x/forms/installation](https://filamentphp.com/docs/3.x/forms/installation).

2. **Filament v3 with Tailwind v4:**  
   Publish the built-in CSS assets with the following command:

    ```bash
    php artisan vendor:publish --tag="simple-contact-form-assets"
    ```

3. **Filament v4 (expects Tailwind v4):**  
   Add the following to your `app.css` or your stylesheet:

    ```css
    @import '../../vendor/filament/filament/resources/css/theme.css';

    @source '../../app/Filament/**/*';
    @source '../../resources/views/filament/**/*';
    ```

    Then build your assets:

    ```bash
    npm run build
    ```

## Configuration

You can customize the plugin's resources using the following options:

```php
SimpleContactFormPlugin::make()
        ->modelLabel('Custom Contact Form') // Singular label for the model
        ->pluralModelLabel('Custom Contact Forms') // Plural label for the model
        ->navigationLabel('My Contact Forms') // Label in the navigation menu
        ->navigationIcon('heroicon-o-envelope') // Icon for navigation
        ->navigationGroup('Communication') // Group in the navigation
        ->navigationSort(100) // Sort order in navigation
        ->navigationParentItem(null) // Parent navigation item (if any)
        ->slug('contact') // Custom route slug
        ->shouldSkipAuth(false) // Require authentication
        ->shouldRegisterNavigation(true) // Show in navigation
        ->hasTitleCaseModelLabel(true); // Use title case for labels
```

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

![image](https://github.com/user-attachments/assets/bdd693e7-222e-44cb-91b7-cc84627f7be4)
![image](https://github.com/user-attachments/assets/e0b31810-aa74-4901-af38-0c560db01307)
![image](https://github.com/user-attachments/assets/a8ca1358-2fac-41fa-a513-eab73d5fd015)
![image](https://github.com/user-attachments/assets/b79934f5-070e-4e82-a9cc-1cf0a0fc8501)
![image](https://github.com/user-attachments/assets/b92d9f24-431e-4acc-98c5-a6c4961b0231)
<img width="647" alt="image" src="https://github.com/user-attachments/assets/639b0333-ee95-45a1-b002-764f23904083" />

### Common Issues

**Emails not sending:**

-   Check your mail configuration in `.env`
-   Verify SMTP credentials
-   Check Laravel log files
-   smtp doc

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

-   [hayward](https://github.com/solutionforest)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

<p align="center"><a href="https://solutionforest.com" target="_blank"><img src="https://github.com/solutionforest/.github/blob/main/docs/images/sf.png?raw=true" width="150"></a></p>

## About Solution Forest

[Solution Forest](https://solutionforest.com) Web development agency based in Hong Kong. We help customers to solve their problems. We Love Open Soruces. 

We have built a collection of best-in-class products:

- [InspireCMS](https://inspirecms.net): A full-featured Laravel CMS with everything you need out of the box. Build smarter, ship faster with our complete content management solution.
- [Filaletter](https://filaletter.solutionforest.net): Filaletter - Filament Newsletter Plugin
- [Website CMS Management](https://filamentphp.com/plugins/solution-forest-cms-website): A hands-on Filament CMS plugin for those who prefer more manual control over their website content management.
