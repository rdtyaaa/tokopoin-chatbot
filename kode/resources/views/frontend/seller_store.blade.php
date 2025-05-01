@extends('frontend.layouts.app')
@section('content')

<div class="store-banner">
    <div class="shop-banner-img">
        <img src="{{show_image(file_path()['shop_first_image']['path'].'/'.@$seller->sellerShop->shop_first_image,file_path()['shop_first_image']['size'])}}" alt="{{$seller->sellerShop->shop_first_image}}">
    </div>
</div>

<div class="store">
    <div class="Container">
        <div class="store-container">
            <div class="store-top">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="store-box">
                            <div class="about-store">
                                <div class="store-info">
                                    <div class="store-logo">
                                        <img src="{{show_image(file_path()['shop_logo']['path'].'/'.@$seller->sellerShop->shop_logo,file_path()['shop_logo']['size'])}}" alt="{{$seller->sellerShop->shop_logo}}">
                                    </div>
                                    <div class="store-info-content">
                                        <h4>
                                            {{$seller->sellerShop->name}}
                                        </h4>
                                        <span>
                                            {{translate("Joined")}}
                                            : {{diff_for_humans($seller->created_at)}}</span>
                                        <div class="store-overal-ratting">
                                            <div class="ratting mb-0">
                                                @php echo show_ratings($seller->rating ?? 0) @endphp
                                            </div>
                                            <span class="ms-2">{{$seller->rating ?? 0}}/</span>5
                                        </div>
                                    </div>
                                </div>
                                <a href="{{route('user.seller.chat.list' , ['seller_id' => @$seller->id])}}" class="chat-btn style-big"><i class="fa-brands fa-rocketchat"></i>
                                </a>
                            </div>
                            <div class="store-description">
                                <p>{{$seller->sellerShop->short_details}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 ps-lg-4">
                        <div class="store-box">
                            <div class="store-summery">
                                <div class="store-summery-item">
                                    <p class="fs-14">{{translate("Total Products")}}</p>
                                    <span>{{$seller->product->where('status', 1)->count()}}</span>
                                </div>
                                <div class="store-summery-item">
                                    <p class="fs-14">
                                        {{translate("Total Followers")}}
                                    </p>
                                    <span>{{$seller->follow->count()}}</span>
                                </div>
                            </div>
                            <div class="store-info-left">
                                <div class="store-info-item me-lg-5 gap-3">
                                    <div class="icon">
                                        <i class="fa-regular fa-envelope"></i>
                                    </div>
                                    <div class="content">
                                        <h6 class="d-flex align-items-center gap-3 fs-13 fw-semibold">
                                            {{translate("Email")}}
                                            :</h6>
                                        <a href="mailto:{{@$seller?->sellerShop?->email ?? @$seller->email }}" class="fs-14">
                                            {{@$seller->sellerShop->email ??  @$seller->email }}
                                        </a>
                                    </div>
                                </div>
                                <div class="store-info-item">
                                    <div class="icon">
                                        <i class="fa-regular fa-address-book"></i>
                                    </div>
                                    <div class="content">
                                        <h6 class="d-flex align-items-center gap-3 fs-13 fw-semibold">
                                            {{translate('Phone')}}
                                        </h6>
                                        <a href="tel:{{@$seller?->sellerShop?->phone ?? $seller->phone }}" class="fs-14">{{@$seller?->sellerShop?->phone ?? $seller->phone }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="store-products-container pt-80">
    <div class="Container">
        <div class="title-with-tab section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>
                        {{translate("All Products")}}
                    </h3>
                </div>
            </div>
            <div class="section-title-right">
                <div class="section-title-tabs">
                    <ul class="nav section-title-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                          <button class="nav-link title-tab-btn active store-active-title-tab" id="product-tab" data-bs-toggle="tab" data-bs-target="#product" type="button" role="tab" aria-controls="product" aria-selected="true">
                            {{translate("All Products")}}
                          </button>
                        </li>
                        <li class="nav-item" role="presentation">
                          <button class="nav-link title-tab-btn store-active-title-tab" id="digital-tab" data-bs-toggle="tab" data-bs-target="#digital" type="button" role="tab" aria-controls="digital" aria-selected="false">
                            {{translate('Digital Products')}}
                          </button>
                        </li>

                    </ul>

                </div>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="product" role="tabpanel" aria-labelledby="product-tab">
                <div class="row g-2 g-md-4">
                    @include('frontend.partials.product', ['products' => $products])

                    <div class="m-4 d-flex align-items-center justify-content-end">
                        {{$products->withQueryString()->links()}}
                    </div>

                </div>
            </div>

            <div class="tab-pane fade" id="digital" role="tabpanel" aria-labelledby="digital-tab">
                <div class="row g-2 g-md-4">
                    @forelse ($digital_products  as $product)
                        <div class="col-lg-3 col-sm-4 col-6">
                            <div class="digital-product">
                                <a href="{{route('digital.product.details', [$product->slug ? $product->slug : make_slug($product->name), $product->id])}}" class="digital-product-img">
                                    <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$product->featured_image}}">
                                </a>
                                <div class="digital-product-info">
                                    <h4 class="product-title">
                                        {{$product->name}}
                                    </h4>
                                    <div class="product-price py-3">
                                
                                            @php
                                                $price      =  $product->digitalProductAttribute 
                                                                    ? @$product->digitalProductAttribute->where('status','1')->first()?->price
                                                                    : 0;

                                                $taxes      =  getTaxes(@$product,$price);
                                                $price      =  $price  + $taxes;
                                            @endphp
                                            
                                            <span>
                                                {{short_amount($price)}}
                                            </span>
                                    </div>

                                    <a href="{{route('digital.product.details', [$product->slug ? $product->slug : make_slug($product->name), $product->id])}}" class="topup-btn ">
                                        {{translate("Top up")}}
                                    </a>
                                </div>
                            </div>
                        </div>

                    @empty
                        <div class="col-12">
                            @include("frontend.partials.empty",['message' => 'No Data Found'])
                    </div>
                    @endforelse
                </div>

                <div class="m-4 d-flex align-items-center justify-content-end">
                    {{$digital_products->withQueryString()->links()}}
                </div>

            </div>
        </div>
    </div>
