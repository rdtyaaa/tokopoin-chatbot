@extends('frontend.layouts.app')
@section('content')

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
        <div class="row">

            <div @if($order->payment_details) class="col-8" @else class="col-12" @endif>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                    <h4 class="card-title">
                                        {{translate("Ordar Details")}}
                                    </h4>
                            </div>
                        </div>
                    </div>
                    <div>
                        @php
                           $subTotal = 0;
                           $originalPrice = 0;
                           $discount = 0;
                           $tax = 0;
                           $totalAmount = 0;

                        @endphp

        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle">
                                    <thead class="table-light">
                                        <tr class="text-muted fs-14">
        
                                            <th scope="col" class="text-start">
                                                {{translate("Product")}}
                                            </th>
                                            <th scope="col" class="text-center">
                                                {{translate("Qty")}}
                                            </th>
                                            <th scope="col" class="text-center">
                                                {{translate("Varient")}}
                                            </th>

                                            <th scope="col" class="text-center">
                                                {{translate("Original Price")}}
                                            </th>

                                            <th scope="col" class="text-center">
                                                {{translate("Tax amount")}}
                                            </th>
                                            <th scope="col" class="text-center">
                                                {{translate("Discount")}}
                                            </th>
                                            <th scope="col" class="text-center">
                                                {{translate("Total Price")}}
                                            </th>
                                            <th scope="col" class="text-center">
                                                {{translate('Status')}}
                                            </th>
        
                                        </tr>
                                    </thead>
                                    <tbody class="border-bottom-0">
                                        @forelse($orderDetails as $orderDetail)
                                            @if($orderDetail->product)
                                                <tr class="fs-14 tr-item" >
                                                    <td>
                                                        <div class="wishlist-product align-items-center">
                                                            <div class="wishlist-product-img">
                                                                <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$orderDetail->product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$orderDetail->product->name}}">
                                                            </div>
                                                            <div class="wishlist-product-info">
                                                                <h4 class="product-title">

                                                                    @php
                                                                        $product = $orderDetail->product;
                                                                        $slug = $product->slug ? $product->slug : make_slug($product->name);
                                                                    @endphp
        
                                                                    <a  href="{{route('product.details',[$slug,$product->id])}}">
                                                                        {{$orderDetail->product->name}}
                                                                    </a>
                                        
                                                                </h4>
        
                                                                <div class="ratting mb-0">
                                                                    @php echo show_ratings($orderDetail->product->review->avg('ratings')) @endphp
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
        
                                                    <td class="text-center">
                                                        {{$orderDetail->quantity}}
                                                    </td>
                                                    <td class=" text-center">
                                                        <span class="badge-soft-dark px-2 py-1 rounded fs-12"> {{$orderDetail->attribute}}</span>
                                                    </td>

                                                    <td class="text-center">
                                                        <span>{{short_amount($orderDetail->original_price)}}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span>{{short_amount($orderDetail->total_taxes)}}</span>
                                                    </td>

                                                    <td class="text-center">
                                                        <span>{{short_amount($orderDetail->discount)}}</span>
                                                    </td>
                                                    



                                                    <td class=" text-center">
                                                        {{short_amount($orderDetail->total_price)}}
                                                    </td>
                                                    <td class=" text-center">
        
                                                        @php echo order_status_badge($orderDetail->status)  @endphp
        
                                                    </td>


                                                    @php
                                                            $originalPrice += $orderDetail->original_price;
                                                            $tax += $orderDetail->total_taxes;
                                                            $discount += $orderDetail->discount;
                                                            $totalAmount += $orderDetail->total_price;
                                                    @endphp
        
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td class="text-center py-5" colspan="100">{{translate('No Data Found')}}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                    <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                        {{translate("Sub Total")}}:</span>
                                    <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap"  id="subtotalamount">
                                        {{short_amount($originalPrice)}}
                                    </span>
                                </li>  
                                
                                <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                    <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                        {{translate("All Taxes")}}:</span>
                                    <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">  {{short_amount($tax)}}</span>
                                </li>

                                <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                    <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                        {{translate("Total Discount")}}:</span>
                                    <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">  {{short_amount($order->discount)}}</span>
                                </li>

                                <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                    <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                        {{translate("Shpping charge")}}:</span>
                                    <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">  {{short_amount($order->shipping_charge)}}</span>
                                </li>

                                <li class="table-active d-flex align-items-center justify-content-between gap-4">
                                    <h6 class="ps-4 py-3 nowrap fs-14 fw-bold">{{translate("Total")}} :</h6>
                                    <span class="text-end pe-4 py-3 nowrap fs-14">
                                       <span id="totalamount" class="fw-bold">
                                 
                                            {{short_amount($order->amount )}}
                                        </span>
                                    </span>
                                </li>
        
                            </div>
                        </div>
        
                    </div>
        
                </div>
            </div>

            @if($order->payment_details)
                <div class="col-4">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{translate("Custom Payment details
                                            ")}}
                                        </h4>
                                </div>
                            </div>
                        </div>
                        <div>
            
                            <div class="card-body">

                                <ul>
                                
                                    @foreach ($order->payment_details as $key => $value )

                                            <li>
                                                <span class="font-weight-bold text-break">{{k2t($key)}} : {{$value}}</span>
                                            </li>
                                        
                                    @endforeach
    
                                </ul>
                            
                            </div>
            
                        </div>
            
                    </div>
                </div>
            @endif



        </div>
     
    </div>
</section>


@endsection

