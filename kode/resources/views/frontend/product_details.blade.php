@extends('frontend.layouts.app')

@push('stylepush')
    <style>
        .magnify-container {
            position: relative;
        }

        .magnify-container .magnified {
            display: block;
            z-index: 10;
        }

        .magnify-container .magnifier {
            height: 20rem;
            width: 20rem;
            position: absolute;
            z-index: 20;
            border: 0.4rem solid white;
            border-radius: 50%;
            background-size: 400%;
            background-image: url("{{ show_image(file_path()['product']['gallery']['path'] . '/' . $product->gallery->first()->image, file_path()['product']['gallery']['size']) }}");
            background-repeat: no-repeat;
            margin-left: -10rem !important;
            margin-top: -10rem !important;
            pointer-events: none;
            display: none;
        }

        @media only screen and (min-width: 320px) {
            .magnify-container .magnifier {
                height: 10rem;
                width: 10rem;
                background-size: 400%;
                margin-left: -5rem !important;
                margin-top: -5rem !important;
            }
        }

        @media only screen and (min-width: 768px) {
            .magnify-container .magnifier {
                height: 20rem;
                width: 20rem;
                background-size: 400%;
                margin-left: -10rem !important;
                margin-top: -10rem !important;
            }
        }

        @media only screen and (min-width: 992px) {
            .magnify-container .magnifier {
                height: 10rem;
                width: 10rem;
                background-size: 400%;
                margin-left: -5rem !important;
                margin-top: -5rem !important;
            }
        }

        @media only screen and (min-width: 1200px) {
            .magnify-container .magnifier {
                height: 20rem;
                width: 20rem;
                background-size: 400%;
                margin-left: -10rem !important;
                margin-top: -10rem !important;
            }
        }

        .magnify-container .magnified img {
            width: 100%;
            height: 100%;
            overflow: hidden;
            border-radius: 0.4rem;
        }
    </style>
@endpush
@php
    use Illuminate\Support\Facades\Crypt;
