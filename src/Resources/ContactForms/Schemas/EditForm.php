<?php

namespace SolutionForest\SimpleContactForm\Resources\ContactForms\Schemas;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EditForm
{
    public $record;

    public $data = [];

    public function mount(): void
    {
        if (! $this->record?->exists) {
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

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Template')
                            ->schema([
                                ActionGroup::make(static::getModelaction())
                                    ->buttonGroup(),
                                Placeholder::make('content_placeholder')
                                    ->label(__('simple-contact-form::simple-contact-form.form.usage'))
                                    ->content(new HtmlString(__('simple-contact-form::simple-contact-form.form.usage_content')))
                                    ->columnSpanFull(),
                                Repeater::make('content')
                                    ->grid(2)
                                    ->label(__('simple-contact-form::simple-contact-form.form.content'))
                                    ->reorderable(false)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->live()
                                    ->afterStateHydrated(function (Repeater $component, $state) {
                                        if (! empty($state)) {
                                            $needsUpdate = false;
                                            $items = $state;

                                            foreach ($items as $key => $item) {
                                                if (! isset($item['id'])) {
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
                                    ->afterStateUpdated(function (Repeater $component, $state, Get $get) {
                                        $items = $get('content') ?? [];
                                        $needsUpdate = false;
                                        foreach ($items as $key => $item) {
                                            if (! isset($item['id'])) {
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

                                        return __('simple-contact-form::simple-contact-form.form.session_label', ['number' => $id]);
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
                                            ->itemLabel(fn (array $state): ?string => $state['string'] ?? null)
                                            ->extraItemActions([
                                                Action::make('edit')
                                                    ->label(__('simple-contact-form::simple-contact-form.form.edit'))
                                                    ->icon('heroicon-o-pencil')
                                                    ->schema(function (array $arguments, $livewire, $state, Repeater $component) {
                                                        $content = $livewire->data['content'] ?? [];

                                                        $items = $component->getState() ?? [];

                                                        $itemKey = $arguments['item'] ?? null;
                                                        $item = $items[$itemKey] ?? null;

                                                        // $record = $content[$item['section']]['items'][$itemKey];
                                                        $record = null;
                                                        foreach ($content as $sectionIndex => $section) {
                                                            if (isset($section['items'][$itemKey])) {
                                                                $record = $section['items'][$itemKey];

                                                                break;
                                                            }
                                                        }

                                                        if (! $record) {
                                                            // Handle case when item is not found
                                                            return []; // Or whatever default form you want to show
                                                        }

                                                        $type = $record['type'] ?? 'text';

                                                        $formSchema = static::getModalForm($type, $content);
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
                                                        $newItem = static::handleContentAdd($data, $data['type'] ?? $originalItem['type'] ?? 'text');

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
                        Tab::make('Mail')
                            ->label(__('simple-contact-form::simple-contact-form.form.mail'))
                            ->schema([
                                // Forms\Components\Actions::make(self::getItemCopyActions()),
                                Placeholder::make('variables_placeholder')
                                    ->label(__('simple-contact-form::simple-contact-form.form.available_variables'))
                                    ->content(function (Get $get) {
                                        $content = $get('content') ?? [];
                                        $variables = [];
                                        foreach ($content as $section) {
                                            if (empty($section['items']) || ! is_array($section['items'])) {
                                                continue;
                                            }
                                            foreach ($section['items'] as $field) {
                                                if (isset($field['name'])) {
                                                    $key = \Illuminate\Support\Str::slug($field['name'], '_');
                                                    $variables[] = "{{{$key}}}";
                                                }
                                            }
                                        }

                                        return count($variables) ? implode(', ', $variables) : __('simple-contact-form::simple-contact-form.no_variables');
                                    })
                                    ->helperText(__('simple-contact-form::simple-contact-form.form.available_variables_help'))
                                    ->columnSpanFull(),
                                // Forms\Components\TextInput::make('email')
                                //     // ->email()
                                //     ->required()
                                //     ->maxLength(255),
                                TextInput::make('subject')
                                    ->label(__('simple-contact-form::simple-contact-form.form.subject'))
                                    ->required()
                                    ->maxLength(255),
                                // Forms\Components\TextInput::make('from')
                                //     ->required()
                                //     ->maxLength(255),
                                TextInput::make('to')
                                    ->label(__('simple-contact-form::simple-contact-form.form.to'))
                                    ->required()
                                    ->maxLength(255),
                                RichEditor::make('email_body')
                                    ->label(__('simple-contact-form::simple-contact-form.form.email_body'))
                                    ->required()
                                    ->columnSpanFull()
                                    ->helperText(__('simple-contact-form::simple-contact-form.form.email_body_help')),
                            ]),
                        Tabs\Tab::make('Messages')
                            ->label(__('simple-contact-form::simple-contact-form.form.messages'))
                            ->schema([
                                TextInput::make('success_message')
                                    ->label(__('simple-contact-form::simple-contact-form.form.success_message'))
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText(__('simple-contact-form::simple-contact-form.form.success_message_help')),
                                TextInput::make('error_message')
                                    ->label(__('simple-contact-form::simple-contact-form.form.error_message'))
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText(__('simple-contact-form::simple-contact-form.form.error_message_help')),
                                // Forms\Components\TextInput::make('validation_error_message')
                                //     ->label('Validation error message')
                                //     ->required()
                                //     ->maxLength(255)
                                //     ->helperText('Message to show after validation error'),
                            ]),
                        Tabs\Tab::make('extra_attuributes')
                            ->label(__('simple-contact-form::simple-contact-form.form.extra_attributes'))
                            ->schema([
                                Textarea::make('extra_attributes')
                                    ->label(__('simple-contact-form::simple-contact-form.form.extra_attributes'))
                                    ->helperText(__('simple-contact-form::simple-contact-form.form.extra_attributes_help'))
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
    public static function getActionList(){
        return ['text', 'date', 'textarea', 'select', 'radio',  'checkbox'];
    }

    public static function getModelaction(): array
    {
        $actionsList = static::getActionList();
        $actions = [];

        foreach ($actionsList as $actionType) {
            $actions[$actionType] = Action::make($actionType)
                ->label(__('simple-contact-form::simple-contact-form.field_types.' . $actionType))
                ->color('primary')
                ->schema(
                    function (array $data, $livewire) use ($actionType) {
                        $content = $livewire->data['content'] ?? [];

                        return static::getModalForm($actionType, $content);
                    }
                )
                ->action(function (array $data, $livewire) use ($actionType) {
                    $content = $livewire->data['content'] ?? [];
                    $newItem = static::handleContentAdd($data, $actionType);
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

                    if (! isset($content[$sectionUuid]['items'])) {
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
                ->label(__('simple-contact-form::simple-contact-form.field.section'))
                ->required()
                ->options(function (Get $get) use ($content): array {
                    $options = [];
                    foreach ($content as $uuid => $item) {
                        $sessionId = $item['id'] + 1;
                        $options[$item['id']] = __('simple-contact-form::simple-contact-form.form.session_label', ['number' => $sessionId]);
                    }

                    if (empty($options)) {
                        $options['0'] = __('simple-contact-form::simple-contact-form.form.session_label', ['number' => 1]);
                        $options['1'] = __('simple-contact-form::simple-contact-form.form.session_label', ['number' => 2]);
                    }

                    return $options;
                })
                ->default(0)
                ->inline();

            $fields[] = Hidden::make('section_id');
        }

        $fields[] = TextInput::make('label')
            ->label(__('simple-contact-form::simple-contact-form.field.label'))
            ->required()
            ->live(onBlur: true)
            ->afterStateUpdated(function ($state, $set, $get) {
                $generatedKey = \Illuminate\Support\Str::slug(str_replace(' ', '_', $state), '_');
                $name = $get('name') ?? '';
                if ($name == '') {
                    $set('name', $generatedKey);
                }
            });
        $fields[] = TextInput::make('name')
            ->label(__('simple-contact-form::simple-contact-form.field.name'))
            ->required()
            ->helperText(__('simple-contact-form::simple-contact-form.field.name_help'))
            ->live()
            ->afterStateUpdated(function ($state, $set) {
                // Convert spaces and punctuation to underscores
                $sanitized = preg_replace('/[\s\p{P}]+/u', '_', $state);
                $set('name', $sanitized);
            });
        $fields[] = Toggle::make('required')
            ->label(__('simple-contact-form::simple-contact-form.field.required'))
            ->default(true);

        switch (strtolower($actionType)) {
            case 'select':
            case 'radio':
                $fields[] = Repeater::make('options')
                    ->label(__('simple-contact-form::simple-contact-form.field.options'))
                    ->schema([
                        TextInput::make('label')
                            ->label(__('simple-contact-form::simple-contact-form.field.option_label'))
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set) {
                                $generatedKey = \Illuminate\Support\Str::slug(str_replace(' ', '_', $state), '_');
                                $set('key', $generatedKey);
                            }),
                        TextInput::make('key')
                            ->label(__('simple-contact-form::simple-contact-form.field.key'))
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

                break;
            case 'checkbox':
                $fields[] = Toggle::make('inline')
                    ->label(__('simple-contact-form::simple-contact-form.field.inline'))
                    ->default(true);

                break;
            case 'date':
                $fields[] = Toggle::make('include_time')
                    ->label(__('simple-contact-form::simple-contact-form.field.include_time'))
                    ->default(false);
                $fields[] = Select::make('date_format')
                    ->label(__('simple-contact-form::simple-contact-form.field.date_format'))
                    ->options(__('simple-contact-form::simple-contact-form.date_formats'))
                    ->default('Y-m-d');
                $fields[] = DatePicker::make('min_date')
                    ->label(__('simple-contact-form::simple-contact-form.field.min_date'))
                    ->helperText(__('simple-contact-form::simple-contact-form.field.min_date_help'));
                $fields[] = DatePicker::make('max_date')
                    ->label(__('simple-contact-form::simple-contact-form.field.max_date'))
                    ->helperText(__('simple-contact-form::simple-contact-form.field.max_date_help'));

                break;
            case 'textarea':
                $fields[] = TextInput::make('min_length')
                    ->label(__('simple-contact-form::simple-contact-form.field.min_length'))
                    ->numeric()
                    ->default(0)
                    ->helperText(__('simple-contact-form::simple-contact-form.field.min_length_help'));
                $fields[] = TextInput::make('max_length')
                    ->label(__('simple-contact-form::simple-contact-form.field.max_length'))
                    ->numeric()
                    ->default(500)
                    ->helperText(__('simple-contact-form::simple-contact-form.field.max_length_help'));

                break;
            default:

                $fields[] = ToggleButtons::make('validation_type')
                    ->label(__('simple-contact-form::simple-contact-form.field.validation_type'))
                    ->options(__('simple-contact-form::simple-contact-form.validation_types'))
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
                $fields[] = Hidden::make('email')
                    ->label(__('simple-contact-form::simple-contact-form.field.email_field'))
                    ->default(false);
                $fields[] = Hidden::make('tel')
                    ->label(__('simple-contact-form::simple-contact-form.field.phone_field'))
                    ->default(false);
                // ->helperText('Enable phone validation for this field');
                $fields[] = Hidden::make('number')
                    ->label(__('simple-contact-form::simple-contact-form.field.number_field'))
                    ->default(false);

                $fields[] = TextInput::make('placeholder')
                    ->label(__('simple-contact-form::simple-contact-form.field.placeholder'))
                    ->helperText(__('simple-contact-form::simple-contact-form.field.placeholder_help'));

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
        if (in_array(strtolower($actionType), ['select', 'radio', 'checkbox']) && ! empty($data['options'])) {
            $newItem['options'] = $data['options'];
            $optionsStr = implode(' | ', array_map(fn ($option) => $option['label'], $data['options']));
            $string .= ' | option =  [ ' . $optionsStr . ' ] ';
        }

        $string .= ' ]';

        $newItem['string'] = $string;

        // $content[] = $newItem;

        // $livewire->data['content'] = $content;

        return $newItem;
    }
}
