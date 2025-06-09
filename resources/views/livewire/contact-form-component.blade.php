<div>
    @error('formError')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ $message }}
        </div>
    @enderror
    
    @if($contactForm->exists)
        <form wire:submit="create">
            {{ $this->form }}
            
            <button type="submit">
                Submit
            </button>
        </form>
    @endif
</div>