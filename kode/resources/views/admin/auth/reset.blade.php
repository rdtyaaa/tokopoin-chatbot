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
                                    {{translate("Update Password")}}||{{site_settings('site_name')}}
                                </p>
                            </div>
                            <div class="p-2 ">
                                <form action="{{ route('admin.password.reset.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{$passwordToken}}">
                                    <div class="mb-3">
                                        <div class="mb-half">

                                        <label class="form-label" for="password-input">
                                            {{translate("Password")}} <span class="text-danger" >*</span>
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input required  type="password" value="" name="password" class="form-control pe-5 password-input" placeholder="{{translate('Enter password')}}" id="password-input">

                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="float-end mb-half">
                                            <a href="{{route('admin.login')}}" class="text-muted">
                                                    {{translate("Login")}}?
                                            </a>
                                        </div>
                                        <label class="form-label" for="confirm-password-input">
                                            {{translate("Confirm Password")}} <span class="text-danger" >*</span>
                                        </label>
                                        <div class="position-relative auth-pass-inputgroup mb-3">
                                            <input required  type="password" value="" name="password_confirmation" class="form-control pe-5 password-input" placeholder="{{translate('Confirm password')}}" id="confirm-password-input" >
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button class="btn btn-success w-100 rounded-10" type="submit">
                                            {{translate("Update")}}
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
