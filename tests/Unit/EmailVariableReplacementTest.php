<?php

use SolutionForest\SimpleContactForm\Livewire\ContactFormComponent;

test('replaces simple variables in text', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'Hello {{name}}, your email is {{email}}.';
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ];
    
    $result = $method->invoke($component, $template, $data);
    
    $expected = 'Hello John Doe, your email is john@example.com.';
    expect($result)->toBe($expected);
});

test('handles missing variables', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'Hello {{name}}, your email is {{email}} and your phone is {{phone}}.';
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        // phone is missing
    ];
    
    $result = $method->invoke($component, $template, $data);
    
    $expected = 'Hello John Doe, your email is john@example.com and your phone is .';
    expect($result)->toBe($expected);
});

test('handles array values', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'Selected options: {{options}}';
    $data = [
        'options' => ['Option 1', 'Option 2', 'Option 3'],
    ];
    
    $result = $method->invoke($component, $template, $data);
    
    $expected = 'Selected options: Option 1, Option 2, Option 3';
    expect($result)->toBe($expected);
});

test('handles array with name key', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'File uploaded: {{attachment}}';
    $data = [
        'attachment' => ['name' => 'document.pdf', 'size' => 1024],
    ];
    
    $result = $method->invoke($component, $template, $data);
    
    $expected = 'File uploaded: document.pdf';
    expect($result)->toBe($expected);
});

test('returns original text if no variables', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'This is a plain text with no variables.';
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ];
    
    $result = $method->invoke($component, $template, $data);
    
    expect($result)->toBe($template);
});

test('handles multiple occurrences of same variable', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'Hello {{name}}. Welcome back, {{name}}! Your email remains {{email}}.';
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ];
    
    $result = $method->invoke($component, $template, $data);
    
    $expected = 'Hello John Doe. Welcome back, John Doe! Your email remains john@example.com.';
    expect($result)->toBe($expected);
});

test('tracks referenced fields', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'Name: {{name}}, Email: {{email}}, Phone: {{phone}}';
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '123-456-7890',
        'address' => '123 Main St', // This should not be tracked as it's not in the template
    ];
    
    $method->invoke($component, $template, $data);
    
    // Check that the referencedFields property contains the fields used in the template
    expect($component->referencedFields)->toBe(['name', 'email', 'phone'])
        ->and($component->referencedFields)->not->toContain('address');
});

test('handles whitespace in variables', function () {
    $component = new ContactFormComponent();
    
    // Use reflection to access the protected replaceVariables method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('replaceVariables');
    $method->setAccessible(true);
    
    $template = 'Hello {{  name  }}, your email is {{ email }}.';
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ];
    
    $result = $method->invoke($component, $template, $data);
    
    $expected = 'Hello John Doe, your email is john@example.com.';
    expect($result)->toBe($expected);
});
