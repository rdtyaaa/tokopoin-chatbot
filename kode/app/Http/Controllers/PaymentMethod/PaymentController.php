<?php

namespace App\Http\Controllers\PaymentMethod;

use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Http\Services\Frontend\CheckoutService;
use App\Http\Services\Frontend\ProductService;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Razorpay\Api\Api;
class PaymentController extends Controller
{

    
    protected CheckoutService $checkoutService ; 
    protected ProductService $productService ; 

    protected ? User $user;
    public function __construct() {
        $this->checkoutService = new CheckoutService();
        $this->productService  = new ProductService();

        $this->middleware(function ($request, $next) {
            $this->user = auth_user('web');
            return $next($request);
        });
    }


    public function preview()
    {

    	$title              = "Payment Info";
    	$paymentTrackNumber = session()->get('payment_track');
    	$paymentLog         = PaymentLog::with(['paymentGateway'])
                                        ->where('trx_number', $paymentTrackNumber)
                                        ->first();
    	return view('user.payment', compact('title', 'paymentLog'));
    }

    public function paymentConfirm()
    {
    	$paymentTrackNumber = session()->get('payment_track');

        $paymentLog = PaymentLog::with(['paymentGateway'])
                            ->where('trx_number', $paymentTrackNumber)
                            ->first();

        if(!$paymentLog) return back()->with("error",translate('Invalid order'));

    	$paymentMethod = PaymentMethod::where('unique_code', $paymentLog->paymentGateway->unique_code)->first();

        

    	if(!$paymentMethod) return back()->with('error',translate("Invalid Payment gateway"));

        if($paymentMethod->type == PaymentMethod::MANUAL){
            $title = "Payment with " .$paymentLog->paymentGateway->name;
            return view('user.payment.manual',compact('title','paymentLog'));
        }


    	if($paymentLog->paymentGateway->unique_code == "STRIPE101"){
            return (new StripePaymentController())->payment();
    	}
        if($paymentLog->paymentGateway->unique_code == "BKASH102"){
    		$title = "Payment with Bkash";
    		return view('user.payment.bkash', compact('title','paymentLog','paymentMethod'));
    	}
        if($paymentLog->paymentGateway->unique_code == "NAGAD104"){
    		$title = "Payment with Nagad";
    		return view('user.payment.nagad', compact('title','paymentLog','paymentMethod'));
    	}
        else if($paymentLog->paymentGateway->unique_code == "PAYPAL102"){
            return (new PaypalPaymentController())->payment();
        }else if($paymentLog->paymentGateway->unique_code == "PAYSTACK103"){
            return (new PaystackPayment())->payment();
        }
        else if($paymentLog->paymentGateway->unique_code == "FLUTTERWAVE105"){
            return (new FlutterwavePaymentController())->payment();
        }
        else if($paymentLog->paymentGateway->unique_code == "INSTA106"){
            $title = "Payment with Instamojo";
            return view('user.payment.instamojo', compact('title', 'paymentMethod', 'paymentLog'));
        }
        else if($paymentLog->paymentGateway->unique_code == "RAZORPAY106"){
            return (new RazorpayPaymentController())->payment();
        }

        else if($paymentLog->paymentGateway->unique_code == "MERCADO101"){
            return (new MercadopagoController())->payment();
        }
       
        else if($paymentLog->paymentGateway->unique_code == "CASHMAAL103"){
            return (new CashmaalPaymentController())->payment();
        }
        else if($paymentLog->paymentGateway->unique_code == "PAYEER105"){
            return (new PayeerPaymentController())->payment();
        }

        else if($paymentLog->paymentGateway->unique_code == "AAMARPAY107"){
            return (new AamarpayPaymentController())->payment();
        }
        else if($paymentLog->paymentGateway->unique_code == "PAYU101"){
            return (new PayumoneyPaymentController())->payment();
        }
        else if($paymentLog->paymentGateway->unique_code == "PAYHERE101"){
            return (new PayherePaymentController())->payment();
        }
    
        else if($paymentLog->paymentGateway->unique_code == "PAYKU108"){
            return (new PaykuPaymentController())->payment();
        }
    
        else if($paymentLog->paymentGateway->unique_code == "PHONEPE102"){
            return (new PhonepePaymentController())->payment();
        }
        else if($paymentLog->paymentGateway->unique_code == "SENANGPAY107"){
            return (new SenangpayPaymentController())->payment();
        }

        else if($paymentLog->paymentGateway->unique_code == "NGENIUS111"){
            return (new NgeniusPaymentController())->payment();
        }

        else if($paymentLog->paymentGateway->unique_code == "ESEWA107"){
            return (new EsewaPaymentController())->payment();
        }
    
        else if($paymentLog->paymentGateway->unique_code == "WEBXPAY109"){
            return (new WebxpayPaymentController())->payment();
        }
    
        session()->forget('payment_track');
        return redirect()->route('home')->with('error',translate('Invalid payment method'));
    
    }

    public function paymentSuccess($trx_number){
        
        $paymentLog = PaymentLog::with(['paymentGateway'])->where('status',PaymentLog::SUCCESS)
                                   ->where('trx_number', $trx_number)
                                   ->firstOrfail();
        if($paymentLog->type  == PaymentType::ORDER->value) $order = Order::where('id', $paymentLog->order_id)->firstOrfail();
        
        return  view("frontend.payment_success",[
            'title'      => translate('Payment Success'),
            'paymentLog' => $paymentLog,
            'order'       => @$order ,
        ]);
    }
    public function paymentFailed(){
        
        return  view("frontend.payment_failed",[
            'title' => translate('Payment Failed')
        ]);
    }
    
    public function orderSuccess($orderId){
        
        $order      = Order::where('order_id', $orderId)->firstOrfail();
        return  view("frontend.order_success",[
            'title'      => translate('Order Success'),
            'order' => $order ,
        ]);
    }






    /**
     * Handle manual payment request
     *
     * @param Request $request
     */
    public function manualPayment(Request $request) : View | RedirectResponse {


  
        $request->validate([
            'gw_id' => 'required'
        ]);
        $paymentTrackNumber = session()->get('payment_track');

        $paymentLog = PaymentLog::with(['paymentGateway'])
                                  ->where('method_id',$request->input('gw_id'))
                                  ->whereIn('status',[PaymentLog::PENDING,PaymentLog::SUCCESS])->where('trx_number',$paymentTrackNumber)
                                  ->firstOrfail();



        if($paymentLog->type == PaymentType::ORDER->value){

            $order      = Order::where('id', $paymentLog->order_id)
                                             ->firstOrfail();
            $order->payment_details = $request->input("custom_input");
            $order->save();
            $paymentLog->status = PaymentLog::SUCCESS;
            $paymentLog->save();
            $this->checkoutService->cleanCart($this->productService->getCartItem($this->user));
            return $this->orderSuccess($order->order_id);

        }

        $paymentLog->custom_info =  $request->input("custom_input");
        $paymentLog->save();
        return redirect()->route('user.deposit.list')
                         ->with("success",translate('Your request is submitted, please wait for confirmation'));
        



    }
}
