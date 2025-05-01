@extends('frontend.layouts.app')

@push('stylepush')

  <style>
        .method-card{
            border: 0.1rem solid var(--gray-5) !important;
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
                               {{translate("Dashboard")}}
                            </h4>
                        </div>

                        <div class="dashboard-overview">
                            <div class="overview-card">
                                <span class="icon"><i class="fa-solid fa-cart-shopping fs-20"></i></span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("All Orders")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">  {{translate("Total")}} : {{$orders->count()}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">
                                    <i class="fa-solid fa-cart-arrow-down fs-20"></i>
                                </span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Placed Order")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2"> {{translate("Total")}} : {{$orders->count()}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">
                                    <i class="fa-solid fa-cart-flatbed fs-20"></i>
                                </span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Shipped Order")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">{{translate("Total")}} : {{$orders->where('status','4')->count()}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon"><i class="fa-solid fa-truck fs-20"></i></span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Delivered Orders")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">{{translate("Total")}} : {{$orders->where('status','5')->count()}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">
                                    <i class="fa-solid fa-heart-circle-check fs-20"></i>
                                </span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("My Wishlist")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">{{translate("Total")}} : {{$user->wishlist->count()}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">  <i class="fa-solid fa-basket-shopping fs-20"></i></span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("My Shopping Cart")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2"> {{translate("Total")}} : {{$items->count()}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">  <i class="fa-solid fa-store fs-20"></i></span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Followed Stores")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">{{translate("Total")}} :{{$user->follower->count()}}</p>
                                </div>
                            </div>

                            <div class="overview-card">
                                <span class="icon">  <i class="fa-solid fa-brands fa-digital-ocean fs-20"></i></span>
                                <div>
                                    <h5 class="fs-14">
                                        {{translate("Digital Order")}}
                                    </h5>
                                    <p class="fs-12 fw-semibold  text-muted pt-2">{{translate("Total")}} : {{$digitalOrders->count()}}</p>
                                </div>
                            </div>
                        </div>

                        <div class="card mt-5">
                            <div class="card-header">
                                <div class="d-flex align-items-start align-items-sm-center justify-content-between flex-sm-row flex-column gap-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{translate("Order List")}}
                                        </h4>
                                    </div>

                                    <div class="d-flex align-items-center gap-3">
                                        <label for="filter" class="nowrap form-label mb-0">
                                            {{translate("Sort By")}}
                                        </label>

                                        <form id="filter-order" action="{{route('user.dashboard')}}" method="post">
                                            @csrf
                                            <select name="search_data" class="form-select form-select-sm" id="filter" aria-label=".form-select-sm example">
                                                <option {{session()->get('order_search') =='all' ? 'selected' :""}} value="all">
                                                    {{translate("All")}}
                                                </option>
                                                <option {{session()->get('order_search') =='0' ? 'selected' :""}} value="0">
                                                    {{translate("Today")}}
                                                </option>
                                                <option {{session()->get('order_search') =='7' ? 'selected' :""}} value="7">
                                                    {{translate("Last 7 Days")}}
                                                </option>
                                                <option {{session()->get('order_search') =='15' ? 'selected' :""}} value="15">
                                                    {{translate("Last 15 Days")}}
                                                </option>
                                                <option {{session()->get('order_search') =='30' ? 'selected' :""}} value="30">
                                                    {{translate("Last 30 Days")}}
                                                </option>
                                            </select>
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
                                                {{translate("order Id")}}
                                                </th>
                                                <th scope="col" class="text-center">
                                                    {{translate("Place On")}}
                                                </th>
                                                <th scope="col" class="text-center">
                                                    {{translate("Quantity")}}
                                                </th>
                                                <th scope="col" class="text-center">
                                                    {{translate("Total")}}
                                                </th>
                                                <th scope="col" class="text-center">
                                                    {{translate("Payment Status")}}
                                                </th>

                                                <th class="text-end">
                                                    {{translate("Options")}}
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="border-bottom-0">
                                            @forelse($orders  as $order)
                                                <tr class="fs-14 tr-item">
                                                    <td class="text-start">
                                                        <a href="{{route('user.order.details',$order->order_id)}}" class="badge-soft-primary py-1 px-2">{{$order->order_id}}</a>
                                                    </td>

                                                    <td class="text-center">{{get_date_time($order->created_at)}}</td>

                                                    <td class="text-center">{{$order->qty}}</td>
                                                    <td class="text-center">{{short_amount($order->amount)}}</td>

                                                    <td class="text-center">
                                                        <span class="badge bg-{{$order->payment_status == 2 ?'success' :"danger"}}">
                                                            @if($order->payment_status == 2)
                                                                {{translate("Paid")}}
                                                            @elseif($order->payment_status == 1)
                                                                {{translate("Unpaid")}}
                                                            @endif
                                                        </span>
                                                    </td>

                                                    <td class="text-end">
                                                        <div class="d-flex align-items-center gap-3 justify-content-end">
                                                            <a href="{{route('user.order.details',$order->order_id)}}" class="badge badge-soft-info fs-12 pointer"><i class="fa-regular fa-eye"></i></a>
                                                            <a href="{{route('user.track.order',$order->order_id)}}" class="badge badge-soft-info fs-12 pointer"><i class="fa-solid fa-location-dot"></i></a>

                                                            @if($order->payment_status == App\Models\Order::UNPAID && $order->status != App\Models\Order::CANCEL)
                                                                <a href="javascript:void(0)" class="order-delete badge badge-soft-danger fs-12 pointer" data-bs-toggle="modal" data-bs-target="#deletePhysicalOrder" data-id="{{$order->id}}">
                                                                <i class="fa-solid fa-trash"></i></a>
                                                            @endif

                                                            @if($order->payment_status == App\Models\Order::UNPAID && $order->status !=  App\Models\Order::CANCEL && $order->payment_type ==  App\Models\Order::PAYMENT_METHOD)
                                                                <a href="{{route('user.order.pay',$order->id)}}" class="badge badge-soft-success fs-12 pointer">
                                                                    <i class="fa-regular fa-money-bill-1"></i>
                                                                </a>
                                                            @endif

                                                        </div>
                                                    </td>

                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-muted py-5" colspan="6">{{translate('No Data Found')}}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 d-flex align-items-center justify-content-end">
                                        {{$orders->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>

                    </div>

                   
                </div>
            </div>
        </div>
    </div>
</section>



<div class="modal fade" id="deleteOrder" tabindex="-1" aria-labelledby="deleteOrder" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteOrderLabel">
                {{translate("Cancel Order")}}
                </h5>
               <button type="button" class="btn btn-danger fs-14 modal-closer rounded-circle" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{route('user.digital.product.order.cancel')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <h3>{{translate('Are you sure want to cancel this digital order')}}?</h3>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-4">
                        <button type="button" class="btn btn-danger fs-12 px-3" data-bs-dismiss="modal">
                            {{translate("Cancel")}}
                        </button>
                        <button type="submit" class="btn btn-success fs-12 px-3">
                            {{translate('Submit')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deletePhysicalOrder" tabindex="-1" aria-labelledby="deletePhysicalOrder" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-top">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePhysicalOrderLabel">
                {{translate("Cancel Order")}}
                </h5>
               <button type="button" class="btn btn-danger fs-14 modal-closer rounded-circle" data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <form action="{{route('user.order.delete')}}" method="POST">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <h3>{{translate('Are you sure want Delete This Order')}}?</h3>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center gap-4">
                        <button type="button" class="btn btn-danger fs-12 px-3" data-bs-dismiss="modal">
                            {{translate("Cancel")}}
                        </button>
                        <button type="submit" class="btn btn-success fs-12 px-3">
                            {{translate('Submit')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@push('scriptpush')
<script>

"use strict"
$('.quantitybutton').on("click", function() {
    var cartItemQuantity = $(this).parents('.wish-item').find('#quantity').val();
    if ($(this).hasClass('increment')) {
        cartItemQuantity++;
    } else {
        if (cartItemQuantity > 1) {
            cartItemQuantity--;
        } else {
            $(".cart--minus").prop("disabled", true);
        }
    }
    $(this).parents('.wish-item').find('#quantity').val(cartItemQuantity);
});

$(document).on('click', '.remove-wishlist-item', function(e) {
    var item = $(this);
    var id = $(this).data('id');
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        url: "{{route('user.wish.item.delete')}}",
        method: "POST",
        data: {
            id: id
        },
        success: function(response) {
            if (response.success) {
                item.parents('.wish-item').remove();
                wishlistItemCount();
                toaster(response.success,'success')

            } else {
                toaster(response.error,'danger')

            }
        }
    });
});

$(".order--cancel").on("click", function() {
    var modal = $("#deleteOrder");
    modal.find('input[name=id]').val($(this).data('id'));
    modal.modal('show');
});
$(".order-delete").on("click", function() {
    var modal = $("#deletePhysicalOrder");
    modal.find('input[name=id]').val($(this).data('id'));
    modal.modal('show');
});

//filter orders
$(document).on('change', '#filter', function(e) {
    $("#filter-order").submit()
    e.preventDefault()
});

</script>
@endpush

