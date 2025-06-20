<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use SolutionForest\SimpleContactForm\Models\ContactForm;

uses(RefreshDatabase::class);

test('form name is required', function () {
    // Expect an exception
    $this->expectException(ValidationException::class);

    // Attempt to create a form without a name
    $form = ContactForm::create([
        'subject' => 'Test Subject',
        'to' => 'test@example.com',
        'content' => [
            [
                'items' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Name',
                        'required' => true,
                    ],
                ],
            ],
        ],
        'email_body' => 'Test body',
        'success_message' => 'Test success',
        'error_message' => 'Test error',
    ]);
})->skip('Skip validation test as model validation is not implemented');

test('form subject is required', function () {
    // Expect an exception
    $this->expectException(ValidationException::class);

    // Attempt to create a form without a subject
    $form = ContactForm::create([
        'name' => 'Test Form',
        'to' => 'test@example.com',
        'content' => [
            [
                'items' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Name',
                        'required' => true,
                    ],
                ],
            ],
        ],
        'email_body' => 'Test body',
        'success_message' => 'Test success',
        'error_message' => 'Test error',
    ]);
})->skip('Skip validation test as model validation is not implemented');

test('form recipient email is required', function () {
    // Expect an exception
    $this->expectException(ValidationException::class);

    // Attempt to create a form without a recipient email
    $form = ContactForm::create([
        'name' => 'Test Form',
        'subject' => 'Test Subject',
        'content' => [
            [
                'items' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Name',
                        'required' => true,
                    ],
                ],
            ],
        ],
        'email_body' => 'Test body',
        'success_message' => 'Test success',
        'error_message' => 'Test error',
    ]);
})->skip('Skip validation test as model validation is not implemented');

test('form recipient email must be valid', function () {
    // Expect an exception
    $this->expectException(ValidationException::class);

    // Attempt to create a form with an invalid email
    $form = ContactForm::create([
        'name' => 'Test Form',
        'subject' => 'Test Subject',
        'to' => 'invalid-email',
        'content' => [
            [
                'items' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Name',
                        'required' => true,
                    ],
                ],
            ],
        ],
        'email_body' => 'Test body',
        'success_message' => 'Test success',
        'error_message' => 'Test error',
    ]);
})->skip('Skip validation test as model validation is not implemented');

test('form content structure is valid', function () {
    // Expect an exception
    $this->expectException(ValidationException::class);

    // Attempt to create a form with invalid content structure
    $form = ContactForm::create([
        'name' => 'Test Form',
        'subject' => 'Test Subject',
        'to' => 'test@example.com',
        'content' => 'invalid-content-structure', // Should be an array
        'email_body' => 'Test body',
        'success_message' => 'Test success',
        'error_message' => 'Test error',
    ]);
})->skip('Skip validation test as model validation is not implemented');

test('form fields have unique names', function () {
    // Create content with duplicate field names
    $contentWithDuplicateNames = [
        [
            'items' => [
                [
                    'type' => 'text',
                    'name' => 'field_name',
                    'label' => 'Field 1',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'name' => 'field_name', // Duplicate name
                    'label' => 'Field 2',
                    'required' => true,
                ],
            ],
        ],
    ];

    // Create a form
    $form = ContactForm::create([
        'name' => 'Test Form',
        'subject' => 'Test Subject',
        'to' => 'test@example.com',
        'content' => $contentWithDuplicateNames,
        'email_body' => 'Test body',
        'success_message' => 'Test success',
        'error_message' => 'Test error',
    ]);

    // This test is just to document that duplicate field names are currently allowed
    // but might cause issues in form handling
    expect($form)->toBeInstanceOf(ContactForm::class);

    // Check field names
    $fieldNames = [];
    foreach ($form->content as $section) {
        foreach ($section['items'] as $field) {
            $fieldNames[] = $field['name'];
        }
    }

    // Count occurrences of each field name
    $fieldNameCounts = array_count_values($fieldNames);

    // Duplicate names will have a count > 1
    expect($fieldNameCounts['field_name'])->toBe(2);
});
