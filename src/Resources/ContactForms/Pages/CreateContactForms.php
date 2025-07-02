<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactForms\Pages;


use Filament\Resources\Pages\CreateRecord;
use SolutionForest\SimpleContactForm\Resources\ContactForms\ContactFormResource;

class CreateContactForms extends CreateRecord
{
    protected static string $resource = ContactFormResource::class;
}
