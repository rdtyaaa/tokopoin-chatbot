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
                        {{translate("Inhouse Orders")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" id="orderList">
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
                <ul class="nav nav-tabs nav-tabs-custom nav-primary mb-3" role="tablist">
                    <li class="nav-item">
                        <a class='nav-link {{request()->routeIs("admin.delivery-man.order.list") ? "active" :"" }} All py-3'  id="All"
                            href="{{route('admin.delivery-man.order.list', request()->route('id'))}}" >
                            <i class="ri-luggage-cart-line me-1 align-bottom"></i>
                            {{translate('All
                            Orders')}}
                        </a>
                    </li>

                    @foreach (App\Models\Order::delevaryStatus() as $key => $val )


                            <li class="nav-item">
                                <a class='nav-link {{request()->routeIs("admin.inhouse.order.placed") ? "active" :""}}  Placed py-3' id="Placed"
                                    href="{{route('admin.delivery-man.order.list',['id'=>request()->route('id') ,'status' =>$key])}}" >
                                    <i class="ri-luggage-cart-line me-1 align-bottom"></i>
                                    {{
                                        k2t($key)
                                    }}
                                    
                                </a>
                            </li>
                                
                    @endforeach

                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>
                                    {{translate(
                                        "Order ID"
                                    )}}
                                </th>
                                <th>
                                    {{translate('Qty')}}
                                </th>
                                <th  >{{translate('Time')}}
                                </th>
                                <th>
                                    {{translate('Customer Info')}}
                                </th>
                                <th>
                                    {{translate('Product Details')}}
                                </th>
                                <th >
                                    {{translate('Amount')}}
                                </th>
                                <th >
                                    {{translate('Delivery')}}
                                </th>
                                <th >
                                    {{translate('Action')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody >
                                @forelse($orders as $order)
                                    <tr>
                                        <td data-label="{{translate('Order Number')}}">
                                            <a  href="{{route('admin.inhouse.order.details', $order->id)}}" class="text-primary fw-bold" data-bs-toggle="tooltip" data-bs-placement="top" title="Order Number">
                                                {{$order->order_id}}
                                            </a>
                                        </td>
                                        <td data-label="{{translate('Qty')}}">
                                            <span>{{$order->qty}}</span><br>
                                        </td>
                                        <td data-label="{{translate('Time')}}">
                                            <span class="fw-bold">{{diff_for_humans($order->created_at)}}</span><br>
                                            {{get_date_time($order->created_at)}}
                                        </td>
                                        <td data-label="{{ translate('Customer Info') }}" class="text-align-left">

                                            @php
                                               $firstName = @$order->billingAddress ?  @$order->billingAddress->first_name : @$order->billing_information->first_name ;
                                               $lastName  = @$order->billingAddress ?  @$order->billingAddress->last_name : @$order->billing_information->last_name ;
                                               $phone  = @$order->billingAddress ?  @$order->billingAddress->phone : $order->billing_information->phone ;
                                               $email  = @$order->billingAddress ?  @$order->billingAddress->email : $order->billing_information->email ;
                                               $city  = @$order->billingAddress ?  @$order->billingAddress->city->name : $order->billing_information->city ;
                                               $country  = @$order->billingAddress ?  @$order->billingAddress->country->name : @$order->billing_information->country ;
                                               $zip  = @$order->billingAddress ?  @$order->billingAddress->zip : @$order->billing_information->zip ;
                                               $address  = @$order->billingAddress ?  @$order->billingAddress->address->address : @$order->billing_information->address ;
                                            @endphp
                                            


                                           <span>{{ translate('Name') }}:
                                               {{ @$order->customer->name ?? @$firstName }}</span><br>
                                           <span>{{ translate('Phone') }}:
                                               {{ @$order->customer->phone ?? @$phone }}</span>
                                           <br>
                                           @if ($order->customer_id)
                                               <a href="{{ route('admin.customer.details', $order->customer_id) }}"
                                                   class="fw-bold text-dark">
                                                   <span>
                                                       {{ translate('Email') }}
                                                       : {{ @$order->customer->email ?? 'N\A' }}</span>
                                               </a>
                                           @else
                                               <span>
                                                   {{ translate('Email') }}
                                                   : {{ @$email ?? 'N\A' }}</span>
                                           @endif
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

                                        <td data-label="{{translate('Order Details')}}" class="tex-align-left">
                                            <button data-details ="{{$order->orderDetails}}" class="ms-2  order-btn btn btn-info btn-sm custom-toggle active" data-bs-toggle="modal"  data-bs-target="#orderDetails">{{translate('view All')}} ({{$order->orderDetails->count()}})</button>
                                        </td>
                                        <td data-label="{{translate('Payment Status')}}">
                                            <span class="fw-bold">
                                               {{(short_amount($order->amount))}}
                                            </span><br>
                                            @if($order->payment_status == App\Models\Order::UNPAID)
                                                <span class="badge badge-soft-danger">{{translate('Unpaid')}}</span>
                                                @elseif($order->payment_status == App\Models\Order::PAID)
                                                <span class="badge badge-soft-success">{{translate('Paid')}}</span>
                                            @endif
                                        </td>
                                        <td data-label="{{translate('Delivery Status')}}">
                                            @if($order->status == App\Models\Order::PLACED)
                                                <span class="badge badge-soft-primary">{{translate('Placed')}}</span>
                                            @elseif($order->status == App\Models\Order::CONFIRMED)
                                                    <span class="badge badge-soft-info">{{translate('Confirmed')}}</span>
                                            @elseif($order->status == App\Models\Order::PROCESSING)
                                                <span class="badge badge-soft-secondary">{{translate('Processing')}}</span>
                                            @elseif($order->status == App\Models\Order::SHIPPED)
                                                    <span class="badge badge-soft-warning">{{translate('Shipped')}}</span>
                                            @elseif($order->status == App\Models\Order::DELIVERED)
                                                    <span class="badge badge-soft-success">{{translate('Delivered')}}</span>
                                            @elseif($order->status == App\Models\Order::CANCEL)
                                                <span class="badge badge-soft-danger">{{translate('Cancel')}}</span>
                                            @endif
                                        </td>
                                        <td data-label="{{translate('Action')}}">
                                            <div class="hstack justify-content-center gap-3">
                                                @if(permission_check('view_order'))
                                                <a title="{{translate('Print')}}" href="{{route('admin.inhouse.order.print', [$order->id, 'inhouse'])}}"
                                                    class="fs-18 link-success">
                                                    <i class="ri-printer-line"></i>
                                                </a>
                                                <a title="{{translate('Details')}}" data-bs-toggle="tooltip" data-bs-placement="top"  href="{{route('admin.inhouse.order.details', $order->id)}}"
                                                    class="fs-18 link-info ms-1"><i class="ri-list-check"></i></a>
                                                @endif
                                                @if(permission_check('update_order'))
                                                    <a title="{{translate('Order Status')}}" data-order_id="{{$order->id}}"   data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0)" class="fs-18 link-warning order_id">
                                                        <i class="ri-pencil-line"></i>
                                                    </a>
                                                @endif
                                                @if(permission_check('delete_order'))
                                                    @if($order->status == App\Models\Order::CANCEL)
                                                        <a href="javascript:void(0);" data-href="{{route('admin.inhouse.order.delete',$order->id)}}" class="delete-item fs-18 link-danger">
                                                            <i class="ri-delete-bin-line"></i></a>
                                                    @endif
                                                @endif
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
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
        
    </div>
</div>

@include('admin.modal.delete_modal')

<div class="modal fade" id="orderStatus" tabindex="-1" aria-labelledby="orderStatus" aria-hidden="true">
    <div class="modal-dialog">
        <form  action="{{route('admin.inhouse.order.status.update')}}" method="post">
            @csrf

            <input type="hidden" name="order_id" value="" id="order_id">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{translate('Status Update')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <select class="form-select" id="select_status">
                            <option value="">{{translate('Select Status')}}</option>
                            <option value="payment_status">{{translate('Payment Status')}}</option>
                            <option value="delivery_status">{{translate('Delivery Status')}}</option>
                        </select>
                    </div>

                    <div class="status_html"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" class="btn btn-success">{{translate('Update')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div  class="modal fade" id="orderDetails" tabindex="-1" aria-labelledby="orderDetails" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered  modal-dialog-scrollable">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Product Info')}}
				</h5>

				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal">
                </button>
			</div>

	            <div class="modal-body">
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-hover table-centered align-middle table-nowrap">
                                <tbody id="product-info">

                                </tbody>
                            </table>
                        </div>

                    </div>
				</div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger " data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	            </div>

        </div>
    </div>
</div>


@endsection

@push('script-push')
<script>
	(function($){
       	"use strict";

        $(".order_id").on('click',function(){

            $('.status_html').html('');
            $("#order_id").val($(this).data("order_id"));
            $("#select_status").val('');

            var modal = $('#orderStatus');

            modal.modal('show');
        });

		$('#select_status').on('change', function(){
			var select_status = $('#select_status').val();
            if(select_status == 'payment_status'){
                $('.status_html').html(`
                    <div class="form-group mt-2">
                        <label for="payment_status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
                        <select class="form-control" name="payment_status" id="payment_status">
                            <option value="2">{{translate('Paid')}}</option>
                            <option value="1">{{translate('Unpaid')}}</option>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <textarea name="payment_note" placeholder="Write short note" class="form-control"></textarea>
                    </div>
                    `);
            }

            if(select_status == 'delivery_status'){
                $('.status_html').html(`
                    <div class="form-group mt-2">
                        <label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="status">
                            <option value="">{{translate('Nothing Selected')}}</option>
                            <option value="2">{{translate('Confirmed')}}</option>
                            <option value="3">{{translate('Processed')}}</option>
                            <option value="4">{{translate('Shipped')}}</option>
                            <option value="5">{{translate('Delivered')}}</option>
                            <option value="6">{{translate('Cancel')}}</option>
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <textarea name="delivery_note" placeholder="Write short note" class="form-control"></textarea>
                    </div>
                `)


            }
		});

        $('.order-btn').on('click', function(){
            var modal = $('#orderDetails');
            var order_details = $(this).attr('data-details')

            order_details =JSON.parse(order_details);

            var html = ''
            for(var i  in order_details ){
                if(order_details[i].product){
                    var url =  "{{route('product.details',[":slug",":id"])}}"
                                url = (url.replace(':slug', order_details[i].product.slug  ? order_details[i].product.slug : convertSlug(order_details[i].product.name))).replace(':id', order_details[i].product.id)
                    html += `  <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded p-1 me-2">
                                                <img  onerror="this.onerror=null;this.src='{{route('default.image','200x200')}}';" src="{{ asset('/assets/images/backend/product/featured')}}/${order_details[i].product.featured_image}" alt="${order_details[i].product.featured_image}" class="img-fluid d-block">
                                            </div>
                                            <div>
                                                <h5 class="fs-14 mb-0"><a href="${url}" class="text-reset">${order_details[i].product.name}</a></h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-semibold">
                                            {{translate("Attribute")}}
                                        </span>

                                        <h5 class="fs-14 mb-0 mt-1">
                                            <span class='badge badge-soft-info'> ${order_details[i].attribute} </span>
                                        </h5>

                                    </td>

                                    <td>
                                        <span class="text-muted fw-semibold">{{translate('Amount')}}</span>
                                        <h5 class="fs-14 mb-0 mt-1 fw-normal">{{default_currency()->symbol}}${Math.round(order_details[i].total_price)} </h5>
                                    </td>
                                </tr>`
                            }
            }
            $('#product-info').html(html)
            modal.modal('show');
        });


        function convertSlug(Text) {
            return Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        }




	})(jQuery);
</script>
@endpush
