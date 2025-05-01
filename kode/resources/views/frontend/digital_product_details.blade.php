@extends('frontend.layouts.app')

@push('stylepush')
    <style>


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

@php
     $seller = $digital_product->seller;
@endphp


<section class="pb-80">
	<div class="Container">
		<div class="row g-4">
            <div class="col-xl-7 col-lg-7">
                <div class="digital-product-left">
                    <div class="row g-2 g-md-4 ">
                        @foreach($digital_product->digitalProductAttribute->where('status', 1) as $digitalproduct)
                            <div class="col-sm-6">
                                
                                @php
                                    $price      =  ($digitalproduct->price);
                                    $taxes      =  getTaxes(@$digital_product,$price);
                                    $price      =  $price  + $taxes;
                                @endphp
                                <div class="attr-item digital-product-item" data-price="{{( $price )}}" data-id="{{$digitalproduct->id}}">
                                    <div class="form-check card-radio">
                                        <input id="{{$digitalproduct->id}}-{{$digitalproduct->id}}" name="attr-item05" type="radio" class="form-check-input">
                                        <label class="form-check-label bg--white border-0" for="{{$digitalproduct->id}}-{{$digitalproduct->id}}">
                                            <span class="attr-item-content">
                                                <span class="attr-item-img">
                                                    <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$digital_product->featured_image,file_path()['digital_product']['featured']['size'])}}" alt="{{$digital_product->featured_image}}" />
                                                </span>

                                                <span class="attr-item-details">
                                                    <h4>{{$digitalproduct->name}}</h4>
                                                    <small class="digital-product-discount">{{$digitalproduct->short_details}}</small>
                                                </span>

                                                <span class="attr-item-price">

                                                    <span>{{short_amount($price)}}</span>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="card pd-description-tab mt-md-5 mt-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <h4 class="card-title">
                                        {{translate("Description")}}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="description-content">
                                 @php echo $digital_product->description @endphp
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5 col-lg-5">
                <div class="digital-product-right">

                    <div class="card">
                       <div class="card-header">
                           <div class="d-flex">
                               <div class="flex-grow-1">
                                   <h5 class="card-title mb-0">
                                     {{translate('Total')}}
                                   </h5>
                               </div>


                               <div class="d-flex flex-column align-items-end gap-2">

                                <div>
                                    {{show_currency()}}<span id="total">0</span>
                                 </div>
  
                                  @if($digital_product->taxes)

                                      <div class="d-flex flex-column align-items-end">
              
                                          <button class="badge bg-success" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTax" aria-expanded="false" aria-controls="collapseTax">
                                              {{translate('View taxes')}}
                                          </button>
                                          <div class="collapse" id="collapseTax">
                  
                                                  <ul class="list-group list-group-flush">
                  
                                                        @forelse ($digital_product->taxes as $tax )
                                              
                                                            <li class="list-group-item d-flex align-items-center justify-content-between bg-tax-light fs-13">
                                                                
                                                                {{$tax->name}}
                                                                : <span>
                                                                    
                                                                    @if($tax->pivot->type == 0)
                                                                        {{$tax->pivot->amount}}%
                                                                    @else
                                                                        {{short_amount($tax->pivot->amount)}}
                                                                    @endif
                                                                    
                                                                </span>
                                                            </li>

                                                        @empty
                                
                                                            <li>
                                                                {{translate("Nothing tax configuration added for this product")}}
                                                            </li>
                                                            
                                                        @endforelse
                                                  
                              
                                                  </ul>
                  
                                          </div>
                                      
                                      </div>
                                  @endif
                               </div>
                          





                           </div>
                       </div>

                       <form action="{{route('user.digital.product.order')}}" method="POST">
                                @csrf
                                <input type="hidden" name="digital_attribute_id">
                                <input type="hidden" name="digital_product_id" value="{{$digital_product->id}}">

                            <div class="card-body">




                                <div class="digital-product-calculate">
                                       
                                        @if(!auth()->user())
                                            <div class="mb-4 p-4 custom-email-input">
                                                <label class="form-label" for="email">
                                                     {{translate("Email")}} <span class="text-danger">*</span>
                                                </label>
                                                <input required class="form-control" type="email" id="email" name="email" placeholder="{{translate('Enter your email')}}">
                                            </div>
                                        @endif

                                        @if($digital_product->custom_fileds)
                                           <div class="card-body">
                                                <h5 class="custom-info-title" style="font-size:15px;"> {{translate('Custom information')}} </h5>
            
                                                <div class="custom-inputs mt-4">
            
                                                <div class="row g-4">
                                                    
                                                    @foreach($digital_product->custom_fileds as $key => $value)
                                                    
                                                    
                                                        @php
                                                    
                                                            $col = $value->type == "textarea" ? 'col-sm-12' : 'col-sm-6'; 
                                                    
                                                            $values = $value->data_value ? explode(",", $value->data_value) : [];
                                                    
                                                        
                                                        @endphp
            
                                                        <div class="{{ $col }}">
            
            
                                                        <label for="{{$value->data_name}}" class="form-label">
                                                            {{($value->data_label)}} 
                                                              @if($value->data_required == 1) 
                                                                <span class="text-danger">*</span>
                                                              @endif
                                                        </label>
                                                        
                                                        @if($value->type == "text" || $value->type == "number" )
            
                                                            <input type="{{$value->type}}"  @if($value->data_required == 1) required @endif class="form-control " id="{{$value->data_name}}" name="{{$value->data_name}}" placeholder="Enter {{$value->data_label}}">
                                                        
                                                        @elseif($value->type == "textarea")
                                                            
                                                            
                                                            <textarea  @if($value->data_required == 1) required @endif class="form-control " id="{{$value->data_name}}" name="{{$value->data_name}}" placeholder="Enter {{$value->data_label}}" cols="30" rows="4"></textarea>
                                                        
                                                        @elseif($value->type == "select")
                                                        
                                                        <select   @if($value->data_required == 1) required @endif class="form-select select2 " id="{{$value->data_name}}" name="{{$value->data_name}}[]">
                                                            
                                                                @foreach( $values as $selectValue)
                                                            
                                                                <option value="{{$selectValue}}">
                                                                    {{$selectValue}}
                                                                </option>
                                                            
                                                                @endforeach
            
            
                                                            </select>
            
                                                        @endif
                                                        
            
                                                        </div>
                                                    @endforeach
            
                                                    
            
                                                </div >
            
                                                </div>
            
                                          </div>
                                   
                                        @endif

                                        <div class="d-flex flex-column gap-3 mb-4 mb-lg-5">


                                            @if(auth_user('web') && 
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
                                                                         short_amount(auth_user('web')->balance)
                                                                     }}
                                                                   </p>
                                                                 </span>
                                                             </span>
                                                         </label>
                                                     </div>
                                                 </div>
                                             </div>
                                         @endif

                                         <div id="paymentSelection"  @if(auth_user('web') &&  site_settings('customer_wallet') == App\Enums\StatusEnum::true->status()) class="d-none mt-4"   @endif  >
                                            @foreach($paymentMethods as $paymentMethod)
                                                <div class="form-check card-radio ps-0">
                                                    <input  id="{{$paymentMethod->id}}+{{$paymentMethod->id}}" value="{{$paymentMethod->id}}" name="payment_id" type="radio" class="form-check-input">
                                                    <label class="form-check-label pointer" for="{{$paymentMethod->id}}+{{$paymentMethod->id}}">
                                                        {{$paymentMethod->name}}
                                                    </label>
                                                </div>
                                            @endforeach
                                         </div>



                                        </div>

                                        <div class="digital-product-total-btn flex-column gap-4">

                                            <button   class="btn total-btn-buy-now">
                                                <i class="fa-solid fa-cart-plus fs-3 me-3"></i>
                                                {{translate("BUY NOW")}}
                                            </button>
                                            @if(site_settings('whatsapp_order',App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status() )

                                               @php
                                                $wpMessage  = site_settings('wp_digital_order_message') ;
                    
                                                $message = str_replace(
                                                        [
                                                                            '[product_name]',
                                                                            '[link]',
                                                                        ],
                                                                        [
                                                                            $digital_product->name,
                                                                            url()->current()
                                                                        ],
                                                                        $wpMessage
                                                                );
                                                                
                                                @endphp

                                                <input type="hidden" class="wp-message" value="{{$message}}">
                                                                        
                                                @if($seller && optional($seller->sellerShop)->whatsapp_order ==  App\Enums\StatusEnum::true->status())

                                                    <input type="hidden" class="wp-number" value="{{optional($seller->sellerShop)->whatsapp_number}}">
                                                    <a href="javascript:void(0);"  onclick="social_share()" class="buy-now-btn buy-with-whatsapp">
                                                        <i class="fa-brands fa-whatsapp fs-2 me-3"></i> {{translate("Order Via Whatsapp")}}
                                                    </a>
                        
                                               @endif
                                             
                                                @if(!$seller)
                                                        <input type="hidden" class="wp-number" value="{{site_settings('whats_app_number')}}">
                            
                                                        <a href="javascript:void(0);"  onclick="social_share()" class="buy-now-btn buy-with-whatsapp">
                                                            <i class="fa-brands fa-whatsapp fs-2 me-3"></i> {{translate("Order Via Whatsapp")}}
                                                        </a>
                                                @endif
                                         
                                            @endif

                                       </div>

                                </div>
                            </div>
                       </form>
                    </div>

                    <div class="product-shop digital-product-shop mt-md-5 mt-4">
                        <div class="card w-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    {{translate("Related Product")}}
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="related-product">
                                    @foreach($digital_products as $digital)
                                        <a href="{{route('digital.product.details', [$digital->slug ? $digital->slug : make_slug($digital->name), $digital->id])}}" class="related-card">
                                            <div class="related-card-img">
                                                <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$digital->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$digital->featured_image}}" />
                                            </div>
                                            <div>
                                                <h4 class="product-title">{{$digital->name}}</h4>
                                                <small class="fs-12 text-muted">{{get_date_time($digital->created_at)}}</small>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
	</div>
</section>

@endsection


@push('scriptpush')
<script>
	'use strict';

    @if(auth_user('web') && site_settings('customer_wallet') == App\Enums\StatusEnum::true->status() )

              
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

	$(document).on('click','.digital-product-item', function(){
		var price = $(this).data('price');
        price = parseFloat(price).toFixed(2);
		var id = $(this).data('id');
		$('input[name=digital_attribute_id]').val(id);
		$("#total").text(price);
	    $("#digitalattribute").find('.digital-product-item').removeClass('digital-product-active');
	    $(this).addClass('digital-product-active');
	});
</script>
@endpush
