<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('campaign.{campaignId}', function ($user, $campaignId) {
    return true;
});
