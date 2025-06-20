<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use SolutionForest\SimpleContactForm\Events\BeforeContactFormSubmission;
use SolutionForest\SimpleContactForm\Events\ContactFormSubmitted;
use SolutionForest\SimpleContactForm\Models\ContactForm;

uses(RefreshDatabase::class);

test('form events are properly structured', function () {
    // Test BeforeContactFormSubmission event structure
    $form = new ContactForm([
        'name' => 'Test Form',
        'subject' => 'Test Subject',
        'to' => 'test@example.com',
    ]);

    $formData = ['name' => 'John Doe', 'email' => 'john@example.com'];

    $beforeEvent = new BeforeContactFormSubmission($form, $formData);

    expect($beforeEvent->contactForm)->toBe($form)
        ->and($beforeEvent->formData)->toBe($formData);

    // Test ContactFormSubmitted event structure
    $submittedEvent = new ContactFormSubmitted($form, $formData);

    expect($submittedEvent->contactForm)->toBe($form)
        ->and($submittedEvent->formData)->toBe($formData);
});
