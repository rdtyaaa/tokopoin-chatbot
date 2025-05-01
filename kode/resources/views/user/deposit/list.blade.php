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
                                <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-sm-row flex-column gap-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{translate("Deposit List")}}
                                        </h4>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                     
                                        <form id="filter-order" action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                                         

                                             <div class="d-flex align-items-center gap-3">
                                                <input value="{{request()->input('trx_code')}}" placeholder="{{translate('Search by TRX CODE')}}" class="form-control search-input" type="text" name="trx_code" id="trx_code">

                                                <input placeholder="{{translate('Search by date')}}"  value="{{request()->input('date')}}" class="form-control search-input" type="text" name="date" id="datePicker">

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
                                              

                                                <th>{{translate('Time')}}</th>
                                                <th>{{translate('Transaction Number')}}</th>
                                                <th>{{translate('Method')}}</th>
                                                <th>{{translate('Receivable')}}</th>
                                                <th>{{translate('Charge')}}</th>
                                                <th>{{translate('Paid')}}</th>
                                                <th>{{translate('Status')}}</th>
                                                <th>{{translate('Actions')}}</th>

                                            </tr>
                                        </thead>

                                        <tbody class="border-bottom-0">
                                            @forelse($reports as $report)
                                                <tr>
                                                    <td data-label="{{translate('Time')}}">
                                                        <span class="fw-bold">{{diff_for_humans($report->created_at)}}</span><br>
                                                        {{get_date_time($report->created_at)}}
                                                    </td>
                
                                                    <td data-label="{{translate('Method')}}">
                                                        <span class="fw-bold">{{(@$report->trx_number)}}</span>
                                                    </td>
                                                    <td data-label="{{translate('Method')}}">
                                                        <span class="fw-bold">{{(@$report->paymentGateway ? $report->paymentGateway->name :"N/A")}}</span>
                                                    </td>
                
                                                    <td data-label="{{translate('Receivable')}}">
                                                        {{round(($report->amount))}}{{default_currency()->name}}
                                                    </td>
                
                                                    <td data-label="{{translate('Charge')}}">
                                                        <span class="text-danger fw-bold">	{{round(($report->charge),2)}} {{default_currency()->name}} </span>
                                                    </td>
                
                                                    <td data-label="{{translate('Paid')}}">
                                                        <span class="fw-bold text-success">
                                                            {{round(($report->final_amount))}} {{@$report->paymentGateway->currency->name ?? default_currency()->name}}										</span>
                                                    </td>
                
                                                    <td data-label="{{translate('Status')}}">
                                                        @if($report->status == 1)
                                                            <span class="badge badge-soft-warning">{{translate('Pending')}}</span>
                                                        @elseif($report->status == 2)
                                                            <span class="badge badge-soft-primary">{{translate('Received')}}</span>
                                                        @elseif($report->status == 3)
                                                            <span class="badge badge-soft-danger">{{translate('Rejected')}}</span>
                                                            <a href="javascript:void(0)" class="link-warning fs-18 feedbackinfo" data-bs-toggle="modal" data-bs-target="#feedback" data-feedback="{{$report->feedback}}"><i class="las la-info"></i></a>
                                                        @endif
                                                    </td>
                                                    <td data-label="{{translate('Action')}}">
                                                        <a href="{{route('user.deposit.show',$report->id)}}" class="badge badge-soft-info fs-12 pointer"><i class="fa-regular fa-eye"></i></a>
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
                                        {{$reports->withQueryString()->links()}}
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
        (function($){
            "use strict";
            flatpickr("#datePicker", {
                dateFormat: "Y-m-d",
                mode: "range",
            });
        })(jQuery);
    </script>
@endpush
