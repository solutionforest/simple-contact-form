<?php

namespace SolutionForest\SimpleContactForm\Livewire;

use Filament\Forms\Components;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use SolutionForest\SimpleContactForm\Models\ContactForm;

class ContactFormComponent extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public ?string $formId = null;

    public ContactForm $contactForm;

    public function mount($id): void
    {
        $this->formId = $id;
        $this->contactForm = ContactForm::findOrFail($id);
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $schema = [];
        foreach ($this->contactForm->content ?? [] as $field) {
            $fieldType = $field['type'] ?? 'text';
            $fieldSchema = $this->getFieldSchema($fieldType, $field);
            if ($fieldSchema) {
                $schema[] = $fieldSchema;
            }
        }

        return $form
            ->schema($schema)
            ->statePath('data');
    }

    private function getFieldSchema(string $type, array $field)
    {
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $required = $field['required'] ?? false;
        $placeholder = $field['placeholder'] ?? null;

        switch (strtolower($type)) {
            case 'text':
                return Components\TextInput::make($name)
                    ->label($label)
                    ->placeholder($placeholder)
                    ->required($required);

            case 'email':
                return Components\TextInput::make($name)
                    ->label($label)
                    ->email()
                    ->placeholder($placeholder)
                    ->required($required);

            case 'tel':
                return Components\TextInput::make($name)
                    ->label($label)
                    ->tel()
                    ->placeholder($placeholder)
                    ->required($required);

            case 'textarea':
                return Components\Textarea::make($name)
                    ->label($label)
                    ->placeholder($placeholder)
                    ->required($required);

            case 'select':
                return Components\Select::make($name)
                    ->label($label)
                    ->options(collect($field['options'] ?? [])->pluck('label', 'key')->toArray())
                    ->required($required);

            case 'checkbox':
                return Components\Checkbox::make($name)
                    ->label($label)
                    ->required($required);

            case 'radio':
                return Components\Radio::make($name)
                    ->label($label)
                    ->options(collect($field['options'] ?? [])->pluck('label', 'key')->toArray())
                    ->required($required);

            case 'file':
                $component = Components\FileUpload::make($name)
                    ->label($label);

                if (! empty($field['file_types'])) {
                    $component->acceptedFileTypes(array_map(fn ($type) => ".$type", $field['file_types']));
                }

                if (! empty($field['max_size'])) {
                    $component->maxSize($field['max_size'] * 1024);
                }

                return $component->required($required);

            default:
                return Components\TextInput::make($name)
                    ->label($label)
                    ->placeholder($placeholder)
                    ->required($required);
        }
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function submit()
    {
        $data = $this->form->getState();

    }

    public function render(): View
    {
        return view('simple-contact-form::livewire.contact-form-component');
    }
}
