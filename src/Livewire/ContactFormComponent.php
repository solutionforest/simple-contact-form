<?php

namespace SolutionForest\SimpleContactForm\Livewire;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Flex;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Log;
use Exception;
use Filament\Forms\Components;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use SolutionForest\SimpleContactForm\Models\ContactForm;

/**
 * @property Schema $form
 */
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
            $this->addError('formError', __('simple-contact-form::simple-contact-form.errors.form_not_found'));
        }

        // if ($this->contactForm->extra_attributes) {
        //     $this->customClass = $this->formatAttributesForHtml($this->contactForm->extra_attributes);
        // } else {
        //     $this->customClass = $customClass;
        // }

        if (isset($this->contactForm->extra_attributes) && $this->contactForm->extra_attributes) {
            $this->customClass = $this->formatAttributesForHtml($this->contactForm->extra_attributes);
        } else {
            $this->customClass = $customClass;
        }
    }

    public function hasFormContent(): bool
    {

        $content = $this->contactForm->content ?? [];
        foreach ($content as $section) {
            if (! empty($section['items'])) {
                return true;
            }
        }

        return false;
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

    public function form(Schema $form): Schema
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
            ->components(
                [
                    Flex::make($schema)
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

                return TextInput::make($name)
                    ->label($label)
                    ->placeholder($placeholder)
                    ->email($field['email'] ?? false)
                    ->tel($field['tel'] ?? false)
                    ->numeric($field['number'] ?? false)
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'textarea':
                return Textarea::make($name)
                    ->label($label)
                    // ->placeholder($placeholder)
                    ->minLength($field['min_length'] ?? null)
                    ->maxLength($field['max_length'] ?? null)
                    ->required($required);

            case 'select':
                return Select::make($name)
                    ->label($label)
                    ->options(collect($field['options'] ?? [])->pluck('label', 'key')->toArray())
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'checkbox':
                return Checkbox::make($name)
                    ->label($label)
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'radio':
                return Radio::make($name)
                    ->label($label)
                    // ->inline()
                    ->options(collect($field['options'] ?? [])->pluck('label', 'key')->toArray())
                    // ->extraAttributes($extraAttributes)
                    ->required($required);

            case 'date':
                $dateComponent = null;
                if (! empty($field['include_time'])) {
                    $dateComponent = DateTimePicker::make($name);
                } else {
                    $dateComponent = DatePicker::make($name);
                }

                return $dateComponent
                    ->label($label)
                    ->placeholder($placeholder)
                    ->format(! empty($field['date_format']) ? $field['date_format'] : 'Y-m-d')
                    ->minDate(! empty($field['min_date']) ? $field['min_date'] : null)
                    ->maxDate(! empty($field['max_date']) ? $field['max_date'] : null)
                    ->required($required);

            default:
                return TextInput::make($name)
                    ->label($label)
                    ->placeholder($placeholder)
                    // ->extraAttributes($extraAttributes)
                    ->required($required);
        }
    }

    public function create(): void
    {

        $formData = $this->form->getState();

        // $emailFrom = $this->contactForm->from ?? config('mail.from.address');
        $emailTo = $this->contactForm->to ?? '';
        $emailSubject = $this->contactForm->subject ?? __('simple-contact-form::simple-contact-form.form.default_subject');
        $emailBody = $this->contactForm->email_body ?? '';

        // $replacedFrom = $this->replaceVariables($emailFrom, $formData);
        $replacedTo = $this->replaceVariables($emailTo, $formData);
        $replacedSubject = $this->replaceVariables($emailSubject, $formData);
        $replacedBody = $this->replaceVariables(str($emailBody)->sanitizeHtml(), $formData);

        try {
            if (! config('simple-contact-form.mail.enable', true)) {
                Notification::make()
                    ->title(__('simple-contact-form::simple-contact-form.errors.mail_disabled'))
                    ->body(__('simple-contact-form::simple-contact-form.errors.mail_disabled_message'))
                    ->warning()
                    ->send();

                return;
            }

            Mail::send([], [], function ($message) use ($replacedTo, $replacedSubject, $replacedBody) {
                $message->to($replacedTo)
                    ->subject($replacedSubject)
                    ->html($replacedBody);
                // Handle attachments

            });

            Notification::make()
                ->title($this->contactForm->success_message ?? __('simple-contact-form::simple-contact-form.notifications.success'))
                // ->body('Your contact form has been successfully submitted.')
                ->success()
                ->send();

            $this->form->fill(); // Reset form

        } catch (Exception $e) {
            // Error handling
            Log::error('Contact form email error: ' . $e->getMessage());

            Notification::make()
                ->title($this->contactForm->error_message ?? __('simple-contact-form::simple-contact-form.notifications.error'))
                ->body(__('simple-contact-form::simple-contact-form.errors.mail_error') . $e->getMessage())
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
