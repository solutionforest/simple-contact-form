<div class="w-full max-w-2xl mx-auto">




    @filamentStyles
    @livewireStyles
    @livewireScripts
    @livewire('notifications')
    <form wire:submit="create" class="space-y-4">
        <div class="form-container">
            {{ $this->form }}
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus:ring-2 rounded-lg fi-btn-color-blue bg-primary-600 text-dark shadow-sm hover:bg-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400 focus:ring-primary-500/50 dark:focus:ring-primary-400/50 fi-size-md gap-1.5 px-3 py-2">
                <span class="fi-btn-label">Submit</span>
            </button>
        </div>
    </form>

    @filamentScripts

</div>