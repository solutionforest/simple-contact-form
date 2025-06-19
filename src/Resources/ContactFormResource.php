<?php

namespace SolutionForest\SimpleContactForm\Resources;

use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
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

    public $record;

    public $data = [];

    public function mount(): void
    {
        if (!$this->record?->exists) {
            $this->data['content'] = [
                \Illuminate\Support\Str::uuid()->toString() => [
                    'id' => 0, // Keep id for backward compatibility and UI display
                    'items' => [],
                ],
                \Illuminate\Support\Str::uuid()->toString() => [
                    'id' => 1, // Keep id for backward compatibility and UI display
                    'items' => [],
                ],
            ];
        }
        //  else {
        //     $content = $this->record->content ?? [];

        //     // Check if content needs to be migrated to UUID format
        //     $needsMigration = empty($content) || is_numeric(array_key_first($content) ?? '') || !isset(array_values($content)[0]['id']);
        //     if ($needsMigration) {
        //         $newContent = [];

        //         // Initialize with default structure if empty
        //         if (empty($content)) {
        //             $content = [
        //                 [
        //                     'id' => 0,
        //                     'items' => [],
        //                 ],
        //                 [
        //                     'id' => 1,
        //                     'items' => [],
        //                 ],
        //             ];
        //         }

        //         // Convert existing content to UUID structure
        //         foreach ($content as $index => $section) {
        //             $sectionId = $section['id'] ?? $index;
        //             $sectionUuid = \Illuminate\Support\Str::uuid()->toString();

        //             $newItems = [];
        //             if (isset($section['items']) && is_array($section['items'])) {
        //                 foreach ($section['items'] as $item) {
        //                     $itemUuid = \Illuminate\Support\Str::uuid()->toString();
        //                     $newItems[$itemUuid] = $item;
        //                 }
        //             }

        //             $newContent[$sectionUuid] = [
        //                 'id' => $sectionId,
        //                 'items' => $newItems,
        //             ];
        //         }

        //         $this->record->content = $newContent;
        //         $this->data['content'] = $newContent;
        //     } else {
        //         $this->data['content'] = $content;
        //     }
        // }
    }

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
                                    ->grid(2)
                                    ->label('Content')
                                    ->reorderable(false)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->live()
                                    ->afterStateHydrated(function (Repeater $component, $state) {
                                        if (!empty($state)) {
                                            $needsUpdate = false;
                                            $items = $state;

                                            foreach ($items as $key => $item) {
                                                if (!isset($item['id'])) {
                                                    $needsUpdate = true;

                                                    break;
                                                }
                                            }
                                            if ($needsUpdate) {
                                                $index = 0;
                                                foreach ($items as $key => $item) {
                                                    $items[$key]['id'] = $index;
                                                    $index++;
                                                }
                                                $component->state($items);
                                            }
                                        }
                                    })
                                    ->afterStateUpdated(function (Repeater $component, $state, Forms\Get $get) {
                                        $items = $get('content') ?? [];
                                        $needsUpdate = false;
                                        foreach ($items as $key => $item) {
                                            if (!isset($item['id'])) {
                                                $needsUpdate = true;

                                                break;
                                            }
                                        }
                                        if ($needsUpdate) {
                                            $index = 0;
                                            foreach ($items as $key => $item) {
                                                $items[$key]['id'] = $index;
                                                $index++;
                                            }
                                            $component->state($items);
                                        }
                                    })
                                    ->itemLabel(function (array $state) {
                                        $id = ($state['id'] ?? '0') + 1;

                                        return "Session {$id}";
                                    })
                                    ->maxItems(2)
                                    ->schema([
                                        Repeater::make('items')
                                            ->label(false)
                                            ->defaultItems(0)
                                            ->addable(false)
                                            ->columnSpanFull()
                                            ->collapsed(true)
                                            ->collapsible(false)
                                            ->itemLabel(fn(array $state): ?string => $state['string'] ?? null)
                                            ->extraItemActions([
                                                FormAction::make('edit')
                                                    ->label('Edit')
                                                    ->icon('heroicon-o-pencil')
                                                    ->form(function (array $arguments, $livewire, $state, Repeater $component) {
                                                        $content = $livewire->data['content'] ?? [];

                                                        $items = $component->getState() ?? [];
                                                        // dd($content,$items, $component->getItemState($arguments['item']));
                                                        $itemKey = $arguments['item'] ?? null;
                                                        $item = $items[$itemKey] ?? null;
                                                        //  dd($itemKey, $item,$content,$arguments);
                                                        // $record = $content[$item['section']]['items'][$itemKey];
                                                        $record = null;
                                                        foreach ($content as $sectionIndex => $section) {
                                                            if (isset($section['items'][$itemKey])) {
                                                                $record = $section['items'][$itemKey];

                                                                break;
                                                            }
                                                        }

                                                        if (!$record) {
                                                            // Handle case when item is not found
                                                            return []; // Or whatever default form you want to show
                                                        }

                                                        $type = $record['type'] ?? 'text';

                                                        $formSchema = self::getModalForm($type, $content);
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
                                                        $itemIndex = $arguments['item'] ?? null;
                                                        $originalSection = null;
                                                        $originalItem = null;

                                                        foreach ($content as $sectionIndex => $section) {
                                                            if (isset($section['items'][$itemIndex])) {
                                                                $originalSection = $sectionIndex;
                                                                $originalItem = $section['items'][$itemIndex];

                                                                break;
                                                            }
                                                        }

                                                        if ($originalSection === null) {
                                                            return;
                                                        }

                                                        $targetSectionId = $data['section'] ?? null;
                                                        $targetSection = $originalSection;

                                                        if ($targetSectionId !== null) {
                                                            foreach ($content as $uuid => $section) {
                                                                if (isset($section['id']) && $section['id'] == $targetSectionId) {
                                                                    $targetSection = $uuid;

                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        $newItem = self::handleContentAdd($data, $data['type'] ?? $originalItem['type'] ?? 'text');

                                                        if ($originalSection != $targetSection) {
                                                            unset($content[$originalSection]['items'][$itemIndex]);
                                                            $content[$targetSection]['items'][$itemIndex] = $newItem;
                                                        } else {
                                                            $content[$originalSection]['items'][$itemIndex] = $newItem;
                                                        }

                                                        $livewire->data['content'] = $content;
                                                    }),
                                            ]),
                                    ])
                                    ->defaultItems(2),

                            ]),

                        Tabs\Tab::make('Mail')
                            ->schema([
                                // Forms\Components\Actions::make(self::getItemCopyActions()),
                                Placeholder::make('variables_placeholder')
                                    ->label('Available Variables :')
                                    ->content(function (Forms\Get $get) {
                                        $content = $get('content') ?? [];
                                        $variables = [];
                                        foreach ($content as $section) {
                                            if (empty($section['items']) || !is_array($section['items'])) {
                                                continue;
                                            }
                                            foreach ($section['items'] as $field) {
                                                if (isset($field['name'])) {
                                                    $key = \Illuminate\Support\Str::slug($field['name'], '_');
                                                    $variables[] = "{{{$key}}}";
                                                }
                                            }
                                        }
                                        return count($variables) ? implode(', ', $variables) : 'No variables available';
                                    })
                                    ->helperText('You can use these variables in your email ')
                                    ->columnSpanFull(),
                                // Forms\Components\TextInput::make('email')
                                //     // ->email()
                                //     ->required()
                                //     ->maxLength(255),
                                Forms\Components\TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),
                                // Forms\Components\TextInput::make('from')
                                //     ->required()
                                //     ->maxLength(255),
                                Forms\Components\TextInput::make('to')
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('email_body')
                                    ->label('Email body')
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText('You can use the variables like : user name : {{name}}'),
                            ]),

                        Tabs\Tab::make('Messages')
                            ->schema([
                                Forms\Components\TextInput::make('success_message')
                                    ->label('Success message')
                                    // ->required()
                                    ->maxLength(255)
                                    ->helperText('Message to show after successful form submission'),
                                Forms\Components\TextInput::make('error_message')
                                    ->label('Error message')
                                    // ->required()
                                    ->maxLength(255)
                                    ->helperText('Message to show after form submission error'),
                                // Forms\Components\TextInput::make('validation_error_message')
                                //     ->label('Validation error message')
                                //     ->required()
                                //     ->maxLength(255)
                                //     ->helperText('Message to show after validation error'),
                            ]),
                        Tabs\Tab::make('extra_attuributes')
                            ->label('Extra Attributes')
                            ->schema([
                                Textarea::make('extra_attributes')
                                    ->label('Extra Attributes')
                                    ->helperText('Optional extra attributes for the form, e.g., "class"="border border-dark rounded-xl m-w-2xl p-6"')
                                    ->rows(2)
                                    ->live(),
                                // ->afterStateUpdated(function ($state, $set) {
                                //     // Process the extra attributes if needed
                                //     // For example, you can parse them into an array or validate them
                                //     $attributes = trim($state);
                                //     $set('extra_attributes', $attributes);
                                // })
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
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('subject')
                //     ->searchable()
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
        $actionsList = ['text', 'date', 'textarea', 'select', 'radio', 'file', 'checkbox'];
        $actions = [];

        foreach ($actionsList as $actionType) {
            $actions[$actionType] = FormAction::make($actionType)
                ->label(ucfirst($actionType))
                ->color('primary')
                ->form(
                    function (array $data, $livewire) use ($actionType) {
                        $content = $livewire->data['content'] ?? [];

                        return self::getModalForm($actionType, $content);
                    }
                )
                ->action(function (array $data, $livewire) use ($actionType) {
                    $content = $livewire->data['content'] ?? [];
                    $newItem = self::handleContentAdd($data, $actionType);
                    $sectionId = $data['section'] ?? 0;

                    $sectionUuid = null;
                    foreach ($content as $uuid => $section) {
                        if (isset($section['id']) && $section['id'] == $sectionId) {
                            $sectionUuid = $uuid;

                            break;
                        }
                    }
                    if ($sectionUuid === null) {
                        $sectionUuid = \Illuminate\Support\Str::uuid()->toString();
                        $content[$sectionUuid] = [
                            'id' => $sectionId,
                            'items' => [],
                        ];
                    }

                    if (!isset($content[$sectionUuid]['items'])) {
                        $content[$sectionUuid]['items'] = [];
                    }

                    $itemUuid = \Illuminate\Support\Str::uuid()->toString();
                    $content[$sectionUuid]['items'][$itemUuid] = $newItem;

                    $livewire->data['content'] = $content;

                });
        }

        return $actions;
    }

    public static function getModalForm($actionType, $content = null): array
    {

        $fields = [];
        if ($content) {
            $fields[] = ToggleButtons::make('section')
                ->label('Section')
                ->required()
                ->options(function (Forms\Get $get) use ($content): array {
                    $options = [];
                    foreach ($content as $uuid => $item) {
                        $sessionId = $item['id'] + 1 ?? 1;
                        $options[$item['id']] = "Session {$sessionId}";
                    }

                    if (empty($options)) {
                        $options['0'] = 'Session 1';
                        $options['1'] = 'Session 2';
                    }

                    return $options;
                })
                ->default(0)
                ->inline();

            $fields[] = Forms\Components\Hidden::make('section_id');
        }

        $fields[] = Forms\Components\TextInput::make('label')
            ->label('Field Label')
            ->required()
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state, $set ,$get) {
                $generatedKey = \Illuminate\Support\Str::slug(str_replace(' ', '_', $state), '_');
                $name = $get('name') ?? '';
                if($name == '') {
                    $set('name', $generatedKey);
                }
            })
        ;
        $fields[] = Forms\Components\TextInput::make('name')
            ->label('Field Name')
            ->required()
            ->helperText('use for identification in the email body , no spaces or special characters')
            ->live()
            ->afterStateUpdated(function ($state, $set) {
                // Convert spaces and punctuation to underscores
                $sanitized = preg_replace('/[\s\p{P}]+/u', '_', $state);
                $set('name', $sanitized);
            });
        $fields[] = Forms\Components\Toggle::make('required')
            ->label('Required Field')
            ->default(true);

        switch (strtolower($actionType)) {
            case 'select':
            case 'radio':
                $fields[] = Repeater::make('options')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('option label')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
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
                    ->itemLabel(fn(array $state): ?string => $state['label'] ?? null);

                break;
            case 'file':
                $fields[] = Forms\Components\Select::make('file_types')
                    ->label('Allowed File Types')
                    ->multiple()
                    ->options([
                        'image/jpeg' => 'JPG',
                        'image/jpeg' => 'JPEG',
                        'image/png' => 'PNG',
                        'application/pdf' => 'PDF',
                        'application/msword' => 'DOC',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                        'application/vnd.ms-excel' => 'XLS',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'XLSX',
                        'text/plain' => 'TXT',
                        'application/zip' => 'ZIP',
                    ])
                    ->searchable()
                    ->helperText('Select allowed file types');
                $fields[] = Forms\Components\TextInput::make('max_size')
                    ->label('Maximum File Size (mb) ')
                    ->numeric()
                    ->default(5)
                    ->helperText('Maximum file size in megabytes');

                break;
            case 'checkbox':
                $fields[] = Forms\Components\Toggle::make('inline')
                    ->label('Inline Options')
                    ->default(true);

                break;
            case 'date':
                $fields[] = Forms\Components\Toggle::make('include_time')
                    ->label('Include Time')
                    ->default(false);
                $fields[] = Forms\Components\Select::make('date_format')
                    ->label('Date Format')
                    ->options([
                        'Y-m-d' => 'YYYY-MM-DD',
                        'd/m/Y' => 'DD/MM/YYYY',
                        'm/d/Y' => 'MM/DD/YYYY',
                        'Y.m.d' => 'YYYY.MM.DD',
                    ])
                    ->default('Y-m-d');
                $fields[] = Forms\Components\DatePicker::make('min_date')
                    ->label('Minimum Date')
                    ->helperText('Optional minimum selectable date');
                $fields[] = Forms\Components\DatePicker::make('max_date')
                    ->label('Maximum Date')
                    ->helperText('Optional maximum selectable date');

                break;
            case 'textarea':
                $fields[] = Forms\Components\TextInput::make('min_length')
                    ->label('Minimum Length')
                    ->numeric()
                    ->default(0)
                    ->helperText('Minimum number of characters required');
                $fields[] = Forms\Components\TextInput::make('max_length')
                    ->label('Maximum Length')
                    ->numeric()
                    ->default(500)
                    ->helperText('Maximum number of characters allowed');

                break;
            default:
                // $fields[] = Forms\Components\Toggle::make('email')
                //     ->label('Email Field')
                //     ->default(false);
                // $fields[] = Forms\Components\Toggle::make('tel')
                //     ->label('Phone Field')
                //     ->default(false);
                //     // ->helperText('Enable phone validation for this field');
                // $fields[] = Forms\Components\Toggle::make('number')
                //     ->label('Number Field')
                //     ->default(false);

                $fields[] = Forms\Components\ToggleButtons::make('validation_type')
                    ->label('Validation Type')
                    ->options([
                        'none' => 'None',
                        'email' => 'Email',
                        'tel' => 'Phone',
                        'number' => 'Number'
                    ])
                    ->inline()
                    ->default('none')
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        // Reset the validation fields if the type changes
                        if ($state === 'none') {
                            $set('email', false);
                            $set('tel', false);
                            $set('number', false);
                        } elseif ($state === 'email') {
                            $set('email', true);
                            $set('tel', false);
                            $set('number', false);
                        } elseif ($state === 'tel') {
                            $set('email', false);
                            $set('tel', true);
                            $set('number', false);
                        } elseif ($state === 'number') {
                            $set('email', false);
                            $set('tel', false);
                            $set('number', true);
                        }
                    });
                $fields[] = Forms\Components\Hidden::make('email')
                    ->label('Email Field')
                    ->default(false);
                $fields[] = Forms\Components\Hidden::make('tel')
                    ->label('Phone Field')
                    ->default(false);
                // ->helperText('Enable phone validation for this field');
                $fields[] = Forms\Components\Hidden::make('number')
                    ->label('Number Field')
                    ->default(false);

                $fields[] = Forms\Components\TextInput::make('placeholder')
                    ->label('Placeholder Text')
                    ->helperText('Optional placeholder text for the field');

                break;
        }

        // $fields[] = Forms\Components\Textarea::make('extra_attributes')
        //     ->label('Extra Attributes')
        //     ->helperText('Optional extra attributes for the field, e.g., "data-custom=custom_value"')
        //     ->rows(2)
        //     ->live();
        // ->afterStateUpdated(function ($state, $set) {
        //     // Process the extra attributes if needed
        //     // For example, you can parse them into an array or validate them
        //     $attributes = trim($state);
        //     $set('extra_attributes', $attributes);
        // });

        return $fields;
    }

    public static function handleContentAdd($data, $actionType)
    {

        // $content = $livewire->data['content'] ?? [];

        $newItem = [
            'section' => $data['section'] ?? null,
            'label' => $data['label'] ?? '',
            'name' => $data['name'] ?? '',
            'type' => $actionType,
            'required' => $data['required'] ?? false,
            // 'extra_attributes' => $data['extra_attributes'] ?? '',
        ];

        $string = '[ ' . $actionType . ' | ' . ($data['label'] ?? '') . ' | ' . ($data['required'] ? 'required' : 'optional');
        // if (isset($data['extra_attributes']) ) {
        //     $newItem['extra_attributes'] = $data['extra_attributes'];
        // }
        if (isset($data['placeholder'])) {
            $newItem['placeholder'] = $data['placeholder'];
            $string .= ' | placeholder = [ ' . $data['placeholder'] . ' ] ';
        }
        if (isset($data['email']) && $data['email']) {
            $newItem['email'] = $data['email'];
            $string .= ' | email validation';
        }
        if (isset($data['tel']) && $data['tel']) {
            $newItem['tel'] = $data['tel'];
            $string .= ' | phone validation';
        }
        if (isset($data['number']) && $data['number']) {
            $newItem['number'] = $data['number'];
            $string .= ' | number validation';
        }
        if (isset($data['min_length'])) {
            $newItem['min_length'] = $data['min_length'];
            $string .= ' | min length = ' . $data['min_length'];
        }
        if (isset($data['max_length'])) {
            $newItem['max_length'] = $data['max_length'];
            $string .= ' | max length = ' . $data['max_length'];
        }
        if (isset($data['include_time'])) {
            $newItem['include_time'] = $data['include_time'];
            $string .= ' | ' . ($data['include_time'] ? 'with time' : 'date only');
        }
        if (isset($data['date_format'])) {
            $newItem['date_format'] = $data['date_format'];
            $string .= ' | format = ' . $data['date_format'];
        }
        if (isset($data['min_date'])) {
            $newItem['min_date'] = $data['min_date'];
            $string .= ' | min date = ' . $data['min_date'];
        }
        if (isset($data['max_date'])) {
            $newItem['max_date'] = $data['max_date'];
            $string .= ' | max date = ' . $data['max_date'];
        }
        if (isset($data['inline'])) {
            $newItem['inline'] = $data['inline'];
            $string .= ' | ' . ($data['inline'] ? 'inline' : 'stacked');
        }
        if (isset($data['validation_type']) && $data['validation_type'] !== 'none') {
            $newItem['validation_type'] = $data['validation_type'];
            // $string .= ' | validation = ' . $data['validation_type'];
        }
        if (in_array(strtolower($actionType), ['select', 'radio', 'checkbox']) && !empty($data['options'])) {
            $newItem['options'] = $data['options'];
            $optionsStr = implode(' | ', array_map(fn($option) => $option['label'], $data['options']));
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
        // dd($newItem);
        return $newItem;
    }

}
