
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{$tab}}
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf

            <div class="row g-4 mb-4">
    
          
                <div class="col-xl-6">
                    <div>
                        <label for="email_otp_login" class="form-label">
                            {{translate('Email OTP Login')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[email_otp_login]" id="email_otp_login"  class="form-select">
                            <option {{site_settings('email_otp_login', App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('email_otp_login', App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div>
                        <label for="phone_otp_login" class="form-label">
                            {{translate('Phone OTP login')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[phone_otp_login]" id="phone_otp_login"  class="form-select">
                            <option {{site_settings('phone_otp_login' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('phone_otp_login', App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>


                <div class="col-xl-12">
                    <div>
                        <label for="login_with_password" class="form-label">
                            {{translate('Login with password')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[login_with_password]" id="login_with_password"  class="form-select">
                            <option value="">
                                {{translate("Select status")}}
                            </option>
                            <option {{site_settings('login_with_password',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('login_with_password',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>

            </div>

            <div class="text-start">
                <button type="submit"
                    class="btn btn-success waves ripple-light"
                    id="add-btn">
                    {{translate('Submit')}}
                </button>
            </div>

        </form>
    </div>

</div>

