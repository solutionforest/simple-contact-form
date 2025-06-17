<div class="w-full max-w-2xl mx-auto">
   @livewire('notifications')
    <form wire:submit="create" class="space-y-4">
        <div class="form-container">
            {{ $this->form }}
        </div>
        
        <div class="flex justify-end">
            <button type="submit" >
                Submit
            </button>
        </div>
    </form>
      @filamentStyles
      @filamentScripts
    <style>
        
        /* Custom styling to override Filament defaults */
        /* .simple-contact-form .fi-form {
          
            --spacing: 0.25rem;
            gap: var(--spacing) !important;
        }
        
        .simple-contact-form .grid-cols-1,
        .simple-contact-form .fi-fo-component-ctn {
            gap: 0.25rem !important;
        } */
        
        /* .simple-contact-form .fi-input-wrp {
            margin-bottom: 0.25rem;
        }
         */
        /* Optional: Add more specific styling if needed */
        /* .simple-contact-form .fi-input,
        .simple-contact-form .fi-select-input,
        .simple-contact-form .fi-textarea {
            border-color: #e5e7eb;
            transition: all 0.2s;
        } */
        
        /* .simple-contact-form .fi-input:focus,
        .simple-contact-form .fi-select-input:focus,
        .simple-contact-form .fi-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        } */
    </style>
</div>