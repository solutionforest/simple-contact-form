<?php

namespace SolutionForest\SimpleContactForm;

use Filament\Contracts\Plugin;
use Filament\Panel;
use SolutionForest\SimpleContactForm\Resources\ContactForms\ContactFormResource as ContactFormsContactFormResource;

class SimpleContactFormPlugin implements Plugin
{
    protected string $modelLabel = 'Contact Form';

    protected string $pluralModelLabel = 'Contact Forms';

    protected ?string $navigationLabel = null;

    protected ?string $navigationGroup = null;

    protected string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected int $navigationSort = 100;

    protected ?string $navigationParentItem = null;

    protected string $slug = 'contact-forms';

    protected bool $shouldSkipAuth = true;

    protected bool $shouldRegisterNavigation = true;

    protected bool $hasTitleCaseModelLabel = true;

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

    public function boot(Panel $panel): void {}

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

    // Fluent setters
    public function modelLabel(string $label): static
    {
        $this->modelLabel = $label;

        return $this;
    }

    public function pluralModelLabel(string $label): static
    {
        $this->pluralModelLabel = $label;

        return $this;
    }

    public function navigationLabel(string $label): static
    {
        $this->navigationLabel = $label;

        return $this;
    }

    public function navigationGroup(?string $group): static
    {
        $this->navigationGroup = $group;

        return $this;
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;

        return $this;
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;

        return $this;
    }

    public function navigationParentItem(?string $parent): static
    {
        $this->navigationParentItem = $parent;

        return $this;
    }

    public function slug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function shouldSkipAuth(bool $skip = true): static
    {
        $this->shouldSkipAuth = $skip;

        return $this;
    }

    public function shouldRegisterNavigation(bool $register = true): static
    {
        $this->shouldRegisterNavigation = $register;

        return $this;
    }

    public function hasTitleCaseModelLabel(bool $titleCase = true): static
    {
        $this->hasTitleCaseModelLabel = $titleCase;

        return $this;
    }

    // Getters for Resource to use
    public function getModelLabel(): string
    {
        return $this->modelLabel;
    }

    public function getPluralModelLabel(): string
    {
        return $this->pluralModelLabel;
    }

    public function getNavigationLabel(): string
    {
        return $this->navigationLabel ?? $this->pluralModelLabel;
    }

    public function getNavigationGroup(): ?string
    {
        return $this->navigationGroup;
    }

    public function getNavigationIcon(): string
    {
        return $this->navigationIcon;
    }

    public function getNavigationSort(): int
    {
        return $this->navigationSort;
    }

    public function getNavigationParentItem(): ?string
    {
        return $this->navigationParentItem;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getShouldSkipAuth(): bool
    {
        return $this->shouldSkipAuth;
    }

    public function getShouldRegisterNavigation(): bool
    {
        return $this->shouldRegisterNavigation;
    }

    public function getHasTitleCaseModelLabel(): bool
    {
        return $this->hasTitleCaseModelLabel;
    }
}
