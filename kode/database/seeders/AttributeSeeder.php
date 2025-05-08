<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;
use App\Enums\BrandStatus;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        $attributes = [
            'Warna' => ['Merah', 'Biru', 'Hijau', 'Hitam', 'Putih', 'Abu-abu', 'Cokelat', 'Kuning', 'Ungu', 'Pink'],
            'Ukuran' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'],
            'Material' => ['Katun', 'Polyester', 'Kulit Asli', 'Kulit Sintetis', 'Logam', 'Kaca', 'Kayu', 'Plastik'],
            'Kapasitas' => ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB'],
            'Berat' => ['< 1kg', '1 - 2kg', '2 - 5kg', '> 5kg'],
            'Daya Listrik' => ['100W', '250W', '500W', '750W', '1000W', '1500W'],
            'Resolusi Kamera' => ['8MP', '12MP', '16MP', '48MP', '64MP', '108MP'],
            'Jenis Bahan Pangan' => ['Organik', 'Non-Organik', 'Segar', 'Bekukan', 'Kemasan'],
            'Jenis Tanaman' => ['Sayuran', 'Buah', 'Herbal', 'Hias', 'Bibit'],
            'Kompatibilitas' => ['iOS', 'Android', 'Windows', 'Mac', 'Linux'],
            'Konektivitas' => ['Wi-Fi', 'Bluetooth', 'NFC', '4G', '5G', 'USB-C'],
            'Model Sepatu' => ['Sneakers', 'Boots', 'Flat', 'Heels', 'Loafers', 'Sandal'],
            'Tipe Mesin' => ['Manual', 'Otomatis', 'Semi Otomatis', 'Listrik', 'Hybrid'],
            'Jenis Kendaraan' => ['Motor', 'Mobil', 'Truk', 'Sepeda', 'Skuter'],
            'Durasi Garansi' => ['3 Bulan', '6 Bulan', '1 Tahun', '2 Tahun', 'Tanpa Garansi'],
            'Tipe Produk Digital' => ['eBook', 'Software', 'Game', 'Lisensi', 'Langganan'],
            'Kondisi' => ['Baru', 'Bekas', 'Refurbished'],
            'Lainnya' => ['Custom', 'Tidak Ditentukan', 'Unik', 'Multifungsi'],
        ];

        foreach ($attributes as $attributeName => $values) {
            $attribute = Attribute::create([
                'name' => $attributeName,
                'status' => BrandStatus::ACTIVE,
                'uid' => Str::uuid(),
            ]);

            foreach ($values as $val) {
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'name' => Str::slug($val),
                    'display_name' => $val,
                    'uid' => Str::uuid(),
                ]);
            }
        }
    }
}
