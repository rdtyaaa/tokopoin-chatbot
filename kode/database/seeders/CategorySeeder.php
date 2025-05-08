<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $image = 'https://cdn3d.iconscout.com/3d/premium/thumb/gallery-3d-icon-download-in-png-blend-fbx-gltf-file-formats--picture-photo-image-photography-camera-user-interface-pack-icons-5209349.png';
        $serial = 1;

        // Membersihkan data kategori jika sudah ada
        Category::truncate();

        $categories = [
            'Elektronik' => ['Handphone', 'Laptop', 'Tablet', 'TV', 'Aksesoris Elektronik'],
            'Pakaian' => ['Pria', 'Wanita', 'Anak-anak', 'Muslim', 'Pakaian Dalam'],
            'Kecantikan & Perawatan' => ['Skincare', 'Makeup', 'Parfum', 'Hair Care', 'Body Care'],
            'Rumah Tangga' => ['Perabotan', 'Alat Dapur', 'Dekorasi', 'Kebersihan'],
            'Sembako & Makanan' => ['Beras', 'Minyak', 'Makanan Kaleng', 'Cemilan', 'Minuman'],
            'Bayi & Anak' => ['Popok', 'Mainan Anak', 'Pakaian Anak', 'Perlengkapan Bayi'],
            'Kesehatan' => ['Obat-obatan', 'Vitamin', 'Alat Kesehatan'],
            'Olahraga' => ['Perlengkapan Fitness', 'Sepeda', 'Pakaian Olahraga'],
            'Otomotif' => ['Sparepart', 'Aksesoris Motor', 'Aksesoris Mobil'],
            'Alat Tulis & Kantor' => ['ATK', 'Printer', 'Kertas', 'Alat Ukur'],
            'Buku & Edukasi' => ['Buku Sekolah', 'Novel', 'Komik', 'Ensiklopedia'],
            'Hobi & Koleksi' => ['Action Figure', 'Model Kit', 'Koleksi Koin', 'Kartu Koleksi'],
            'Pertanian & Peternakan' => ['Benih', 'Pupuk', 'Alat Pertanian', 'Pakan Ternak'],
            'Peralatan Industri' => ['Alat Berat', 'Safety Gear', 'Mesin Produksi'],
            'Properti & Real Estate' => ['Tanah', 'Rumah', 'Ruko', 'Apartemen'],
            'Perhiasan & Aksesori' => ['Kalung', 'Cincin', 'Gelang', 'Jam Tangan'],
            'Peralatan Dapur' => ['Panci', 'Wajan', 'Blender', 'Rice Cooker'],
            'Game & Konsol' => ['Playstation', 'Xbox', 'Nintendo', 'Game PC'],
            'Drone & Kamera' => ['Drone', 'DSLR', 'Action Cam', 'Tripod'],
            'Furniture' => ['Sofa', 'Meja', 'Kursi', 'Lemari'],
        ];

        // Menambahkan kategori induk dan anak
        foreach ($categories as $parent => $children) {
            // Membuat atau mendapatkan kategori induk
            $parentCategory = Category::firstOrCreate([
                'name' => json_encode(['id' => $parent]),  // Mengubah format menjadi {"id":"Elektronik"}
                'parent_id' => null,
            ], [
                'serial' => $serial++,
                'banner' => $image,
                'image_icon' => $image,
                'meta_title' => $parent,
                'meta_description' => $parent,
                'meta_image' => $image,
                'status' => '1',
                'top' => 1,
                'slug' => Str::slug($parent),
                'uid' => Str::uuid(),
            ]);

            // Menambahkan kategori anak
            foreach ($children as $child) {
                // Pastikan slug unik
                $slug = Str::slug($child);
                if (Category::where('slug', $slug)->exists()) {
                    $slug .= '-' . Str::random(3); // Menambahkan string random jika slug sudah ada
                }

                Category::firstOrCreate([
                    'name' => json_encode(['id' => $child]),  // Mengubah format menjadi {"id":"Handphone"}
                    'parent_id' => $parentCategory->id,
                ], [
                    'serial' => $serial++,
                    'banner' => $image,
                    'image_icon' => $image,
                    'meta_title' => $child,
                    'meta_description' => $child,
                    'meta_image' => $image,
                    'status' => '1',
                    'top' => 0,
                    'slug' => $slug,
                    'uid' => Str::uuid(),
                ]);
            }
        }

        // Tambahkan kategori lainnya di akhir
        Category::firstOrCreate([
            'name' => json_encode(['id' => 'Kategori Lainnya']),  // Mengubah format menjadi {"id":"Kategori Lainnya"}
            'parent_id' => null,
        ], [
            'serial' => $serial++,
            'banner' => $image,
            'image_icon' => $image,
            'meta_title' => 'Kategori Lainnya',
            'meta_description' => 'Kategori Lainnya',
            'meta_image' => $image,
            'status' => '1',
            'top' => 0,
            'slug' => Str::slug('Kategori Lainnya'),
            'uid' => Str::uuid(),
        ]);
    }
}
