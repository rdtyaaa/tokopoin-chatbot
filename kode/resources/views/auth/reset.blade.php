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
    <div class="Container ">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-6 col-md-8 ">
                <div class="form-wrapper">
                    <form action="{{ route('password.update') }}" method="POST" >
                        @csrf
                        <input type="hidden" name="token" value="{{$passwordToken}}">

                        <div class="form-group mb-5">
                            <label for="password" class="form-label">
                                {{translate('Password')}}
                                <span class="text-danger" >*</span>
                            </label>
                            <input class="form-control" id="password" type="password" name="password" placeholder="{{translate('Enter Password')}}" required>

                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label mb-3">
                                {{translate('Confirm Password')}}
                                 <span class="text-danger" >*</span>
                            </label>
                            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" placeholder="{{translate('Enter Confirm Password')}}"  required>

                        </div>

                        <button type="submit" class="form-submit-btn mt-5">{{translate('Update')}}</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
