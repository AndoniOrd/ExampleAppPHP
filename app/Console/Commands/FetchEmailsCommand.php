<?php

namespace App\Console\Commands;

use App\Jobs\FetchEmailJob;
use App\Models\Provider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchEmailsCommand extends Command
{
    protected $signature = 'emails:fetch';
    protected $description = 'Fetch emails from all active providers';

    public function handle()
    {
        $this->info('Starting email fetch process');
        
        // Obtener todos los proveedores activos con configuración IMAP
        $providers = Provider::where('active', true)
            ->whereNotNull('imap_host')
            ->whereNotNull('imap_port')
            ->whereNotNull('imap_username')
            ->whereNotNull('imap_password')
            ->get();
            
        if ($providers->isEmpty()) {
            $this->warn('No active providers found with IMAP configuration');
            return Command::SUCCESS;
        }
        
        $this->info("Found {$providers->count()} active providers");
        
        foreach ($providers as $provider) {
            $this->info("Dispatching fetch job for provider: {$provider->name}");
            FetchEmailJob::dispatch($provider);
        }
        
        $this->info('All fetch jobs dispatched successfully');
        return Command::SUCCESS;
    }
}