<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Language::where('code','en')->exists()) {
            $lang = new Language();
            $lang->name = 'English';
            $lang->code = 'en';
            $lang->created_by = '1';
            $lang->status = '1';
            $lang->is_default = '1';
            $lang->save();
        }
    }
}
