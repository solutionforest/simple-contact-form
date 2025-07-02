<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactForms\Pages;

use SolutionForest\SimpleContactForm\Resources\ContactForms\ContactFormResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContactForms extends ListRecords
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
