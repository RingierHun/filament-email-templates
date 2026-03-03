<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Forms\Components;

use Filament\Forms\Components\Field;

class UnlayerEditor extends Field
{
    protected string $view = 'filament-email-templates::forms.components.unlayer-editor';

    protected $mergeTags = [];

    public function mergeTags(array|\Closure $tags): static
    {
        $this->mergeTags = $tags;
        return $this;
    }

    public function getMergeTags(): array
    {
        return $this->evaluate($this->mergeTags) ?? [];
    }
}
