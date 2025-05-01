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
                        {{translate('Dashboard')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Digital Orders")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Order List')}}
                        </h5>
                    </div>

                </div>
            </div>

            <div class="card-body  border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="Search for order ID, customer">
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
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>
                                    {{translate(
                                        "Time"
                                    )}}
                                </th>
                                <th>
                                    {{translate('Order Number')}}
                                </th>
                                @if(request()->routeIs('admin.digital.order.product.seller'))
                                    <th>{{translate('Seller')}}</th>
                                @endif
                                <th>{{translate('Customer')}}
                                </th>
                                <th>
                                    {{translate('Status')}}
                                </th>
                                <th>
                                    {{translate('Amount')}}
                                </th>
                                <th>
                                    {{translate('Details')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                                @forelse($orders as $order)
                                    <tr>
                                        <td data-label="{{translate('Time')}}">
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Time">
                                                <span class="fw-bold">{{diff_for_humans($order->created_at)}}</span><br>
                                                {{get_date_time($order->created_at)}}
                                            </span>
                                        </td>
                                        <td data-label="{{translate('Order Number')}}">
                                         

                                            <a  href="{{route('admin.digital.order.product.details', $order->id)}}" class="text-primary fw-bold" data-bs-toggle="tooltip" data-bs-placement="top" title="Order Number">
                                                {{$order->order_id}}
                                            </a>
                                        </td>
                                            @if(request()->routeIs('admin.digital.order.product.seller'))
                                                <td data-label="{{translate('Seller')}}">
                                                    <a href="{{route('admin.seller.info.details', $order->digitalProductOrder->product->seller_id)}}" class="fw-bold text-dark">{{translate($order->digitalProductOrder->product->seller->username)}}</a>
                                                </td>
                                            @endif
                    
                                        <td data-label="{{translate('Customer')}}">
                                        {{$order->customer ? $order->customer->name :@$order->billing_information->email}}
                                            @if($order->customer )
                                                (<a href="{{route('admin.customer.details', $order->customer_id)}}" class="fw-bold text-dark">{{($order->customer->email)}}</a>)
                                            @endif
                                        </td>
                                        <td data-label="{{translate('Payment Status')}}">
                                            @if($order->payment_status == App\Models\Order::UNPAID)
                                                <span class="badge badge-soft-danger my-1">{{translate('Unpaid')}}</span>
                                            @elseif($order->payment_status == App\Models\Order::PAID)
                                                <span class="badge badge-soft-success my-1">{{translate('Paid')}}</span>
                                            @endif
                                        </td>
                                        <td data-label="{{translate('Amount')}}">
                                            <span class="fw-bold">{{short_amount($order->amount)}}</span>
                                        </td>
                                        <td data-label={{translate('Action')}}>
                                            <div class="hstack justify-content-center gap-3">
                                                <a class="link-success fs-18 " data-bs-toggle="tooltip" data-bs-placement="top" title="Details"  href="{{route('admin.digital.order.product.details', $order->id)}}"><i class="ri-list-check"></i></a>
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

                <div class="pagination-wrapper  d-flex justify-content-end mt-4">
                        {{$orders->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
