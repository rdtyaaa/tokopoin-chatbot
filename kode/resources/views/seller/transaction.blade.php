@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("All Transaction log")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Transactions")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">

            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Transactions')}}
                        </h5>
                    </div>
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search By TRX ID')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}" class="form-control search"
                                    placeholder="{{translate('Search by date')}}">
                                <i class="ri-time-line search-icon"></i>

                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Search')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}" class="btn btn-danger w-100 waves ripple-light"> <i
                                        class="ri-refresh-line me-1 align-bottom"></i>
                                    {{translate('Reset')}}
                                </a>
                            </div>
                        </div>

                    </div>

                </form>
            </div>


            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" id="orderTable">
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>{{translate('Date')}}</th>
                                <th>{{translate('Transaction Number')}}</th>
                                <th>{{translate('Amount')}}</th>
                                <th>{{translate('Post Balance')}}</th>
                                <th>{{translate('Detail')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td data-label="{{translate('Time')}}">
                                        <span class="fw-bold">{{diff_for_humans($transaction->created_at)}}</span><br>
                                        {{get_date_time($transaction->created_at)}}
                                    </td>
                                    <td data-label="{{translate('Transaction Number')}}">
                                        {{($transaction->transaction_number)}}
                                    </td>
                                    <td data-label="{{translate('Amount')}}">
                                        <span
                                            class="@if($transaction->transaction_type == '+')text-success @else text-danger @endif fw-bold">{{ $transaction->transaction_type }}
                                           {{(short_amount($transaction->amount))}}
                                        </span>
                                    </td>
                                    <td data-label="{{translate('Post Balance')}}">
                                        <span class="fw-bold"> {{(short_amount($transaction->post_balance))}}
                                            </span>
                                    </td>
                                    <td data-label="{{translate('Details')}}">
                                        {{($transaction->details) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="border-bottom-0" colspan="100">
                                        @include('admin.partials.not_found')
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper d-flex justify-content-end mt-4 ">
                    {{$transactions->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


