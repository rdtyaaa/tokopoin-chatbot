<?php
namespace App\Http\Utility\Wallet;

use App\Enums\PaymentType;
use App\Enums\RewardPointStatus;
use App\Enums\StatusEnum;
use App\Models\DeliveryMan;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\RewardPointLog;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class WalletRecharge
{




 

    /**
     * Summary of walletUpdate
     * @param \App\Models\PaymentLog $log
     * @return array
     */
    public static function walletUpdate(PaymentLog $log) : array{


        $amount = $log->amount;
        if($log->user)   $user = $log->user;
        if($log->seller) $user = $log->seller;

        #CREATE TRANSACTION
        $transaction = Transaction::create([
            'seller_id'          => $log->seller ? $user->id : null,
            'user_id'            => $log->user ? $user->id : null,
            'amount'             => $amount,
            'post_balance'       => $user->balance,
            'transaction_type'   => Transaction::PLUS,
            'transaction_number' => trx_number(),
            'details'            => 'Balance Deposited VIA '.$log->paymentGateway->name,
        ]);
        
        $user->balance += $amount;
        $user->save();
        $log->status = PaymentLog::SUCCESS;
        $log->save();
        
        return [
            'status'  => true ,
            'message' => translate('Deposit success')
        ];



    }




    /**
     * Summary of creteLog
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param \App\Models\PaymentMethod $method
     * @param int $amount
     * @param bool $isUser
     * @return \App\Models\PaymentLog
     */
    public static function creteLog(\Illuminate\Contracts\Auth\Authenticatable | null $user, PaymentMethod $method , int $amount ,bool $isUser = false ): PaymentLog{

    
        $charge                   = ($amount * $method->percent_charge / 100);
        $final_amount             = ($amount  + $charge )*$method->rate;

        return  PaymentLog::create([
            'method_id'    => $method->id,
            'seller_id'    => !$isUser ? @$user->id : null,
            'user_id'      => $isUser ?  @$user->id  : null,
            'charge'       => $charge,
            'rate'         => $method->rate,
            'amount'       => $amount ,
            'final_amount' => $final_amount,
            'trx_number'   => trx_number(),
            'status'       => PaymentLog::PENDING,
            'type'         => PaymentType::WALLET->value,
        ]);

        
    }



    /**
     * Summary of generatePointLog
     * @param \App\Models\User $user
     * @param \App\Models\Order $order
     * @return void
     */
    public  static function generatePointLog(User $user , Order $order) : void{


        $reward_point_via           = site_settings('reward_point_by',0);

        $rewardPointConfigurations  = !is_array(site_settings('order_amount_based_reward_point',[])) 
                                                    ? json_decode(site_settings('order_amount_based_reward_point',[]),true) 
                                                    : [];
        $productWisePoint  = 0;

        $expireDays  =  site_settings('reward_point_expired_after',null);

        $expired_at  = $expireDays  ? Carbon::now()->addDays((int)$expireDays) : null ;


        if($reward_point_via == StatusEnum::false->status()){
            $order                      = $order->load(['orderDetails','orderDetails.product']);

            $orderDetails =    $order->orderDetails;

            if($orderDetails){

                foreach($orderDetails as $orderDetail){
                    $product = $orderDetail->product ;

                    if($product &&  $product->point > 0){
                        $pointLog             = new RewardPointLog();
                        $pointLog->user_id    = $user->id;
                        $pointLog->product_id = $product->id;
                        $pointLog->point      = $product->point;
                        $pointLog->status     = RewardPointStatus::PENDING->value;
                        $pointLog->expired_at = $expired_at;
                        $pointLog->details    = $product->point ." point added for purchasing product " . $product->name;
                        $pointLog->save();
                    
                    }
                }

            }

        }else{

            $total = $order->amount;
            
            $configuration =   collect($rewardPointConfigurations)
                                            ->filter(function ($item) use ($total) : bool {
                                                $item = (object)($item);
                                                return $total > $item->min_amount && $total <= $item->less_than_eq;
                                            })->first();

            $configuration = is_array($configuration)  
                                            ? (object)$configuration
                                            : null ; 

            $point =  @$configuration->point ?? (int) site_settings('default_reward_point',0) ;


            $pointLog             = new RewardPointLog();
            $pointLog->user_id    = $user->id;
            $pointLog->order_id   = $order->id;
            $pointLog->point      = $point;
            $pointLog->status      = RewardPointStatus::PENDING->value;
            $pointLog->expired_at = $expired_at;
            $pointLog->details    = $point ." point added for order number " . $order->order_id;

            $pointLog->save();
            
        }
    


    }




    /**
     * Summary of generateDeliveryManPointLog
     * @param \App\Models\DeliveryMan $deliveryMan
     * @param \App\Models\Order $order
     * @return void
     */
    public static function generateDeliveryManPointLog(DeliveryMan $deliveryMan , Order $order): Void{


        $rewardPointConfigurations  = !is_array(site_settings('deliveryman_reward_point_configuration',[])) 
                                            ? json_decode(site_settings('deliveryman_reward_point_configuration',[]),true) 
                                            : [];


            

        $total = $order->amount;
            
        $configuration =   collect($rewardPointConfigurations)
                                        ->filter(function ($item) use ($total) : bool {
                                            $item = (object)($item);
                                            return $total > $item->min_amount && $total <= $item->less_than_eq;
                                        })->first();

        $configuration = is_array($configuration)  
                                        ? (object)$configuration
                                        : null ; 

        $point =  @$configuration->point ?? (int) site_settings('deliveryman_default_reward_point',0) ;



        $pointLog                     = new RewardPointLog();
        $pointLog->delivery_man_id    = $deliveryMan->id;
        $pointLog->order_id           = $order->id;
        
        $pointLog->post_point         = $deliveryMan->point;
        $pointLog->point              = $point;
        $pointLog->status             = RewardPointStatus::PENDING->value;
        $pointLog->details            = $point ." point added for order number " . $order->order_id;
        $pointLog->save();
        
        $deliveryMan->point+=$point;
        $deliveryMan->save();


    }

}
