<div class="w-full max-w-2xl mx-auto">
    @livewireStyles
    @filamentStyles
    @livewire('notifications')
    <form wire:submit="create" {{ $this->customClass }} >
        <div class="form-container">
            {{ $this->form }}
        </div>

        <div class="flex justify-end">
            <!-- <button type="submit"
                >
                <span class="fi-btn-label">Submit</span>
            </button> -->
            <x-filament::button type="submit" >
                <span class="fi-btn-label">Submit</span>
            </x-filament::button>
        </div>
    </form>
     @livewireScripts
    @filamentScripts
    

</div>