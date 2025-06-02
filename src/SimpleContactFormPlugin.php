<?php

namespace SolutionForest\SimpleContactForm;

use Filament\Contracts\Plugin;
use Filament\Panel;
use SolutionForest\SimpleContactForm\Resources\ContactFormResource;

class SimpleContactFormPlugin implements Plugin
{
    public function getId(): string
    {
        return 'simple-contact-form';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ContactFormResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
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
