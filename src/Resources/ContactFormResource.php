<?php

namespace SolutionForest\SimpleContactForm\Resources;

use Filament\Forms;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\TestStatus\Notice;
use SolutionForest\SimpleContactForm\Models\ContactForm;
use SolutionForest\SimpleContactForm\Resources\ContactFormResource\Pages;
use SolutionForest\SimpleContactForm\Forms\Components\CustomModal;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as FormAction;
class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Template')
                            ->schema([
                                Forms\Components\Actions::make(self::getModelaction()),
                                    
                                Textarea::make('content')
                                    ->label('Content')
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText('You can use the tags to define the type of input you want to use.')
                                    ->rows(10)
                                    ->cols(20),
                                

                            ]),
                            
                        Tabs\Tab::make('Mail')
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('from')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('to')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        Tabs\Tab::make('Email body')
                            ->schema([

                                Placeholder::make('available_variables')
                                    ->label('Available variables')
                                    ->content(function (Forms\Get $get) {
                                        $content = $get('content') ?? [];
                                        // $variables = [];
                                        // foreach ($content as $field) {
                                        //     if (isset($field['label'])) {
                                        //         $key = \Illuminate\Support\Str::slug($field['label'], '_');
                                        //         $variables[] = "{{$key}}";
                                        //     }
                                        // }
                                        // if (empty($variables)) {
                                        //     return new HtmlString('<div style="text-align: center; padding: 10px; color: #666;">No variables are currently defined</div>');
                                        // }
                                        // $html = '<div style="display: flex; flex-wrap: wrap; gap: 8px;">';
                                        // foreach ($variables as $var) {
                                        //     $html .= '<div
                                        //             onclick="navigator.clipboard.writeText(\'{' . $var . '}\');  " 
                                        //             style="background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 4px; padding: 4px 8px; cursor: pointer; font-family: monospace; position: relative;"
                                                   
                                        //             onmouseover="this.style.backgroundColor=\'#e5e7eb\'" 
                                        //             onmouseout="this.style.backgroundColor=\'#f3f4f6\'">
                                        //             ' . $var . '
                                                   
                                        //         </div>';
                                        // }

                                        // return new HtmlString($html);
                                    })
                                    ->columnSpanFull(),
                                MarkdownEditor::make('email_body')
                                    ->label('Email body')
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText('You can use the variables like : user name : {{name}}'),
                            ]),
                    ])
                    ->columnSpanFull(),
                    
            ]);

    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('content')
                //     ->limit(50)
                //     ->searchable(),
                Tables\Columns\TextColumn::make('from')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('to')
                    ->searchable()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable(),
            ])->filters([
                //
            ])->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactForms::route('/'),
            'create' => Pages\CreateContactForm::route('/create'),
            'edit' => Pages\EditContactForm::route('/{record}/edit'),
        ];
    }

   

    public static function getModelaction(): array
    {   
        $actionsList = ['text', 'email', 'url', 'tel', 'number', 'date', 'textarea', 'select', 'checkbox', 'radio', 'file'];
        $actions = [];
        
        foreach ($actionsList as $actionType) {
            $actions[$actionType] = FormAction::make($actionType)
            ->label(ucfirst($actionType))
            ->color('primary')
            ->form(self::getModalForm($actionType)) 
            // ->slideOver()
            ->action(function (array $data) use ($actionType) {
                // Empty action for now
            });
        }
       

        return $actions;
    }

    public static function getModalForm($actionType):array{
        $fields = [
            Forms\Components\TextInput::make('label')
                ->label('Field Label')
                ->required(),
            Forms\Components\TextInput::make('name')
                ->label('Field Name')
                ->required()
                ->helperText('This will be used as the form field name'),
            Forms\Components\Toggle::make('required')
                ->label('Required Field')
                ->default(true),
        ]; 
        if (in_array(strtolower($actionType), ['select', 'radio', 'checkbox'])) {
            $fields[] = Repeater::make('options')
            ->schema([
                Forms\Components\TextInput::make('label')
                ->label('option label')
                ->required()
                ->maxLength(255)
                ->live()
                ->reactive()
                ->afterStateUpdated(function ($state, $set) {
                    $generatedKey = \Illuminate\Support\Str::slug(str_replace(' ', '_', $state), '_');
                    $set('key', $generatedKey);
                }),
                Forms\Components\TextInput::make('key')
                ->label('key')
                ->required()
                ->readOnly()
                ->maxLength(255),
            ])
            ->columns(2)
            ->collapsible()
            ->collapsed()
            ->minItems(1)
            ->maxItems(10)
            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null);
        }elseif (strtolower($actionType) === 'file') {
            $fields[] = Forms\Components\TextInput::make('file_types')
                ->label('Allowed File Types')
                ->placeholder('e.g. jpg,png,pdf')
                ->helperText('Comma-separated list of allowed file types');
            $fields[] = Forms\Components\TextInput::make('max_size')
                ->label('Maximum File Size (MB)')
                ->numeric()
                ->default(5)
                ->helperText('Maximum file size in megabytes');
        }else{
            $fields[] = Forms\Components\TextInput::make('placeholder')
                ->label('Placeholder Text')
                ->helperText('Optional placeholder text for the field');
        }
        return $fields;
    }

    public static function handleContentAdd($data){

    }
}
