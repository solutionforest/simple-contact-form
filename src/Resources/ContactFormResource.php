<?php

namespace SolutionForest\SimpleContactForm\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use SolutionForest\SimpleContactForm\Models\ContactForm;
use SolutionForest\SimpleContactForm\Resources\ContactFormResource\Pages;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public function mount(): void {}

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

                                Placeholder::make('content_placeholder')
                                    ->label('Usage')
                                    ->content(new HtmlString('the item you add will display as following :
                                                        [type | label | placeholder / options / [ accept | max_size ] ]'))
                                    ->columnSpanFull(),
                                Repeater::make('content')
                                    ->label('Content')
                                    ->columnSpanFull()
                                    ->collapsed(true)
                                    ->collapsible(false)
                                    ->itemLabel(fn (array $state): ?string => $state['string'] ?? null)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Field name')
                                                    // ->distinct()
                                            ->nullable()
                                            ->readOnly(),

                                    ])
                                    ->extraItemActions([
                                        FormAction::make('edit')
                                            ->label('Edit')
                                            ->icon('heroicon-o-pencil')
                                            ->form(function (array $arguments, $livewire) {
                                                $content = $livewire->data['content'] ?? [];
                                                $record = $content[$arguments['item']];
                                                $type = $record['type'] ?? 'text';
                                                $formSchema = self::getModalForm($type);
                                                foreach ($formSchema as &$field) {
                                                    $fieldName = $field->getName();
                                                    if (isset($record[$fieldName])) {
                                                        $field->default($record[$fieldName]);
                                                    }
                                                }

                                                return $formSchema;
                                            })
                                            ->action(function (array $data, $livewire, $arguments) {
                                                $content = $livewire->data['content'] ?? [];
                                                $record = $content[$arguments['item']];
                                                $type = $record['type'] ?? 'text';
                                                $newItem = self::handleContentAdd($data, $type);
                                                $content[$arguments['item']] = $newItem;
                                                $livewire->data['content'] = $content;
                                                // return ;
                                            }),

                                    ])
                                    ->defaultItems(0)
                                    ->addable(false),
                            ]),

                        Tabs\Tab::make('Mail')
                            ->schema([
                                // Forms\Components\Actions::make(self::getItemCopyActions()),
                                Placeholder::make('variables_placeholder')
                                    ->label('Available Variables :')
                                    ->content(function (Forms\Get $get) {
                                        $content = $get('content') ?? [];
                                        $variables = [];
                                        foreach ($content as $field) {
                                            if (isset($field['name'])) {
                                                $key = \Illuminate\Support\Str::slug($field['name'], '_');
                                                $variables[] = "{{{$key}}}";
                                            }
                                        }
                                        
                                        return count($variables) ?  implode(', ', $variables) : 'No variables available';
                                    })
                                    ->helperText('You can use these variables in your email ')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('email')
                                    // ->email()
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
                                MarkdownEditor::make('email_body')
                                    ->label('Email body')
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText('You can use the variables like : user name : {{name}}'),
                            ]),

                        Tabs\Tab::make('Messages')
                            ->schema([
                                Forms\Components\TextInput::make('success_message')
                                    ->label('Success message')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Message to show after successful form submission'),
                                Forms\Components\TextInput::make('error_message')
                                    ->label('Error message')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Message to show after form submission error'),
                                Forms\Components\TextInput::make('validation_error_message')
                                    ->label('Validation error message')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Message to show after validation error'),
                            ]),

                    ])

                    ->columnSpanFull(),

            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
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
                // Tables\Columns\TextColumn::make('from')
                //     ->searchable()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('to')
                //     ->searchable()
                //     ->sortable(),
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
                ->action(function (array $data, $livewire) use ($actionType) {
                    $content = $livewire->data['content'] ?? [];
                    $newItem = self::handleContentAdd($data, $actionType);
                    $content[] = $newItem;
                    $livewire->data['content'] = $content;
                    // return $newItem;
                });
        }

        return $actions;
    }

    public static function getModalForm($actionType): array
    {
        $fields = [
            Forms\Components\TextInput::make('label')
                ->label('Field Label')
                ->required(),
            Forms\Components\TextInput::make('name')
                ->label('Field Name')
                ->required()
                ->helperText('use for identification in the email body , no spaces or special characters')
                ->live()
                ->afterStateUpdated(function ($state, $set) {
                    // Convert spaces and punctuation to underscores
                    $sanitized = preg_replace('/[\s\p{P}]+/u', '_', $state);
                    $set('name', $sanitized);
                }),
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
        } elseif (strtolower($actionType) === 'file') {
            $fields[] = Forms\Components\Select::make('file_types')
                ->label('Allowed File Types')
                ->multiple()
                ->options([
                    'jpg' => 'JPG',
                    'jpeg' => 'JPEG',
                    'png' => 'PNG',
                    'pdf' => 'PDF',
                    'doc' => 'DOC',
                    'docx' => 'DOCX',
                    'xls' => 'XLS',
                    'xlsx' => 'XLSX',
                    'txt' => 'TXT',
                    'zip' => 'ZIP',
                ])
                ->searchable()
                ->helperText('Select allowed file types');
            $fields[] = Forms\Components\TextInput::make('max_size')
                ->label('Maximum File Size (MB)')
                ->numeric()
                ->default(5)
                ->helperText('Maximum file size in megabytes');
        } else {
            $fields[] = Forms\Components\TextInput::make('placeholder')
                ->label('Placeholder Text')
                ->helperText('Optional placeholder text for the field');
        }

        return $fields;
    }

    public static function handleContentAdd($data, $actionType)
    {

        // $content = $livewire->data['content'] ?? [];

        $newItem = [
            'label' => $data['label'] ?? '',
            'name' => $data['name'] ?? '',
            'type' => $actionType,
            'required' => $data['required'] ?? false,
        ];

        $string = '[ ' . $actionType . ' | ' . ($data['label'] ?? '') . ' | ' . ($data['required'] ? 'required' : 'optional');

        if (isset($data['placeholder'])) {
            $newItem['placeholder'] = $data['placeholder'];
            $string .= ' | placeholder = [ ' . $data['placeholder'] . ' ] ';
        }

        if (in_array(strtolower($actionType), ['select', 'radio', 'checkbox']) && ! empty($data['options'])) {
            $newItem['options'] = $data['options'];
            $optionsStr = implode(' | ', array_map(fn ($option) => $option['label'], $data['options']));
            $string .= ' | option =  [ ' . $optionsStr . ' ] ';
        }
        if (strtolower($actionType) === 'file') {
            if (isset($data['file_types'])) {
                $newItem['file_types'] = $data['file_types'];
                $typeStr = implode(' | ', $data['file_types']);
                $string .= ' | accept =  [ ' . $typeStr . ' ]';
            }
            if (isset($data['max_size'])) {
                $newItem['max_size'] = $data['max_size'];
                $string .= ' | szie = ' . $data['max_size'] . ' mb ]';
            }
        }

        $string .= ' ]';

        $newItem['string'] = $string;

        // $content[] = $newItem;

        // $livewire->data['content'] = $content;

        return $newItem;
    }
}
