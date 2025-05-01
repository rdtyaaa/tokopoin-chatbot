<div class="quick-view-container product-details-container">
    <div class="product-detail-left pe-lg-5">
        <div class="product-thumbnail-slider">
                <img class="qv-lg-image" src="{{show_image(file_path()['product']['gallery']['path'].'/'.$product->gallery->first()->image,file_path()['product']['gallery']['size'])}}" alt="{{@$product->gallery->first()->image}}">
        </div>

        @php
           $seller = $product->seller;
        @endphp
        <div class="small-img">
            <div class="small-img-item">
                @foreach($product->gallery as $gallery)
                    <div class="gallery-sm-img quick-view-img">
                        <img src="{{show_image(file_path()['product']['gallery']['path'].'/'.$gallery->image,file_path()['product']['gallery']['size'])}}" alt="{{$gallery->image}}">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="product-detail-middle">
        <h3 class="details-product-title">
            {{($product->name)}}
        </h3>
        <div class="product-item-review">
            <div class="ratting mb-0">
               @php echo show_ratings($product->rating()->avg('rating')) @endphp <small>({{$product->rating()->count()}} {{translate('Reviews')}})</small>
            </div>
            <small>{{$product->order->count()}}
                {{translate("Orders")}}
            </small>
        </div>
        <div class="product-item-price  price-section">
            @php
                    $price      =  (@$product->stock->first()?->price ?? $product->price);
                 
          
            @endphp
            @if(count($product->campaigns) != 0 && $product->campaigns->first()->end_time > Carbon\Carbon::now()->toDateTimeString() &&   $product->campaigns->first()->status == '1')

                        @if(($product->campaigns->first()->pivot->discount) == 0)
                              <span>{{(short_amount($price ))}}</span>
                        @else
                            <span>
                                {{(short_amount(discount($price,$product->campaigns->first()->pivot->discount,$product->campaigns->first()->pivot->discount_type)))}}
                            </span>
                            <del>
                                {{(short_amount($price))}}
                            </del>
                        @endif
                    @else

                    @if(($product->discount_percentage) > 0)
                 
                        <span>
                            {{short_amount(cal_discount($product->discount_percentage, $price))}}
                        </span>
                        <del> {{short_amount($price)}}</del>

                        @else
                        <span>
                            {{short_amount($price)}}
                        </span>


                    @endif
            @endif
        </div>


        @if($product->taxes)
            <div class="pt-3">

                <button class="badge bg-success mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTax" aria-expanded="false" aria-controls="collapseTax">
                    {{translate('View taxes')}}
                </button>
                <div class="collapse" id="collapseTax">

                        <ul class="list-group list-group-flush">

                            @forelse ($product->taxes as $tax )
                            
                                <li class="list-group-item d-flex align-items-center justify-content-between bg-tax-light">
                                    
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

        <div class="product-item-summery">
            @php echo $product->short_description @endphp
        </div>
        @php
            $randNum = rand(5,99999999);
            $randNum = $randNum."details".$randNum;
        @endphp
        <form class="attribute-options-form-{{$randNum}} quick-view-form">
            <input type="hidden" name="id" value="{{ $product->id }}">
            @if(count($product->campaigns) != 0 && $product->campaigns->first()->end_time > Carbon\Carbon::now()->toDateTimeString() &&   $product->campaigns->first()->status == '1')
               <input type="hidden" name="campaign_id" value="{{ $product->campaigns->first()->id }}">
            @endif
            @foreach (json_decode($product->attributes_value) as $key => $attr_val)

                @php
                    $attributeOption =  get_cached_attributes()->find($attr_val->attribute_id);
                    $attributValues  =  @$attributeOption->value;

                @endphp

                <div class="product-colors">
                    <span> {{ $attributeOption->name }}:</span>
                    <div class="variant">
                        @foreach ($attr_val->values as $key => $value)

                                    @php
                                      $displayName =  $value;

                                      if($attributValues){
                                        $attributeValue =  $attributValues->where('name',$value)->first();
                                        if($attributeValue){
                                            $displayName = $attributeValue->display_name 
                                                                ?  $attributeValue->display_name 
                                                                : $attributeValue->name;
                                        }

                                      }
                                
                                   @endphp
                            <div class="variant-item">
                                <input @if ($key == 0) checked @endif type="radio" class="btn-check attribute-select"   name="attribute_id[{{ $attr_val->attribute_id }}]" value="{{str_replace(' ', '', $value)}}" id="success-outlined-{{$value}}" autocomplete="off">
                                <label class="btn-outline-success variant-btn" for="success-outlined-{{$value}}">{{ $displayName }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="weight">
                <div class="product-colors">
                    <span> 
                        {{translate('Weight')}} : {{$product->weight}} {{translate('KG')}}
                    </span>
                </div>
           </div>
            @php 
               $stockQty = (int) @$product->stock->first()->qty ??  0;
            @endphp
            <div class="stock-status" id="quick-view-stock">
                @if($stockQty > 0)
                    <div class="instock">
                        <i class="fa-solid fa-circle-check"></i>
                        <p>
                            {{translate("In Stock")}}
                        </p>
                    </div>
                @else
                    <div class="outstock">
                        <i class="fas fa-times-circle"></i>
                        <p>
                            {{translate("Stock out")}}
                        </p>
                    </div>
                @endif
           </div>

     

            <div class="product-actions-type">
                <div class="input-step">
                    <button type="button" class="update_qty x decrement ">–</button>
                    <input type="number" data-view='quick-view' id="quantity" class="quick-view-quantity product-quantity"  name="quantity" value="1" min='0'>
                    <button type="button" class="update_qty y increment ">+</button>
                </div>
                @php
                    $authUser = auth_user('web');
                    $wishedProducts = $authUser ? $authUser->wishlist->pluck('product_id')->toArray() : [];
                @endphp
                <a href="javascript:void(0)"  data-product_id = '{{$randNum }}' class="buy-now addtocartbtn">
                    <i class="fa-solid fa-cart-shopping"></i>
                </a>
                <button data-product_id ="{{$product->id}}" class="product-details-love-btn wishlistitem">
                    <i class="@if(in_array($product->id,$wishedProducts))
                        fa-solid
                    @else
                        fa-regular
                    @endif fa-heart"></i>
                </button>
                <button class="product-details-love-btn comparelist wave-btn" data-product_id="{{$product->id}}"><i class="fa-solid fa-code-compare"></i></button>
            </div>
        </form>
        <div class="product-detail-btn">
            <a href="javascript:void(0)" data-checkout = "yes" data-product_id = "{{$randNum}}" class="buy-now-btn quick-buy-btn addtocartbtn">
                <i class="fa-solid fa-cart-plus fs-2 me-3"></i>{{translate("Buy Now")}}
            </a>
            @if(site_settings('whatsapp_order',App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status() )

                @php
                            $wpMessage  = site_settings('wp_order_message');
                            $message = str_replace(
                                                    [
                                                        '[product_name]',
                                                        '[link]',
                                                    ],
                                                    [
                                                        $product->name,
                                                        route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])
                                                    ],
                                            $wpMessage);
                            
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
        <div class="stock-and-social">
            <div class="product-details-social">
                <span> {{translate("Share")}}  :</span>
                <div class="product-details-social-link">
                    <a href="https://www.facebook.com/sharer.php?u={{urlencode(url()->current())}}" target="__blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://twitter.com/share?url={{urlencode(url()->current())}}&text=Simple Share Buttons&hashtags=simplesharebuttons" target="__blank"><i class="fa-brands fa-twitter"></i></a>
                    <a href="http://www.linkedin.com/shareArticle?mini=true&url={{urlencode(url()->current())}}" target="__blank"><i class="fa-brands fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>












