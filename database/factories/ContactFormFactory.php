<?php

namespace SolutionForest\SimpleContactForm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SolutionForest\SimpleContactForm\Models\ContactForm;

class ContactFormFactory extends Factory
{
    protected $model = ContactForm::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'subject' => $this->faker->sentence,
            'to' => $this->faker->email,
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
                        [
                            'type' => 'textarea',
                            'name' => 'message',
                            'label' => 'Your Message',
                            'required' => true,
                        ],
                    ],
                ],
            ],
            'email_body' => 'Name: {{name}}<br>Email: {{email}}<br>Message: {{message}}',
            'success_message' => 'Thank you for your message!',
            'error_message' => 'There was an error sending your message.',
        ];
    }
}
