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
        $brandNames = [
            'Samsung', 'Apple', 'Xiaomi', 'Oppo', 'Vivo', 'Realme',
            'Canon', 'Nikon', 'Sony', 'Fujifilm', 'Panasonic',
            'DJI', 'Parrot', 'Autel', 'Skydio', 'Yuneec',
            'Indofood', 'Nestle', 'Sari Roti', 'Wings', 'Kapal Api',
            'Monsanto', 'Syngenta', 'Bayer', 'Cargill', 'DuPont',
            'Unilever', 'P&G', 'Kao', 'Henkel', 'L’Oreal',
            'Zara', 'Nike', 'Adidas', 'LG', 'Sony', 'Huawei',
            'Asus', 'Lenovo', 'Microsoft', 'Dell', 'HP', 'Other'
        ];

        $serial = 1;
        $top = 2;

        foreach ($brandNames as $index => $brandName) {
            // Tentukan apakah brand ini masuk dalam top (misalnya untuk kategori tertentu, top bisa 1 atau 2)
            if ($index % 2 == 0) {
                $top = 1;
            } else {
                $top = 2;
            }

            $name = [
                'en' => $brandName
            ];

            // Membuat brand langsung tanpa kategori
            Brand::create([
                'name' => json_encode($name),
                'serial' => $serial++, // Increment serial untuk tiap brand
                'status' => 1,
                'top' => $top,
            ]);
        }
    }
}
