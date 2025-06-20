<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use SolutionForest\SimpleContactForm\Models\ContactForm;

uses(RefreshDatabase::class);

test('can duplicate a contact form', function () {
    // Arrange
    $originalForm = ContactForm::create([
        'name' => 'Original Form',
        'subject' => 'Original Subject',
        'to' => 'original@example.com',
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
        'email_body' => 'Original body with {{name}} and {{email}}',
        'success_message' => 'Original success',
        'error_message' => 'Original error',
    ]);

    // Act - Create a duplicate by copying attributes and saving as new
    $duplicateData = $originalForm->toArray();
    unset($duplicateData['id']);
    $duplicateData['name'] = 'Duplicate of ' . $originalForm->name;

    $duplicateForm = new ContactForm;
    $duplicateForm->fill($duplicateData);
    $duplicateForm->save();

    // Assert
    expect($duplicateForm->id)->not->toBe($originalForm->id)
        ->and($duplicateForm->name)->toBe('Duplicate of Original Form')
        ->and($duplicateForm->subject)->toBe($originalForm->subject)
        ->and($duplicateForm->to)->toBe($originalForm->to)
        ->and($duplicateForm->content)->toBe($originalForm->content)
        ->and($duplicateForm->email_body)->toBe($originalForm->email_body)
        ->and($duplicateForm->success_message)->toBe($originalForm->success_message)
        ->and($duplicateForm->error_message)->toBe($originalForm->error_message);

    // Database assertion
    $this->assertDatabaseCount('simple_contact_form_table', 2);
});

test('can bulk delete contact forms', function () {
    // Arrange - Create multiple forms
    $forms = [];
    for ($i = 1; $i <= 5; $i++) {
        $forms[] = ContactForm::create([
            'name' => "Test Form {$i}",
            'subject' => "Subject {$i}",
            'to' => "test{$i}@example.com",
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
            'email_body' => "Body {$i}",
            'success_message' => "Success {$i}",
            'error_message' => "Error {$i}",
        ]);
    }

    // Verify initial count
    $this->assertDatabaseCount('simple_contact_form_table', 5);

    // Select forms to delete (forms 1, 3, 5)
    $idsToDelete = [$forms[0]->id, $forms[2]->id, $forms[4]->id];

    // Act - Perform bulk delete
    ContactForm::whereIn('id', $idsToDelete)->delete();

    // Assert
    $this->assertDatabaseCount('simple_contact_form_table', 2);

    // Check which forms remain
    $remainingForms = ContactForm::all();
    $remainingIds = $remainingForms->pluck('id')->toArray();

    expect($remainingIds)->toContain($forms[1]->id)
        ->and($remainingIds)->toContain($forms[3]->id)
        ->and($remainingIds)->not->toContain($forms[0]->id)
        ->and($remainingIds)->not->toContain($forms[2]->id)
        ->and($remainingIds)->not->toContain($forms[4]->id);
});

test('can search contact forms by name or email', function () {
    // Arrange - Create forms with different names and emails
    ContactForm::create([
        'name' => 'Contact Us Form',
        'subject' => 'Website Contact',
        'to' => 'contact@example.com',
        'content' => [],
        'email_body' => 'Contact body',
        'success_message' => 'Contact success',
        'error_message' => 'Contact error',
    ]);

    ContactForm::create([
        'name' => 'Feedback Form',
        'subject' => 'Website Feedback',
        'to' => 'feedback@example.com',
        'content' => [],
        'email_body' => 'Feedback body',
        'success_message' => 'Feedback success',
        'error_message' => 'Feedback error',
    ]);

    ContactForm::create([
        'name' => 'Support Request',
        'subject' => 'Support Ticket',
        'to' => 'support@example.com',
        'content' => [],
        'email_body' => 'Support body',
        'success_message' => 'Support success',
        'error_message' => 'Support error',
    ]);

    // Act & Assert - Search by name
    $contactForms = ContactForm::where('name', 'like', '%Contact%')->get();
    expect($contactForms)->toHaveCount(1)
        ->and($contactForms->first()->name)->toBe('Contact Us Form');

    // Act & Assert - Search by email
    $supportForms = ContactForm::where('to', 'like', '%support%')->get();
    expect($supportForms)->toHaveCount(1)
        ->and($supportForms->first()->name)->toBe('Support Request');

    // Act & Assert - Search for multiple results
    $allForms = ContactForm::where(function ($query) {
        $query->where('name', 'like', '%Form%')
            ->orWhere('to', 'like', '%@example.com%');
    })->get();

    expect($allForms)->toHaveCount(3);
});

test('can add extra attributes to a form', function () {
    // Arrange
    $extraAttributes = 'class="custom-form","data-id"="123"';

    // Act
    $form = ContactForm::create([
        'name' => 'Form With Attributes',
        'subject' => 'Form Attributes Test',
        'to' => 'attributes@example.com',
        'content' => [],
        'email_body' => 'Test body',
        'success_message' => 'Test success',
        'error_message' => 'Test error',
        'extra_attributes' => $extraAttributes,
    ]);

    // Refresh the model to ensure data is loaded correctly
    $form->refresh();

    // Assert
    expect($form->extra_attributes)->toBe($extraAttributes);

    // Database assertion
    $this->assertDatabaseHas('simple_contact_form_table', [
        'name' => 'Form With Attributes',
        'extra_attributes' => $extraAttributes,
    ]);
});
