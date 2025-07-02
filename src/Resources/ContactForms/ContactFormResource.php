<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactForms;
use Filament\Resources\Resource;
use SolutionForest\SimpleContactForm\Models\ContactForm;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\CreateContactForms;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Schemas\EditForm;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Schemas\ViewInfoList;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Tables\ContactFormTable;
use Filament\Tables\Table;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\EditContactForms;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\ListContactForms;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\ViewContactForms;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EditForm::configure($schema);
    }

    // public static function infolist(Schema $schema): Schema
    // {
    //     return ViewInfoList::configure($schema);
    // }

    public static function table(Table $table): Table
    {
        return ContactFormTable::configure($table);
    }


     public static function getPages(): array
    {
        return [
            'index' => ListContactForms::route('/'),
            'create' => CreateContactForms::route('/create'),
            'view' => ViewContactForms::route('/{record}'),
            'edit' => EditContactForms::route('/{record}/edit'),
        ];
    }




    


}