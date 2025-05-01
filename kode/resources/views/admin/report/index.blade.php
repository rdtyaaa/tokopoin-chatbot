
@extends('admin.layouts.app')
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
                    <li class="breadcrumb-item active">
                        {{translate("Transaction logs")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        {{translate('Transaction Log List')}}
                    </h5>
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search by customer ,Seller TRX ID, Email')}}">
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

            <div class="card-body pt-0">
                <ul class="nav nav-tabs nav-tabs-custom nav-primary mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.report.user.transaction') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.report.user.transaction')}}" >
                            <i class="ri-user-shared-line me-1 align-bottom"></i>
                            {{translate('User
                            Transactions')}}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.report.seller.transaction') ? 'active' :''}}   py-3"  id="Placed"
                            href="{{route('admin.report.seller.transaction')}}" >
                            <i class="ri-user-line me-1 align-bottom"></i>
                            {{translate('Seller Transactions')}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.report.deliveryman.transaction') ? 'active' :''}}   py-3"  id="guest"
                            href="{{route('admin.report.deliveryman.transaction')}}" >
                            <i class="ri-user-line me-1 align-bottom"></i>
                            {{translate('Deliveryman Transactions')}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.report.guest.transaction') ? 'active' :''}}   py-3"  id="guest"
                            href="{{route('admin.report.guest.transaction')}}" >
                            <i class="ri-user-line me-1 align-bottom"></i>
                            {{translate('Guest Transactions')}}

                        </a>
                    </li>

                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">

                                <th>{{translate('Date')}}</th>
                                    @if(request()->routeIs('admin.report.user.transaction'))
                                        <th>{{translate('Customer')}}</th>
                                    @elseif(request()->routeIs('admin.report.seller.transaction'))
                                        <th>{{translate('Seller')}}</th>
                                    @elseif(request()->routeIs('admin.report.deliveryman.transaction'))
                                         <th>{{translate('Deliveryman')}}</th>
                                    @else
                                        <th>
                                            {{translate('Guest')}}
                                        </th>
                                    @endif
                                <th>
                                    {{translate('Transaction ID')}}
                                </th>
                                <th >
                                    {{translate('Amount')}}
                                </th>
                                @if(request()->routeIs('admin.report.seller.transaction') ||  request()->routeIs('admin.report.deliveryman.transaction'))
                                    <th >
                                        {{translate('Post Balance')}}
                                    </th>
                                @endif

                                <th >
                                    {{translate('Details')}}
                                </th>

                            </tr>
                        </thead>

                        <tbody class="list form-check-all">

                            @forelse($transactions as $transaction)
                                <tr>
                                    <td data-label="{{translate('Date')}}">
                                        <span class="fw-bold">{{diff_for_humans($transaction->created_at)}}</span><br>
                                        {{get_date_time($transaction->created_at)}}
                                    </td>

                                    @if($transaction->user)
                                        <td data-label="{{translate('User')}}">
                                            <span>{{@$transaction->user->name}}</span><br>
                                            <a href="{{route('admin.customer.details', $transaction->user_id)}}" class="fw-bold text-dark">{{(@$transaction->user->email)}}</a>
                                        </td>
                                    @elseif($transaction->seller_id)
                                        <td data-label="{{translate('Seller')}}">
                                            <span>{{@$transaction->seller->name}}</span><br>
                                            <a href="{{route('admin.seller.info.details', $transaction->seller_id)}}" class="fw-bold text-dark">{{(@$transaction->seller->email)}}</a>
                                        </td>

                                    @elseif($transaction->deliveryman_id)

                                        <td data-label="{{translate('Deliveryman')}}">
                                            <span>{{@$transaction->deliveryman->first_name}}</span><br>
                                            <a href="{{route('admin.delivery-man.edit', $transaction->deliveryman_id)}}" class="fw-bold text-dark">{{(@$transaction->deliveryman->email)}}</a>
                                        </td>
                    
                                    @else
                                        <td data-label="{{translate('Guest')}}">
                                            {{ @$transaction->guest_user->email ?? 'Guest User'}}
                                        </td>
                                    @endif

                                    <td data-label="{{translate('TRX ID')}}">
                                        {{(@$transaction->transaction_number)}}
                                    </td>

                                    <td data-label="{{translate('Amount')}}">
                                        <span class="@if($transaction->transaction_type == '+') text-success @else text-danger @endif fw-bold">
                                            {{$transaction->transaction_type == "+" ? '+' : '-'}}{{round(($transaction->amount))}} {{default_currency()->name}}</span>

                                    </td>

                                    @if(request()->routeIs('admin.report.seller.transaction') || request()->routeIs('admin.report.deliveryman.transaction'))
                                        <td data-label="{{translate('Post Balance')}}">
                                            <span class="fw-bold text-primary">{{round(($transaction->post_balance))}} {{default_currency()->name}}</span>
                                        </td>
                                    @endif

                                    <td data-label="{{translate('Details')}}">
                                        {{$transaction->details }}
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

                <div class="pagination-wrapper d-flex justify-content-end mt-4">
                    {{$transactions->appends(request()->all())->links()}}
                </div>

            </div>
        </div>

    </div>
</div>
@endsection


