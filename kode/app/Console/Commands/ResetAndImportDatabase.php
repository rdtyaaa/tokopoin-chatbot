<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ResetAndImportDatabase extends Command
{
    protected $signature = 'reset:import-db'; // <- Ini nama perintahnya
    protected $description = 'Wipe DB, import database.sql, and run seeders';

    public function handle()
    {
        $this->info("Wiping database...");
        Artisan::call('db:wipe', ['--force' => true]);

        $this->info("Importing raw SQL...");
        $sqlFile = resource_path('database/database.sql');
        DB::unprepared(file_get_contents($sqlFile));

        $this->info("Running seeders...");
        Artisan::call('db:seed', ['--force' => true]);

        $this->info("All done.");
    }
}
