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
                              <span> {{$paymentLog->paymentGateway->currency->symbol}}{{round($paymentLog->final_amount)}}</span>
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


                                  <form action="{{$data->data_url}}" method="POST" class="form">
                                    @csrf
                                    
                                       <input type="hidden" id="amount" name="amount" value="{{$data->amount}}" required>
                                       <input type="hidden" id="tax_amount" name="tax_amount" value ="{{$data->tax_amount}}" required>
                                       <input type="hidden" id="total_amount" name="total_amount" value="{{$data->amount}}" required>
                                       <input type="hidden" id="transaction_uuid" name="transaction_uuid" value="{{$data->transaction_uuid}}"required>
                                       <input type="hidden" id="product_code" name="product_code" value ="{{$data->product_code}}" required>
                                       <input type="hidden" id="product_service_charge" name="product_service_charge" value="{{$data->product_service_charge}}" required>
                                       <input type="hidden" id="product_delivery_charge" name="product_delivery_charge" value="{{$data->product_delivery_charge}}" required>
                                       <input type="hidden" id="success_url" name="success_url" value="{{$data->success_url}}" required>
                                       <input type="hidden" id="failure_url" name="failure_url" value="{{$data->failure_url}}" required>
                                       <input type="hidden" id="signed_field_names" name="signed_field_names" value="{{$data->message}}" required>
                                       <input type="hidden" id="signature" name="signature" value="{{$data->signature}}" required>

                                      <div class="text-center mt-5 w-100">
                                        <button  class="btn btn-success btn-lg fs-14 w-100">
                                            {{translate("Pay now")}} 
                                        </button>
                                      </div>
                                  </form>

                   
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


