@extends('seller.layouts.app')

@section('main_content')
<div class="page-content">
	<div class="container-fluid">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Deposit Details")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item"><a href="{{route('seller.deposit.list')}}">
                        {{translate('Deposits')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Deposit  Details")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">

			
			<div class="card-body">

 
				<div class="row g-4">

                
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
                                
    
                    
    
                            </div>
    
                        </div> 
                        
                    </div>
                 
        
					
	        	</div>
			</div>
		</div>
	</div>
</div>


@endsection
