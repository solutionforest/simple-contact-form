const preset = require('./vendor/filament/filament/tailwind.config.preset')

module.exports = {
    presets: [preset],
    content: [
        './resources/views/**/*.blade.php',
        './src/**/*.php',
        './vendor/filament/**/*.blade.php',
        './vendor/filament/**/*.js',
    ],
    important: '.simple-contact-form-wrapper', // Scope to prevent conflicts
}
