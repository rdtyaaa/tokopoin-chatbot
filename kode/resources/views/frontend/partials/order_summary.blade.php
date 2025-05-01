    <div class="table-responsive table-card position-relative order-summary-table">
        @php  
            
            
            $shippingConfiguration =  json_decode(site_settings('shipping_configuration')); 

            $flatShippingRate      =  @$shippingConfiguration->standard_shipping_fee ?? 0;
            
            $reward_point_system        = site_settings('club_point_system') == App\Enums\StatusEnum::true->status();

            $reward_point_via          = site_settings('reward_point_by',0);

            $rewardPointConfigurations  = !is_array(site_settings('order_amount_based_reward_point',[])) 
                                                            ? json_decode(site_settings('order_amount_based_reward_point',[]),true) 
                                                            : [];
            $productWisePoint  = 0;

         @endphp




        <table class="table table-borderless align-middle mb-0 w-100">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="fs-14">
                        {{translate('Product')}}
                    </th>

                  
                    <th scope="col" class="text-end fs-14 nowrap">
                        {{translate("Price")}}
                    </th>


                    @if(auth()->user()&& 
                       $reward_point_system && 
                       $reward_point_via == App\Enums\StatusEnum::false->status())
                        <th scope="col" class="text-end fs-14 nowrap">
                            {{translate("Point")}}
                        </th>
                    @endif

                    @if(@$shippingConfiguration->shipping_option == 'PRODUCT_CENTRIC')
                        <th scope="col" class="text-end fs-14 nowrap">
                            {{translate("Shipping")}}
                        </th>
                    @endif

                </tr>
            </thead>
            @php
                $subTotal       =  0;
                $shippingCharge =  (@$shippingConfiguration->shipping_option == 'FLAT') ? $flatShippingRate : 0 ;

                if(@$shippingConfiguration->shipping_option == 'LOCATION_BASED'){
                    $shippingCharge  = @$city->shipping_fee ?? 0;
                }

                if(@$shippingConfiguration->shipping_option == 'CARRIER_SPECIFIC'){
                    $shippingCharge  = @$shippingDeliveryCharge ?? 0;
                }

                $discount       =  0;
                $taxAmount      =  0;
       
            @endphp
            <tbody>
                @foreach($items as $data)

                    <tr>
                        <td class="text-start">
                            <div class="d-flex align-items-start gap-4 nowrap">
                                    <div class="checkout-pro-img m-0 position-relative">
                                        <img class="m-0" src="{{show_image(file_path()['product']['featured']['path'].'/'.$data->product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$data->product->name}}">

                                        <span data-id="{{$data->id}}" class="remove-product addtocart-remove-btn remove-cart-data"><i class="fa-solid fa-xmark"></i></span>
                                    </div>
                                    <div class="check-item">
                                        <h4 class="product-title pb-1">
                                            <a href="{{route('product.details',[$data->product->slug ? $data->product->slug : make_slug($product->name),$data->product->id])}}">
                                                {{limit_words($data->product->name,2)}}
                                            </a>
                                        </h4>
                                        <p class="text-muted fs-12 lh-1">{{short_amount($data->price - $data->total_taxes)}} x {{$data->quantity}}  ({{$data->attributes_value}}) </p>
                                    </div>
                            </div>
                        </td>

                        <td class="text-end nowrap">{{short_amount(($data->price - $data->total_taxes)*$data->quantity)}}</td>

                        @if(auth()->user() && 
                            $reward_point_system && 
                            $reward_point_via == App\Enums\StatusEnum::false->status())
                              <td class="text-end nowrap">{{$data->product->point}}</td>
                        @endif


                        @if(@$shippingConfiguration->shipping_option == 'PRODUCT_CENTRIC')
                            @php
                                $shippingFees =  $data->product->shipping_fee;
                                if($data->product->shipping_fee_multiply == 1 )$shippingFees *=$data->quantity;
                            @endphp
                            <td class="text-end nowrap">{{short_amount($shippingFees)}}</td>

                        @endif


                    </tr>
                    @php
                       $productWisePoint += @$data->product->point ?? 0;
                       $subTotal  += (($data->original_price-$data->total_taxes )*$data->quantity);
                       $discount  += ($data->discount*$data->quantity);
                       $taxAmount += ($data->total_taxes*$data->quantity);
                       if(@$shippingConfiguration->shipping_option == 'PRODUCT_CENTRIC'){
                           $shippingCharge+=$shippingFees;
                       }
                       
                    @endphp
                @endforeach
            </tbody>
        </table>
        @php
            $subTotal  = short_amount($subTotal,false,false);
            $discount  = short_amount($discount,false,false);
            $taxAmount = short_amount($taxAmount,false,false);
            $shippingCharge = short_amount($shippingCharge,false,false);
        @endphp

        <div class="order-summary-loader loader-spinner d-none ">
            <div class="spinner-border" role="status">
                <span class="visually-hidden"></span>
            </div>
        </div>

    </div>
