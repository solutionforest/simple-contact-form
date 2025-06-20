<?php

namespace SolutionForest\SimpleContactForm\View\Components;

use Illuminate\View\Component;
use SolutionForest\SimpleContactForm\Models\ContactForm;

class SimpleContactForm extends Component
{
    /**
     * The contact form instance or ID.
     *
     * @var \SolutionForest\SimpleContactForm\Models\ContactForm|int|string
     */
    public $form;

    /**
     * Create a new component instance.
     *
     * @param  \SolutionForest\SimpleContactForm\Models\ContactForm|int|string  $form
     * @param  string|null  $class
     * @return void
     */
    public function __construct($form, $class = null)
    {
        if($form){
            $this->form = $form;
        }
        else {
            $this->form = null;
        }

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('simple-contact-form::components.simple-contact-form');
    }
}