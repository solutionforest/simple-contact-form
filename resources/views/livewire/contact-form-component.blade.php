
<div class="simple-contact-form-wrapper">
    <form wire:submit="create" {!! $this->customClass !!}>
        <div>
            {{ $this->form }}
        </div>
        @if($this->hasFormContent())
            <div class="flex justify-end mt-3">
                <x-filament::button type="submit">
                    <span class="fi-btn-label">Submit</span>
                </x-filament::button>
            </div>
        @endif
    </form>
</div>