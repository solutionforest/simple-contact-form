<?php

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use SolutionForest\SimpleContactForm\Livewire\ContactFormComponent;

test('can create text field schema', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'test_field',
        'label' => 'Test Field',
        'required' => true,
        'placeholder' => 'Enter test value',
    ];

    $schema = $method->invoke($component, 'text', $field);

    expect($schema)->toBeInstanceOf(TextInput::class)
        ->and($schema->getName())->toBe('test_field')
        ->and($schema->getLabel())->toBe('Test Field')
        ->and($schema->getPlaceholder())->toBe('Enter test value');

    // Testing required status separately to handle differences in implementation
    expect($schema->isRequired())->toBeTrue();
});

test('can create email field schema', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'email_field',
        'label' => 'Email Field',
        'required' => true,
        'placeholder' => 'Enter your email',
        'email' => true,
    ];

    $schema = $method->invoke($component, 'text', $field);

    // Email fields are implemented as TextInput with email=true
    expect($schema)->toBeInstanceOf(TextInput::class)
        ->and($schema->getName())->toBe('email_field')
        ->and($schema->getLabel())->toBe('Email Field')
        ->and($schema->getPlaceholder())->toBe('Enter your email');
});

test('can create textarea field schema', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'message_field',
        'label' => 'Message Field',
        'required' => true,
        'min_length' => 10,
        'max_length' => 500,
    ];

    $schema = $method->invoke($component, 'textarea', $field);

    expect($schema)->toBeInstanceOf(Textarea::class)
        ->and($schema->getName())->toBe('message_field')
        ->and($schema->getLabel())->toBe('Message Field');
});

test('can create checkbox field schema', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'agreement_field',
        'label' => 'I agree to the terms',
        'required' => true,
    ];

    $schema = $method->invoke($component, 'checkbox', $field);

    expect($schema)->toBeInstanceOf(Checkbox::class)
        ->and($schema->getName())->toBe('agreement_field')
        ->and($schema->getLabel())->toBe('I agree to the terms');
});

test('can create select field schema', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'topic_field',
        'label' => 'Topic',
        'required' => true,
        'options' => [
            ['key' => 'support', 'label' => 'Support'],
            ['key' => 'sales', 'label' => 'Sales'],
            ['key' => 'general', 'label' => 'General Inquiry'],
        ],
    ];

    $schema = $method->invoke($component, 'select', $field);

    expect($schema)->toBeInstanceOf(Select::class)
        ->and($schema->getName())->toBe('topic_field')
        ->and($schema->getLabel())->toBe('Topic');
});

test('can create radio field schema', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'radio_field',
        'label' => 'Radio Options',
        'required' => true,
        'options' => [
            ['key' => 'option1', 'label' => 'Option 1'],
            ['key' => 'option2', 'label' => 'Option 2'],
        ],
    ];

    $schema = $method->invoke($component, 'radio', $field);

    expect($schema)->toBeInstanceOf(Radio::class)
        ->and($schema->getName())->toBe('radio_field')
        ->and($schema->getLabel())->toBe('Radio Options');
});

test('can create date field schema', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'date_field',
        'label' => 'Date Field',
        'required' => true,
        'placeholder' => 'Select date',
        'date_format' => 'Y-m-d',
    ];

    $schema = $method->invoke($component, 'date', $field);

    expect($schema)->toBeInstanceOf(DatePicker::class)
        ->and($schema->getName())->toBe('date_field')
        ->and($schema->getLabel())->toBe('Date Field')
        ->and($schema->getPlaceholder())->toBe('Select date');

    // Test with time included
    $fieldWithTime = [
        'name' => 'datetime_field',
        'label' => 'Date Time Field',
        'required' => true,
        'include_time' => true,
        'date_format' => 'Y-m-d H:i',
    ];

    $schemaWithTime = $method->invoke($component, 'date', $fieldWithTime);

    expect($schemaWithTime)->toBeInstanceOf(DateTimePicker::class)
        ->and($schemaWithTime->getName())->toBe('datetime_field')
        ->and($schemaWithTime->getLabel())->toBe('Date Time Field');
});

test('returns default field for invalid field type', function () {
    $component = new ContactFormComponent;

    // Use reflection to access the protected getFieldSchema method
    $reflectionClass = new \ReflectionClass($component);
    $method = $reflectionClass->getMethod('getFieldSchema');
    $method->setAccessible(true);

    $field = [
        'name' => 'invalid_field',
        'label' => 'Invalid Field',
        'placeholder' => 'This is a placeholder',
    ];

    $schema = $method->invoke($component, 'invalid_type', $field);

    // The implementation returns a TextInput for unknown field types
    expect($schema)->toBeInstanceOf(TextInput::class)
        ->and($schema->getName())->toBe('invalid_field')
        ->and($schema->getLabel())->toBe('Invalid Field')
        ->and($schema->getPlaceholder())->toBe('This is a placeholder');
});
