<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\EmailProvider;

class EmailProviderService
{
    /**
     * Get a list of all available email providers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProviders()
    {
        try {
            // Assuming you have an email_providers table
            return EmailProvider::where('active', true)->get();
        } catch (\Exception $e) {
            Log::error('Failed to fetch email providers', [
                'error' => $e->getMessage()
            ]);
            
            return collect([]);
        }
    }

    /**
     * Get a random email provider.
     *
     * @return \App\Models\EmailProvider|null
     */
    public function getRandomProvider()
    {
        $providers = $this->getAllProviders();
        
        if ($providers->isEmpty()) {
            return null;
        }
        
        // Get a random provider
        return $providers->random();
    }

    /**
     * Get a provider by ID.
     *
     * @param int $id
     * @return \App\Models\EmailProvider|null
     */
    public function getProviderById($id)
    {
        try {
            return EmailProvider::where('id', $id)
                ->where('active', true)
                ->first();
        } catch (\Exception $e) {
            Log::error('Failed to fetch email provider by ID', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
}