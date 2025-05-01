<?php

namespace App\Http\Services\Seller;

use App\Enums\Settings\TokenKey;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Seller;

use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\PlanSubscription;
use App\Models\PricingPlan;
use App\Models\Product;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdraw;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
class SellerService extends Controller
{




    /**
     * Get seller dashboard data 
     *
     * @param Seller|null $seller
     * @return array
     */
    public function getGraphData( ? Seller $seller) :array {
     

        $data['yearly_order_report'] = sort_by_month(array_merge(...array_map(function ($month) {
                                                return [ key($month) => reset($month)];
                                            }, Order::sellerOrder($seller->id)->selectRaw("MONTHNAME(created_at) as months, COUNT(*) as total ,COUNT(CASE WHEN order_type = 101 THEN 1 END) as digital, COUNT(CASE WHEN order_type = 102 THEN 1 END) as physical")
                                            ->whereYear('created_at',  date("Y"))
                                            ->groupBy('months')
                                            ->get()
                                            ->map(fn (Order $order) =>
                                                [
                                                    $order->months => [
                                                        'total'    => $order->total,
                                                        'digital'  => $order->digital,
                                                        'physical' => $order->physical,
                                                    ]
                                                ]
                                            )->toArray())),[
                                                'total'    => 0,
                                                'digital'  => 0,
                                                'physical' => 0,
                                            ]);


      
                   
            $data['monthly_order_report'] = Order::sellerOrder($seller->id)
                                                ->whereMonth('created_at', Carbon::now()->month)
                                                ->selectRaw('COUNT(CASE WHEN status = 1  THEN status END) AS placed')
                                                ->selectRaw('COUNT(CASE WHEN status = 2  THEN status END) AS confirmed')
                                                ->selectRaw('COUNT(CASE WHEN status = 3  THEN status END) AS processing')
                                                ->selectRaw('COUNT(CASE WHEN status = 4  THEN status END) AS shipped')
                                                ->selectRaw('COUNT(CASE WHEN status = 5  THEN status END) AS delivered')
                                                ->selectRaw('COUNT(CASE WHEN status = 6  THEN status END) AS cancel')
                                                ->first()->toArray();
            return $data;
   


    }



    
    /**
     * Get a specific seller dashboard overview data
     *
     * @param Seller $seller
     * @return array
     */
    public function getDashboardOverview( ? Seller $seller) :array {

        
        $data['total_physical_product'] = Product::sellerProduct()
                                                ->physical()
                                                ->where('seller_id', $seller->id)
                                                ->count();

        $data['total_digital_product']  = Product::sellerProduct()
                                                 ->digital()
                                                 ->where('seller_id', $seller->id)
                                                 ->count();

        $data['total_withdraw_amount']  = api_short_amount(Withdraw::where('seller_id', $seller->id)
                                                         ->approved()
                                                         ->sum('amount'));


        $data['total_ticket']           = SupportTicket::where("seller_id", $seller->id)->count();

        $data['total_digital_order']    = Order::sellerOrder($seller->id)
                                                    ->digitalOrder()
                                                    ->count();


        $data['total_physical_order']    = Order::sellerOrder($seller->id)
                                                    ->physicalOrder()
                                                    ->count();


        $data['total_placed_order']    = Order::sellerOrder($seller->id)
                                                    ->physicalOrder()
                                                    ->placed()
                                                    ->count();

        $data['total_shipped_order']    = Order::sellerOrder($seller->id)
                                                    ->physicalOrder()
                                                    ->shipped()
                                                    ->count();

        $data['total_cancel_order']    = Order::sellerOrder($seller->id)
                                                    ->physicalOrder()
                                                    ->cancel()
                                                    ->count();

        $data['total_delivered_order']    = Order::sellerOrder($seller->id)
                                                    ->physicalOrder()
                                                    ->delivered()
                                                    ->count();


        return $data;



    }





