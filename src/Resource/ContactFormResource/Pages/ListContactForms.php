<?php

namespace SolutionForest\SimpleContactForm\ContactFormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use SolutionForest\SimpleContactForm\ContactFormResource;

class ListContactForms extends ListRecords
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
