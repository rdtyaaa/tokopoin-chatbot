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
                    <a href="{{route('all.category')}}" class="title-tab-btn {{request()->routeIs('all.category') ?'active-title-tab' : '' }} ">
                        {{translate("All")}}
                    </a>
                    <a href="{{route('top.category')}}" class="title-tab-btn  {{request()->routeIs('top.category') ?'active-title-tab' : '' }}">
                       {{translate("Top Category")}}
                    </a>

                </div>
            </div>
        </div>

        <div class="all-categories">
            <div class="row g-2 g-md-4">
                @forelse($listings as $category)
                    <div class="col-xl-2 col-lg-3 col-sm-4 col-6">
                        <a href="{{route('category.product', [$category->slug ? $category->slug :  make_slug(get_translation($category->name)), $category->id])}}" class="categorie-item">
                            <div class="categorie-item-img">
                                <img src="{{show_image(file_path()['category']['path'].'/'.$category->banner,file_path()['category']['size'])}}" alt="{{$category->banner}}">
                            </div>
                            <div class="categorie-item-content">
                                <h4>
                                    {{ get_translation($category->name) }}
                                </h4>
                                <p>{{$category->houseProduct->count()}}
                                    {{translate("Items Available")}}
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

            <div class="mt-5 mx-4 d-flex align-items-center justify-content-end">
                    {{$listings->withQueryString()->links()}}
            </div>
        </div>
    </div>
</section>

@endsection

