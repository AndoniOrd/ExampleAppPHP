<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Facades\Filament;

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
    
    public function mount($record): void
    {
        parent::mount($record);
        
        // Add CKEditor scripts
        $this->addCkeditorScripts();
    }
    
    protected function addCkeditorScripts(): void
    {
        // Add CKEditor script to the page
        Filament::registerScripts([
            'ckeditor-script' => 'https://cdn.ckeditor.com/4.16.2/standard-all/ckeditor.js',
            'ckeditor-config' => asset('js/ckeditor-config.js'),
        ]);
        
        // Add custom CSS for the editor
        Filament::registerStyles([
            'ckeditor-styles' => asset('css/ckeditor-styles.css'),
        ]);
    }
}