</section>


<section class="pt-80 pb-80">
    <div class="Container">

        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>
                        {{translate("Related Shops")}}
                    </h3>

                </div>
            </div>
            <div class="section-title-right">
                <a href="{{route('shop')}}" class="view-more-btn">
                    {{translate('View More')}}
                </a>
            </div>
        </div>

        <div class="related-shop-items">
            <div class="row g-4">
                @forelse($sellers  as $key => $seller)
                    <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                        <div class="trende-item">
                            <div class="trende-item-left">
                                <div class="trende-item-logo">
                                    <img src="{{show_image(file_path()['shop_logo']['path'].'/'.@$seller->sellerShop->shop_logo,file_path()['shop_logo']['size'])}}" alt="{{$seller->sellerShop->shop_logo}}">
                                </div>
                                <div class="trende-item-info">
                                    <h5>{{$seller->sellerShop->name}}</h5>
                                    <div class="d-inline-flex align-items-center justify-content-start ratting">
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
                                                            <h6><a href="{{route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])}}">
                                                                {{$product->name}}
                                                            </a></h6>

                                                            <div class="product-price">
                                                                    @php
                                                                        $price      =  (@$product->stock->first()?->price ?? $product->price);
                                                   
                                          
                                                                    @endphp

                                                                    @if(($product->discount_percentage) > 0)
                                                                        <span>
                                                                            {{short_amount(cal_discount($product->discount_percentage,$price) )}}
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
    </div>
</section>

@endsection
