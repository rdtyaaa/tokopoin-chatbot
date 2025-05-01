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
                        <div class="pb-4">
                            <h4 class="card-title">
                               {{translate("Reward overview")}}
                            </h4>
                        </div>

                  

                        <div class="card mt-5">
                          

                            <div class="card-body">


                                 <div class="row">
                                     

                                    <div @if($reward_log->status == App\Enums\RewardPointStatus::PENDING->value)  class="col-6" @else    class="col-12" @endif >

                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <h4 class="card-title fs-16">
                                                            {{ translate('Basic info') }}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body">

                                                <div class="d-flex align-items-start flex-column gap-4 billing-list ">

                                                    <span class="fs-14 d-flex align-items-center gap-3"> 
                                                        <small class="text-muted fs-14">{{ translate('Point') }}:</small>

                                                       {{$reward_log->point}}
                                                   </span>
                                                    <span class="fs-14 d-flex align-items-center gap-3"> 
                                                        <small class="text-muted fs-14">{{ translate('Product') }}:</small>
                                                        {{$reward_log->product ? $reward_log->product->name : "N/A" }}
                                                   </span>
                                                    <span class="fs-14 d-flex align-items-center gap-3"> 
                                                        <small class="text-muted fs-14">{{ translate('Order') }}:</small>

                                                        @if($reward_log->order)
                                                        <a href="{{route('user.order.details',$reward_log->order->order_id)}}" class="badge-soft-primary py-1 px-2">{{$reward_log->order->order_id}}</a>
                                                        @else
                                                            N/A
                                                        @endif
                                                   </span>
                                                    <span class="fs-14 d-flex align-items-center gap-3"> 
                                                        <small class="text-muted fs-14">{{ translate('Status') }}:</small>

                                                        @php echo reward_status($reward_log->status) @endphp
                                                   </span>
                                                    <span class="fs-14 d-flex align-items-center gap-3"> 
                                                        <small class="text-muted fs-14">{{ translate('Expired at') }}:</small>

                                                        {{
                                                            $reward_log->expired_at 
                                                                     ? get_date_time($reward_log->expired_at) 
                                                                     : 'N/A' 
                                                         }}
                                                   </span>
                                                    <span class="fs-14 d-flex align-items-center gap-3"> 
                                                        <small class="text-muted fs-14">{{ translate('Created at') }}:</small>

                                                        {{$reward_log->created_at 
                                                            ? get_date_time($reward_log->created_at) 
                                                            : 'N/A'  }}
                                                   </span>

                                                    <span class="fs-14 d-flex align-items-center gap-3"> 
                                                        <small class="text-muted fs-14">{{ translate('Redeemed at') }}:</small>
                                                        {{$reward_log->redeemed_at 
                                                            ? get_date_time($reward_log->redeemed_at) 
                                                            : 'N/A'  }}
                                                   </span>
                                                   
                                                </div>
                                            </div>
                                        </div>

                                    </div>


                                    @if($reward_log->status == App\Enums\RewardPointStatus::PENDING->value)
                                        <div class="col-6">

                                            <div class="d-flex align-items-center justify-content-center flex-column gap-3 h-100">

                                                @php
                                                    $conversionRate = (int) site_settings('customer_wallet_point_conversion_rate',1); 
                                                    $amount = round($reward_log->point / $conversionRate,4);

                                                @endphp

                                                <p>
                                                    {{translate('You will get')}} <span class="text-primary fw-bold" >  {{short_amount($amount)}} </span> {{translate('For')}} {{$reward_log->point}} {{translate('Point')}}
                                                </p>
                                                <a href="{{route('user.redeem.points',$reward_log->id)}}" class="btn btn-lg btn-success mt-2">
                                                {{
                                                    translate('Redeemed Now')
                                                }}
                                                </a>
                                            </div>

                                        </div>

                                     @endif


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
<script>

"use strict"

        flatpickr("#datePicker", {
            dateFormat: "Y-m-d",
            mode: "range",
        });


</script>
@endpush

