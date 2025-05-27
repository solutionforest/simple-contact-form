<?php

namespace SolutionForest\SimpleContactForm\ContactFormResource\Pages;

use SolutionForest\SimpleContactForm\ContactFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactForm extends EditRecord
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
