<?php

namespace NoteBrainsLab\FilamentEmailTemplates;

use Filament\Contracts\Plugin;
use Filament\Panel;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;


class FilamentEmailTemplatesPlugin implements Plugin
{
    protected string $navigationGroup = 'Email Templates';
    protected ?string $navigationIcon = null;
    protected ?int $navigationSort = 1;
    protected bool $navigationBadge = true;

    public function getId(): string
    {
        return 'filament-email-templates';
    }

    public function navigationGroup(string $group): static
    {
        $this->navigationGroup = $group;
        return $this;
    }

    public function getNavigationGroup(): string
    {
        return $this->navigationGroup;
    }

    public function navigationIcon(string $icon): static
    {
        $this->navigationIcon = $icon;
        return $this;
    }

    public function getNavigationIcon(): ?string
    {
        return $this->navigationIcon;
    }

    public function navigationSort(int $sort): static
    {
        $this->navigationSort = $sort;
        return $this;
    }

    public function getNavigationSort(): ?int
    {
        return $this->navigationSort;
    }

    public function navigationBadge(bool $show = true): static
    {
        $this->navigationBadge = $show;
        return $this;
    }

    public function isNavigationBadgeEnabled(): bool
    {
        return $this->navigationBadge;
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            EmailTemplateResource::class,
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
