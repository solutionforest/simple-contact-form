<?php

namespace SolutionForest\SimpleContactForm\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SolutionForest\SimpleContactForm\Models\ContactForm;

class BeforeContactFormSubmission
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The contact form instance.
     *
     * @var ContactForm
     */
    public $contactForm;

    /**
     * The form data about to be submitted.
     *
     * @var array
     */
    public $formData;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ContactForm $contactForm, array $formData)
    {
        $this->contactForm = $contactForm;
        $this->formData = $formData;
    }
}
