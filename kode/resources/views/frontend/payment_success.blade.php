@extends('frontend.layouts.app')
@section('content')

<section class="payment-success pt-80 pb-80 bg--primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
               <div class="payment-wrapper">
               <div class="icon icon-success">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" /><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" /></svg>
                </div>
                <div class="title">
                    <h4 class="success--title text-center mb-5">
                         {{translate('Payment Successful')}}
                    </h4>
                </div>
                <ul class="payment-list">

                    <li>
                        <span>{{translate('Payment Method')}}</span> <span>
                            {{$paymentLog->paymentGateway->name}} 
                        </span>
                    </li>
                    <li>
                        <span>  {{translate("Amount")}}</span> <span>
                            <span>{{short_amount($paymentLog->amount)}}</span>
                        </span>
                    </li>
                    <li>
                        <span>
                            {{translate("Charge")}}
                        </span>
                        <span>{{short_amount($paymentLog->charge)}}</span>
                    </li>
                    <li>
                        <span>
                            {{translate("Paid Amout")}}
                        </span>
                        <span>{{short_amount($paymentLog->amount + $paymentLog->charge)}}</span>
                    </li>

                    @if(@$order)
                        <li>
                            <span>
                                {{translate("Order Id")}}
                            </span>
                            <span>{{$order->order_id}}</span>
                        </li>
                    @endif

                    <li>
                        <span>
                            {{translate("Transaction Number")}}
                        </span>
                        <span>{{$paymentLog->trx_number}}</span>
                    </li>
              
                </ul>
                
                    <div class="buttons-group d-flex justify-content-center gap-4">
                        <a href="{{route('home')}}" class="dark--btn"><i class="las la-home me-3"></i>
                            {{translate("Back To Home")}}
                        </a>
                    </div>
               </div>         
            </div>
        </div>
    </div>
</section>

@endsection