<section class="pb-80">
    <div class="Container">
        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>{{@frontend_section_data($digital_product_section->value,'heading')}} </h3>
                    <p>{{@frontend_section_data($digital_product_section->value,'sub_heading')}}</p>
                </div>
            </div>
            <div class="section-title-right">
                <a href="{{route('digital.product')}}" class="view-more-btn">
                     {{translate('View More')}}
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-3 col-lg-4 col-md-5">
                <div class="digital-product-banner sticky-side-div">
                    <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($digital_product_section->value,'image'),@frontend_section_data($digital_product_section->value,'image','size'))}}" alt="digital-product-banner.jpg">
                </div>
            </div>
            <div class="col-xl-9 col-lg-8 col-md-7">
                <div class="row g-2 g-md-4">
                    @forelse ($digital_products  as $product)
                        <div class="col-xl-3 col-lg-4 col-6">
                            <div class="digital-product">
                                <a href="{{route('digital.product.details', [$product->slug ? $product->slug : make_slug($product->name), $product->id])}}" class="digital-product-img">
                                    <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['digital_product']['featured']['size'])}}" alt="{{$product->featured_image}}">
                                </a>
                                <div class="digital-product-info">
                                    <h4 class="product-title">
                                        {{$product->name}}
                                    </h4>

                                    <div class="d-flex justify-content-between align-items-center my-4">
                                        <div class="product-price">

                                            @php
                                                $price      =  $product->digitalProductAttribute
                                                                    ?@$product->digitalProductAttribute->where('status','1')->first()->price
                                                                    :0;
                                                $taxes      =  getTaxes(@$product,$price);
                                                $price      =  $price  + $taxes;
                                            @endphp

                                            <span>
                                                {{short_amount($price)}}
                                            </span>
                                        </div>
                                        <div class="ratting">
                                            @php echo show_ratings($product->review->avg('rating')) @endphp
                                        </div>
                                    </div>
                                    <a href="{{route('digital.product.details', [$product->slug ? $product->slug : make_slug($product->name), $product->id])}}" class="topup-btn ">
                                        {{translate("Buy now")}}
                                    </a>
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
    </div>
</section>


@if( @frontend_section_data($promo_banner->value,'position') == 'digital-products')
   @includeWhen($promo_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_banner])
@endif

@if( @frontend_section_data($promo_second_banner->value,'position') == 'digital-products')
    @includeWhen($promo_second_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_second_banner])
@endif
