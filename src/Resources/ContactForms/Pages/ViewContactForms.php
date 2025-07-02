<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactForms\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use SolutionForest\SimpleContactForm\Resources\ContactForms\ContactFormResource;

class ViewContactForms extends ViewRecord
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
