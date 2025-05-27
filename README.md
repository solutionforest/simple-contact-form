# Simple Contact Form - FilamentPHP Plugin

A lightweight, customizable contact form plugin for FilamentPHP that provides an easy-to-use alternative to Contact Form 7. Build and manage contact forms with a simple, intuitive interface directly from your Filament admin panel.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/solution-forest/simple-contact-form.svg?style=flat-square)](https://packagist.org/packages/solution-forest/simple-contact-form)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/solutionforest/simple-contact-form/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/solutionforest/simple-contact-form/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/solutionforest/simple-contact-form/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/solutionforest/simple-contact-form/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/solution-forest/simple-contact-form.svg?style=flat-square)](https://packagist.org/packages/solution-forest/simple-contact-form)


## Features

- 🚀 **Easy Installation** - Get up and running in minutes
- 📝 **Form Builder** - Drag-and-drop form field management
- 📧 **Email Notifications** - Automatic email sending on form submission
- 💾 **Database Storage** - Store form submissions in your database
- 🎨 **Customizable Templates** - Full control over form appearance
- 🔒 **Spam Protection** - Built-in honeypot and optional reCAPTCHA support
- 📊 **Submission Management** - View and manage form submissions in Filament
- 🌐 **Multi-language Support** - Translatable form fields and messages
- 📱 **Responsive Design** - Mobile-friendly forms out of the box
- 🔧 **Developer Friendly** - Extensible with hooks and events

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

You can publish the config file with:

```bash
php artisan vendor:publish --tag="simple-contact-form-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="simple-contact-form-views"
```

## Configuration

The configuration file will be published to `config/simple-contact-form.php`:

```php
return [
    'storage' => [
        'enabled' => true,
        'table' => 'contact_form_submissions',
    ],
    
    'email' => [
        'enabled' => true,
        'from' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME'),
    ],
    
    'spam_protection' => [
        'honeypot' => true,
        'recaptcha' => false,
        'recaptcha_site_key' => env('RECAPTCHA_SITE_KEY'),
        'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY'),
    ],
    
    'file_uploads' => [
        'enabled' => true,
        'disk' => 'public',
        'max_size' => 5120, // KB
        'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'png'],
    ],
];
```

## Usage

### Creating a Form

1. Navigate to the "Contact Forms" section in your Filament admin panel
2. Click "Create Form"
3. Add fields using the form builder:
   - Text Input
   - Email Input
   - Textarea
   - Select Dropdown
   - Radio Buttons
   - Checkboxes
   - File Upload
   - Hidden Fields

### Displaying Forms

Use the Blade component in your views:

```blade
<x-simple-contact-form :form="contact" />
```

Or use the shortcode in your content:

```
[simple-contact-form id="1"]
```

### Handling Submissions

Submissions are automatically stored in the database and can be viewed in the Filament admin panel.

To handle submissions programmatically:

```php
use SolutionForest\SimpleContactForm\Events\FormSubmitted;

class ContactFormListener
{
    public function handle(FormSubmitted $event)
    {
        $form = $event->form;
        $data = $event->data;
        
        // Custom processing logic
    }
}
```

### Customizing Email Templates

Publish and modify the email templates:

```bash
php artisan vendor:publish --tag="simple-contact-form-emails"
```

Edit the templates in `resources/views/vendor/simple-contact-form/emails/`

## Advanced Features

### Custom Field Types

Register custom field types:

```php
use SolutionForest\SimpleContactForm\Facades\SimpleContactForm;

SimpleContactForm::registerFieldType('custom_field', CustomFieldType::class);
```

### Validation Rules

Add custom validation rules to fields:

```php
$field->rules(['required', 'min:5', 'custom_rule']);
```

### Conditional Logic

Show/hide fields based on user input:

```php
$field->showIf('other_field', 'equals', 'specific_value');
```

### Multi-step Forms

Create multi-step forms with progress indicators:

```php
$form->steps([
    'personal_info' => 'Personal Information',
    'contact_details' => 'Contact Details',
    'message' => 'Your Message',
]);
```

## API Reference

### Form Model

```php
$form = SimpleContactForm::create([
    'name' => 'Contact Us',
    'description' => 'Get in touch with our team',
    'recipient_email' => 'contact@example.com',
]);

$form->addField([
    'type' => 'text',
    'name' => 'full_name',
    'label' => 'Full Name',
    'required' => true,
]);
```

### Available Hooks

```php
// Before form submission
SimpleContactForm::beforeSubmit(function ($form, $data) {
    // Modify data before processing
});

// After successful submission
SimpleContactForm::afterSubmit(function ($form, $submission) {
    // Additional processing
});
```

## Troubleshooting

### Common Issues

**Forms not displaying:**
- Ensure you've published the assets: `php artisan vendor:publish --tag="simple-contact-form-assets"`
- Clear cache: `php artisan cache:clear`

**Emails not sending:**
- Check your mail configuration in `.env`
- Verify SMTP credentials
- Check Laravel log files

**File uploads not working:**
- Ensure storage directory is writable
- Check file size limits in PHP configuration
- Verify allowed file types in config

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
