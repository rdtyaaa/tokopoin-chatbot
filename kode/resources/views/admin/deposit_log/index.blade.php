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
                        {{translate("Deposit Logs")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" id="orderList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        {{translate('Deposit log list')}}
                    </h5>
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="trx_code" value="{{request()->input('trx_code')}}" class="form-control search"
                                    placeholder="{{translate('Filter by TRX Number')}}">
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
                        <a class="nav-link {{request()->routeIs('admin.deposit.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.deposit.index')}}" >
                            <i class="ri-bank-card-line me-1 align-bottom"></i>
                            {{translate('All
                            Deposits')}}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.deposit.pending') ? 'active' :''}}   py-3"  id="Placed"
                            href="{{route('admin.deposit.pending')}}" >
                            <i class="ri-loader-line me-1 align-bottom"></i>
                            {{translate('Pending Deposits')}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.deposit.approved') ? 'active' :''}} Confirmed py-3"  id="Confirmed"
                            href="{{route('admin.deposit.approved')}}" >
                            <i class="ri-check-line me-1 align-bottom"></i>
                            {{translate("Approved Deposits")}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link Processing {{request()->routeIs('admin.deposit.rejected') ? 'active' :''}}   py-3"  id="Processing"
                            href="{{route('admin.deposit.rejected')}}" >
                            <i class="ri-close-circle-line me-1 align-bottom"></i>
                            {{translate('Rejected Deposits')}}
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
                                    {{translate('TRX Number')}}
                                </th>
                                <th>
                                    {{translate('User/Seller')}}
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

                                <th >
                                    {{translate('Action')}}
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

                                        <td data-label="{{translate('TRX Number')}}">

                                            {{$paymentLog->trx_number}}
                           
                                        </td>

                                        <td data-label="{{translate('user')}}">
                                            @if($paymentLog->user)
                                                <a title="{{translate('User')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.customer.details', $paymentLog->user_id)}}" class="fw-bold text-dark">{{(@$paymentLog->user->name)}}</a><br>
                                                {{(@$paymentLog->user->email)}}

                                            @elseif($paymentLog->seller)

                                            <a title="{{translate('Seller')}}" data-bs-toggle="tooltip" data-bs-placement="top"   href="{{route('admin.seller.info.details', $paymentLog->seller_id)}}" class="fw-bold text-dark">{{(@$paymentLog->seller->username)}}</a><br>
                                            {{(@$paymentLog->seller->name)}}
                                               
                                            @else
                                                {{ translate('Guest User') }}
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


                                        <td data-label="{{ translate('Action') }}">

                                            <div class="hstack justify-content-center gap-3">
            
                                                <a title="Details" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    href="{{route('admin.deposit.show',$paymentLog->id)}}"
                                                    class="fs-18 link-info ms-1"><i class="ri-list-check"></i>
                                                </a>
                                            </div>
    
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


