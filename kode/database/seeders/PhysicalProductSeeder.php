<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use App\Models\PlanSubscription;
use App\Models\ShippingDelivery;
use App\Http\Utility\ProductGallery;
use App\Models\ProductShippingDelivery;

class PhysicalProductSeeder extends Seeder
{
    public function run()
    {
        // Mengambil data seller yang sudah ada
        $seller = \App\Models\Seller::first(); // Bisa disesuaikan dengan seller yang aktif

        // Mengambil subscription yang aktif
        $subscription = PlanSubscription::where('seller_id', $seller->id)->where('status', 1)->first();

        if (!$subscription) {
            echo 'Seller does not have an active subscription.';
            return;
        }

        if ($subscription->total_product < 1) {
            echo 'Not enough product balance.';
            return;
        }

        // Mengambil kategori yang sudah ada
        $category = \App\Models\Category::first(); // Mengambil kategori pertama, bisa disesuaikan

        // Mengambil atribut yang sudah ada
        $attribute = \App\Models\Attribute::first(); // Mengambil atribut pertama, bisa disesuaikan

        // Menambahkan produk dengan menggunakan data yang sudah ada
        $product = Product::create([
            'name' => 'Sample Product',
            'slug' => 'sample-product',
            'point' => 100,
            'seller_id' => $seller->id,
            'product_type' => Product::PHYSICAL,
            'price' => 20000,
            'weight' => 1,
            'shipping_fee' => 5000,
            'shipping_fee_multiply' => 1,
            'discount' => 18000,
            'discount_percentage' => 10,
            'minimum_purchase_qty' => 1,
            'maximum_purchase_qty' => 10,
            'brand_id' => 1, // Asumsi ada brand dengan ID 1
            'category_id' => $category->id, // Mengambil kategori yang sudah ada
            'sub_category_id' => 1, // Bisa disesuaikan jika ada subkategori
            'short_description' => 'This is a short description.',
            'description' => 'This is a long description of the product.',
            'shipping_country' => 'ID',
            'featured_image' => 'featured_image.jpg',
            'meta_title' => 'Sample Product Meta Title',
            'meta_image' => 'meta_image.jpg',
            'meta_keywords' => 'sample, product, seeder',
            'meta_description' => 'Meta description for the product.',
            'warranty_policy' => '1 year warranty.',
            'status' => Product::NEW,
        ]);

        // Menambahkan pilihan atribut jika ada
        $choice_options = json_encode(
            [
                [
                    'attribute_id' => $attribute->id,
                    'values' => ['Red', 'Blue', 'Green'], // Asumsi atribut memiliki nilai seperti ini
                ],
            ],
            JSON_UNESCAPED_UNICODE,
        );

        $product->attributes_value = $choice_options;
        $product->attributes = json_encode([$attribute->id]);
        $product->save();

        // Menambahkan galeri gambar produk
        // $galleryImage = ['image1.jpg', 'image2.jpg'];
        // ProductGallery::imageStore($galleryImage, $product->id);

        // Menambahkan pengiriman produk
        // $shippingDeliveries = ShippingDelivery::pluck('id');
        // foreach ($shippingDeliveries as $value) {
        //     ProductShippingDelivery::create([
        //         'product_id' => $product->id,
        //         'shipping_delivery_id' => $value,
        //     ]);
        // }

        // Kurangi kuota produk subscription
        $subscription->total_product -= 1;
        $subscription->save();

        echo 'Product has been seeded successfully.';
    }
}
