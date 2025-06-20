<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use SolutionForest\SimpleContactForm\Models\ContactForm;

uses(RefreshDatabase::class);

test('can create a contact form', function () {
    // Arrange
    $formData = [
        'name' => 'Test Contact Form',
        'subject' => 'New Contact Submission',
        'to' => 'test@example.com',
        'content' => [
            [
                'items' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Full Name',
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'email',
                        'label' => 'Email Address',
                        'email' => true,
                        'required' => true,
                    ],
                ],
            ],
        ],
        'email_body' => 'Name: {{name}}<br>Email: {{email}}',
        'success_message' => 'Thank you for your message!',
        'error_message' => 'There was an error sending your message.',
    ];

    // Act
    $form = ContactForm::create($formData);

    // Assert
    expect($form)->toBeInstanceOf(ContactForm::class)
        ->and($form->name)->toBe('Test Contact Form')
        ->and($form->subject)->toBe('New Contact Submission')
        ->and($form->to)->toBe('test@example.com')
        ->and($form->content)->toBe($formData['content'])
        ->and($form->email_body)->toBe('Name: {{name}}<br>Email: {{email}}')
        ->and($form->success_message)->toBe('Thank you for your message!')
        ->and($form->error_message)->toBe('There was an error sending your message.');

    // Database assertion
    $this->assertDatabaseHas('simple_contact_form_table', [
        'name' => 'Test Contact Form',
        'subject' => 'New Contact Submission',
        'to' => 'test@example.com',
    ]);
});

test('can retrieve a contact form', function () {
    // Arrange
    $form = ContactForm::create([
        'name' => 'Retrievable Form',
        'subject' => 'Retrieve Test',
        'to' => 'retrieve@example.com',
        'content' => [
            [
                'items' => [
                    [
                        'type' => 'text',
                        'name' => 'name',
                        'label' => 'Full Name',
                        'required' => true,
                    ],
                ],
            ],
        ],
        'email_body' => 'Name: {{name}}',
        'success_message' => 'Retrieved successfully!',
        'error_message' => 'Retrieval error.',
    ]);

    // Act
    $retrievedForm = ContactForm::find($form->id);

    // Assert
    expect($retrievedForm)->toBeInstanceOf(ContactForm::class)
        ->and($retrievedForm->name)->toBe('Retrievable Form')
        ->and($retrievedForm->subject)->toBe('Retrieve Test')
        ->and($retrievedForm->to)->toBe('retrieve@example.com');
});

test('can update a contact form', function () {
    // Arrange
    $form = ContactForm::create([
        'name' => 'Original Form',
        'subject' => 'Original Subject',
        'to' => 'original@example.com',
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
        'email_body' => 'Original body',
        'success_message' => 'Original success',
        'error_message' => 'Original error',
    ]);

    // Act
    $form->update([
        'name' => 'Updated Form',
        'subject' => 'Updated Subject',
        'to' => 'updated@example.com',
        'email_body' => 'Updated body',
        'success_message' => 'Updated success',
        'error_message' => 'Updated error',
    ]);

    // Refresh the model to get the updated data
    $form->refresh();

    // Assert
    expect($form->name)->toBe('Updated Form')
        ->and($form->subject)->toBe('Updated Subject')
        ->and($form->to)->toBe('updated@example.com')
        ->and($form->email_body)->toBe('Updated body')
        ->and($form->success_message)->toBe('Updated success')
        ->and($form->error_message)->toBe('Updated error');

    // Database assertion
    $this->assertDatabaseHas('simple_contact_form_table', [
        'id' => $form->id,
        'name' => 'Updated Form',
        'subject' => 'Updated Subject',
        'to' => 'updated@example.com',
    ]);
});

test('can delete a contact form', function () {
    // Arrange
    $form = ContactForm::create([
        'name' => 'Form to Delete',
        'subject' => 'Delete Subject',
        'to' => 'delete@example.com',
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
        'email_body' => 'Delete body',
        'success_message' => 'Delete success',
        'error_message' => 'Delete error',
    ]);

    $formId = $form->id;

    // Act
    $result = $form->delete();

    // Assert
    expect($result)->toBeTrue();

    // Database assertion
    $this->assertDatabaseMissing('simple_contact_form_table', [
        'id' => $formId,
    ]);
});

test('can handle form content with multiple fields and sections', function () {
    // Arrange
    $complexContent = [
        [
            'items' => [
                [
                    'type' => 'text',
                    'name' => 'name',
                    'label' => 'Full Name',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'name' => 'email',
                    'label' => 'Email Address',
                    'email' => true,
                    'required' => true,
                ],
            ],
        ],
        [
            'items' => [
                [
                    'type' => 'textarea',
                    'name' => 'message',
                    'label' => 'Your Message',
                    'required' => true,
                ],
                [
                    'type' => 'checkbox',
                    'name' => 'agreement',
                    'label' => 'I agree to terms',
                    'required' => true,
                ],
            ],
        ],
        [
            'items' => [
                [
                    'type' => 'select',
                    'name' => 'topic',
                    'label' => 'Topic',
                    'required' => true,
                    'options' => [
                        ['key' => 'support', 'label' => 'Support'],
                        ['key' => 'sales', 'label' => 'Sales'],
                    ],
                ],
            ],
        ],
    ];

    $form = ContactForm::create([
        'name' => 'Complex Form',
        'subject' => 'Complex Subject',
        'to' => 'complex@example.com',
        'content' => $complexContent,
        'email_body' => 'Complex body with multiple fields',
        'success_message' => 'Complex success',
        'error_message' => 'Complex error',
    ]);

    // Act
    $retrievedForm = ContactForm::find($form->id);

    // Assert
    expect($retrievedForm->content)->toBe($complexContent)
        ->and(count($retrievedForm->content))->toBe(3)
        ->and(count($retrievedForm->content[0]['items']))->toBe(2)
        ->and(count($retrievedForm->content[1]['items']))->toBe(2)
        ->and(count($retrievedForm->content[2]['items']))->toBe(1);

    // Check specific field
    expect($retrievedForm->content[2]['items'][0]['type'])->toBe('select')
        ->and($retrievedForm->content[2]['items'][0]['name'])->toBe('topic')
        ->and(count($retrievedForm->content[2]['items'][0]['options']))->toBe(2);
});
