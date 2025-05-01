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
        <div class="title-with-tab section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>
                        {{translate($title)}}
                    </h3>
                </div>
            </div>
            <div class="section-title-right">
                <div class="section-title-tabs">
                    <a href="{{route('all.brand')}}" class="title-tab-btn {{request()->routeIs('all.brand') ?'active-title-tab' : '' }} ">
                        {{translate("All")}}
                    </a>
                    <a href="{{route('top.brand')}}" class="title-tab-btn  {{request()->routeIs('top.brand') ?'active-title-tab' : '' }}">
                       {{translate("Top Brands")}}
                    </a>

                </div>
            </div>
        </div>

        <div class="all-brands-container">
            <div class="row g-2 g-sm-4">
                @forelse($brands as $brand)
                    <div class="col-xxl-2 col-xl-3 col-md-4 col-sm-6">
                        <a href="{{route('brand.product', [ $brand->slug ? $brand->slug : make_slug(get_translation($brand->name)), $brand->id])}}" class="brand-item">
                            <div class="top-brand-logo">
                                    <img src="{{show_image(file_path()['brand']['path'].'/'.$brand->logo,file_path()['brand']['size'])}}" alt="{{$brand->logo}}">
                            </div>
                            <div class="top-brand-info">
                                <h5 class="text-break">{{(get_translation($brand->name))}}</h5>
                                    <p class="fs-14 text-mute">
                                        <small>({{$brand->houseProduct->count()}})</small>
                                        {{translate('Products')}}
                                    </p>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        @include("frontend.partials.empty",['message' => 'No Data Found'])
                    </div>
                @endforelse
            </div>

            <div class="mt-4 mx-4 d-flex align-items-center justify-content-end">
                {{$brands->withQueryString()->links()}}
            </div>
        </div>
    </div>
</section>

@endsection
