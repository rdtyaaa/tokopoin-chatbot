@extends('frontend.layouts.app')
@push('stylepush')
    <style>
        .h-400 {
            height: 400px;
        }
        .map-search-input {
            width: 100%;
            max-width: 250px;
            position: relative!important;
            top: 6px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            background-color: white!important;

        }

        .form-check-label{
            padding-bottom: 1.5rem !important;
        }

        .custom-payment{
            width:60px;
            height:60px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
    </style>
@endpush
@section('content')



    <div class="breadcrumb-banner">
        <div class="breadcrumb-banner-img">
            <img src="{{ show_image(file_path()['frontend']['path'] . '/' . @frontend_section_data($breadcrumb->value, 'image'), @frontend_section_data($breadcrumb->value, 'image', 'size')) }}"
                alt="breadcrumb.jpg">
        </div>
        <div class="page-Breadcrumb">
            <div class="Container">
                <div class="breadcrumb-container">
                    <h1 class="breadcrumb-title">{{ $title }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">
                                    {{ translate('home') }}
                                </a></li>

                            <li class="breadcrumb-item active" aria-current="page">
                                {{ translate($title) }}
                            </li>

                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="pb-80">
        <div class="Container"> 
            <form action="{{ route('user.order.recheckout') }}" method="POST">

                <input type="hidden" name="id" value="{{$order->id }}">
                @csrf
                <div class="row g-4">
                    <div class="col-xxl-8 col-xl-7 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    {{ translate('Checkout') }}
                                </h5>
                            </div>

                            <div class="card-body checkout-tab">
                               

                                <div class="tab-content checkout-form-content">

                        
                                        @if(
                                           site_settings('customer_wallet') == App\Enums\StatusEnum::true->status() )
                                        
                                            <div class="tab-header">
                                                <h5>
                                                    {{ translate('Payment Type') }}
                                                </h5>

                                                <p class="text-muted">
                                                    {{ translate('Please select A Payment Type') }}
                                                </p>
                                            </div>

                                            <div class="row mb-4">
                                                <div class=" col-md-6">
                                                    <div class="form-check card-radio">
                                                        <input type="radio" id="Traditional"
                                                            name="wallet_payment" checked  value="{{  App\Enums\StatusEnum::false->status() }}"
                                                            class="form-check-input payment-radio-btn payment-type">
                                                        <label class="form-check-label pointer"
                                                            for="Traditional">
                                                            <span class="d-flex align-items-center gap-4">
                                                                <span class="payment_icon custom-payment">
                                                                    <i class="fa-solid fa-money-bill-transfer"></i>
                                                                </span>

                                                                <span class="fs-14 text-wrap">
                                                                    {{
                                                                        translate('Traditional')
                                                                    }}
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check card-radio">
                                                        <input type="radio" id="wallet_payment"
                                                            name="wallet_payment" value="{{  App\Enums\StatusEnum::true->status() }}"
                                                            class="form-check-input payment-radio-btn payment-type">
                                                        <label class="form-check-label pointer"
                                                            for="wallet_payment">
                                                            <span class="d-flex align-items-center gap-4">
                                                                <span class="payment_icon custom-payment">
                                                                    <i class="fa-solid fa-wallet"></i>
                                                                </span>

                                                                <span class="fs-14 text-wrap">
                                                                    {{
                                                                        translate('Wallet')
                                                                    }}
                                                                   <p class="fs-12 mt-2">
                                                                    {{ 
                                                                        short_amount($user->balance)
                                                                    }}
                                                                  </p>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div id="paymentSelection"  @if(site_settings('customer_wallet') == App\Enums\StatusEnum::true->status()) class="d-none mt-4"   @endif  >
                                            <div class="tab-header">
                                                <h5>
                                                    {{ translate('Payment Selection') }}
                                                </h5>
    
                                                <p class="text-muted">
                                                    {{ translate('Please select A Payment Method') }}
                                                </p>
                                            </div>
    
    
           
    
    
    
                                            @if (site_settings('digital_payment', App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status())
                                                <div class="tab-header">
                                                    <h5>
                                                        {{ translate('Digital Payment') }}
                                                    </h5>
    
                                                </div>
                                                <div class="row g-4">
                                                    @foreach ($paymentMethods as $paymentMethod)
                                                        <div class="col-xl-4 col-md-6">
                                                            <div class="form-check card-radio">
                                                                <input type="radio" id="payment-{{ $paymentMethod->id }}"
                                                                    name="payment_id" value="{{ $paymentMethod->id }}"
                                                                    class="form-check-input payment-radio-btn">
                                                                <label class="form-check-label pointer"
                                                                    for="payment-{{ $paymentMethod->id }}">
                                                                    <span class="d-flex align-items-center gap-4">
                                                                        <span class="payment_icon">
                                                                            <img src="{{ show_image(file_path()['payment_method']['path'] . '/' . $paymentMethod->image, file_path()['payment_method']['size']) }}"
                                                                                alt="{{ $paymentMethod->image }}">
                                                                        </span>
    
                                                                        <span class="fs-14 text-wrap">
                                                                            {{ $paymentMethod->name }}
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
    
                                                </div>
                                            @endif
    
    
    
                                            @if (site_settings('offline_payment', App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status())
                                                <div class="tab-header mt-5">
                                                    <h5>
                                                        {{ translate('Manual Payment') }}
                                                    </h5>
                                                </div>
                                                <div class="row g-4">
                                                    @foreach ($manualPaymentMethods as $manualPaymentMethod)
                                                        <div class="col-xl-4 col-md-6">
                                                            <div class="form-check card-radio">
                                                                <input type="radio"
                                                                    id="payment-{{ $manualPaymentMethod->id }}"
                                                                    name="payment_id" value="{{ $manualPaymentMethod->id }}"
                                                                    class="form-check-input payment-radio-btn">
                                                                <label class="form-check-label pointer"
                                                                    for="payment-{{ $manualPaymentMethod->id }}">
                                                                    <span class="d-flex align-items-center gap-4">
                                                                        <span class="payment_icon">
                                                                            <img src="{{ show_image(file_path()['payment_method']['path'] . '/' . $manualPaymentMethod->image, file_path()['payment_method']['size']) }}"
                                                                                alt="{{ $manualPaymentMethod->image }}">
                                                                        </span>
    
                                                                        <span class="fs-14 text-wrap">
                                                                            {{ $manualPaymentMethod->name }}
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
    
                                                </div>
                                            @endif
                                        </div>




                                        <div
                                            class="d-flex align-items-start justify-content-sm-between justify-content-center flex-wrap gap-4 mt-5">
                                           
                                            <button type="submit"
                                                class="nexttab check-input btn-label  wave-btn oder-btn"><i
                                                    class="fa-solid fa-cart-shopping label-icon align-middle fs-14 ">

                                                </i>

                                                {{ translate('Order') }}
                                            </button>
                                        </div>
                             

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-5 col-lg-4">
                        <div class="card checkout-product">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title fs-18 mb-0">
                                            {{ translate('Order Summary') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body order-summary">


                                <div class="table-responsive table-card position-relative order-summary-table">
                                  
                            
                            
                            
                                    <table class="table table-borderless align-middle mb-0 w-100">
                                        <thead class="table-light">
                                            <tr>
                                                <th scope="col" class="fs-14">
                                                    {{translate('Product')}}
                                                </th>
                            
                                              
                                                <th scope="col" class="text-end fs-14 nowrap">
                                                    {{translate("Price")}}
                                                </th>
                            
                                             
                                            </tr>
                                        </thead>
                                        @php
                                            $orderDeatils  = $order->orderDetails;

                                            $originalPrice = 0;
                                            $discount = 0;
                                            $tax = 0;
                                            $totalAmount = 0;
                                        @endphp
                                        <tbody>
                                            @foreach ($orderDeatils as $orderDetail)
                                                @php 
                                                  $product = $orderDetail->product;
                                                @endphp
                                                @if($product)
                                                    <tr>
                                                        <td class="text-start">
                                                            <div class="d-flex align-items-start gap-4 nowrap">
                                                                    <div class="checkout-pro-img m-0 position-relative">
                                                                        <img class="m-0" src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$product->name}}">
                                
                                                                    </div>
                                                                    <div class="check-item">
                                                                        <h4 class="product-title pb-1">
                                                                            <a href="{{route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])}}">
                                                                                {{limit_words($product->name,2)}}
                                                                            </a>
                                                                        </h4>
                                                                        <p class="text-muted fs-12 lh-1">{{ short_amount($orderDetail->original_price / $orderDetail->quantity) }} x {{$orderDetail->quantity}}  ({{$orderDetail->attribute}}) </p>
                                                                    </div>
                                                            </div>
                                                        </td>
                                
                                                        <td class="text-end nowrap">{{ short_amount($orderDetail->original_price / $orderDetail->quantity*$orderDetail->quantity) }}</td>
                                
                                     

                                                    </tr>
                                                @php
                                                        $originalPrice += $orderDetail->original_price;
                                                        $tax += $orderDetail->total_taxes;
                                                        $discount += $orderDetail->discount;
                                                        $totalAmount += $orderDetail->total_price;
                                                @endphp
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                   
                            
                                    <div class="order-summary-loader loader-spinner d-none ">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden"></span>
                                        </div>
                                    </div>

                                </div>
                                <ul>
                                

                                    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                            {{translate("Sub Total")}}:</span>
                                        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap"  > {{ short_amount($originalPrice) }}</span>
                                    </li>  
                                    
                                    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                            {{translate("All Taxes")}}:</span>
                                        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">{{ short_amount($tax) }}</span>
                                    </li>
                                    
                                    
                                    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                            {{translate("Regular discount")}}:</span>
                                        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">   {{ short_amount($order->discount) }}</span>
                                    </li>  
                                    
                                
                                    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
                                        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
                                            {{translate("Shipping fees")}}:</span>
                                        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap"> {{ short_amount($order->shipping_charge) }}</span>
                                    </li>  
                                
                                 
                
                                    <li class="table-active d-flex align-items-center justify-content-between gap-4"  id="subtotalamount">
                                        <h6 class="ps-4 py-3 nowrap fs-14 fw-bold">{{translate("Total")}} :</h6>
                                        <span class="text-end pe-4 py-3 nowrap fs-14">
                                        <span id="totalamount" class="fw-bold"  >
                                                {{ short_amount($order->amount) }}
                                            </span>
                                        </span>
                                    </li>
                                
                                 
                                </ul> 

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </section>


    


     

@endsection



@push('scriptpush')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ site_settings('gmap_client_key') }}&libraries=places&v=3.49"
        defer></script>

    <script>
        "use strict";

        
        @if(site_settings('customer_wallet') == App\Enums\StatusEnum::true->status() )

             if($('.payment-type').val() == 1){
                $('#paymentSelection').addClass('d-none')
             }else{
                $('#paymentSelection').removeClass('d-none')
             }

            $(document).on('change','.payment-type',function(){
                var value = $(this).val()
                if(value == 1){
                    $('#paymentSelection').addClass('d-none')
                }else{
                    $('#paymentSelection').removeClass('d-none')
                }
            })

        @endif









 









    








    </script>
@endpush
