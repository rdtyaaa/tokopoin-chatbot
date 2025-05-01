
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{translate('Facebook Pixel Setting')}}
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf

            <div class="row g-4 mb-4">
    
               
                <div class="col-lg-6">
                    <label for="facebook_pixel_id" class="form-label">
                        {{translate('Facebook pixel ID')}}
                    </label>
                    <input type="text" name="site_settings[facebook_pixel_id]" class="form-control" id="facebook_pixel_id"  value="{{site_settings('facebook_pixel_id')}}" >
                </div>
                
                <div class="col-lg-6">
                    <label for="facebook_pixel" class="form-label">
                        {{translate('Facebook Pixel')}}
                    </label>

                    <select name="site_settings[facebook_pixel]" id="facebook_pixel"  class="form-select">
                        <option value="">
                            {{translate('Select status')}}
                        </option>
                        <option {{site_settings('facebook_pixel') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                               {{translate("Active")}}
                       </option>
                        <option {{site_settings('facebook_pixel') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate("Inactive")}}
                       </option>
                   </select>

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


<div class="border rounded mt-4">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{translate('Google Analytics Setting')}}
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf

            <div class="row g-4 mb-4">
    
               
                <div class="col-lg-6">
                    <label for="google_tracking_id" class="form-label">
                        {{translate('Tracking ID')}}
                    </label>
                    <input type="text" name="site_settings[google_tracking_id]" class="form-control" id="google_tracking_id"  value="{{site_settings('google_tracking_id')}}" >
                </div>
                

                <div class="col-lg-6">

                    <label for="google_analytics" class="form-label">
                        {{translate('Google Analytics')}}
                    </label>

                    <select name="site_settings[google_analytics]" id="google_analytics"  class="form-select">
                        <option value="">
                            {{translate('Select status')}}
                        </option>
                        <option {{site_settings('google_analytics') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                               {{translate("Active")}}
                       </option>
                        <option {{site_settings('google_analytics') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate("Inactive")}}
                       </option>
                   </select>

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
