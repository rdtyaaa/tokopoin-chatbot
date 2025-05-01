

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
                    <div class="col-md-12">
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
                                        <span>{{short_amount($log->amount)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">                                        <span>
                                            {{translate("Charge")}}
                                        </span>
                                        <span>{{short_amount($log->charge)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">                                        <span>
                                            {{translate("Payable")}}
                                        </span>
                                        <span>{{short_amount($log->amount + $log->charge)}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">                                        <span>
                                            {{translate("In")}}
                                            {{$log->paymentGateway->currency->name}}</span>
                                            <span>
                                            
                                                {{show_amount(round($log->final_amount),$log->paymentGateway->currency->symbol)}}
                                            </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
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
                                            <img class="img-fluid img-thumbnail avatar-xl" src="{{show_image(file_path()['payment_method']['path'].'/'.$method->image,file_path()['payment_method']['size'])}}" alt="{{$method->name}}">
                                        </div>
                                        <h5 class="mt-3 fs-16 ">{{$method->name}}</h5>
                                    </div>
                                    <div class="text-center mt-5">
                                        
                                        
                                        @php
                                                $paymnetGateway =  $log->paymentGateway;
                                                $paymentParams   =  $paymnetGateway->payment_parameter;
                                        @endphp


                                        <form action="{{route('seller.deposit.manual.request')}}" method="POST">

                                            @csrf

                                            <input type="hidden" name="gw_id" value="{{  $paymnetGateway->id }}">


                                            <div class="row g-3">

                                                @foreach ($paymentParams as $param)

                                                    <div class="col-md-12">
                                                    
                                                        <label for="{{$param->name}}" class="form-label text-start">
                                                            {{k2t($param->name)}} @if($param->is_required) <span class="text-danger" >*</span>@endif
                                                        </label>
                                                        @if($param->type == 'text' || $param->type == 'date' || $param->type == 'email')
                                                            <input  class="form-control user-info" type="{{$param->type}}" id="{{$param->name}}" name="custom_input[{{$param->name}}]" value="{{old('custom_input.'.$param->name)}}"
                                                            placeholder="{{k2t($param->name)}}" {{ $param->is_required ? "required" :"" }} >
                                                        @else

                                                            <textarea class="form-control" {{ $param->is_required ? "required" :"" }}  placeholder="{{k2t($param->name)}}" id="{{$param->name}}" name="custom_input[{{$param->name}}]" cols="5" rows="5">{{old('custom_input.'.$param->name)}}</textarea>

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
	</div>
</div>


@endsection



