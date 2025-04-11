<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function afterSave(): void
{
    // Update the preview tab with the latest content after saving
    $this->dispatch('email-preview-updated', [
        'content' => $this->record->content,
    ]);
}

    public function afterMount(): void
    {
        // Add JavaScript to update the preview when the page loads
        $this->registerListeners([
            'email-preview-updated' => [
                function ($event) {
                    $previewElement = $this->form->getFlatFields()['preview'] ?? null;
                    if ($previewElement) {
                        $previewElement->state(new HtmlString($event['content']));
                    }
                },
            ],
        ]);
    }
}