<?php

namespace SolutionForest\SimpleContactForm\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \SolutionForest\SimpleContactForm\SimpleContactForm
 */
class SimpleContactForm extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \SolutionForest\SimpleContactForm\SimpleContactForm::class;
    }
}
