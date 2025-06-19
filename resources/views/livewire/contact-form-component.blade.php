<div class="inline-block w-auto">
    @once
        @livewireStyles
        @filamentStyles
        @livewire('notifications')
    @endonce
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

    @once
        @livewireScripts
        @filamentScripts
    @endonce
</div>