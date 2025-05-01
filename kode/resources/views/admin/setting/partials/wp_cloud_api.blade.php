<form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

    @csrf

    <div class="row g-4 mb-4">

        <div class="col-xl-6">
            <div>
                <label for="access_token" class="form-label">
                    {{translate('Access Token')}}
                    <span class="text-danger" >* </span>
                
                </label>

                <input type="text" placeholder="{{translate('Cloud API Access Token')}}" class="form-control" id="access_token" name="site_settings[wp_access_token]" value="{{site_settings('wp_access_token')}}">
            </div>
        </div>
        
        <div class="col-xl-6">
            <div>
                <label for="business_account_id" class="form-label">
                    {{translate('Business Account ID')}}
                    <span class="text-danger" >* </span>
                
                </label>

                <input type="text" placeholder="{{translate('Business Account ID')}}" class="form-control" id="business_account_id" name="site_settings[wp_business_account_id]" value="{{site_settings('wp_business_account_id')}}">
            </div>
        </div>

        <div class="col-xl-6">
            <div>
                <label for="wp_business_phone" class="form-label">
                    {{translate('Phone number ID')}}
                    <span class="text-danger" >* </span>

                </label>

                <input type="text" placeholder="{{translate('Business Phone Number')}}" class="form-control" id="wp_business_phone" name="site_settings[wp_business_phone]" value="{{site_settings('wp_business_phone')}}">
            </div>
        </div>

        <div class="col-xl-6">
            <div>
                <label for="wp_receiver_id" class="form-label">
                    {{translate('Receiver Number')}}
                    <span class="text-danger" >* </span>

                    <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('The number that you want to receive order notification message (enter number with your country code)')}}"></i>

                </label>

                <input type="text" placeholder="{{translate('880XXXXXXX')}}" class="form-control" id="wp_receiver_id" name="site_settings[wp_receiver_id]" value="{{site_settings('wp_receiver_id')}}">
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