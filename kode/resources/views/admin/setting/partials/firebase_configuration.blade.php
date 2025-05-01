
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{$tab}}       
            <a href="https://console.firebase.google.com/" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Firebase API Configuration')}}" class="wp-config-btn text-danger ms-1" >
                <i class="ri-settings-3-line"></i>
            </a>
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf
            <div class="row g-4 mb-4">
                <div class="col-lg-12">
                    <label for="firebase_api_key" class="form-label">
                        {{translate('Firebase Json Configuration')}}
                    </label>

                    <textarea rows="8" type="text" name="site_settings[firebase_api_key]" class="form-control" id="firebase_api_key">{{!is_demo()? site_settings('firebase_api_key',"@@@@@") : "@@@@@"}}</textarea>
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
