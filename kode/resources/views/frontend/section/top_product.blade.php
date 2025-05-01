<section class="pt-80 pb-80">
    <div class="Container">
        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>{{@frontend_section_data($top_product_section->value,'heading')}} </h3>
                    <p>{{@frontend_section_data($top_product_section->value,'sub_heading')}}</p>
                </div>
            </div>
            <div class="section-title-right">
                <a href="{{route('product')}}" class="view-more-btn">
                     {{translate('View More')}}
                </a>
            </div>

        </div>

        <div class="best-selling-items">
            <div class="row g-4">
                @forelse($top_products   as $product)
                    <div class="col-xl-4 col-md-6">
                        <div class="best-selling-item">
                            <div class="product-img">
                                <a href="{{route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])}}">
                                    <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$product->featured_image}}">
                                </a>
                            </div>
                            <div class="product-info">
                                <div class="stock-status">

                                    <div class='{{($product->stock->sum("qty")) > 0 ? "instock": "outstock"}} mt-0 mb-2'>
                                        <i class='@if(($product->stock->sum("qty")) > 0) fa-solid fa-circle-check @else fas fa-times-circle @endif'></i>
                                        <p>{{($product->stock->sum("qty")) > 0 ? translate('In Stock') :  translate('Stock out')  }}</p>
                                    </div>

                                </div>
                                  <div class="priceAndRatting">
                                        <div class="ratting">
                                            @php echo show_ratings($product->review->avg('rating')) @endphp
                                        </div>


                                            <div class="product-price">

                                                @php
                                                    $price      =  (@$product->stock->first()?->price ?? $product->price);
                 
                       
                                                @endphp

                                                @if(($product->discount_percentage) > 0)

                                                    <span>
                                                        {{short_amount(cal_discount($product->discount_percentage,$price))}}
                                                    </span>

                                                    <del>
                                                        {{short_amount($price)}}</del>
                                                @else
                                                    <span>
                                                        {{short_amount($price)}}
                                                    </span>

                                                @endif


                                            </div>

                                    </div>
                                    <h4 class="product-title">
                                        <a href="{{route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])}}">
                                           {{$product->name}}
                                        </a>
                                    </h4>



                                @php
                                    $randNum = rand(1,10000000);
                                    $randNum  = $randNum."-".$randNum;
                                @endphp
                                <form class="attribute-options-form-{{ $randNum  }}">
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                </form>

                                @php
                                    $authUser       = auth_user('web');
                                    $wishedProducts = $authUser ? $authUser->wishlist->pluck('product_id')->toArray() : [];
                                @endphp

                                <div class="product-action">
                                    <a href="javascript:void(0)"  data-product_id = '{{$randNum }}' class="buy-now addtocartbtn">
                                        <span class="buy-now-icon"><svg  version="1.1"  x="0" y="0" viewBox="0 0 511.997 511.997"   xml:space="preserve" ><g><path d="M405.387 362.612c-35.202 0-63.84 28.639-63.84 63.84s28.639 63.84 63.84 63.84 63.84-28.639 63.84-63.84-28.639-63.84-63.84-63.84zm0 89.376c-14.083 0-25.536-11.453-25.536-25.536s11.453-25.536 25.536-25.536c14.083 0 25.536 11.453 25.536 25.536s-11.453 25.536-25.536 25.536zM507.927 115.875a19.128 19.128 0 0 0-15.079-7.348H118.22l-17.237-72.12a19.16 19.16 0 0 0-18.629-14.702H19.152C8.574 21.704 0 30.278 0 40.856s8.574 19.152 19.152 19.152h48.085l62.244 260.443a19.153 19.153 0 0 0 18.629 14.702h298.135c8.804 0 16.477-6.001 18.59-14.543l46.604-188.329a19.185 19.185 0 0 0-3.512-16.406zM431.261 296.85H163.227l-35.853-150.019h341.003L431.261 296.85zM173.646 362.612c-35.202 0-63.84 28.639-63.84 63.84s28.639 63.84 63.84 63.84 63.84-28.639 63.84-63.84-28.639-63.84-63.84-63.84zm0 89.376c-14.083 0-25.536-11.453-25.536-25.536s11.453-25.536 25.536-25.536 25.536 11.453 25.536 25.536-11.453 25.536-25.536 25.536z" opacity="1" data-original="#000000" ></path></g></svg></span>
                                        {{translate("Add to cart")}}
                                    </a>
                                    <button data-product_id ="{{$product->id}}" class="heart-btn wishlistitem"><i class="@if(in_array($product->id,$wishedProducts))
                                        fa-solid
                                    @else
                                        fa-regular
                                    @endif fa-heart"></i></button>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        @include("frontend.partials.empty",['message' => 'No product found'])
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>


@if( @frontend_section_data($promo_banner->value,'position') == 'top-products')
  @includeWhen($promo_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_banner])
@endif

@if( @frontend_section_data($promo_second_banner->value,'position') == 'top-products')
    @includeWhen($promo_second_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_second_banner])
@endif
