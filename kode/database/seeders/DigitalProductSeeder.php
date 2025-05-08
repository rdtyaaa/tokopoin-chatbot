<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\DigitalProductAttribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DigitalProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Ebook Panduan Sukses UMKM',
                'point' => 0,
                'slug' => 'ebook-panduan-sukses-umkm',
                'description' => '<p>Lengkap dan praktis untuk pelaku usaha kecil.</p>',
                'meta_title' => 'Ebook UMKM',
                'meta_keywords' => 'ebook, umkm, bisnis',
                'meta_description' => 'Ebook untuk pelaku UMKM.',
                'category_id' => 1,
                'sub_category_id' => 2,
                'status' => 1,
                'custom_fields' => [
                    'format_file' => [
                        'data_label' => 'Format File',
                        'data_required' => true,
                        'data_value' => 'PDF',
                        'data_name' => 'format_file',
                        'type' => 'text',
                    ],
                    'jumlah_halaman' => [
                        'data_label' => 'Jumlah Halaman',
                        'data_required' => true,
                        'data_value' => '120',
                        'data_name' => 'jumlah_halaman',
                        'type' => 'number',
                    ],
                ],
                'attributes' => [
                    ['name' => 'Lisensi Reguler', 'price' => 0],
                    ['name' => 'Lisensi Komersial', 'price' => 10000],
                ],
            ],
            [
                'name' => 'Template Website HTML',
                'point' => 0,
                'slug' => 'template-website-html',
                'description' => '<p>Template modern responsif untuk web portofolio.</p>',
                'meta_title' => 'Template Web',
                'meta_keywords' => 'template, website, html, css',
                'meta_description' => 'Template HTML untuk portofolio.',
                'category_id' => 1,
                'sub_category_id' => 3,
                'status' => 1,
                'custom_fields' => [
                    'browser_support' => [
                        'data_label' => 'Dukungan Browser',
                        'data_required' => true,
                        'data_value' => 'Chrome, Firefox, Safari',
                        'data_name' => 'browser_support',
                        'type' => 'text',
                    ],
                    'responsive' => [
                        'data_label' => 'Responsif',
                        'data_required' => true,
                        'data_value' => 'Ya',
                        'data_name' => 'responsive',
                        'type' => 'radio',
                    ],
                ],
                'attributes' => [
                    ['name' => 'Versi Personal', 'price' => 0],
                    ['name' => 'Versi Developer', 'price' => 20000],
                ],
            ],
            [
                'name' => 'Font Kaligrafi Modern',
                'point' => 0,
                'slug' => 'font-kaligrafi-modern',
                'description' => '<p>Font elegan untuk branding atau undangan.</p>',
                'meta_title' => 'Font Kaligrafi',
                'meta_keywords' => 'font, kaligrafi, desain',
                'meta_description' => 'Font cantik kaligrafi.',
                'category_id' => 1,
                'sub_category_id' => 4,
                'status' => 1,
                'custom_fields' => [
                    'format_font' => [
                        'data_label' => 'Format Font',
                        'data_required' => true,
                        'data_value' => 'TTF, OTF',
                        'data_name' => 'format_font',
                        'type' => 'text',
                    ],
                    'jumlah_glyph' => [
                        'data_label' => 'Jumlah Glyph',
                        'data_required' => false,
                        'data_value' => '230',
                        'data_name' => 'jumlah_glyph',
                        'type' => 'number',
                    ],
                ],
                'attributes' => [
                    ['name' => 'Free Use', 'price' => 0],
                    ['name' => 'Extended License', 'price' => 15000],
                ],
            ],
        ];

        foreach ($products as $data) {
            $product = Product::create([
                'name' => $data['name'],
                'point' => $data['point'],
                'slug' => $data['slug'],
                'product_type' => Product::DIGITAL,
                'category_id' => $data['category_id'],
                'sub_category_id' => $data['sub_category_id'],
                'description' => $data['description'],
                'meta_title' => $data['meta_title'],
                'meta_keywords' => $data['meta_keywords'],
                'meta_description' => $data['meta_description'],
                'meta_image' => 'https://img.freepik.com/premium-psd/3d-digital-marketing-icons-promote-website-computer-advertising_643838-85.jpg',
                'featured_image' => 'https://img.freepik.com/premium-psd/3d-digital-marketing-icons-promote-website-computer-advertising_643838-85.jpg',
                'status' => $data['status'],
                'custom_fileds' => $data['custom_fields'],
            ]);

            foreach ($data['attributes'] as $attr) {
                DigitalProductAttribute::create([
                    'product_id' => $product->id,
                    'name' => $attr['name'],
                    'price' => $attr['price'],
                ]);
            }
        }
    }
}
