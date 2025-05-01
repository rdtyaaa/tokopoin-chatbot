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
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="card-title">
                            {{translate("Cart product list")}}
                        </h4>
                    </div>
                    @if(auth_user('web'))

                        <a class="view-more-btn" href="{{route('user.dashboard')}}">
                            {{translate('Dashboard')}}
                        </a>

                    @endif
                </div>
            </div>
            <div class="card-body cart-list position-relative">
                  <div class="cart-table">
                       @include('frontend.ajax.cart_list')
                  </div>

                 <div class="cart-item-loader loader-spinner d-none ">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
