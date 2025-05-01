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

<section class="pb-80">
	<div class="Container">
		<div class="row g-4 flex-items-lg-start categories-container">
            <div class="col-xl-3 col-lg-4">
                <div class="card sticky-side-div">
                    <div class="card-header px-25 py-15">
                        <h5 class="card-title">
                            {{translate('Filter By Category')}}
                        </h5>
                    </div>

                    <div class="accordion accordion-flush filter-accordion">
                        <div class="px-25 py-15">
                            <div>
                                <p class="text-uppercase fs-13 fw-semibold filter-by">
                                    {{translate('Category')}}
                                </p>
                                @php
                                   $digitalCategories =  App\Models\Category::whereHas('digitalProduct')->where('status', '1')
                                                                    ->withCount(['parent','product','houseProduct','digitalProduct','physicalProduct'])
                                                                    ->whereNull('parent_id')
                                                                    ->orderBy('serial', 'ASC')
                                                                    ->with(['physicalProduct'])->get();

                                @endphp
                                <ul class="list-unstyled mb-0 mt-3 filter-list">
                                    @forelse($digitalCategories as $category)
                                        <li>
                                            <a href="{{route('category.product', [$category->slug ? $category->slug : make_slug(get_translation($category->name)), $category->id,'digital'])}}" class="d-flex cate-menu-active align-items-center position-relative">
                                                <div class="flex-grow-1">
                                                    <h5 class=" mb-0 listname    @if(request()->routeIs('category.product'))
                                                        {{request()->route('id') == $category->id ? 'cate-menu-active' :'' }}
                                                      @endif">
                                                        {{get_translation($category->name)}}
                                                    </h5>
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
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8">
                <div class="category-product">
                    <div class="product-filter-right">
                        <div >
                            <h5 class="mb-2 card-title">
                                {{translate("Digital Products")}}
                            </h5>
                            <p class="fs-12">
                                {{translate("Total")}} {{$digital_products->count()}} {{translate("products found")}}
                            </p>
                        </div>
                    </div>

                    <div class="row g-2 g-md-4">
                        @forelse ($digital_products as $product)
                            <div class="col-xl-3 col-md-4 col-6">
                                <div class="digital-product">
                                    <a href="{{route('digital.product.details', [$product->slug ? $product->slug : make_slug($product->name), $product->id])}}" class="digital-product-img">
                                        <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['digital_product']['featured']['size'])}}" alt="{{$product->featured_image}}">
                                    </a>
                                    <div class="digital-product-info">
                                        <h4 class="product-title text-muted">
                                            {{$product->name}}
                                        </h4>
                                        <div class="product-price py-3">
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
	</div>
</section>
@endsection
