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
<section class="shop pb-80">
    <div class="Container">

        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>
                        {{translate("All Shops")}}
                    </h3>
                    <p>
                        {{translate($title)}}
                    </p>
                </div>
            </div>
        </div>


        <div>
            <div class="row g-2 g-md-4">
                @forelse($sellers as $seller)
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <div class="shop-item">
                            <div class="shop-thumbnail">
                                <a href="{{route('seller.store.visit',[make_slug($seller->sellerShop->name), $seller->id])}}">
                                    <img src="{{show_image(file_path()['shop_first_image']['path'].'/'.@$seller->sellerShop->shop_first_image,file_path()['shop_first_image']['size'])}}" alt="{{$seller->sellerShop->shop_first_image}}">
                                </a>
                                <div class="shop-logo">
                                        <img src="{{show_image(file_path()['shop_logo']['path'].'/'.@$seller->sellerShop->shop_logo,file_path()['shop_logo']['size'])}}" alt="{{$seller->sellerShop->shop_logo}}">
                                </div>
                            
                            </div>
                            <div class="shop-info">
                                <div class="shop-item-top">

                                    <div class="shop-content">
                                        <h5>{{$seller->sellerShop->name}}</h5>
                                        <div class="ratting">
                                            @php echo show_ratings($seller->rating ? $seller->rating :0 ) @endphp

                                        </div>

                                        <a href="{{route('user.seller.chat.list' , ['seller_id' => @$seller->id])}}" class="chat-btn"><i class="fa-brands fa-rocketchat"></i>
                                        </a>


                                        <div class="shop-follower"><i class="fa-brands fa-product-hunt"></i><span>  {{ $seller->product->count() }} </span>
                                            {{translate('Products')}}
                                        </div>
                                    </div>
                                </div>

                                <div class="shop-actions">
                                    <a href="{{route('seller.store.visit',[make_slug($seller->sellerShop->name), $seller->id])}}" class="shop-action on-active"><i class="fa-solid fa-shop"></i>
                                        {{translate(' View
                                        Store')}}
                                    </a>

                                    @if(auth()->check())
                                        @if(in_array(auth()->user()->id,$seller->follow->pluck('following_id')->toArray()))
                                            <a class="shop-action"
                                            href="{{route('user.follow', $seller->id)}}"> <i class="fa-regular fa-user"></i> {{translate('Following')}}
                                            </a>
                                        @else
                                            <a class="shop-action" href="{{route('user.follow', $seller->id)}}">  <i class="fa-regular fa-user"></i>   {{translate('Follow')}}</a>
                                        @endif
                                     @else
                                        <a  class="shop-action" href="{{route('user.follow', $seller->id)}}">  <i class="fa-regular fa-user"></i>  {{translate('Follow')}}</a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                @empty

                    <div class="col-12">
                        @include("frontend.partials.empty",['message' => 'No Data Found'])
                   </div>

                @endforelse
            </div>

            <div class="mt-5 mx-4 d-flex align-items-center justify-content-end">
                {{$sellers->withQueryString()->links()}}
            </div>
        </div>


    </div>
</section>


@endsection
