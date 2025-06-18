<div class="inline-block w-auto">
    @livewireStyles
    @filamentStyles
    @livewire('notifications')
    <form wire:submit="create" {!! $this->customClass !!} >
        <div >
            {{ $this->form }}
        </div>

        <div class="flex justify-end">
            <x-filament::button type="submit" >
                <span class="fi-btn-label">Submit</span>
            </x-filament::button>
        </div>
    </form>
     @livewireScripts
    @filamentScripts
    

</div>