<ul>

    @if(auth()->user())
        <li class="py-4 coupon-input">
            <div class="input-group">
                <input name="coupon_code" type="text" class="form-control border-2" placeholder="Enter your Coupon" aria-label="Enter your Coupon">
                <button type="button" class="input-group-text btn-success fs-14 apply-btn " >
                    {{translate("Apply")}}
                </button>
            </div>
        </li>
    @endif

    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
            {{translate("Sub Total")}}:</span>
        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap"  >{{show_amount($subTotal)}}</span>
    </li>  
    
    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
            {{translate("All Taxes")}}:</span>
        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">{{show_amount($taxAmount)}}</span>
    </li>
    
    
    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
            {{translate("Regular discount")}}:</span>
        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">{{show_amount($discount)}}</span>
    </li>  
    

    <li class="d-flex align-items-center justify-content-between gap-4 subtotal">
        <span class="fw-semibold ps-4 py-4  fs-14 nowrap">
            {{translate("Shipping fees")}}:</span>
        <span class="fw-semibold text-end pe-4 py-3  fs-14 nowrap">{{show_amount($shippingCharge)}}</span>
    </li>  

    @php 
         $couponAmount = Arr::get(@session()->get('coupon') ?? [],'amount',0);
    @endphp

    <li class="order-coupon-item d-flex align-items-center justify-content-between gap-4 @if(!session()->has('coupon')) d-none @endif">
        <span  class="ps-4 py-2 nowrap fs-14">
            {{translate("Coupon Discount")}}
            <span class="text-muted">({{translate("Coupon")}})</span>
            : </span>
        <span class="text-end pe-4 py-2 nowrap fs-14">  <span>- {{show_currency()}}<span
            id="couponamount">{{$couponAmount}}</span></span></span>
    </li>

    <li class="order-cost-item order-shipping-cost d-none d-flex align-items-center justify-content-between gap-4">
        <span class="ps-4 py-3 nowrap fs-14">{{translate("Shipping Charge")}} :</span>
        <span class="text-end pe-4 py-3 nowrap fs-14" >
            {{show_currency()}}<span id="shipping_cost">0</span>
        </span>
    </li>

    @php
        $total = (($subTotal -  $couponAmount) + $shippingCharge)-$discount + $taxAmount;
    @endphp

    <li class="table-active d-flex align-items-center justify-content-between gap-4" data-sub ="{{$total}}" id="subtotalamount">
        <h6 class="ps-4 py-3 nowrap fs-14 fw-bold">{{translate("Total")}} :</h6>
        <span class="text-end pe-4 py-3 nowrap fs-14">
           <span id="totalamount" class="fw-bold"  >
             
                {{show_amount($total )}}
            </span>
        </span>
    </li>

    @if(auth()->user() && $reward_point_system)
        <li class="table-active d-flex align-items-center justify-content-between gap-4">
            <h6 class="ps-4 py-3 nowrap fs-14 fw-bold">{{translate("Total Point Earned")}} :</h6>
            <span class="text-end pe-4 py-3 nowrap fs-14">
            <span id="totalamount" class="fw-bold">

                  @if($reward_point_via == App\Enums\StatusEnum::false->status())
                            {{ $productWisePoint}}
                  @else
                     @php

                        $configuration =   collect($rewardPointConfigurations)
                                                ->filter(function ($item) use ($total) : bool {
                                                    $item = (object)($item);
                                                    return $total > $item->min_amount && $total <= $item->less_than_eq;
                                                })->first();

                        $configuration = is_array($configuration)  
                                                ? (object)$configuration
                                                : null ; 

  
                     @endphp
                      {{ @$configuration->point ?? (int) site_settings('default_reward_point',0) }}
                  @endif
                </span>
            </span>
        </li>
    @endif

</ul> 