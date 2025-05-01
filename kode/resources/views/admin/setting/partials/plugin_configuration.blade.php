
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{translate('Twak to')}}
            <a href="https://dashboard.tawk.to/" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Tawk To Configuration')}}" class="wp-config-btn text-danger ms-1">
                <i class="ri-settings-3-line"></i>
            </a>
        </h5>
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.plugin.update')}}">

            @csrf
            @php(
                $tawks = site_settings('tawk_to',null) ? json_decode(site_settings('tawk_to'),true) :
                [
                'property_id' => '@@',
                'widget_id'   => '@@',
                'status'      => '1',
                ])
            <div class="row g-4 mb-4">
                @foreach($tawks as $key => $tawk)
                    <div class="col-lg-6">
                        <label for="{{$key}}" class="form-label">
                            {{
                                ucwords(str_replace("_"," ",$key))
                            }} <span  class="text-danger"  >*</span>
                        </label>
                        @if($key == 'status')

                            <select class="form-select" name="tawk[{{$key}}]" id="{{$key}}">
                                    <option {{$tawk == '1' ? 'selected' :""}} value="1">
                                        {{translate('Active')}}
                                    </option>
                                    <option {{$tawk == '0' ? 'selected' :""}} value="0">
                                        {{translate('Inactive')}}
                                    </option>
                            </select>

                        @else
                            <input type="text" name="tawk[{{$key}}]" id="{{$key}}" class="form-control" value="{{$tawk}}" placeholder="{{translate('Enter Tawk').$key}}" required>
                        @endif

                    </div>
                @endforeach

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
<div class="border rounded mt-2">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{translate('WhatsApp Plugin')}}
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf
          
            <div class="row g-4 mb-4">
              
                 <div class="col-xl-12">
                    <div>
                        <label for="order-prefix" class="form-label">
                            {{translate('Welcome message')}} <span class="text-danger" >*</span>
                        </label>
                        <input id="order-prefix"  type="text" name="site_settings[whats_app_number_int_message]"  class="form-control" value="{{site_settings('whats_app_number_int_message')}}">
                    </div>
                </div>
                   


                <div class="col-xl-6">
                    <div>
                        <label for="whats_app_number" class="form-label">
                            {{translate('WhatsApp Number')}} <span class="text-danger" >*</span>
                        </label>
                        <input id="whats_app_number"  type="text" name="site_settings[whats_app_number]"  class="form-control" value="{{site_settings('whats_app_number')}}">
                    </div>
                </div>
                   
                <div class="col-lg-6">

                    <label for="whats_app_plugin" class="form-label">
                        {{translate('Status')}}
                    </label>

                    <select name="site_settings[whats_app_plugin]" id="whats_app_plugin"  class="form-select">
                        <option value="">
                            {{translate('Select status')}}
                        </option>
                        <option {{site_settings('whats_app_plugin') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                               {{translate("Active")}}
                       </option>
                        <option {{site_settings('whats_app_plugin') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
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
