<?php

namespace App\Filament\Widgets; // Namespace correcto

use Filament\Widgets\Widget;
use App\Models\CampaignPlanning;

class CampaignProgress extends Widget
{
    public $campaignId;

    protected static string $view = 'filament.widgets.campaign-progress'; // Ruta corregida

    public function mount(): void
    {
        $campaign = CampaignPlanning::whereIn('status_type', ['scheduled', 'processing'])
            ->latest('scheduled_time') // ¿La columna se llama scheduled_time o scheduled_at?
            ->first();

        $this->campaignId = $campaign?->id ?? 0;
    }
}