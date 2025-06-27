<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactFormResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use SolutionForest\SimpleContactForm\Resources\ContactFormResource;

class EditContactForm extends EditRecord
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //

    //     return $data;
    // }
}
