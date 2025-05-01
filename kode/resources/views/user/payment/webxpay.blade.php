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


                                  <form action="{{$data->base_url}}" method="{{$data->method}}" class="form">
                                    @csrf
                                     
                                    
                                       @foreach($data as $k=> $v)
                                            <input hidden name="{{$k}}" value="{{$v}}"/>
                                       @endforeach
                                       
                                       
                                         <button class="btn btn-success btn-lg fs-14  mt-4">
                                             {{translate('Submit')}}
                                        </button>

                                      

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
