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
                        <li class="breadcrumb-item">
                            <a href="{{url('/')}}">
                                {{translate('home')}}
                            </a>
                       </li>

                        <li class="breadcrumb-item active" aria-current="page">
                            {{translate($title)}}
                        </li>

                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section  class="pb-80">
    <div class="Container">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-6 col-md-8 ">
                <div class="form-wrapper">
                    <form action="{{ route('email.password.verify.code') }}" method="POST" >
                        @csrf

                        <div class="form-group">
                            <label for="code" class="form-label">
                                {{translate('Code')}} <span class="text-danger" >*</span>
                            </label>

                            <input id="code" type="text" class="form-control" name="code" placeholder="{{translate('Enter Verify Code')}}">

                        </div>

                        <button type="submit" class="form-submit-btn mt-5 ">{{translate('Submit')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
