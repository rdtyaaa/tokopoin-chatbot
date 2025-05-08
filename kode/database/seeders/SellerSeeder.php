<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\SellerShopSetting;
use App\Models\PricingPlan;
use App\Models\PlanSubscription;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        // Tambahkan dua plan
        $gratis = PricingPlan::firstOrCreate([
            'name' => 'Gratis Selamanya'
        ], [
            'amount' => 0,
            'duration' => 30,
            'total_product' => 10,
            'status' => 1,
        ]);

        $hemat = PricingPlan::firstOrCreate([
            'name' => 'Paket Hemat'
        ], [
            'amount' => 50000,
            'duration' => 60,
            'total_product' => 100,
            'status' => 1,
        ]);

        $image = 'https://media.istockphoto.com/id/2173059563/vector/coming-soon-image-on-white-background-no-photo-available.jpg?s=612x612&w=0&k=20&c=v0a_B58wPFNDPULSiw_BmPyhSNCyrP_d17i2BPPyDTk=';

        for ($i = 1; $i <= 10; $i++) {
            $seller = Seller::create([
                'username' => 'seller' . $i,
                'email' => 'seller' . $i . '@example.com',
                'password' => Hash::make('password'),
                'status' => 1,
                'balance' => 100000,
            ]);

            SellerShopSetting::create([
                'seller_id' => $seller->id,
                'name' => 'Toko ' . $i,
                'email' => 'toko' . $i . '@example.com',
                'phone' => '0812345678' . $i,
                'address' => 'Alamat Toko ' . $i,
                'short_details' => 'Deskripsi singkat toko ' . $i,
                'shop_logo' => $image,
                'shop_first_image' => $image,
                'shop_second_image' => $image,
                'shop_third_image' => $image,
                'seller_site_logo' => $image,
                'status' => 1,
            ]);

            // Alternating plan
            $plan = ($i % 2 === 0) ? $hemat : $gratis;

            if ($plan->amount > 0) {
                $seller->balance -= $plan->amount;
                $seller->save();

                Transaction::create([
                    'seller_id' => $seller->id,
                    'amount' => $plan->amount,
                    'post_balance' => $seller->balance,
                    'transaction_type' => Transaction::MINUS,
                    'transaction_number' => Str::upper(Str::random(10)),
                    'details' => 'Subscription ' . $plan->name . ' plan',
                ]);
            }

            PlanSubscription::create([
                'seller_id' => $seller->id,
                'plan_id' => $plan->id,
                'total_product' => $plan->total_product,
                'expired_date' => Carbon::now()->addDays($plan->duration),
                'status' => PlanSubscription::RUNNING,
            ]);
        }
    }
}
