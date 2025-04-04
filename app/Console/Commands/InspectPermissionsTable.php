<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InspectPermissionsTable extends Command
{
    protected $signature = 'inspect:permissions';
    protected $description = 'Inspect the permissions table structure';

    public function handle()
    {
        $columns = Schema::getColumnListing('permissions');
        $this->info('Permissions table columns:');
        foreach ($columns as $column) {
            $this->line("- $column");
        }
        return 0;
    }
}