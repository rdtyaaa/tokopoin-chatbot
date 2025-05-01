@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate($title)}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item"><a href="{{route('seller.withdraw.history')}}">
                        {{translate("Logs")}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Details")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" id="orderList">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <div>
                            <h5 class="card-title mb-0">
                                {{translate('Withdraw log Details')}}
                            </h5>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="@if($withdraw->withdraw_information)col-lg-6 @else col-lg-12 @endif">
                        <div class="rounded_box">
                            <div>
                                <h6 class="mb-3">{{translate('Customer information')}}</h6>
                                <ul class="list-group">
                                  
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{translate('Method')}}
                                        <span class="font-weight-bold">{{(@$withdraw->method ? $withdraw->method->name : translate("N/A"))}}</span>
                                    </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{translate('Time')}}
                                        <span class="font-weight-bold">{{get_date_time($withdraw->created_at)}}</span>
                                    </li>

                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{translate('Amount')}}
                                        <span class="font-weight-bold">{{round($withdraw->amount)}} {{default_currency()->name}}</span>
                                    </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{translate('Charge')}}
                                        <span class="font-weight-bold">{{round($withdraw->charge)}} {{default_currency()->name}}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{translate('Receivable')}}
                                        <span class="font-weight-bold">{{round($withdraw->final_amount)}} {{@$withdraw->currency->name}}</span>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
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
                                </ul>
                            </div>
                        </div>
                    </div>
                    @if($withdraw->withdraw_information)
                        <div class="col-lg-6">
                            <div class="p-3 rounded_box">
                                <h6 class="mb-3">{{translate('User Withdraw Information')}}</h6>

                                <ul class="list-group">
                                    
                                    @if($withdraw->withdraw_information)
                                        @foreach(json_decode($withdraw->withdraw_information,true) as $key => $value)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">

                                                {{text_sorted($key)}}  
                                                <p>{{Arr::get($value,'data_name','N/A')}}</p>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

