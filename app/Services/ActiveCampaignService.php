<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ActiveCampaignService
{
    /**
     * The Active Campaign API URL.
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * The Active Campaign API key.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The list ID to fetch emails from.
     *
     * @var string
     */
    protected $listId;

    /**
     * Create a new Active Campaign service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUrl = config('services.activecampaign.url');
        $this->apiKey = config('services.activecampaign.key');
        $this->listId = config('services.activecampaign.list_id');
    }

    /**
     * Get emails from the specified list.
     *
     * @param int|null $limit
     * @return array
     */
    public function getEmailsFromList($limit = 100)
    {
        try {
            // Use caching to avoid multiple API calls in the same period
            $cacheKey = 'active_campaign_emails_' . $this->listId;
            
            return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($limit) {
                $response = Http::withHeaders([
                    'Api-Token' => $this->apiKey
                ])->get("{$this->apiUrl}/api/3/contacts", [
                    'listid' => $this->listId,
                    'limit' => $limit
                ]);
                
                if (!$response->successful()) {
                    Log::error('Failed to fetch emails from Active Campaign', [
                        'status' => $response->status(),
                        'response' => $response->json()
                    ]);
                    
                    return [];
                }
                
                $contacts = $response->json()['contacts'] ?? [];
                $emails = [];
                
                foreach ($contacts as $contact) {
                    // Check if contact has email and is subscribed
                    if (!empty($contact['email']) && ($contact['status'] == 1)) {
                        $emails[] = [
                            'email' => $contact['email'],
                            'name' => $contact['firstName'] ?? '',
                            'last_name' => $contact['lastName'] ?? '',
                            'contact_id' => $contact['id']
                        ];
                    }
                }
                
                return $emails;
            });
        } catch (\Exception $e) {
            Log::error('Exception while fetching emails from Active Campaign', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }
}