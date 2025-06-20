# Testing Guide for Simple Contact Form Plugin

This document provides guidance on how to run and fix the test suite for the Simple Contact Form plugin.

## Current Test Status

The test suite consists of both feature tests and unit tests. Some tests are currently skipped due to environment configuration issues or implementation differences. This guide explains how to address these issues to make all tests pass.

## Prerequisites

- PHP 8.0 or higher
- Composer
- Laravel 9.0+ or Filament 2.0+

## Running Tests

Run the tests with the following command:

```bash
vendor/bin/pest
```

## Fixing Skipped Tests

### Feature Tests: ContactFormSubmissionTest.php

1. **Encryption Key Issue**
   - Set an encryption key in your test environment. In TestCase.php, add:
   ```php
   protected function getEnvironmentSetUp($app)
   {
       parent::getEnvironmentSetUp($app);
       
       $app['config']->set('app.key', 'base64:'.base64_encode(Str::random(32)));
       // other configuration...
   }
   ```

2. **Missing validation_error_message Field**
   - Add this field to the migration in `database/migrations/create_simple_contact_form_table.php.stub`:
   ```php
   $table->text('validation_error_message')->nullable();
   ```
   - Or update the test to not expect this field

### Feature Tests: ContactFormResourceTest.php

1. **Authentication for Admin Tests**
   - Create a user factory in your test suite or use a mock:
   ```php
   // In TestCase.php or a separate file
   protected function actingAsAdmin()
   {
       // Create a mock user with admin privileges
       $user = new \Illuminate\Foundation\Auth\User();
       $user->id = 1;
       $user->name = 'Admin';
       $user->email = 'admin@example.com';
       
       return $this->actingAs($user);
   }
   ```
   
   - Update beforeEach in ContactFormResourceTest.php:
   ```php
   beforeEach(function () {
       $this->actingAsAdmin();
   });
   ```

### Unit Tests: FieldSchemaTest.php

1. **Implement FileUpload and Select Fields**
   - Review the actual implementation of file upload and select fields in the component
   - Update tests to match the current implementation
   - For non-matching field types, update test expectations

2. **Invalid Field Type Handling**
   - Update the test to match how the component actually handles invalid field types

### Unit Tests: EmailVariableReplacementTest.php

1. **Object Handling**
   - Implement proper toString conversion for objects in the `replaceVariables` method:
   ```php
   if (is_object($varValue)) {
       if (method_exists($varValue, '__toString')) {
           $varValue = (string) $varValue;
       } else {
           // Handle objects without toString method
           $varValue = get_class($varValue);
       }
   }
   ```

## Additional Test Coverage Ideas

1. **Test for multiple forms on same page**
2. **Test for conditional display of submit button**
3. **Test for proper file upload attachment to emails**
4. **Test for hook/event system implementation**
5. **Test compatibility with different Filament versions**

## CI/CD Integration

When implementing continuous integration:

1. Set up a proper testing database
2. Configure the encryption key in the CI environment
3. Add any necessary migrations
4. Consider using GitHub Actions or other CI services

By addressing these issues, you'll have a robust test suite that validates all aspects of the Simple Contact Form plugin.
