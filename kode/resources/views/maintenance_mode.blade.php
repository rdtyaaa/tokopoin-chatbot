
@extends('frontend.layouts.error')
@push('stylepush')

<style>
   

    body {
      color: #000;
      font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Helvetica,
        Arial, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;
      overflow-x: hidden !important;
    }
    .maintenance {
      min-height: 100vh;
      height: 100%;
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow-x: hidden;
    }

    .maintenance-wrapper {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      align-items: center;
      width: 80%;
      margin: 90px 0;
    }

    .logo {
      width: 200px;
      margin-bottom: 25px;
      display: inline-block;
    }
    .logo img {
      width: 100%;
      height: auto;
    }

    .error-msg-container > h1 {
      font-size: 56px;
      margin-bottom: 40px;
    }
    .error-msg-container > p {
      font-size: 18px;
      margin-bottom: 20px;
    }
    .error-msg-container a {
      color: #654ce6;
      text-decoration: none;
    }
    .main-img {
      margin: 0 auto;
    }
    .main-img img {
      width: 100%;
      height: 100%;
    }
    @media screen and (max-width: 1199.98px) {
      .maintenance-wrapper {
        width: 90%;
      }
    }

    @media screen and (max-width: 991.98px) {
      .maintenance-wrapper {
        grid-template-columns: repeat(1, 1fr);
        gap: 50px;
      }
    }

    @media (max-width: 767.98px) and (min-width: 600px) {
      .error-msg-container > h1 {
        font-size: 42px;
      }
    }

    @media screen and (max-width: 599.98px) {
      .error-msg-container > h1 {
        font-size: 32px;
      }
    }
  </style>


@endpush
@section('content')

    <div class="maintenance px-3 px-lg-0">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="error-msg-container">
                        <a href="{{route('home')}}" class="logo">
                            <img src="{{ show_image('assets/images/backend/logoIcon/'.site_settings('site_logo'), file_path()['site_logo']['size']) }}" alt="site_logo.png">
                        </a>
                        
                        <h1>
                            {{translate('Site Under Maintenance')}}
                        </h1>
                        <p>
                            {{translate("We're sorry for the inconvenience, but we're performing some essential maintenance on our website. Please check back soon. In the meantime, feel free to contact us at")}}
                            <a href="mailto:{{site_settings('mail_from')}}">{{site_settings('mail_from')}}</a>
                            {{translate(" for assistance. Thank you for your patience!")}}
                        </p>
                
                        </div>
                </div>
                <div class="col-lg-5">
                    <div class="main-img">
                        <img src="{{asset('assets/images/maintenance.jpg')}}" alt="maintenance.jpg"/>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