    /**
     * Update seller shop settings
     *
     * @param Request $request
     * @param Seller $seller
     * @return boolean
     */
    public function updateSellerShop(Request $request , Seller $seller) : bool {



            $shopSetting        = $seller->sellerShop;
            $shopLogo           = $shopSetting->shop_logo;
            $sellerSiteLogo     = $shopSetting->seller_site_logo;
            $logoIcon           = $shopSetting->logoicon;
            $shopFeatureImage   = $shopSetting->shop_first_image;


            // upload shop logo
            if($request->hasFile('shop_logo')) {
                try {
                    $shopLogo = store_file($request->file('shop_logo'), file_path()['shop_logo']['path'], null, $shopLogo);
                }catch (\Exception $exp) {
        
                }
            }

            // upload site logo
            if($request->hasFile('site_logo')) {
                try {
                    $sellerSiteLogo = store_file($request->file('site_logo'), file_path()['seller_site_logo']['path'] , null,$sellerSiteLogo );
                }catch (\Exception $exp) {

                }
            }


            //upload seller logo 
            if($request->hasFile('site_logo_icon')) {
                try {
                    $logoIcon = store_file($request->file('site_logo_icon'), file_path()['seller_site_logo']['path'],null, $logoIcon);

                }catch (\Exception $exp) {

                }
            }


            // shop feature image
            if($request->hasFile('shop_feature_image')) {
                try {
                    $shopFeatureImage = store_file($request->file('shop_feature_image'), file_path()['shop_first_image']['path'], null, $shopFeatureImage);
                }catch (\Exception $exp) {
                
                }
            }

       

            $shopSetting->name          = $request->input("name");
            $shopSetting->email         = $request->input('email');
            $shopSetting->phone         = $request->input('phone');
            $shopSetting->address       = $request->input('address');
            $shopSetting->short_details = $request->input('short_details');

            $shopSetting->whatsapp_number = $request->input('whatsapp_number');
            $shopSetting->whatsapp_order  = $request->input('whatsapp_order');

            $shopSetting->shop_logo        = $shopLogo;
            $shopSetting->seller_site_logo = $sellerSiteLogo;
            $shopSetting->logoicon         = $logoIcon;
            $shopSetting->shop_first_image = $shopFeatureImage;

            $shopSetting->save();


            return true;


    }



    /**
     * Update seller profile
     *
     * @param Request $request
     * @param Seller $seller
     * @return boolean
     */
    public function updateProfile(Request $request ,Seller $seller ) : bool {


        $seller->name  = $request->input('name');
        $seller->email = $request->input('email');
        $seller->phone = $request->input('phone');
        $address = [
            'address' => $request->input('address'),
            'city'    => $request->input('city'),
            'state'   => $request->input('state'),
            'zip'     => $request->input('zip')
        ];
        $seller->address = $address;
        if($request->hasFile('image')) {
            try {
                $removefile = $seller->image ?: null;
                $seller->image = store_file($request->file('image'), file_path()['profile']['seller']['path'], file_path()['profile']['seller']['size'], $removefile);
            }catch (\Exception $exp){
         
            }
        }
        $seller->save();



        return true; 
    }




    /**
     * Update seller password
     *
     * @param Request $request
     * @param Seller $seller
     * @return bool
     */
    public function updatePassword( Request $request ,Seller $seller) : bool {
    
        if (!Hash::check($request->input("current_password"), $seller->password)) return false;
        $seller->password = Hash::make($request->input("password"));
        $seller->save();
        return true;

    }



    /**
     * Get latest transaction
     *
     * @param Seller|null $seller
     * @param integer $take
     * @return Collection
     */
    public function getLatestTransaction(? Seller $seller ,int $take = 5)  : Collection {

        return Transaction::whereNotNull('seller_id')
                                        ->where('seller_id', $seller->id)
                                        ->latest()
                                        ->take($take)
                                        ->get();

    }



