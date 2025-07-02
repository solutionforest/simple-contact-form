<?php

namespace SolutionForest\SimpleContactForm;

use Filament\Contracts\Plugin;
use Filament\Panel;
use SolutionForest\SimpleContactForm\Resources\ContactFormResource;
use SolutionForest\SimpleContactForm\Resources\ContactForms\ContactFormResource as ContactFormsContactFormResource;

class SimpleContactFormPlugin implements Plugin
{
    public function getId(): string
    {
        return 'simple-contact-form';
    }

    public function register(Panel $panel): void
    {
         $panel->resources([
            ContactFormsContactFormResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
       

       
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
