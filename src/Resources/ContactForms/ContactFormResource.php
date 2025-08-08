<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactForms;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Panel; 
use Filament\Tables\Table;
use SolutionForest\SimpleContactForm\Models\ContactForm;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\CreateContactForms;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\EditContactForms;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\ListContactForms;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Pages\ViewContactForms;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Schemas\EditForm;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Schemas\ViewInfoList;
use SolutionForest\SimpleContactForm\Resources\ContactForms\Tables\ContactFormTable;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;
    
    // protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public static function getSlug(?Panel $panel = null): string
    {
        return static::getPlugin()->getSlug();
    }
    public static function form(Schema $schema): Schema
    {
        return EditForm::configure($schema);
    }

    public static function shouldSkipAuthorization(): bool
    {
        return static::getPlugin()->getShouldSkipAuth();
    }

    public static function getModelLabel(): string
    {
        return static::getPlugin()->getModelLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return static::getPlugin()->getPluralModelLabel();
    }

    public static function hasTitleCaseModelLabel(): bool
    {
        return static::getPlugin()->getHasTitleCaseModelLabel();
    }

    public static function getNavigationGroup(): ?string
    {
        return static::getPlugin()->getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return static::getPlugin()->getNavigationLabel();
    }

    public static function getNavigationIcon(): string
    {
        return static::getPlugin()->getNavigationIcon();
    }

    public static function getNavigationSort(): int
    {
        return static::getPlugin()->getNavigationSort();
    }

    public static function getNavigationParentItem(): ?string
    {
        return static::getPlugin()->getNavigationParentItem();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::getPlugin()->getShouldRegisterNavigation();
    }

    protected static function getPlugin(): \SolutionForest\SimpleContactForm\SimpleContactFormPlugin
    {
        return \SolutionForest\SimpleContactForm\SimpleContactFormPlugin::get();
    }

    public static function table(Table $table): Table
    {
        return ContactFormTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactForms::route('/'),
            'create' => CreateContactForms::route('/create'),
            // 'view' => ViewContactForms::route('/{record}'),
            'edit' => EditContactForms::route('/{record}/edit'),
        ];
    }
}
