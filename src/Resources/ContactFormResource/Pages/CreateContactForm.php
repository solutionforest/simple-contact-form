<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactFormResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use SolutionForest\SimpleContactForm\Resources\ContactFormResource;

class CreateContactForm extends CreateRecord
{
    protected static string $resource = ContactFormResource::class;
}
