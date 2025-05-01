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

                        <div class="dashboard-overview">

                            <div class="overview-card">
                                <span class="icon"><i class="fa-solid fa-award fs-20"></i></span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Total point")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">  {{translate("Total Point")}} : {{$reward_overview->total}}</p>
                                </div>
                            </div>
                            <div class="overview-card">
                                <span class="icon"><i class="fa-solid fa-award fs-20"></i></span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Pending point")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">  {{translate("Pending Point")}} : {{$reward_overview->pending}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">
                                    <i class="fa-solid fa-award fs-20"></i>
                                </span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Redeemed Point")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2"> {{translate("Redeemed Point")}} : {{$reward_overview->redeemed}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">
                                    <i class="fa-solid fa-award fs-20"></i>
                                </span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Expired Point")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">{{translate("Total")}} : {{$reward_overview->expired}}</p>
                                </div>
                            </div>

                        </div>

                        <div class="card mt-5">
                            <div class="card-header">
                                <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-sm-row flex-column gap-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{translate("Reward Points")}}
                                        </h4>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                     
                                        <form id="filter-order" action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                                         

                                             <div class="d-flex align-items-center gap-3">
                                               
                                                <select class="search-input" name="status" id="status">

                                                     <option value="">
                                                         {{translate('Select status')}}
                                                     </option>

                                                     @foreach (App\Enums\RewardPointStatus::toArray() as $key => $value )

                                                        <option {{request()->input('status') == $value ? 'selected' : ''}} value="{{$value}}">
                                                            {{ $key }}
                                                        </option>

                                                     @endforeach
                                                     
                                                </select>

                                                <input placeholder="{{translate('Search by date')}}" value="{{request()->input('date')}}" class="form-control search-input" type="text" name="date" id="datePicker">

                                                 <button class="btn btn-lg btn-dark">
                                                    {{translate("Search")}}
                                                 </button>
                                                 <a href="{{ route(Route::currentRouteName(),Route::current()->parameters()) }}"  class="btn btn-lg btn-danger">
                                                    {{translate("Reset")}}
                                                 </a>
                                             </div>
                                         
                                        </form>
                                    </div>
                                </div>
                            </div>

                          

                            <div class="card-body">
                                <div class="table-responsive table-responsive-sm">
                                    <table class="table table-nowrap align-middle mt-0">
                                        <thead class="table-light">
                                            <tr class="text-muted fs-14">
                                                <th scope="col" class="text-start">
                                                    {{translate("Total point")}}
                                                </th>
                                                <th scope="col" class="text-start">
                                                    {{translate("Product")}}
                                                </th>
                                                <th scope="col" class="text-start">
                                                    {{translate("Order")}}
                                                </th>
                                                <th scope="col" class="text-center">
                                                    {{translate("Status")}}
                                                </th>
                                                <th scope="col" class="text-center">
                                                    {{translate("Expired at")}}
                                                </th>
                                                <th scope="col" class="text-center">
                                                    {{translate("Created at")}}
                                                </th>
                               
                                                <th class="text-end">
                                                    {{translate("Options")}}
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="border-bottom-0">
                                            @forelse($reward_logs  as $reward_log)
                                                <tr class="fs-14 tr-item">

                                                    <td class="text-center">{{$reward_log->point}}</td>

                                                    <td class="text-center">{{$reward_log->product ? $reward_log->product->name : "N/A" }}</td>

                                                    <td class="text-start">
                                                        @if($reward_log->order)
                                                           <a href="{{route('user.order.details',$reward_log->order->order_id)}}" class="badge-soft-primary py-1 px-2">{{$reward_log->order->order_id}}</a>
                                                        @else
                                                             N/A
                                                        @endif
                                                    </td>


                                                    <td class="text-center">
                                                          
                                                        @php echo reward_status($reward_log->status) @endphp
                                                        
                                                    </td>


                                                    <td class="text-center">
                                                        {{
                                                           $reward_log->expired_at 
                                                                    ? get_date_time($reward_log->expired_at) 
                                                                    : 'N/A' 
                                                        }}
                                                    </td>
                                                    <td class="text-center">
                                                        {{$reward_log->created_at 
                                                           ? get_date_time($reward_log->created_at) 
                                                           : 'N/A'  }}
                                                    </td>
                                       


                                                   
                                                    <td class="text-end">
                                                        <a href="{{route('user.reward.point.show',$reward_log->id)}}" class="badge badge-soft-info fs-12 pointer"><i class="fa-regular fa-eye"></i></a>
                                                    </td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-muted py-5" colspan="8">{{translate('No Data Found')}}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 d-flex align-items-center justify-content-end">
                                        {{$reward_logs->withQueryString()->links()}}
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

