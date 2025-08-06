<?php

namespace SolutionForest\SimpleContactForm;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use SolutionForest\SimpleContactForm\Commands\SimpleContactFormCommand;
use SolutionForest\SimpleContactForm\Testing\TestsSimpleContactForm;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SimpleContactFormServiceProvider extends PackageServiceProvider
{
    public static string $name = 'simple-contact-form';

    public static string $viewNamespace = 'simple-contact-form';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->publishAssets() // This will publish the CSS file
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('solutionforest/simple-contact-form');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();

            // Register language files for publishing separately
            if (app()->runningInConsole()) {
                $this->publishes([
                    $package->basePath('/../resources/lang') => resource_path('lang/vendor/simple-contact-form'),
                ], 'simple-contact-form-lang');
            }
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('contact-form', \SolutionForest\SimpleContactForm\Livewire\ContactFormComponent::class);
        }
        if (class_exists(Blade::class)) {
            Blade::component('simple-contact-form', \SolutionForest\SimpleContactForm\View\Components\SimpleContactForm::class);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/simple-contact-form/{$file->getFilename()}"),
                ], 'simple-contact-form-stubs');
            }

            // Publish bundled CSS with Filament styles
            $this->publishes([
                __DIR__ . '/../resources/dist/simple-contact-form.css' => public_path('vendor/simple-contact-form/simple-contact-form.css'),
            ], 'simple-contact-form-assets');
        }
        //   if (class_exists(\Filament\Facades\Filament::class)) {
        //     \Filament\Facades\Filament::registerPlugin(
        //         \SolutionForest\SimpleContactForm\SimpleContactFormPlugin::make()
        //     );
        // }
        // Testing
        Testable::mixin(new TestsSimpleContactForm);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'solutionforest/simple-contact-form';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('simple-contact-form', __DIR__ . '/../resources/dist/components/simple-contact-form.js'),
            Css::make('simple-contact-form-styles', __DIR__ . '/../resources/dist/simple-contact-form.css'),
            Js::make('simple-contact-form-scripts', __DIR__ . '/../resources/dist/simple-contact-form.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            SimpleContactFormCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_simple_contact_form_table',
        ];
    }
}