    /**
     * Get subscription by uid
     *
     * @param string $uid
     * @param Seller $seller
     * @return PlanSubscription | null
     */
    public function getSubscriptionByUid(string $uid ,Seller $seller) : PlanSubscription | null {

        return PlanSubscription::with(['plan'])
                      ->where('seller_id', $seller->id)
                      ->where('uid',$uid)->first();
    }



    /**
     * Get paginated transaction
     *
     * @param Seller|null $seller
     * @return LengthAwarePaginator
     */
    public function getPaginatedTransaction(? Seller $seller)  : LengthAwarePaginator {

        return Transaction::whereNotNull('seller_id')
                                        ->search()
                                        ->date()
                                        ->where('seller_id', $seller->id)
                                        ->latest()
                                        ->paginate(site_settings('pagination_number',10))
                                        ->appends(request()->all());

    }



    /**
     * Get subscription plan
     *
     * @return Collection
     */
    public function getSubscriptionPlan() : Collection{
        return    PricingPlan::active()->orderBy('amount', 'DESC')->get();
    }




     /**
     * Get paginated subscription
     *
     * @param Seller|null $seller
     * @return LengthAwarePaginator
     */
    public function getPaginatedSubscription(? Seller $seller)  : LengthAwarePaginator {

        return     PlanSubscription::date()
                              ->where('seller_id', $seller->id)
                              ->with('plan')
                              ->latest()
                              ->paginate(site_settings('pagination_number',10))
                              ->appends(request()->all());
    }



    /**
     * Get plan by id 
     *
     * @param integer $id
     * @return PricingPlan
     */
    public function getPlanById(int $id) : PricingPlan | null{
        return PricingPlan::find($id);
    }


    



    /**
     * Create a new subscription 
     * 
     * @param PricingPlan $plan
     * @param Seller $seller
     * @return array
     */
    public function createSubscription(PricingPlan $plan , Seller $seller): array{


       // Check if the seller has sufficient balance
        if($plan->amount > $seller->balance) {
            return [
                'status' => false,
                'message' => translate('You do not have a sufficient balance for subscribing'),
            ];
        }


        // Check if the seller is trying to subscribe to the Free plan again

        if ($plan->name == 'Free' && PlanSubscription::where('plan_id', $plan->id)
            ->where('seller_id', $seller->id)
            ->exists()) {
                return [
                        'status' => false,
                        'message' => translate('You cannot subscribe to the Free plan twice'),
                ];
        }

    

        DB::transaction(function() use ($seller,$plan) {

            // Invalidate previous subscriptions
            $this->InvalidatedPreviousSubscriptions($seller);

         
             // Store the new subscription
            $subscription = $this->storeSubscription([
                    'seller_id'       => $seller->id,
                    'plan_id'         => $plan->id,
                    'total_product'   => $plan->total_product,
                    'expired_date'    => Carbon::now()->addDays($plan->duration),
                    'status'          => PlanSubscription::RUNNING,
            ]);

            // Update seller's balance and create transaction
            $seller->balance -= $plan->amount;
            $seller->save();
            $transaction = $this->createTransaction($seller ,$plan->amount ,Transaction::MINUS ,'Subscription ' .$plan->name. ' plan');

            return $subscription;
        });

        return [
            'status' => true,
            'message' => translate('Plan subscribed'),
        ];

 
    }



    /**
     * Remew a specific subscrption
     *
     * @param PlanSubscription $subscriptin
     * @param Seller $seller
     * @return array
     */
    public function renewSubscription(PlanSubscription $subscription , Seller $seller) : array {


        // Check if the seller has sufficient balance
        if($subscription->plan->amount > $seller->balance) {
            return [
                'status' => false,
                'message' => translate('You do not have a sufficient balance for subscribing'),
            ];
           
        }
      // Check if the seller is trying to renew to the Free plan again
        if(@$subscription->plan->name == 'Free'){
            return [
                'status' => false,
                'message' => translate('You cannot subscribe to the Free plan twice'),
            ];
     
        }
        
        $seller->balance -= $subscription->plan->amount;
        $seller->save();


        $transaction = $this->createTransaction($seller ,$subscription->plan->amount ,Transaction::MINUS ,'Subscription ' .$subscription->plan->name. ' plan');


        $subscription->expired_date = $subscription->expired_date->addDays($subscription->plan->duration);
        $subscription->total_product = $subscription->plan->total_product;
        $subscription->status = PlanSubscription::RUNNING;
        $subscription->save();


        return [
            'status' => true,
            'message' => translate('Plan renewed'),
        ];




    }


