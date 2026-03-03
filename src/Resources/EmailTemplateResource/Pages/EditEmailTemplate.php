<?php

namespace NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\Width;
use Filament\Resources\Pages\EditRecord;
use NoteBrainsLab\FilamentEmailTemplates\Resources\EmailTemplateResource;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Called from JavaScript BEFORE any save.
     * Receives the full Unlayer-generated HTML + JSON, stores them,
     * then triggers the actual Filament save — all in ONE Livewire request.
     * This avoids the "client state overwrites server state" race condition.
     */
    public function syncUnlayerExport(string $html, array $design): void
    {
        $this->data['unlayer_state'] = json_encode([
            'design' => $design,
            'html'   => $html,
        ]);

        $this->save();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        $data['unlayer_state'] = json_encode([
            'design' => $record->body_json,
            'html'   => $record->body_html,
        ]);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