@endphp
@section('content')


    <section class="product-details pb-80 pt-80">
        <div class="Container">
            <ul class="route">
                <li><a href="{{ route('home') }}">{{ translate('Home') }} /</a></li>

                <li>
                    <a
                        href="{{ route('category.product', [$product->category->slug ? $product->category->slug : make_slug(get_translation($product->category->name)), $product->category_id]) }}">{{ get_translation($product->category->name) }}
                    </a>
                </li>
            </ul>

            @php
                $seller = $product->seller;
                $authUser = auth_user('web');
                $wishedProducts = $authUser ? $authUser->wishlist->pluck('product_id')->toArray() : [];
            @endphp

            <div class="product-details-container">
                <div class="product-detail-left">
                    <div class="small-img">
                        <div class="small-img-item">
                            @foreach ($product->gallery as $gallery)
                                <div class="gallery-sm-img product-gallery-small-img">
                                    <img src="{{ show_image(file_path()['product']['gallery']['path'] . '/' . $gallery->image, file_path()['product']['gallery']['size']) }}"
                                        alt="{{ $gallery->image }}">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="product-thumbnail-slider">
                        <div class="magnify-container">
                            <div class="magnifier">
                            </div>
                            <div class="magnified">
                                <img class="qv-lg-image"
                                    src="{{ show_image(file_path()['product']['gallery']['path'] . '/' . $product->gallery->first()->image, file_path()['product']['gallery']['size']) }}"
                                    alt="{{ @$product->gallery->first()->image }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-detail-middle">
                    <h3 class="details-product-title">
                        {{ $product->name }}
                        @if ($product->status == '0')
                            <span class="product_tag">
                                {{ translate('New Arrival') }}
                            </span>
                        @endif
                    </h3>

                    <div class="product-item-review">
                        <div class="ratting mb-0">
                            @php echo show_ratings($product->review->avg('rating')) @endphp
                            <small class="text-muted">({{ $product->review->count() }} {{ translate('Review') }})</small>
                        </div>
                        <small>{{ $product->order->count() }}
                            {{ translate('Orders') }}
                        </small>
                    </div>

                    <div class="product-price price-section">

                        @php
                            $price = @$product->stock->first()?->price ?? $product->price;

                        @endphp



                        @if (count($product->campaigns) != 0 &&
                                $product->campaigns->first()->end_time > Carbon\Carbon::now()->toDateTimeString() &&
                                $product->campaigns->first()->status == '1')
                            @if (short_amount($product->campaigns->first()->pivot->discount) == 0)
                                <span class="varient-product-price">{{ short_amount($price) }}
                                </span>
                            @else
                                <span>
                                    {{ short_amount(discount($price, $product->campaigns->first()->pivot->discount, $product->campaigns->first()->pivot->discount_type)) }}
                                </span>
                                <del>
                                    {{ short_amount($price) }}
                                </del>
                            @endif
                        @else
                            @if ($product->discount_percentage > 0)
                                <span>
                                    {{ short_amount(cal_discount($product->discount_percentage, $price)) }}
                                </span>
                                <del> {{ short_amount($price) }}</del>
                            @else
                                <span>
                                    {{ short_amount($price) }}
                                </span>
                            @endif
                        @endif





                    </div>

                    @if ($product->taxes)
                        <div class="pt-3">

                            <button class="badge bg-success mb-3" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseTax" aria-expanded="false" aria-controls="collapseTax">
                                {{ translate('View taxes') }}
                            </button>
                            <div class="collapse" id="collapseTax">

                                <ul class="list-group list-group-flush">

                                    @forelse ($product->taxes as $tax)
                                        <li
                                            class="list-group-item d-flex align-items-center justify-content-between bg-tax-light">

                                            {{ $tax->name }}
                                            : <span>

                                                @if ($tax->pivot->type == 0)
                                                    {{ $tax->pivot->amount }}%
                                                @else
                                                    {{ short_amount($tax->pivot->amount) }}
                                                @endif


                                            </span>
                                        </li>


                                    @empty

                                        <li>
                                            {{ translate('Nothing tax configuration added for this product') }}
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
                        $randNum = rand(5, 99999999);
                        $randNum = $randNum . 'details' . $randNum;
                    @endphp

                    <form class="attribute-options-form-{{ $randNum }} quick-view-form">

                        <input type="hidden" name="id" value="{{ $product->id }}">
                        @if (count($product->campaigns) != 0 &&
                                $product->campaigns->first()->end_time > Carbon\Carbon::now()->toDateTimeString() &&
                                $product->campaigns->first()->status == '1')
                            <input type="hidden" name="campaign_id" value="{{ $product->campaigns->first()->id }}">
                        @endif

                        @php
                            $stocks = $product->stock;
                        @endphp
                        @foreach (json_decode($product->attributes_value) as $key => $attr_val)
                            @php
                                $attributeOption = get_cached_attributes()->find($attr_val->attribute_id);
                                $attributValues = @$attributeOption->value;

                            @endphp
                            <div class="product-colors">
                                <span> {{ @$attributeOption->name }}:</span>
                                <div class="variant">
                                    @foreach ($attr_val->values as $key => $value)
                                        @php
                                            $displayName = $value;

                                            if ($attributValues) {
                                                $attributeValue = $attributValues->where('name', $value)->first();
                                                if ($attributeValue) {
                                                    $displayName = $attributeValue->display_name
                                                        ? $attributeValue->display_name
                                                        : $attributeValue->name;
                                                }
                                            }

                                        @endphp
                                        <div class="variant-item">
                                            <input @if ($key == 0) checked @endif type="radio"
                                                class="btn-check attribute-select"
                                                name="attribute_id[{{ $attr_val->attribute_id }}]"
                                                value="{{ str_replace(' ', '', $value) }}"
                                                id="success-outlined-{{ $value }}">
                                            <label class="btn-outline-success variant-btn"
                                                for="success-outlined-{{ $value }}"> {{ $displayName }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach


                        <div class="weight">
                            <div class="product-colors">
                                <span>
                                    {{ translate('Weight') }} : {{ $product->weight }} {{ translate('KG') }}
                                </span>
                            </div>
                        </div>

                        @if (count($product->campaigns) != 0 &&
                                $product->campaigns->first()->end_time > Carbon\Carbon::now()->toDateTimeString() &&
                                $product->campaigns->first()->status == '1')
                            <input type="hidden" name="campaign_id" value="{{ $product->campaigns->first()->id }}">
                        @endif

                        @php
                            $stockQty = (int) @$product->stock->first()->qty ?? 0;
                        @endphp

                        <div class="stock-status" id="quick-view-stock">
                            @if ($stockQty > 0)
                                <div class="instock">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <p>
                                        {{ translate('In Stock') }}
                                    </p>
                                </div>
                            @else
                                <div class="outstock">
                                    <i class="fas fa-times-circle"></i>
                                    <p>
                                        {{ translate('Stock out') }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="product-actions-type">
                            <div class="input-step">
                                <button type="button" class="update_qty x decrement">–</button>
                                <input type="number" class="product-quantity" name="quantity" value="1"
                                    id="quantity">
                                <button type="button" class="update_qty y increment">+</button>
                            </div>

                            <a href="javascript:void(0)" data-product_id = '{{ $randNum }}'
                                class="buy-now addtocartbtn">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                            <button data-product_id ="{{ $product->id }}" class="product-details-love-btn wishlistitem">
                                <i
                                    class="@if (in_array($product->id, $wishedProducts)) fa-solid
                            @else
                                fa-regular @endif fa-heart"></i>
                            </button>
                            <button class="product-details-love-btn comparelist wave-btn"
                                data-product_id="{{ $product->id }}"><i class="fa-solid fa-code-compare"></i></button>
                            @if ($product->seller_id && @$seller)
                                <a href="{{ route('user.seller.chat.list', ['seller_id' => $product->seller->id, 'product_id' => $product->id]) }}"
                                    class="buy-now"><i class="fa-brands fa-rocketchat"></i></a>
                            @endif
                            </a>
                        </div>
                    </form>

                    <div class="product-detail-btn">
                        <a href="javascript:void(0)" data-checkout = "yes" data-product_id = '{{ $randNum }}'
                            class="buy-now-btn quick-buy-btn addtocartbtn">
                            <i class="fa-solid fa-cart-plus fs-2 me-3"></i> {{ translate('Buy Now') }}
                        </a>

                        @if (site_settings('whatsapp_order', App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status())
                            @php
                                $wpMessage = site_settings('wp_order_message');

                                $message = str_replace(
                                    ['[product_name]', '[link]'],
                                    [$product->name, url()->current()],
                                    $wpMessage,
                                );

                            @endphp

                            <input type="hidden" class="wp-message" value="{{ $message }}">


                            @if ($seller && optional($seller->sellerShop)->whatsapp_order == App\Enums\StatusEnum::true->status())
                                <input type="hidden" class="wp-number"
                                    value="{{ optional($seller->sellerShop)->whatsapp_number }}">
                                <a href="javascript:void(0);" onclick="social_share()"
                                    class="buy-now-btn buy-with-whatsapp">
                                    <i class="fa-brands fa-whatsapp fs-2 me-3"></i> {{ translate('Order Via Whatsapp') }}
                                </a>
                            @endif

                            @if (!$seller)
                                <input type="hidden" class="wp-number" value="{{ site_settings('whats_app_number') }}">

                                <a href="javascript:void(0);" onclick="social_share()"
                                    class="buy-now-btn buy-with-whatsapp">
                                    <i class="fa-brands fa-whatsapp fs-2 me-3"></i> {{ translate('Order Via Whatsapp') }}
                                </a>
                            @endif
                        @endif

                    </div>
                    <div class="stock-and-social">
                        <div class="product-details-social">
                            <span>
                                {{ translate('Share') }}
                                :</span>
                            <div class="product-details-social-link">
                                <a href="https://www.facebook.com/sharer.php?u={{ urlencode(url()->current()) }}"
                                    target="__blank"><i class="fa-brands fa-facebook-f"></i></a>
                                <a href="https://twitter.com/share?url={{ urlencode(url()->current()) }}&text=Simple Share Buttons&hashtags=simplesharebuttons"
                                    target="__blank"><i class="fa-brands fa-twitter"></i></a>
                                <a href="http://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}"
                                    target="__blank"><i class="fa-brands fa-linkedin"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="Container">
            <div class="row g-4">
                <div class="col-xl-3 order-xl-1 order-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <h4 class="card-title mb-0">{{ translate('Related Product') }}</h4>
                                </div>
                                <div>
                                    <a href="{{ route('category.product', [$product->category->slug ? $product->category->slug : make_slug(get_translation($product->category->name)), $product->category->id]) }}"
                                        class="fs-14 view-all-btn">{{ translate('view all') }} <i
                                            class="fa-solid fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="related-product">
                                @forelse($products->take(5) as $rltd_product)
                                    <div class="product-categories-list">
                                        <div class="product-categories-list-img">
                                            <a
                                                href="{{ route('product.details', [$rltd_product->slug ? $rltd_product->slug : make_slug($rltd_product->name), $rltd_product->id]) }}">
                                                <img src="{{ show_image(file_path()['product']['featured']['path'] . '/' . $rltd_product->featured_image, file_path()['product']['featured']['size']) }}"
                                                    alt="{{ $rltd_product->name }}">
                                            </a>
                                        </div>

                                        <div class="product-info p-0">
                                            <h4 class="product-title sidebar-title"> <a
                                                    href="{{ route('product.details', [$rltd_product->slug ? $rltd_product->slug : make_slug($rltd_product->name), $rltd_product->id]) }}">
                                                    {{ $rltd_product->name }}
                                                </a>
                                            </h4>

                                            <div class="priceAndRatting">
                                                <div class="product-price">

                                                    @php
                                                        $price =
                                                            @$rltd_product->stock->first()?->price ??
                                                            $rltd_product->price;

                                                    @endphp

                                                    @if ($rltd_product->discount_percentage > 0)
                                                        <span>
                                                            {{ short_amount(cal_discount($rltd_product->discount_percentage, $price)) }}
                                                        </span>

                                                        <del>
                                                            {{ short_amount($price) }}</del>
                                                    @else
                                                        <span>
                                                            {{ short_amount($price) }}
                                                        </span>
                                                    @endif

                                                </div>
                                            </div>
                                            @php
                                                $rand = rand(1, 10000000);
                                                $rand = $rand . '_rltd_' . $rand;
                                            @endphp

                                            <form class="attribute-options-form-{{ $rand }}">
                                                <input type="hidden" name="id" value="{{ $rltd_product->id }}">
                                            </form>

                                            <div class="product-action">
                                                <a href="javascript:void(0)" data-product_id="{{ $rand }}"
                                                    class="buy-now wave-btn addtocartbtn">
                                                    <span class="buy-now-icon"></span>
                                                    {{ translate('Add to cart') }}
                                                </a>
                                                <button data-product_id ="{{ $rltd_product->id }}"
                                                    class="heart-btn wishlistitem"><i
                                                        class="@if (in_array($rltd_product->id, $wishedProducts)) fa-solid
                                            @else
                                                fa-regular @endif fa-heart"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                @empty

                                    <div class="text-enter fs-12">
                                        {{ translate('No products avaialable') }}
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-9 order-xl-2 order-1">
                    <div class="card pd-description-tab">
                        <div class="nav tab" id="nav-tab" role="tablist">
                            <button class="nav-link tablinks active" id="description-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-description" type="button" role="tab"
                                aria-controls="nav-description" aria-selected="true">
                                {{ translate('Description') }}
                            </button>
                            <button class="nav-link tablinks" id="reviews-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-reviews" type="button" role="tab" aria-controls="nav-reviews"
                                aria-selected="false">
                                {{ translate('Reviews') }}
                            </button>

                            @if ($product->warranty_policy)
                                <button class="nav-link tablinks" id="warranty-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-warranty" type="button" role="tab"
                                    aria-controls="nav-warranty" aria-selected="false">
                                    {{ translate('Warranty Policy') }}
                                </button>
                            @endif

                            <button class="nav-link tablinks" id="shipping-tab" data-bs-toggle="tab"
                                data-bs-target="#nav-shipping" type="button" role="tab"
                                aria-controls="nav-shipping" aria-selected="false">

                                {{ translate('Shipping Information') }}
                            </button>
                        </div>



                        <div class="tab-content pd-description" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-description" role="tabpanel"
                                aria-labelledby="description-tab">
                                <div class="description-content fs-14">
                                    @php echo $product->description @endphp
                                </div>
                            </div>

                            <div class="tab-pane fade" id="nav-reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <h4 class="fs-16">
                                    {{ translate('Ratings & Reviews') }}
                                </h4>

                                <div class="review-content">
                                    <div class="overall-ratting">
                                        <div class="review-overview sticky-side-div">
                                            <div>
                                                <div class="pb-3">
                                                    <div class="bg-light rounded-2 mb-2 px-4 py-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <div class="ratting mb-0">
                                                                    @php echo show_ratings($product->review->avg('rating')) @endphp
                                                                </div>
                                                            </div>

                                                            <div class="flex-shrink-0">
                                                                <p class="fs-14 mb-0">
                                                                    {{ round($product->review->avg('rating')) }}
                                                                    {{ translate('out of 5') }} </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 text-center">
                                                        <div class="text-muted fs-14">{{ translate('Total') }}
                                                            <span class="fw-medium">
                                                                {{ $product->review->count() }}
                                                            </span> {{ translate('Reviews') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-3">
                                                    @for ($i = 5; $i > 0; $i--)
                                                        <div class="row align-items-center g-2">
                                                            <div class="col-auto">
                                                                <div class="p-2">
                                                                    <h6 class="fs-14 mb-0">{{ $i }}
                                                                        {{ translate('star') }}</h6>
                                                                </div>
                                                            </div>

                                                            <div class="col">
                                                                <div class="p-2">
                                                                    <div class="progress progress-sm">
                                                                        <div class="progress-bar ratting-progres-{{ $i }}"
                                                                            role="progressbar" aria-valuenow="50.16"
                                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-auto">
                                                                <div class="p-2">
                                                                    <span class="text-muted fs-14 mb-0">
                                                                        {{ $product->review->where('rating', $i)->count() }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>

                                            @php

                                            @endphp

                                            @if (auth()->user() &&
                                                    product_add_review($product->id, auth()->user()->id) &&
                                                    !in_array(auth()->user()->id, @$product->review->pluck('user_id')->toArray() ?? []))
                                                <div class="mt-4 text-center">
                                                    <button type="button" class="AddReview-btn" data-bs-toggle="modal"
                                                        data-bs-target="#addReviewModal">
                                                        {{ translate('Add Your Review') }}
                                                    </button>
                                                </div>
                                            @endif


                                        </div>
                                    </div>
                                    @if ($product->review->isNotEmpty())
                                        <div class="position-relative">

                                            <div class="previous-reviews">

                                            </div>

                                            <div class="load-more-loader spinner-loader d-none">
                                                <div class="spinner-border text-dark" role="status">
                                                    <span class="visually-hidden"></span>
                                                </div>
                                            </div>

                                            <div
                                                class="d-flex align-items-center justify-content-center w-100 load-more-div d-none mb-2 mt-5">
                                                <button class="view-more-btn justify-content-center load-more-review">
                                                    {{ translate('Load More') }}
                                                </button>
                                            </div>


                                        </div>
                                    @else
                                        <div class="py-5 text-center">
                                            <p>{{ $product->review->count() }} {{ translate('Review for') }}
                                                {{ $product->name }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if ($product->warranty_policy)
                                <div class="tab-pane fade" id="nav-warranty" role="tabpanel"
                                    aria-labelledby="warranty-tab">
                                    <div class="description-content">
                                        {{ $product->warranty_policy }}
                                    </div>
                                </div>
                            @endif

                            <div class="tab-pane fade" id="nav-shipping" role="tabpanel" aria-labelledby="shipping-tab">
                                <div class="shipping-information">

                                    @if ($product->shippingDelivery)
                                        <div class="deliver-location">
                                            <label for="shipping-country" class="shipping-country form-label">
                                                {{ translate('Shipping Zone') }}
                                            </label>



                                            <select class="form-select" id="shipping-country">
                                                <option>
                                                    {{ translate('Select A Zone') }}
                                                </option>
                                                @foreach ($product->shippingDelivery as $country)
                                                    @if ($country->shippingDelivery)
                                                        <option value="{{ @$country->shippingDelivery->name }}">
                                                            {{ @$country->shippingDelivery->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>

                                        <div id="shipping-information">
                                            <div class="service-standard">

                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    @php
        $top_product_section = frontend_section('top-products');
    @endphp

    @includeWhen($top_product_section->status == '1', 'frontend.section.top_product', [
        'top_product_section' => $top_product_section,
    ])

    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-labelledby="addReviewModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ translate('Add Review') }}
                    </h5>
                    <button type="button" class="btn btn-light fs-14 modal-closer rounded-circle"
                        data-bs-dismiss="modal"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="add-review">
                        <form action="{{ route('user.product.review') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div class="rate">
                                <input type="radio" id="star5" name="rate" value="5">
                                <label for="star5" title="text"></label>
                                <input type="radio" id="star4" name="rate" value="4">
                                <label for="star4" title="text"></label>
                                <input type="radio" id="star3" name="rate" value="3">
                                <label for="star3" title="text"></label>
                                <input type="radio" id="star2" name="rate" value="2">
                                <label for="star2" title="text"></label>
                                <input type="radio" id="star1" name="rate" value="1">
                                <label for="star1" title="text"></label>
                            </div>

                            <textarea rows="5" name="review" placeholder="Your review" class="form-control my-4"></textarea>

                            <button class="add-review-btn">
                                {{ translate('Submit Review') }}
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scriptpush')
    <script>
        "use strict"


        $(document).on('change', '#shipping-country', function(e) {
            e.preventDefault()
            const countryName = $(this).val();
            if (countryName != 'Select A Country') {
                $.ajax({

                    url: "{{ route('product.shippingMethod') }}",
                    method: "get",
                    data: {
                        searchData: countryName
                    },
                    dataType: 'json',
                    success: function(response) {

                        $('.service-standard').html('')
                        $('.service-standard').append(`
                    <h4>
                        ${response.shippingMethod.name}

                    </h4>
                    <ul>
                        <li>
                            <small>{{ translate('Standard delivery') }}</small>
                            <span>${response.shippingMethod.duration} {{ translate('WORKING DAYS') }}</span>
                        </li>
                    </ul>
                    ${response.shippingMethod.description}
                `)
                    }
                });
            } else {
                $('.service-standard').html('')
            }

        })


        // Image magnifier

        $(".magnified").hover(function(e) {

            var imgPosition = $(".magnify-container").position(),
                imgHeight = $(".magnified").height(),
                imgWidth = $(".magnified").width();
            $(".magnifier").css({
                top: 0,
                left: 0
            }).show();

            $(this).mousemove(function(e) {
                var posX = e.pageX - imgPosition.left,
                    posY = e.pageY - imgPosition.top,
                    percX = (posX / imgWidth) * 100,
                    percY = (posY / imgHeight) * 100,
                    perc = percX + "% " + percY + "%";

                $(".magnifier").css({
                    top: posY,
                    left: posX,
                    backgroundPosition: perc
                });
            });
        }, function() {

            $(".magnifier").hide();
        });

        var page = 1;

        loadReviews(page)


        $(document).on('click', '.load-more-review', function(e) {
            page++;
            loadReviews(page);
            e.preventDefault()
        })

        function loadReviews(page) {

            $.ajax({
                url: "{{ route('get.product.review') }}",
                type: "get",
                data: {
                    'page': page,
                    'id': '{{ $product->id }}',

                },
                dataType: 'json',
                beforeSend: function() {
                    $('.load-more-loader').removeClass('d-none');
                },
                success: (function(response) {

                    $('.load-more-loader').addClass('d-none');
                    if (response.status) {
                        $('.previous-reviews').append(response.review_html)
                        if (response.next_page) {
                            $('.load-more-div').removeClass('d-none')
                        } else {
                            $('.load-more-div').addClass('d-none')
                        }
                    }


                }),

                error: (function(response) {
                    $('.load-more-loader').addClass('d-none');

                    $('.previous-reviews').html(`
                        <div class="text-center text-danger mt-10">
                            {{ translate('Something went wrong !! Please Try agian') }}
                        </div>
                    `)

                })
            })
        }
    </script>
@endpush
