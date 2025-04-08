<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use Filament\Resources\Pages\Page;
use Illuminate\Http\Request;

class PreviewEmailTemplate extends Page
{
    protected static string $resource = EmailTemplateResource::class;

    protected static string $view = 'filament.resources.email-templates.preview';
    
    public ?string $showCode = null;
    
    public function mount(Request $request): void
    {
        $this->record = $this->resolveRecord($request->route('record'));
        
        $this->showCode = $request->query('code') ? true : false;
    }
}