    /**
     * Handle plan update request 
     *
     * @param PricingPlan $plan
     * @param Seller $seller
     * @return array
     */
    public function updateSubscription(PricingPlan $plan , Seller $seller ) :array {

        // Check if the plan already requested 
        if(PlanSubscription::where('status', PlanSubscription::REQUESTED)
                        ->where('plan_id', $plan->id)
                        ->where('seller_id', $seller->id)
                        ->exists()) {
            return [
                'status' => false,
                'message' => translate('You already had a pending request  under this plan'),
            ];
        }
        

        // Check if the seller has sufficient balance
        if($plan->amount > $seller->balance) {
            return [
                'status' => false,
                'message' => translate('You do not have a sufficient balance for subscribing'),
            ];
        }

        // Check if there's already a running subscription
        if (PlanSubscription::where('status', PlanSubscription::RUNNING)
                                ->where('plan_id', $plan->id)
                                ->where('seller_id', $seller->id)
                                ->exists()) {
                return [
                            'status' => false,
                            'message' => translate('Already subscribed to this plan'),
                ];
        }

        // Check if the seller is trying to subscribe to the Free plan again

        if ($plan->name == 'Free' && PlanSubscription::where('plan_id', $plan->id)
            ->where('seller_id', $seller->id)
            ->exists()) {
                return [
                        'status' => false,
                        'message' => translate('You cannot subscribe to the Free plan twice'),
                ];
        }



        DB::transaction(function() use ($seller,$plan) {

             // Store the new subscription
            $subscription = $this->storeSubscription([
                    'seller_id'       => $seller->id,
                    'plan_id'         => $plan->id,
                    'total_product'   => $plan->total_product,
                    'expired_date'    => Carbon::now()->addDays($plan->duration),
                    'status'          => PlanSubscription::REQUESTED,
            ]);

            // Update seller's balance and create transaction
            $seller->balance -= $plan->amount;
            $seller->save();
            $transaction = $this->createTransaction($seller ,$plan->amount ,Transaction::MINUS ,'Subscription ' .$plan->name. ' plan');

            return $subscription;
        });

                        
        return [
            'status' => true,
            'message' => translate('Plan update request sent successfully'),
        ];

    }




    /**
     * Store a new subscription
     *
     * @param array $data
     * @return PlanSubscription
     */
    public function storeSubscription(array $data ) :PlanSubscription{
       return  PlanSubscription::create($data);
    }




    /**
     * Invalidated previous subscription
     *
     * @param Seller $seller
     * @return void
     */
    public function InvalidatedPreviousSubscriptions(Seller $seller) : void{

        PlanSubscription::where('seller_id',$seller->id)->update([
            'status'    => PlanSubscription::EXPIRED,
        ]);
        
    }



    /**
     * Create a new transaction 
     * 
     * @param Seller $seller 
     * @param  int | float $amount 
     * @param string $trxType 
     * @param string $details 
     * 
     * @return Transaction
     */
    public function createTransaction(Seller $seller , int | float $amount , string $trxType , string $details ) :Transaction {

        return Transaction::create([
            'seller_id'           => $seller->id,
            'amount'              => $amount,
            'post_balance'        => $seller->balance,
            'transaction_type'    => $trxType,
            'transaction_number'  => trx_number(),
            'details'             => $details ,
        ]);
    }
}