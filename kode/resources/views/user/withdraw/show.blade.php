@extends('frontend.layouts.app')

@push('stylepush')

  <style>
        .method-card{
            border: 0.1rem solid var(--gray-5) !important;
        }
        .search-input{
            padding-block: 6px;
            border-radius: 4px;
        }
  </style>

@endpush

@section('content')
   @php
     $promo_banner = frontend_section('promotional-offer');
   @endphp

<div class="breadcrumb-banner">
    <div class="breadcrumb-banner-img">
        <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($breadcrumb->value,'image'),@frontend_section_data($breadcrumb->value,'image','size'))}}" alt="breadcrumb.jpg">
    </div>
    <div class="page-Breadcrumb">
        <div class="Container">
            <div class="breadcrumb-container">
                <h1 class="breadcrumb-title">{{($title)}}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">
                            {{translate('home')}}
                        </a></li>

                        <li class="breadcrumb-item active" aria-current="page">
                            {{translate($title)}}
                        </li>

                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="pb-80">
    <div class="Container">
        <div class="row g-4">
            @include('user.partials.dashboard_sidebar')

            <div class="col-xl-9 col-lg-8">
                <div class="profile-user-right">
                    <a href="{{@frontend_section_data($promo_banner->value,'image','url')}}" class="d-block">
                        <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($promo_banner->value,'image'),@frontend_section_data($promo_banner->value,'image','size'))}}" alt="banner.jpg">
                    </a>

                    <div class="mt-5" >
                    
     

                        <div class="card mt-5">
                            <div class="card-header">
                                    <div class="d-flex justify-content-between   gap-3">
                                        <h4 class="card-title">
                                            {{translate("Withdraw Details")}}
                                        </h4>

                                        <a href="{{route('user.withdraw.list')}}" class="btn btn-lg btn-success ">
                                            {{translate('Withdraw list')}}
                                        </a>
            
                                    </div>

                             
                            </div>

                            <div class="card-body">

                             
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        {{translate('Method')}}
                                        <span class="font-weight-bold">{{(@$withdraw->method ? $withdraw->method->name : translate("N/A"))}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        <span class="font-weight-bold">{{get_date_time($withdraw->created_at)}}</span>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        {{translate('Amount')}}
                                        <span class="font-weight-bold">{{round($withdraw->amount)}} {{default_currency()->name}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        {{translate('Charge')}}
                                        <span class="font-weight-bold">{{round($withdraw->charge)}} {{default_currency()->name}}</span>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        {{translate('Receivable')}}
                                        <span class="font-weight-bold">{{round($withdraw->final_amount)}} {{@$withdraw->currency->name}}</span>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        {{translate('Feedback')}}
                                        <span class="font-weight-bold">
                                            {{
                                               $withdraw->feedback ?? 'N/A' 
                                            }}
                                        </span>
                                    </li>

                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                        {{translate('Status')}}
                                        <span class="font-weight-bold">
                                            @if($withdraw->status == 1)
                                                <span class="badge badge-soft-success">{{translate('Received')}}</span>
                                            @elseif($withdraw->status == 2)
                                                <span class="badge badge-soft-warning">{{translate('Pending')}}</span>
                                            @elseif($withdraw->status == 3)
                                                <span class="badge badge-soft-danger">{{translate('Rejected')}}</span>
                                            @endif
                                        </span>
                                    </li>

                 
                                    
                                    @if($withdraw->withdraw_information)
                                        @foreach(json_decode($withdraw->withdraw_information,true) as $key => $value)
                                        <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                                {{text_sorted($key)}}  
                                                <p>{{Arr::get($value,'data_name','N/A')}}</p>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                              

                          
                            </div>
                        </div>

                    </div>

                   
                </div>
            </div>
        </div>
    </div>
</section>

@endsection



