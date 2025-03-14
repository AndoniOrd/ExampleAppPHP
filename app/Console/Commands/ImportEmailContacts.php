<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmailContactsImport;

class ImportEmailContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:emailcontacts {file : The path to the Excel file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Email Contacts from an XLSX file into the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        try {
            Excel::import(new EmailContactsImport, $file);
            $this->info("Import successful!");
        } catch (\Exception $e) {
            $this->error("Error during import: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
