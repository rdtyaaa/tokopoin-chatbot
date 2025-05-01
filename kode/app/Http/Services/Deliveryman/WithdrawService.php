<?php

namespace App\Http\Services\Deliveryman;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Models\DeliveryMan;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Models\WithdrawMethod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Validator;
class WithdrawService extends Controller
{



    /**
     * Get withdraw list
     *
     * @param DeliveryMan $deliveryMan
     * @return LengthAwarePaginator
     */
    public function getPaginatedList(DeliveryMan $deliveryMan) :LengthAwarePaginator{

        return Withdraw::with('method', 'currency')
                            ->whereNull('seller_id')
                            ->where('deliveryman_id', $deliveryMan->id)
                            ->date()
                            ->search()
                            ->where('status', '!=', Withdraw::INITIATE)
                            ->latest()->paginate(site_settings('pagination_number',10))
                            ->appends(request()->all());
    }



    /**
     * Get all active withdraw method
     *
     * @return Collection
     */
    public function getMethod() : Collection {
        
        return  WithdrawMethod::with(['currency'])
                                    ->active()
                                    ->latest()
                                    ->get();
    }



    /**
     * Create a withdraw request
     *
     * @param Request $request
     * @param DeliveryMan $deliveryMan
     * @return array
     */
    public function createRequest(Request $request , DeliveryMan $deliveryMan) : array {


                $withdrawMethod = WithdrawMethod::where('id', $request->input('id'))
                                            ->active()
                                            ->firstOrFail();
                                            
                // check seller balance                            
                if ($request->input('amount') > $deliveryMan->balance) {
                        return [
                            'status'  => false,
                            'message' => translate("You do not have sufficient balance for withdraw.")
                        ];
                }

                // check withdraw limit
                if($request->input('amount') < $withdrawMethod->min_limit || $request->input('amount') > $withdrawMethod->max_limit) {
                    return [
                        'status'  => false,
                        'message' => translate("Please follow withdraw limit")
                    ];
                }
              
                $withdrawCharge = $withdrawMethod->fixed_charge + ($request->amount * $withdrawMethod->percent_charge / 100);
                $afterCharge    = $request->amount - $withdrawCharge;
                $finalAmount    = $afterCharge * $withdrawMethod->rate;

                $withdraw                = new Withdraw();
                $withdraw->method_id     = $withdrawMethod->id;
                $withdraw->deliveryman_id  = $deliveryMan->id;
                $withdraw->amount        = $request->input('amount');
                $withdraw->currency_id   = $withdrawMethod->currency_id;
                $withdraw->rate          = $withdrawMethod->rate;
                $withdraw->charge        = $withdrawCharge;
                $withdraw->final_amount  = $finalAmount;
                $withdraw->trx_number    = trx_number();
                $withdraw->status        = Withdraw::INITIATE;
                $withdraw->created_at    = Carbon::now();
                $withdraw->save();

                return [
                    'withdraw' => $withdraw,
                    'status'   => true,
                    'message'  => translate("Withdraw request created")
                ];



    }




    
    /**
     * Withdraw information store
     *
     * @param Request $request
     * @param DeliveryMan $deliveryMan
     * @return array
     */
    public function store (Request $request , DeliveryMan $deliveryMan) :array {

        $withdraw =  Withdraw::where('id', $request->input('id'))
                             ->where('status',  Withdraw::INITIATE)
                             ->whereNull('seller_id')
                             ->where('deliveryman_id', $deliveryMan->id)
                             ->first();

        if(!$withdraw)  return [ 'status'  => false, 'message' => translate("Invalid withdraw request")];

        if($withdraw->amount > $deliveryMan->balance) return [ 'status'  => false, 'message' => translate("You do not have sufficient balance for withdraw.")];
    
        $rules = [];

        if ($withdraw->method->user_information != null) {
            foreach ($withdraw->method->user_information as $key => $value) {
                $rules[$key] = ['required'];
                if($value->type == 'text'){
                    array_push($rules[$key], 'max:191');
                }
                else{
                    array_push($rules[$key], 'max:300');
                }
            }
        }



        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) return [ 'status'  => false, 'message' => "Validation error . please fill up all  required inputs"];
        

        $collection = collect($request);
        $userInformationData = [];
        if ($withdraw->method->user_information != null) {
            foreach ($collection as $firstKey => $firstValue) {
                foreach ($withdraw->method->user_information as $key => $value) {
                    if ($firstKey != $key){
                        continue;
                    }else{
                        if($value->type == 'file'){

                        }else{
                            $userInformationData[$key] = $firstValue;
                            $userInformationData[$key] = [
                                'data_name' => $firstValue,
                                'type' => $value->type,
                            ];
                        }
                    }
                }
            }
            $withdraw->withdraw_information = $userInformationData;
        }
        $withdraw->status = Withdraw::PENDIGN;
        $withdraw->save();

        $deliveryMan->balance  -=  $withdraw->amount;
        $deliveryMan->save();

        $transaction = Transaction::create([
            'deliveryman_id'       => $deliveryMan->id,
            'amount'               => $withdraw->amount,
            'post_balance'         => $deliveryMan->balance,
            'transaction_type'     => Transaction::MINUS,
            'transaction_number'   => $withdraw->trx_number,
            'details'              => show_amount($withdraw->final_amount ,default_currency()->symbol) .  ' Withdraw Via ' . $withdraw->method->name,
        ]);


        $mailCode = [
            'trx'             => $withdraw->trx,
            'amount'          => ($withdraw->amount),
            'charge'          => ($withdraw->charge),
            'currency'        => default_currency()->name,
            'rate'            => ($withdraw->rate),
            'method_name'     => $withdraw->method->name,
            'method_currency' => $withdraw->currency->name,
            'method_amount'   => ($withdraw->final_amount),
            'user_balance'    => ($deliveryMan->balance)
        ];


        SendMailJob::dispatch($deliveryMan,'WITHDRAW_REQUEST_AMOUNT',$mailCode);


        return [
            'status'   => true,
            'message'  => translate("Withdraw request created")
        ];
      
    }



  

}