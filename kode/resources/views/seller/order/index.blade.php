@extends('seller.layouts.app')
@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ translate('Inhouse Order') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">
                                {{ translate('Dashboard') }}
                            </a></li>
                        <li class="breadcrumb-item active">
                            {{ translate('Inhouse Orders') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card" id="orderList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{ translate('Order List') }}
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="card-body  border border-dashed border-end-0 border-start-0">
                    <form action="{{ route(Route::currentRouteName(), Route::current()->parameters()) }}" method="get">
                        <div class="row g-3">
                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" name="search" value="{{ request()->input('search') }}"
                                        class="form-control search" placeholder="Search for order ID, customer">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" id="datePicker" name="date"
                                        value="{{ request()->input('date') }}" class="form-control search"
                                        placeholder="{{ translate('Search by date') }}">
                                    <i class="ri-time-line search-icon"></i>

                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
                                            class="ri-equalizer-fill me-1 align-bottom"></i>
                                        {{ translate('Search') }}
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <a href="{{ route(Route::currentRouteName(), Route::current()->parameters()) }}"
                                        class="btn btn-danger w-100 waves ripple-light"> <i
                                            class="ri-refresh-line me-1 align-bottom"></i>
                                        {{ translate('Reset') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="card-body pt-0">
                    <ul class="nav nav-tabs nav-tabs-custom nav-primary mb-3" role="tablist">
                        <li class="nav-item">
                            <a class='nav-link {{ request()->routeIs('seller.order.index') ? 'active' : '' }} All py-3'
                                id="All" href="{{ route('seller.order.index') }}">
                                <i class="ri-luggage-cart-line me-1 align-bottom"></i>
                                {{ translate('All Orders') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class='nav-link {{ request()->routeIs('seller.order.pending') ? 'active' : '' }}  Placed py-3'
                                id="pending" href="{{ route('seller.order.pending') }}">
                     
                                <i class="ri-pause-circle-line me-1 align-bottom"></i>
                                {{ translate('Pending Orders') }}
                             
                            </a>
                        </li>
              
                        <li class="nav-item">
                            <a class='nav-link {{ request()->routeIs('seller.order.placed') ? 'active' : '' }}  Placed py-3'
                                id="Placed" href="{{ route('seller.order.placed') }}">
                                <i class="ri-map-pin-line me-1 align-bottom"></i>
                                {{ translate('Placed  Orders') }}

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class='nav-link {{ request()->routeIs('seller.order.confirmed') ? 'active' : '' }} Confirmed py-3'
                                id="Confirmed" href="{{ route('seller.order.confirmed') }}">
                                <i class="ri-checkbox-multiple-fill me-1 align-bottom"></i>
                                {{ translate('Confirmed Order') }}

                            </a>
                        </li>
                        <li class="nav-item">
                            <a class='nav-link Processing {{ request()->routeIs('seller.order.processing') ? 'active' : '' }}   py-3'
                                id="Processing" href="{{ route('seller.order.processing') }}">
                                <i class="ri-refresh-line me-1 align-bottom"></i>
                                {{ translate('Processing') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class='nav-link Shipped {{ request()->routeIs('seller.order.shipped') ? 'active' : '' }}   py-3'
                                id="Shipped" href="{{ route('seller.order.shipped') }}">
                                <i class="ri-ship-line me-1 align-bottom"></i>
                                {{ translate('Shipped') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class='nav-link py-3 {{ request()->routeIs('seller.order.delivered') ? 'active' : '' }}   Delivered'
                                id="Delivered" href="{{ route('seller.order.delivered') }}">
                                <i class="ri-checkbox-circle-line  me-1 align-bottom"></i>

                                {{ translate('Delivered') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class='nav-link {{ request()->routeIs('seller.order.cancel') ? 'active' : '' }}   py-3 Returns'
                                id="Returns" href="{{ route('seller.order.cancel') }}">
                                <i class="ri-close-circle-line me-1 align-bottom"></i>

                                {{ translate('Canceled Order') }}
                            </a>
                        </li>

                        
                        <li class="nav-item">
                            <a class='nav-link {{ request()->routeIs('seller.order.return') ? 'active' : '' }}   py-3 Returns'
                                id="Returns" href="{{ route('seller.order.return') }}">
                                
                                <i class="ri-text-wrap me-1 align-bottom"></i>

                                {{ translate('Return Order') }}
                            </a>
                        </li>

                    </ul>

                    <div class="table-responsive table-card">
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th>
                                        {{ translate('Order ID') }}
                                    </th>

                                    <th>
                                        {{ translate('Qty') }}
                                    </th>
                                    <th>{{ translate('Time') }}
                                    </th>
                                    <th>
                                        {{ translate('Customer Info') }}
                                    </th>
                                    <th>
                                        {{ translate('Product Detail') }}
                                    </th>
                                    <th>
                                        {{ translate('Amount') }}
                                    </th>
                                    <th>
                                        {{ translate('Delivery') }}
                                    </th>
                                    <th>
                                        {{ translate('Action') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($orders as $order)
                                    <tr>

                                        <td data-label="{{ translate('Order Number') }}">

                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="Order Number"
                                                href="{{ route('seller.order.details', $order->id) }}">
                                                {{ $order->order_id }}
                                            </a>
                                        </td>

                                        <td data-label="{{ translate('Qty') }}">
                                            <span>{{ $order->qty }}</span><br>
                                        </td>

                                        <td data-label="{{ translate('Time') }}">
                                            <span class="fw-bold">{{ diff_for_humans($order->created_at) }}</span><br>
                                            {{ get_date_time($order->created_at) }}
                                        </td>

                                        <td data-label="{{ translate('Customer Info') }}" class="text-align-left">


                                            @php
                                                $firstName = @$order->billingAddress ?  @$order->billingAddress->first_name : @$order->billing_information->first_name ;
                                                $lastName  = @$order->billingAddress ?  @$order->billingAddress->last_name : @$order->billing_information->last_name ;
                                                $phone  = @$order->billingAddress ?  @$order->billingAddress->phone : @$order->billing_information->phone ;
                                                $email  = @$order->billingAddress ?  @$order->billingAddress->email : @$order->billing_information->email ;
                                                $city  = @$order->billingAddress ?  @$order->billingAddress->city->name : @$order->billing_information->city ;
                                                $country  = @$order->billingAddress ?  @$order->billingAddress->country->name : @$order->billing_information->country ;
                                                $zip  = @$order->billingAddress ?  @$order->billingAddress->zip : @$order->billing_information->zip ;
                                                $address  = @$order->billingAddress ?  @$order->billingAddress->address->address : @$order->billing_information->address ;
                                            @endphp



                                            <span>{{ translate('Name') }}:
                                                {{ @$order->customer->name ?? @$firstName }}</span><br>
                                            <span>{{ translate('Phone') }}:
                                                {{ @$order->customer->phone ?? @$phone }}</span>
                                            <br>
                                            <span>
                                                {{ translate('Email') }}
                                                :
                                                {{ @$order->customer->email ?? @$email }}</span>
                                            <br>

                                            <span>
                                                {{ translate('Country') }}
                                                : {{ @$country }}</span> <br>

                                            <span>
                                                {{ translate('City') }}
                                                : {{ @$city}}</span> <br>
                                            <span>
                                                {{ translate('Address') }}
                                                :
                                                {{ @$address }}
                                            </span> <br>

                                             
                                            <span class="badge bg-primary">{{ translate('Shipping Method') }}:
                                                {{ @$order->shipping->name ?? 'N\A' }}</span> <br>
                                        </td>

                                        <td data-label="{{ translate('Product Name') }}" class="tex-align-left">

                                            <button data-details ="{{ $order->orderDetails }}"
                                                class="ms-2 order-btn btn btn-info btn-sm custom-toggle active"
                                                data-bs-toggle="modal"
                                                data-bs-target="#orderDetails">{{ translate('view All') }}
                                                ({{ $order->orderDetails->count() }})
                                            </button>
                                        </td>

                                        <td data-label="{{ translate('Amount') }}">

                                            @php
                                                $totalAmount = @$order->orderDetails->sum('total_price') ?? 0;

                                                if (@$order->shipping_charge) {
                                                    $totalAmount += $order->shipping_charge;
                                                }

                                            @endphp
                                            <span class="fw-bold">
                                                {{ short_amount($totalAmount) }}</span><br>
                                            @if ($order->payment_status == App\Models\Order::UNPAID)
                                                <span class="badge badge-soft-danger">{{ translate('Unpaid') }}</span>
                                            @elseif($order->payment_status == App\Models\Order::PAID)
                                                <span class="badge badge-soft-success">{{ translate('Paid') }}</span>
                                            @endif
                                        </td>

                                        <td data-label="{{ translate('Delivery Status') }}">
                                            
                                            @php echo order_status_badge($order->status)  @endphp
                                        </td>

                                        <td data-label="{{ translate('Action') }}">

                                            <div class="hstack justify-content-center gap-3">
                                                <a title="Print" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    href="{{ route('seller.order.print', [$order->id]) }}"
                                                    class="fs-18 link-success order_id">
                                                    <i class="ri-printer-line"></i>
                                                </a>

                                                <a title="Details" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    href="{{ route('seller.order.details', $order->id) }}"
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

                    <div class="pagination-wrapper d-flex justify-content-end mt-4 ">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="orderDetails" tabindex="-1" aria-labelledby="orderDetails" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ translate('Product Info') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>

                <div class="modal-body">
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                <tbody id="product-info">

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-md btn-danger "
                        data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-push')
    <script>
        (function($) {
            "use strict";

            $('.order-btn').on('click', function() {
                var modal = $('#orderDetails');
                var order_details = $(this).attr('data-details')

                order_details = JSON.parse(order_details);
                var html = ''
                for (var i in order_details) {

                    if (order_details[i].product) {
                        var url = "{{ route('product.details', [':slug', ':id']) }}"
                        url = (url.replace(':slug', order_details[i].product.slug ? order_details[i].product.slug : convertSlug(order_details[i].product.name))).replace(':id',
                            order_details[i].product.id)
                        html += `  <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded p-1 me-2">
                                                <img onerror="this.onerror=null;this.src='{{ route('default.image', '200x200') }}';" src="{{ asset('/assets/images/backend/product/featured') }}/${order_details[i].product.featured_image}" alt="${order_details[i].product.featured_image}" class="img-fluid d-block">
                                            </div>
                                            <div>
                                                <h5 class="fs-14 my-1"><a href="${url}" class="text-reset">${order_details[i].product.name}</a></h5>
                                                <span class="text-muted">
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">
                                            <span class='badge badge-soft-info'> ${order_details[i].attribute} </span>
                                        </h5>
                                        <span class="text-muted">
                                            {{ translate('Attribute') }}
                                        </span>
                                    </td>

                                    <td>
                                        <h5 class="fs-14 my-1 fw-normal">{{ show_currency() }}${Math.round(order_details[i].total_price)} </h5>
                                        <span class="text-muted">{{ translate('Amount') }}</span>
                                    </td>
                                </tr>`;
                    }
                }
                $('#product-info').html(html)
                modal.modal('show');
            });

            //convert string to slug start
            function convertSlug(Text) {
                return Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
            }




        })(jQuery);
    </script>
@endpush
