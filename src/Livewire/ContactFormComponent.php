<?php

namespace SolutionForest\SimpleContactForm\Livewire;

use Exception;
use Filament\Forms\Components;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Split;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use SolutionForest\SimpleContactForm\Models\ContactForm;

class ContactFormComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    public ?array $data = [];

    public ?string $formId = null;

    public ContactForm $contactForm;

    public ?array $email = null;

    public ?string $customClass = null;

    public $referencedFields = [];

    public function mount($id, $customClass = null): void
    {
        $this->formId = $id;

        try {
            $this->contactForm = ContactForm::findOrFail($id);
            $this->form->fill();
        } catch (Exception $e) {
            $this->contactForm = new ContactForm;
            $this->addError('formError', 'Contact form not found.');
        }

        if ($this->contactForm->extra_attributes) {

            $this->customClass = $this->formatAttributesForHtml($this->contactForm->extra_attributes);
            // dd($this->customClass );
        } else {
            $this->customClass = $customClass;
        }
    }

    private function formatAttributesForHtml(?string $attributesText): string
    {
        if (empty($attributesText)) {
            return '';
        }
        $result = [];
        $pattern = '/(?:"([^"]+)"|\'([^\']+)\')=(?:"([^"]*)"|\'([^\']*)\'|([^,]*))?(?:,|$)/';

        if (preg_match_all($pattern, $attributesText, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $key = $match[1] ?? $match[2] ?? '';
                $value = $match[3] ?? $match[4] ?? $match[5] ?? '';

                if ($key !== '') {

                    $value = trim($value, '"\'');

                    $result[] = $key . '="' . htmlspecialchars($value) . '"';
                }
            }
        }

        return implode(' ', $result);
    }

    public function form(Form $form): Form
    {
        $schema = [];
        foreach ($this->contactForm->content ?? [] as $section) {
            if (empty($section['items'])) {
                continue;
            }

            $sectionFields = [];
            foreach ($section['items'] as $field) {
                $fieldType = $field['type'] ?? 'text';
                if (empty($fieldType)) {
                    continue;
                }
                $fieldSchema = $this->getFieldSchema($fieldType, $field);
                if ($fieldSchema) {
                    $sectionFields[] = $fieldSchema;
                }
            }

            // Skip empty sections
            if (empty($sectionFields)) {
                continue;
            }
            $schema[] = Grid::make()
                ->columns(1)
                ->schema($sectionFields)

                ->columnSpanFull();
        }

        return $form
            ->schema(
                [
                    Split::make($schema)
                        ->from('md'),

                    // ->schema($schema),
                ]
            )
            // ->extraAttributes(
            //     $this->formatExtraAttributes($this->contactForm->extra_attributes ?? null) ?? []
            // )
            ->statePath('data');
    }

    private function getFieldSchema(string $type, array $field)
    {
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $required = $field['required'] ?? false;
        $placeholder = $field['placeholder'] ?? null;
        // $extraAttributes = $this->formatExtraAttributes($field['extra_attributes'] ?? null) ?? [];

        switch (strtolower($type)) {
            case 'text':
                
                return Components\TextInput::make($name)
                    ->label($label)
                    ->placeholder($placeholder)
                    ->email($field['email'] ?? false)
                    // ->tel($field['tel'] ?? false)
                    ->numeric($field['number'] ?? false)
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'textarea':
                return Components\Textarea::make($name)
                    ->label($label)
                    // ->placeholder($placeholder)
                    ->minLength($field['min_length'] ?? null)
                    ->maxLength($field['max_length'] ?? null)
                    ->required($required);

            case 'select':
                return Components\Select::make($name)
                    ->label($label)
                    ->options(collect($field['options'] ?? [])->pluck('label', 'key')->toArray())
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'checkbox':
                return Components\Checkbox::make($name)
                    ->label($label)
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'radio':
                return Components\Radio::make($name)
                    ->label($label)
                    // ->inline()
                    ->options(collect($field['options'] ?? [])->pluck('label', 'key')->toArray())
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'file':
                return Components\FileUpload::make($name)
                    ->label($label)
                    ->acceptedFileTypes(
                        ! empty($field['file_types'])
                        ? $field['file_types']
                        : null
                    )
                    ->maxSize(
                        ! empty($field['max_size'])
                        ? ($field['max_size'] * 1024)
                        : null
                    )
                    ->disk('public')
                    ->visibility('public')
                    ->directory('contact-uploads')
                    ->preserveFilenames()
                    ->live()
                    // ->extraAttributes($extraAttributes)
                    ->required($required);
            case 'date':
                $dateComponent = null;
                if (! empty($field['include_time'])) {
                    $dateComponent = Components\DateTimePicker::make($name);
                } else {
                    $dateComponent = Components\DatePicker::make($name);
                }

                return $dateComponent
                    ->label($label)
                    ->placeholder($placeholder)
                    ->format(! empty($field['date_format']) ? $field['date_format'] : 'Y-m-d')
                    ->minDate(! empty($field['min_date']) ? $field['min_date'] : null)
                    ->maxDate(! empty($field['max_date']) ? $field['max_date'] : null)
                    ->required($required);

            default:
                return Components\TextInput::make($name)
                    ->label($label)
                    ->placeholder($placeholder)
                    // ->extraAttributes($extraAttributes)
                    ->required($required);
        }
    }

    /**
     * Convert extra_attributes from text format to array
     * Format example: "class"="form-control","data-id"="123"
     *
     * @param  mixed  $attributes  Text format attributes or array
     * @return array|null Converted attributes array, or null if conversion fails
     */
    private function formatExtraAttributes($attributes)
    {
        // If already an array, return it
        if (is_array($attributes)) {
            return $attributes;
        }

        // If empty, return null
        if (empty($attributes)) {
            return null;
        }

        // If it's a string, try to parse it into an array
        if (is_string($attributes)) {
            try {

                // If not JSON, try to parse "key"="value","key"="value" format
                $result = [];
                $pattern = '/(?:"([^"]+)"|\'([^\']+)\')=(?:"([^"]*)"|\'([^\']*)\'|([^,]*))?(?:,|$)/';
                if (preg_match_all($pattern, $attributes, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $key = $match[1] ?? $match[2] ?? '';
                        $value = $match[3] ?? $match[4] ?? $match[5] ?? '';

                        if ($key !== '') {
                            $result[$key] = $value;
                        }
                    }

                    return $result;
                }
                // If the above pattern doesn't match, try a simpler pattern
                $pattern = '/([^=,]+)=([^,]*)(?:,|$)/';
                if (preg_match_all($pattern, $attributes, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $key = trim($match[1], '"\'');
                        $value = trim($match[2], '"\'');

                        if ($key !== '') {
                            $result[$key] = $value;
                        }
                    }

                    return $result;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Error parsing extra_attributes: ' . $e->getMessage(), [
                    'attributes' => $attributes,
                ]);
            }
        }

        // If conversion fails, return null
        return null;
    }

    public function create(): void
    {

        $formData = $this->form->getState();

        // $emailFrom = $this->contactForm->from ?? config('mail.from.address');
        $emailTo = $this->contactForm->to ?? '';
        $emailSubject = $this->contactForm->subject ?? 'New Contact Form Submission';
        $emailBody = $this->contactForm->email_body ?? '';

        // $replacedFrom = $this->replaceVariables($emailFrom, $formData);
        $replacedTo = $this->replaceVariables($emailTo, $formData);
        $replacedSubject = $this->replaceVariables($emailSubject, $formData);
        $replacedBody = $this->replaceVariables(str($emailBody)->sanitizeHtml(), $formData);

        try {
            Mail::send([], [], function ($message) use ($replacedTo, $replacedSubject, $replacedBody, $formData) {
                $message->to($replacedTo)
                    ->subject($replacedSubject)
                    ->html($replacedBody);
                // Handle attachments

                foreach ($formData as $key => $value) {

                    // if (is_array($value) && isset($value['livewire'])) {
                    //     $path = storage_path('app/livewire-tmp/' . $value['livewire']);
                    //     if (file_exists($path)) {
                    //         $originalName = $value['name'] ?? basename($path);
                    //         $message->attach($path, ['as' => $originalName]);
                    //     }
                    // } else
                    if (in_array($key, $this->referencedFields)) {
                        if (is_string($value) && strpos($value, 'contact-uploads/') === 0) {
                            $path = storage_path('app/public/' . $value);
                            if (file_exists($path)) {
                                $originalName = basename($value);
                                $message->attach($path, ['as' => $originalName]);
                            }
                        }
                    }
                }

            });

            Notification::make()
                ->title($this->contactForm->success_message ?? 'Success')
                // ->body('Your contact form has been successfully submitted.')
                ->success()
                ->send();

            $this->form->fill(); // Reset form

        } catch (\Exception $e) {
            // Error handling
            \Illuminate\Support\Facades\Log::error('Contact form email error: ' . $e->getMessage());

            Notification::make()
                ->title($this->contactForm->error_message ?? 'Error')
                ->body('Error sending email: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function replaceVariables(string $text, array $data): string
    {
        $this->referencedFields = [];
        preg_match_all('/\{\{([^}]+)\}\}/', $text, $matches);

        if (empty($matches[1])) {
            return $text;
        }

        foreach ($matches[1] as $key => $varName) {
            $varName = trim($varName);
            $this->referencedFields[] = $varName;
            $varValue = $data[$varName] ?? '';
            if (is_array($varValue) || is_object($varValue)) {
                if (is_array($varValue)) {

                    if (isset($varValue['name'])) {
                        $varValue = $varValue['name'];
                    } else {
                        $varValue = implode(', ', $varValue);
                    }
                } else {

                    $varValue = (string) $varValue;
                }
            }

            $text = str_replace($matches[0][$key], $varValue, $text);
        }

        return $text;
    }

    public function submit()
    {
        $data = $this->form->getState();

    }

    public function render(): View
    {
        return view('simple-contact-form::livewire.contact-form-component', [
            'form' => $this->form,
        ]);
    }
}
