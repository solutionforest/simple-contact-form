<?php

namespace SolutionForest\SimpleContactForm\ContactFormResource\Pages;

use SolutionForest\SimpleContactForm\ContactFormResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContactForm extends CreateRecord
{
    protected static string $resource = ContactFormResource::class;
}
