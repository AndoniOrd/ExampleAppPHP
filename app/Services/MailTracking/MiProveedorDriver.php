<?php

namespace App\Services\MailTracking;

use Illuminate\Support\Facades\Http;
use Vormkracht10\Mails\Events\MailEvent;

class MiProveedorDriver
{
    protected $apiKey;
    protected $endpoint;

    public function __construct($apiKey, $endpoint)
    {
        $this->apiKey = $apiKey;
        $this->endpoint = $endpoint;
    }

    public function send(array $parameters): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->post($this->endpoint . '/send', $parameters);

        return $response->json();
    }

    public function processWebhook(array $data): MailEvent
    {
        return new MailEvent([
            'event_type' => $data['status'] ?? 'unknown',
            'email' => $data['recipient'] ?? null,
            'details' => $data,
        ]);
    }
}
