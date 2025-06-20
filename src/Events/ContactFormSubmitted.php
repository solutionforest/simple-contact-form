<?php

namespace SolutionForest\SimpleContactForm\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SolutionForest\SimpleContactForm\Models\ContactForm;

class ContactFormSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The contact form instance.
     *
     * @var \SolutionForest\SimpleContactForm\Models\ContactForm
     */
    public $contactForm;

    /**
     * The submitted form data.
     *
     * @var array
     */
    public $formData;

    /**
     * Create a new event instance.
     *
     * @param  \SolutionForest\SimpleContactForm\Models\ContactForm  $contactForm
     * @param  array  $formData
     * @return void
     */
    public function __construct(ContactForm $contactForm, array $formData)
    {
        $this->contactForm = $contactForm;
        $this->formData = $formData;
    }
}
