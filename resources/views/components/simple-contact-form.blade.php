
<div class="simple-contact-form-wrapper w-auto">
    @once
        
        @if(file_exists(public_path('build/assets/index.css')) || file_exists(public_path('css/simple-contact-form.css')))
            
            @if(file_exists(public_path('css/simple-contact-form.css')))
                <link rel="stylesheet" href="{{ asset('css/simple-contact-form.css') }}">
            @else
                @vite(['resources/css/app.css'])
            @endif
        @elseif(!app()->environment('testing'))
            <link rel="stylesheet" href="{{ asset('vendor/simple-contact-form/simple-contact-form.css') }}">
        @endif
        @livewireStyles
        @filamentStyles
        @livewire('notifications')
    @endonce
    @if($form)
        <livewire:contact-form :id="$form" />
    @else
        <div class="p-4 bg-red-100 text-red-800 rounded">
            Contact form not found. Please provide a valid form ID.
        </div>
    @endif
    @once
        @livewireScripts
        @filamentScripts
    @endonce
</div>