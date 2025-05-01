<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\Settings\GlobalConfig;
use App\Enums\Status;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = GlobalConfig::COUNTRIES;

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'name' => $country['name'],
                'code' => $country['code'],
                'status' => Status::ACTIVE,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
