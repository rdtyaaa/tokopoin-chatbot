@extends('admin.layouts.auth')
@section('main_content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-center flex-column">
            <div class="row w-100 justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-6">
                    <div class="card mt-4">
                        <div class="card-body px-4 py-5">
                            <h3 class="card-title text-center mb-4">
                                 {{translate('Register Here')}}
                            </h3>
                            <div class="text-center mt-2 mb-3">
                                <div class="w-50 mx-auto" >
                                    <a href="{{route('seller.dashboard')}}">
                                        <img src="{{show_image(file_path()['site_logo']['path'].'/'.site_settings('site_logo'),file_path()['site_logo']['size'])}}" class="w-100 h-100" alt="form-logo">  </a>
                                </div>
                            </div>
                            <div class="p-3">
                                <form action="{{route('seller.store')}}" id="registration-form" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            {{translate("Username")}} <span class="text-danger" >*</span>
                                        </label>
                                        <input type="text" name="username" value="{{old('username')}}" required   class="form-control" id="username" placeholder="Enter username">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            {{translate("Email")}} <span class="text-danger" >*</span>
                                        </label>
                                        <input type="text"  value="{{old('email')}}"  name="email" placeholder="Enter Email" class="form-control"
                                        id="email" />
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="password-input">
                                            {{translate("Password")}} <span class="text-danger" >*</span>
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input required  type="password" name="password" class="form-control pe-5 password-input" placeholder="Enter password" id="password-input">
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i id="toggle-password" class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="float-end mb-half">
                                            <a href="{{route('seller.login')}}" class="text-muted">
                                                    {{translate("Login")}} ?
                                            </a>
                                        </div>
                                        <label class="form-label" for="password_confirmation">
                                            {{translate("Confirm Password")}} <span class="text-danger" >*</span>
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input required  type="password" name="password_confirmation" class="form-control pe-5 password-input" placeholder="Enter Confirm Password" id="password_confirmation">
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i id="toggle-confirm-password" class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button 

                                        @if(site_settings('seller_captcha',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status())       
                                            class="g-recaptcha btn-lg btn btn-success w-100 rounded-10 rounded-5"
                                            data-sitekey="{{site_settings('recaptcha_public_key')}}"
                                            data-callback='onSubmit'
                                            data-action='register'
                                            @else
                                                class="btn-lg btn btn-success w-100 rounded-10 rounded-5"
                                            @endif 
                                            
                                            type="submit">
                                            {{translate("Sign In")}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer mt-3">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <p class="mb-0 text-muted">
                                    {{site_settings('copyright_text')}}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



@if(site_settings('seller_captcha') == App\Enums\StatusEnum::true->status())

    @push('script-push')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endpush

@endif




@push('script-push')

<script>
    'use strict'


    @if(site_settings('seller_captcha') == App\Enums\StatusEnum::true->status())
        function onSubmit(token) {
           document.getElementById("registration-form").submit();
        }
    @endif

    $(document).on('click','#toggle-password',function(e){
        var passwordInput = $("#password-input");
        var passwordFieldType = passwordInput.attr('type');
        if (passwordFieldType == 'password') {
        passwordInput.attr('type', 'text');
        $("#toggle-password").removeClass('ri-eye-fill').addClass('ri-eye-off-fill');
        } else {
        passwordInput.attr('type', 'password');
        $("#toggle-password").removeClass('ri-eye-off-fill').addClass('ri-eye-fill');
        }
   });
    $(document).on('click','#toggle-confirm-password',function(e){
        var passwordInput = $("#password-confirm-input");
        var passwordFieldType = passwordInput.attr('type');
        if (passwordFieldType == 'password') {
        passwordInput.attr('type', 'text');
        $("#toggle-confirm-password").removeClass('ri-eye-fill').addClass('ri-eye-off-fill');
        } else {
        passwordInput.attr('type', 'password');
        $("#toggle-confirm-password").removeClass('ri-eye-off-fill').addClass('ri-eye-fill');
        }
   });

</script>

@endpush
