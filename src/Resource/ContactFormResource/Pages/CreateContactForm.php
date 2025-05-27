<?php

namespace SolutionForest\SimpleContactForm\ContactFormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use SolutionForest\SimpleContactForm\ContactFormResource;

class CreateContactForm extends CreateRecord
{
    protected static string $resource = ContactFormResource::class;
}
