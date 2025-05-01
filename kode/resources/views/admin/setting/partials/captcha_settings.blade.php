
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{$tab}}       <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Activation of Google reCAPTCHA entails CAPTCHA prompts for both login and registration processes to bolster security measures.')}}"></i>
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf

            <div class="row g-4 mb-4">
    
               
                <div class="col-lg-6">
                    <label for="recaptcha_public_key" class="form-label">
                        {{translate('Key')}} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="site_settings[recaptcha_public_key]" class="form-control" id="recaptcha_public_key"  value="{{site_settings('recaptcha_public_key')}}" >
                </div>
                
                <div class="col-lg-6">
                    <label for="recaptcha_secret_key" class="form-label">
                        {{translate('Secret key')}} <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="site_settings[recaptcha_secret_key]" class="form-control" id="recaptcha_secret_key" required  value="{{site_settings('recaptcha_secret_key')}}" >
                </div>
                
                <div class="col-lg-6">
                    <label for="recaptcha_status" class="form-label">
                        {{translate('Recaptcha With Customer Authentication')}}  <span class="text-danger">*</span>
                    </label>

                    <select name="site_settings[recaptcha_status]" id="recaptcha_status"  class="form-select">
                        <option value="">
                            {{translate('Select status')}}
                        </option>
                        <option {{site_settings('recaptcha_status',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                               {{translate("Active")}}
                       </option>
                        <option {{site_settings('recaptcha_status',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate("Inactive")}}
                       </option>
                   </select>
                    
                </div>


                <div class="col-xl-6">
                    
                    <div>
                        <label for="seller_captcha" class="form-label">
                            {{translate('Recaptcha With Seller Authentication')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[seller_captcha]" id="seller_captcha"  class="form-select">
                            <option {{site_settings('seller_captcha') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                            {{translate('Active')}}
                            </option>
                                <option {{site_settings('seller_captcha') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
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

