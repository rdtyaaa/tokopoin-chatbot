


@extends('frontend.layouts.app')
@section('content')
<section class="pt-80 pb-80">
    <div class="Container">
        <div class="pay-preview">
            <div class="row gx-4 gy-5">
                <div class="col-md-6">
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
                <div class="col-md-6">
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
                                <div class=" mt-5 w-100">
                                    @php
                                       $paymnetGateway =  $paymentLog->paymentGateway;
                                       $paymentParams   =  $paymnetGateway->payment_parameter;
                                    @endphp


                                    <form action="{{route('manual.payment')}}" method="POST">

                                        @csrf

                                        <input type="hidden" name="gw_id" value="{{  $paymnetGateway->id }}">


                                        <div class="row g-3">

                                            @foreach ($paymentParams as $param)

                                                <div class="col-md-12">
                                                 
                                                    <label for="{{$param->name}}" class="form-label">
                                                        {{k2t($param->name)}} @if($param->is_required) <span class="text-danger" >*</span>@endif
                                                    </label>
                                                    @if($param->type == 'text' || $param->type == 'date' || $param->type == 'email')
                                                        <input  class="form-control user-info" type="{{$param->type}}" id="{{$param->name}}" name="custom_input[{{$param->name}}]" value="{{old('custom_input.'.$param->name)}}"
                                                        placeholder="{{k2t($param->name)}}" {{ $param->is_required ? "required" :"" }} >
                                                    @else

                                                        <textarea {{ $param->is_required ? "required" :"" }}  placeholder="{{k2t($param->name)}}" id="{{$param->name}}" name="custom_input[{{$param->name}}]" cols="5" rows="5">{{old('custom_input.'.$param->name)}}</textarea>

                                                    @endif
                                                
                                                </div>
 
                                           @endforeach


                                        </div>

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

