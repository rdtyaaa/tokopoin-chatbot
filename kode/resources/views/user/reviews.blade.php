@extends('frontend.layouts.app')
@section('content')
   @php
     $promo_banner = frontend_section('promotional-offer');
   @endphp

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
        <div class="row g-4">
            @include('user.partials.dashboard_sidebar')

            <div class="col-xl-9 col-lg-8">
                <div class="profile-user-right">
                    <a href="{{@frontend_section_data($promo_banner->value,'image','url')}}" class="d-block">
                        <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($promo_banner->value,'image'),@frontend_section_data($promo_banner->value,'image','size'))}}" alt="banner.jpg">
                    </a>

                    <div class="card mt-5">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <h4 class="card-title">
                                        {{translate('My Review')}}
                                    </h4>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle">
                                    <thead class="table-light">
                                        <tr class="text-muted fs-14">
                                            <th scope="col" class="text-start">
                                                {{translate("Product")}}
                                            </th>
                                            <th scope="col">
                                                {{translate("Review")}}
                                            </th>
                                        </tr>
                                    </thead>
                                    @php

                                       $reviews = $user->reviews()
                                                       ->paginate(site_settings('pagination_number',10));
                                    @endphp

                                    <tbody class="border-bottom-0">
                                        @forelse( $reviews  as $review)
                                            @if($review->product)
                                                <tr class="fs-14 tr-item" >
                                                    <td class="text-start">
                                                        <div class="wishlist-product">
                                                            <div class="wishlist-product-img">
                                                                <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$review->product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$review->product->name}}">
                                                            </div>
                                                            <div class="wishlist-product-info">
                                                                <h4 class="product-title">

                                                                    @php
                                                                        $product = $review->product;
                                                                        $slug = $product->slug ? $product->slug : make_slug($product->name);
                                                                    @endphp
                                                 
                                                                    <a  href="{{route('product.details',[$slug,$product->id])}}">
                                                                        {{$product->name}}
                                                                    </a>
                                                                </h4>
                                                                <div class="ratting">
                                                                    @php echo show_ratings($review->rating) @endphp
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        {{limit_words($review->review,40)}}
                                                    </td>

                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td class="text-center text-muted py-5" colspan="2"><p>{{translate('No Data Found')}}</p></td>
                                            </tr>
                                        @endforelse


                                    </tbody>
                                </table>

                            </div>
                            <div class="mt-4 d-flex align-items-center justify-content-end">
                                {{$reviews->links()}}
                            </div>
                        </div>
                    </div>

                 
                </div>
            </div>
        </div>
    </div>
</section>



@endsection



