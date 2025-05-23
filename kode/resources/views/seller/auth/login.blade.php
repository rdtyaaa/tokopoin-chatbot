@extends('admin.layouts.auth')
@section('main_content')
    <div class="container">
        <div class="d-flex align-items-center justify-content-center flex-column">
            <div class="row w-100 justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card mt-4">
                        <div class="card-body p-4">
                            <div class="mt-2 text-center">
                                <div class="w-50 mx-auto">
                                    <a href="{{ route('seller.dashboard') }}">
                                        <img src="{{ show_image(file_path()['site_logo']['path'] . '/' . site_settings('site_logo'), file_path()['site_logo']['size']) }}"
                                            class="w-100 h-100" alt="form-logo">
                                    </a>
                                </div>
                            </div>
                            <div class="p-3">
                                <form action="{{ route('seller.authenticate') }}" id="login-form" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            {{ translate('Username') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="username" required
                                            @if (is_demo()) value="demoseller" @endif class="form-control"
                                            id="username" placeholder="Enter username" data-test-id="LoginInput">
                                    </div>
                                    <div class="mb-3">
                                        <div class="mb-half float-end">
                                            <a href="{{ route('seller.reset.password.request') }}" class="text-muted">
                                                {{ translate('Forgot password') }} ?
                                            </a>
                                        </div>
                                        <label class="form-label" for="password-input">
                                            {{ translate('Password') }} <span class="text-danger">*</span>
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input required @if (is_demo()) value="123123" @endif
                                                type="password" name="password" class="form-control password-input pe-5"
                                                placeholder="Enter password" id="password-input"
                                                data-test-id="PasswordInput">
                                            <button
                                                class="btn btn-link position-absolute text-decoration-none text-muted password-addon end-0 top-0"
                                                type="button" id="password-addon"><i id="toggle-password"
                                                    class="ri-eye-fill align-middle"></i></button>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button
                                            @if (site_settings('seller_captcha', App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status()) class="g-recaptcha btn btn-success w-100 rounded-10"
                                                data-sitekey="{{ site_settings('recaptcha_public_key') }}"
                                                data-callback='onSubmit'
                                                data-action='register'
                                            @else
                                                class="btn btn-success w-100 rounded-10" @endif
                                            type="submit">
                                            {{ translate('Sign In') }}
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
                                <p class="text-muted mb-0">
                                    {{ site_settings('copyright_text') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@if (site_settings('seller_captcha') == App\Enums\StatusEnum::true->status())
    @push('script-push')
        <script src="https://www.google.com/recaptcha/api.js"></script>
    @endpush
@endif



@push('script-push')
    <script>
        'use strict'

        @if (site_settings('seller_captcha') == App\Enums\StatusEnum::true->status())
            function onSubmit(token) {
                document.getElementById("login-form").submit();
            }
        @endif

        $(document).on('click', '#toggle-password', function(e) {
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
