<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Database\Seeders\Admin\AdminCredentialSeeder;
use Database\Seeders\Admin\RoleSeeder;

use Database\Seeders\Admin\SettingsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            // RoleSeeder::class,
            // BrandSeeder::class,
            // CategorySeeder::class,
            // AdminCredentialSeeder::class,
            // SettingsSeeder::class,
            // LangSeeder::class
            // SMSgatewaySeeder::class,
            // TemplateSeeder::class,
            // GeneralSettingsSeeder::class

            UpdateSeeder::class,
            // CountriesTableSeeder::class
    
        ]);
    }
}
