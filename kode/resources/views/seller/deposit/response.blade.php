@extends('seller.layouts.app')

@push('style-push')

 <style>
            .payment-wrapper .checkmark {
                width: 100px;
                height: 100px;
                border-radius: 50%;
                display: block;
                stroke-width: 2;
                stroke: #4bb71b;
                stroke-miterlimit: 10;
                box-shadow: inset 0px 0px 0px #4bb71b;
                -webkit-animation: fill 0.4s ease-in-out 0.4s forwards,
                    scale 0.3s ease-in-out 0.9s both;
                animation: fill 0.4s ease-in-out 0.4s forwards,
                    scale 0.3s ease-in-out 0.9s both;
                position: relative;
                top: 5px;
                right: 5px;
                margin: 0 auto;
            }
            .payment-wrapper .checkmark__circle {
                stroke-dasharray: 166;
                stroke-dashoffset: 166;
                stroke-width: 2;
                stroke-miterlimit: 10;
                stroke: #4bb71b;
                fill: #fff;
                -webkit-animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
                animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
            }

            .payment-wrapper .checkmark__check {
                transform-origin: 50% 50%;
                stroke-dasharray: 48;
                stroke-dashoffset: 48;
                -webkit-animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
                animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
            }


            .payment-wrapper {
                background-color: var(--white);
                padding: 35px;
                border-radius: 10px;
            }
            @media (max-width: 576px) {
                .payment-wrapper {
                    padding: 35px 20px;
                }
            }
            .payment-wrapper .title h4 {
                font-size: 20px;
                font-weight: 600;
            }
            .payment-wrapper .title h4.success--title {
               color: #4bb71b;
            }
            .payment-wrapper .title h4.failed--title {
               color: #ff0303;
            }
            .payment-wrapper .icon {
               margin-bottom: 3.5rem;
            }
            .payment-wrapper .icon svg {
               max-width: 10rem;
            }


            

                        
            @-webkit-keyframes stroke {
                100% {
                    stroke-dashoffset: 0;
                }
            }

            @keyframes stroke {
                100% {
                    stroke-dashoffset: 0;
                }
            }

            @-webkit-keyframes scale {
                0%,
                100% {
                    transform: none;
                }

            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
            }

            @keyframes scale {
            0%,
            100% {
                transform: none;
            }

            50% {
                transform: scale3d(1.1, 1.1, 1);
            }
            }

            @-webkit-keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #4bb71b;
            }
            }

            @keyframes fill {
            100% {
                box-shadow: inset 0px 0px 0px 30px #4bb71b;
            }
            }

            
            .path {
                stroke-dasharray: 1000;
                stroke-dashoffset: 0;
            }
            .path.circle {
                -webkit-animation: dash 2s ease-in-out;
                animation: dash 2s ease-in-out;
            }
            .path.line {
                stroke-dashoffset: 1000;
                -webkit-animation: dash 2s 0.35s ease-in-out forwards;
                animation: dash 2s 0.35s ease-in-out forwards;
            }
            .path.check {
                stroke-dashoffset: -100;
                -webkit-animation: dash-check 2s 0.35s ease-in-out forwards;
                animation: dash-check 2s 0.35s ease-in-out forwards;
            }

            @-webkit-keyframes dash {
            0% {
                stroke-dashoffset: 1000;
            }
            100% {
                stroke-dashoffset: 0;
            }
            }
            @keyframes dash {
            0% {
                stroke-dashoffset: 1000;
            }
            100% {
                stroke-dashoffset: 0;
            }
            }
            @-webkit-keyframes dash-check {
            0% {
                stroke-dashoffset: -100;
            }
            100% {
                stroke-dashoffset: 900;
            }
            }
            @keyframes dash-check {
            0% {
                stroke-dashoffset: -100;
            }
            100% {
                stroke-dashoffset: 900;
            }
            }


 </style>
  
@endpush

@section('main_content')
<div class="page-content">
	<div class="container-fluid">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Deposit Response")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Deposit  Response")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">

			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate("Deposit Response")}}
                        </h5>
					</div>
				</div>
			</div>

			<div class="card-body">

 
				<div class="row g-4">

                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                           <div class="payment-wrapper">
                            <div class="my-4">

                                @if(request()->query('status') == 'success')
                                     <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" /><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" /></svg>
                                @else

                                <div class="icon text-center">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                        <circle class="path circle" fill="none" stroke="#FF0303" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                        <line class="path line" fill="none" stroke="#FF0303" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
                                        <line class="path line" fill="none" stroke="#FF0303" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
                                    </svg> 
                                </div>
                                    
                                @endif
                               
                            </div>
                            <div class="title">
                                <h4 class="success--title text-center mb-5">
                                     @if(request()->query('status') == 'success')
                                        {{translate('Deposit Successful')}}
                                     @else
                                        {{translate('Deposit Failed')}}
                                     @endif
                                </h4>
                            </div>
                            <ul class="list-group list-group-flush">
            
                                <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <span>{{translate('Payment Method')}}</span> <span>
                                        {{$log->paymentGateway->name}} 
                                    </span>
                                </li>
                                <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <span>  {{translate("Amount")}}</span> <span>
                                        <span>{{short_amount($log->amount)}}</span>
                                    </span>
                                </li>
                                <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <span>
                                        {{translate("Charge")}}
                                    </span>
                                    <span>{{short_amount($log->charge)}}</span>
                                </li>
                                <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <span>
                                        {{translate("Paid Amout")}}
                                    </span>
                                    <span>{{short_amount($log->amount + $log->charge)}}</span>
                                </li>
            
                              
            
                                <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                    <span>
                                        {{translate("Transaction Number")}}
                                    </span>
                                    <span>{{$log->trx_number}}</span>
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
