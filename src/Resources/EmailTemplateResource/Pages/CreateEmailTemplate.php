<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Width;
use Filament\Resources\Pages\CreateRecord;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;

class CreateEmailTemplate extends CreateRecord
{
    protected static string $resource = EmailTemplateResource::class;

    /**
     * Called from JavaScript BEFORE any save on the Create page.
     * Receives the full Unlayer-generated HTML + JSON, stores them,
     * then triggers the actual Filament create — all in ONE Livewire request.
     */
    public function syncUnlayerExport(string $html, array $design): void
    {
        $this->data['unlayer_state'] = json_encode([
            'design' => $design,
            'html'   => $html,
        ]);

        // Trigger Filament's own create pipeline (mutateFormDataBeforeCreate runs here)
        $this->create();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['unlayer_state']) && is_string($data['unlayer_state'])) {
            $parsed = json_decode($data['unlayer_state'], true);
            $data['body_json'] = $parsed['design'] ?? null;
            $data['body_html'] = $parsed['html'] ?? null;
        }

        unset($data['unlayer_state']);

        return $data;
    }
}
