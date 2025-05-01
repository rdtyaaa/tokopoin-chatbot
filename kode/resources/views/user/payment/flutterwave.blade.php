@extends('frontend.layouts.app')
@section('content')

<section class="pt-80 pb-80">
    <div class="Container">
        <div class="pay-preview">
            <div class="row gx-4 gy-5">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                            <h4 class="card-title">
                                               {{translate("Payment preview")}}
                                            </h4>
                                    </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-3 pay-item">
                                <span>
                                    {{translate("Amount")}}
                                </span>
                                <span>{{short_amount($paymentLog->amount)}}</span>
                            </div>
                             <div class="d-flex align-items-center justify-content-between gap-3 pay-item">
                                <span>
                                    {{translate("Charge")}}
                                </span>
                                <span>{{short_amount($paymentLog->charge)}}</span>
                            </div>
                             <div class="d-flex align-items-center justify-content-between gap-3 pay-item">
                                <span>
                                    {{translate("Payable")}}
                                </span>
                                <span>{{short_amount($paymentLog->amount + $paymentLog->charge)}}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-3 pay-item-last">
                                <span>
                                    {{translate("In")}}
                                    {{$paymentLog->paymentGateway->currency->name}}</span>
                                    <span>
                                    
                                        {{show_amount(round($paymentLog->final_amount),$paymentLog->paymentGateway->currency->symbol)}}
                                     </span>
                            </div>
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
                            <div class="d-flex align-items-center flex-column">
                                <div class="pay-method">
                                    <div class="pay-method-img">
                                        <img class="img-fluid" src="{{show_image(file_path()['payment_method']['path'].'/'.$paymentLog->paymentGateway->image,file_path()['payment_method']['size'])}}" alt="{{$paymentLog->paymentGateway->name}}">
                                    </div>
                                    <h5 class="mt-3 fs-16">{{$paymentLog->paymentGateway->name}}</h5>
                                </div>
                                <div class="text-center mt-5">
                                        <button type="button" class="mt-3 btn btn-success fs-14 payment-btn"  id="btn-confirm" onClick="pay()">{{translate('Pay Now')}}
                                        </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@push('scriptpush')
<script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
<script>
   "use strict";
    var btn = document.querySelector("#btn-confirm");
    btn.setAttribute("type", "button");
    const API_publicKey = "{{$data->API_publicKey ?? ''}}";

    function pay() {
        var x = getpaidSetup({
            PBFPubKey: API_publicKey,
            customer_email: "{{$data->customer_email ?? 'example@example.com'}}",
            amount: "{{ $data->amount ?? '0' }}",
            customer_phone: "{{ $data->customer_phone ?? '0123' }}",
            currency: "{{ $data->currency ?? 'USD' }}",
            txref: "{{ $data->txref ?? '' }}",
            onclose: function () {
            },
            callback: function (response) {
                var txref = response.tx.txRef;
                var status = response.tx.status;
                window.location = '{{ url('flutterwave/payment/callback') }}/' + txref + '/' + status;
            }
        });
    }
</script>
@endpush
