@extends('admin.layouts.auth')
@section('main_content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-center flex-column">
            <div class="row w-100 justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="text-center mt-2">
                                <div class="w-50 mx-auto" >
                                    <a href="{{route('admin.dashboard')}}">
                                        <img src="{{ show_image('assets/images/backend/logoIcon/'.site_settings('site_logo'), file_path()['site_logo']['size']) }}" class="w-100 h-100" alt="{{site_settings('site_logo')}}">
                                    </a>
                                </div>

                                <p class="text-muted mt-2">
                                    {{translate("Sign in to continue to")}} {{site_settings('site_name')}}
                                </p>
                            </div>
                            <div class="p-2 ">
                                <form action="{{route('admin.authenticate')}}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            {{translate("Username")}} <span class="text-danger" >*</span>
                                        </label>
                                        <input type="text" name="user_name" @if(is_demo()) value="admin" @endif  required   class="form-control" id="username" placeholder="{{translate('Enter username')}}">
                                    </div>
                                    <div class="mb-3">
                                        <div class="float-end mb-half">
                                            <a href="{{route('admin.password.request')}}" class="text-muted">
                                                    {{translate("Forgot password")}}?
                                            </a>
                                        </div>
                                        <label class="form-label" for="password-input">
                                            {{translate("Password")}} <span class="text-danger" >*</span>
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input required @if(is_demo()) value="123123" @endif  type="password"  name="password" class="form-control pe-5 password-input" placeholder="{{translate('Enter Password')}}" id="password-input">
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i id="toggle-password" class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="remember_me" id="auth-remember-check">
                                        <label class="form-check-label" for="auth-remember-check">
                                            {{translate("Remember me")}}
                                        </label>
                                    </div>
                                    <div class="mt-4">
                                        <button class="btn btn-success w-100 rounded-10" type="submit">
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
@push('script-push')
<script>
    'use strict'
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
</script>

@endpush
