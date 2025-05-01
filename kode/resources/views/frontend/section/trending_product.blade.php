<section class="pb-80">
    <div class="Container">
        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>{{@frontend_section_data($trending_sections->value,'heading')}} </h3>
                    <p>{{@frontend_section_data($trending_sections->value,'sub_heading')}}</p>
                </div>
            </div>
            <div class="section-title-right">
                <a href="{{route('product')}}" class="view-more-btn">
                     {{translate('View More')}}
                </a>
            </div>
        </div>
        <div class="row g-4">
            @forelse($bestsellers->take(6)  as $key => $seller)
                <div class="col-xl-4 col-md-6">
                    <div class="trende-item">
                        <div class="trende-item-left">
                            <div class="trende-item-logo">
                                <img src="{{show_image(file_path()['shop_logo']['path'].'/'.@$seller->sellerShop->shop_logo,file_path()['shop_logo']['size'])}}" alt="{{$seller->sellerShop->shop_logo}}">
                            </div>
                            <div class="trende-item-info">
                                <h5>{{$seller->sellerShop->name}}</h5>
                                <div class="d-inline-flex align-items-center justify-content-start ratting mb-0">
                                     @php echo show_ratings($seller->rating ? $seller->rating :0 )  @endphp
                                </div>
                            </div>
                            <div class="shop-btn">
                                <a href="{{route('seller.store.visit',[make_slug($seller->sellerShop->name), $seller->id])}}" class="wave-btn"><span><i class="fa-sharp fa-solid fa-cart-shopping"></i></span> {{translate('View Store')}}</a>
                            </div>
                        </div>
                         @if($seller->product)
                            <div class="trende-item-right">
                                <ul class="trending-product-list">
                                
                         
                                    @forelse($seller->product->filter(function($product){
                                        return $product->product_type == 102 && $product->status == 1 && !$product->deleted_at ;
                                    })->take(3)  as $product)
                                        <li>
                                            <a href="{{route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])}}">
                                                <div class="trending-product-item">
                                                    <div class="image">
                                                        <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$product->featured_image}}">
                                                    </div>
                                                    <div class="content">
                                                        <h6>
                                                              {{limit_words($product->name,2)}}
                                                        </h6>

                                   

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
                                                </div>
                                            </a>
                                        </li>
                                    @empty
                                      <li>
                                          @include("frontend.partials.empty",['message' => 'No Data Found'])
                                      </li>
                                    @endforelse
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12">
                    @include("frontend.partials.empty",['message' => 'No Data Found'])
                </div>
            @endforelse
        </div>
    </div>
</section>
