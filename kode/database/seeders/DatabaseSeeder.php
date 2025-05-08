<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;


use Illuminate\Database\Seeder;
use Database\Seeders\LangSeeder;

use Database\Seeders\BrandSeeder;
use Database\Seeders\SellerSeeder;
use Database\Seeders\UpdateSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TemplateSeeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\SMSgatewaySeeder;
use Database\Seeders\Admin\SettingsSeeder;
use Database\Seeders\CountriesTableSeeder;
use Database\Seeders\GeneralSettingsSeeder;
use Database\Seeders\Admin\AdminCredentialSeeder;

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
            BrandSeeder::class,
            CategorySeeder::class,
            AttributeSeeder::class,
            SellerSeeder::class,
            // DigitalProductSeeder::class,
            // PhysicalProductSeeder::class,
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
