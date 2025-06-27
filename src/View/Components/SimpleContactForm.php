<?php

namespace SolutionForest\SimpleContactForm\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SolutionForest\SimpleContactForm\Models\ContactForm;

class SimpleContactForm extends Component
{
    /**
     * The contact form instance or ID.
     *
     * @var ContactForm|int|string|null
     */
    public $form;

    /**
     * Create a new component instance.
     *
     * @param  ContactForm|int|string  $form
     * @return void
     */
    public function __construct($form)
    {
        if ($form) {
            $this->form = $form;
        } else {
            $this->form = null;
        }

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|Closure|string
     */
    public function render()
    {
        return view('simple-contact-form::components.simple-contact-form');
    }
}
