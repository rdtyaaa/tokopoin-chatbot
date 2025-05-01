@extends('admin.layouts.app')

@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ translate('Deposit Details') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('seller.dashboard') }}">
                                {{ translate('Home') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('seller.deposit.list') }}">
                                {{ translate('Deposit Logs') }}
                            </a>
                        </li>

                        <li class="breadcrumb-item active">
                            {{ translate('Deposit Details') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-8 mx-auto">

                    <div class="card">

                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex">
                                <h5 class="card-title flex-grow-1 mb-0">
                                    {{ translate('Deposit Details') }}
                                </h5>
                            </div>
                        </div>

                        <div class="card-body">
                            <ul class="list-group list-group-flush">

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Selle/Customer') }} :
                                        </span>
                                        <span>
                                            @if(@$paymentLog->seller)
                                            <span>{{@$paymentLog->seller->name}}</span><br>
                                              <a href="{{route('admin.seller.info.details', $paymentLog->seller_id)}}" class="fw-bold text-dark">{{(@$paymentLog->seller->email)}}</a>

                                            @elseif(@$paymentLog->user)
                                                    <a href="{{route('admin.customer.details', $paymentLog->user_id)}}" class="fw-bold text-dark">{{(@$paymentLog->user->name)}}</a><br>
                                                    {{(@$paymentLog->user->email)}}
                                            @else
                                                {{translate('N/A')}}
                                            @endif
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Date') }} :
                                        </span>
                                        <span>
                                            {{ diff_for_humans($paymentLog->created_at) }}
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Method') }} :
                                        </span>
                                        <span>
                                            {{(@$paymentLog->paymentGateway->name ?? 'N/A')}}
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Amount') }} :
                                        </span>
                                        <span>
                                            {{round(($paymentLog->amount))}} {{default_currency()->name}}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Final Amount') }} :
                                        </span>
                                        <span>
                                            {{round(($paymentLog->final_amount))}} {{@$paymentLog->paymentGateway->currency->name ?? default_currency()->name}}
                                        </span>
                                    </li>

                            
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                            <span class="fw-semibold text-break">{{ translate('Status') }} :
                                            
                                            </span>
                                            @if($paymentLog->status == "1")
                                                <span class="badge badge-soft-primary">{{translate('Pending')}}</span>
                                            @elseif($paymentLog->status == "2")
                                                <span class="badge badge-soft-info">{{translate('Received')}}</span>
                                            @elseif($paymentLog->status == "3")
                                                <span class="badge badge-soft-danger">{{translate('Rejected')}}</span>
                                            @endif
                                    </li>

                                    @if($paymentLog->custom_info)

                                       @foreach ($paymentLog->custom_info as $key => $data)


                                        <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                            <span class="fw-semibold text-break">{{ k2t($key) }} :
                                            </span>
                                            <span>
                                                {{ $data }}
                                            </span>
                                        </li>

                                        
                                       @endforeach
                                    
                                    @endif

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="fw-semibold text-break">{{ translate('Feedback') }} :
                                        </span>
                                        <span>
                                            {{ $paymentLog->feedback ?  $paymentLog->feedback  :'N/A' }}
                                        </span>
                                    </li>
                                
                            </ul>
                            

                            @if($paymentLog->status == 1)
                                <div class="text-center mt-3">
                                    
                                    <div class="gap-2 d-flex align-items-center w-">
                                        <button data-id='{{$paymentLog->id}}' data-status ="2"  class=" update-report btn btn-success btn-sm w-100 waves ripple-light">
              
                                            <i class="ri-check-double-fill me-1 align-bottom"></i>
                                            {{translate('Accept')}}
                                        </button>
                                        <button data-id='{{$paymentLog->id}}'   data-status ="3" class="update-report  btn btn-danger btn-sm w-100 waves ripple-light">
                                            <i class="ri-close-line  me-1 align-bottom"></i>
                                            {{translate('Reject')}}
                                        </button>
                                    </div>

                                </div>
                            @endif

                        </div>

                    </div> 
                    
                </div>
            </div>

        </div>
    </div>




    <div class="modal fade" id="reportUpdate" tabindex="-1" aria-labelledby="reportUpdate" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.deposit.update')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="reportId">
                    <input type="hidden" name="status" id="reportStatus">
                    <div class="modal-body">

                         <div>
                             <label for="feedback">
                                 {{translate('Feedback')}} <span class="text-danger" >*</span>
                             </label>
                             <textarea placeholder="{{translate('Type here ....')}}" required class="form-control" name="feedback" id="feedback" cols="30" rows="10"></textarea>
                         </div>
                      
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                        <button type="submit" class="btn btn-danger">{{translate('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('script-push')
<script>
	(function($){
       	"use strict";

		$(".update-report").on("click", function(){
			var modal = $("#reportUpdate");
			modal.find('input[name=id]').val($(this).attr('data-id'));
			modal.find('input[name=status]').val($(this).attr('data-status'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush