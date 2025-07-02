<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactForms\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use SolutionForest\SimpleContactForm\Resources\ContactForms\ContactFormResource;

class EditContactForms extends EditRecord
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
