

@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Deposit Preview")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{$title}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
		
             <div class="card-body">
                <div class="row gx-4 gy-5">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                                <h4 class="card-title">
                                                    {{$title}}
                                                </h4>
                                        </div>
                                </div>
                            </div>
                            <div class="card-body">

                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span>
                                            {{translate("Amount")}}
                                        </span>
                                        <span>{{short_amount($paymentLog->amount)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">                                        <span>
                                            {{translate("Charge")}}
                                        </span>
                                        <span>{{short_amount($paymentLog->charge)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">                                        <span>
                                            {{translate("Payable")}}
                                        </span>
                                        <span>{{short_amount($paymentLog->amount + $paymentLog->charge)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">                                        <span>
                                            {{translate("In")}}
                                            {{$paymentLog->paymentGateway->currency->name}}</span>
                                            <span>
                                            
                                                {{show_amount(round($paymentLog->final_amount),$paymentLog->paymentGateway->currency->symbol)}}
                                            </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                         <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{translate("Payment Method")}}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center  flex-column">
                                    <div class="pay-method text-center">
                                        <div class="pay-method-img">
                                            <img class="img-fluid img-thumbnail avatar-xl" src="{{show_image(file_path()['payment_method']['path'].'/'.$paymentLog->paymentGateway->image,file_path()['payment_method']['size'])}}" alt="{{$paymentLog->paymentGateway->name}}">
                                        </div>
                                        <h5 class="mt-3 fs-16 ">{{$paymentLog->paymentGateway->name}}</h5>
                                    </div>
                                    <div class="text-center mt-5">
                                        <div id="paypal-button-container"></div>
                                    </div>
                                </div>
    
                            </div>
                        </div>
                    </div>
                </div>
             </div>

		
		</div>
	</div>
</div>


@endsection


@push('script-push')

<script src="https://www.paypal.com/sdk/js?client-id={{$data->cleint_id}}">
</script>
<script>
    
    "use strict";
        paypal.Buttons({
        createOrder: function (data, actions) {
            return actions.order.create({
                purchase_units: [
                    {
                        description: "{{ $data->description }}",
                        custom_id: "{{$data->custom_id}}",
                        amount: {
                            currency_code: "{{$data->currency}}",
                            value: "{{$data->amount}}",
                            breakdown: {
                                item_total: {
                                    currency_code: "{{$data->currency}}",
                                    value: "{{$data->amount}}"
                                }
                            }
                        }
                    }
                ]
            });
        },
        onApprove: function (data, actions) {
            return actions.order.capture().then(function (details) {
                var trx = "{{$data->custom_id}}";
                window.location = '{{ url('paypal/payment/callback')}}/' + trx + '/' + details.id

            });
        }
    }).render('#paypal-button-container');
</script>
@endpush