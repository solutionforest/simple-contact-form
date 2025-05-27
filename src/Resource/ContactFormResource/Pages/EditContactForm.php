<?php

namespace SolutionForest\SimpleContactForm\ContactFormResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SolutionForest\SimpleContactForm\ContactFormResource;

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
