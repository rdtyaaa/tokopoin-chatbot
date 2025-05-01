<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i = 0 ;$i<20;$i++){
        
            $name = [
                'en' =>'brand_en_'.$i,
            ];
            $top = 2;
            if($i%2 == 0){
                $top = 1;
            }

            Brand::create([
                'name' => json_encode($name),
                'serial' => $i,
                'status' => '1',
                'top' => $top
            ]);


        }
    }
}
