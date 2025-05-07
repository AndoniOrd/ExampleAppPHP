<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class CampaignProgress extends Widget
{
    public $campaignId;

    protected static string $view = 'filament.widgets.campaign-progress';

    public function mount(): void
    {
        $campaign = \App\Models\CampaignPlanning::whereIn('status_type', ['scheduled', 'processing'])
            ->latest('scheduled_time')
            ->first();

        $this->campaignId = $campaign?->id ?? 0;
    }
}
