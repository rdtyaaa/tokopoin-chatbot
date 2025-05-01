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
                        {{translate("Payment Logs")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" id="orderList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        {{translate('Payment log list')}}
                    </h5>
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search by username or email or name, or payment method')}}">
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
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}"
                                    class="btn btn-danger w-100 waves ripple-light"> <i
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
                        <a class="nav-link {{request()->routeIs('admin.payment.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.payment.index')}}" >
                            <i class="ri-bank-card-line me-1 align-bottom"></i>
                            {{translate('All
                            Payments')}}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.payment.pending') ? 'active' :''}}   py-3"  id="Placed"
                            href="{{route('admin.payment.pending')}}" >
                            <i class="ri-loader-line me-1 align-bottom"></i>
                            {{translate('Pending Payments')}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.payment.approved') ? 'active' :''}} Confirmed py-3"  id="Confirmed"
                            href="{{route('admin.payment.approved')}}" >
                            <i class="ri-check-line me-1 align-bottom"></i>
                            {{translate("Approved Payments")}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link Processing {{request()->routeIs('admin.payment.rejected') ? 'active' :''}}   py-3"  id="Processing"
                            href="{{route('admin.payment.rejected')}}" >
                            <i class="ri-close-circle-line me-1 align-bottom"></i>
                            {{translate('Rejected Payments')}}
                        </a>
                    </li>
                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>
                                    {{translate(
                                        "Time"
                                    )}}
                                </th>
                                <th>
                                    {{translate('User')}}
                                </th>
                                <th  >{{translate('Method')}}
                                </th>
                                <th>
                                    {{translate('Amount')}}
                                </th>
                                <th>
                                    {{translate('Final Amount')}}
                                </th>

                                <th >
                                    {{translate('Status')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($paymentLogs as $paymentLog)
                                    <tr>
                                        <td data-label="{{translate('Time')}}">
                                            <span class="fw-bold">{{diff_for_humans($paymentLog->created_at)}}</span><br>
                                            {{get_date_time($paymentLog->created_at)}}
                                        </td>

                                        <td data-label="{{translate('user')}}">
                                            @if($paymentLog->user)
                                                <a href="{{route('admin.customer.details', $paymentLog->user_id)}}" class="fw-bold text-dark">{{(@$paymentLog->user->name)}}</a><br>
                                                {{(@$paymentLog->user->email)}}
                                            @else
                                                {{ @$paymentLog->order->billing_information->email ?? 'Guest User'}}
                                            @endif


                                        </td>

                                        <td data-label="{{translate('Method')}}">
                                            {{(@$paymentLog->paymentGateway->name ?? 'N/A')}}
                                        </td>

                                        <td data-label="{{translate('Amount')}}">
                                            {{round(($paymentLog->amount))}} {{default_currency()->name}}
                                        </td>

                                        <td data-label="{{translate('Final Amount')}}">
                                            {{round(($paymentLog->final_amount))}} {{@$paymentLog->paymentGateway->currency->name ?? default_currency()->name}}
                                        </td>

                                        <td data-label="{{translate('Status')}}">
                                            @if($paymentLog->status == "1")
                                                <span class="badge badge-soft-primary">{{translate('Pending')}}</span>
                                            @elseif($paymentLog->status == "2")
                                                <span class="badge badge-soft-info">{{translate('Received')}}</span>
                                            @elseif($paymentLog->status == "3")
                                                <span class="badge badge-soft-danger">{{translate('Rejected')}}</span>
                                            @endif
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
                    {{$paymentLogs->appends(request()->all())->links()}}
                </div>
            </div>
        </div>

    </div>
</div>

@endsection


