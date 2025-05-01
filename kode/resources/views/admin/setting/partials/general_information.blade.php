
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

                    <div class="col-12">
                        <div class="border d-flex align-items-center gap-3 py-1 px-2">
                            <label for="debug-mode" class="mb-0">
                                {{translate('Debug Mode')}}
                            </label>
                            <div class="form-check form-switch d-flex align-items-center justify-content-end">
                                <input id="debug-mode" data-value="true" {{env('APP_DEBUG') ? 'checked' :''}} class="form-check-input" type="checkbox">
                            </div>
                        </div>
                    </div>
        
                    <div class="col-xl-6">
                        <label for="site_name" class="form-label">
                            {{translate('Site Name')}} <span class="text-danger" >*</span>
                        </label>
                        <input type="text" class="form-control" name="site_settings[site_name]"   id="site_name" value="{{site_settings('site_name')}}" >
                    </div>
                    
                    <div class="col-xl-6">
                        <div>
                            <label for="copyright_text" class="form-label">
                                {{translate('Copyright Text')}} <span class="text-danger" >*</span>
                            </label>
                            <textarea class="form-control" name="site_settings[copyright_text]"  id="copyright_text" cols="30" rows="1">{{site_settings('copyright_text')}}</textarea>
                        </div>
                    </div>
                    
                    <div class="col-xl-6">
                        <label for="mail_from" class="form-label">
                            {{translate('Email Address')}} <span class="text-danger" >*</span>
                        </label>
                        <input type="email" class="form-control"  id="mail_from" value="{{site_settings('mail_from')}}" name="site_settings[mail_from]"  placeholder="example@gmail.com">
                    </div>
                    
                    <div class="col-xl-6">
                        <label for="phone" class="form-label">
                            {{translate('Phone')}} <span class="text-danger" >*</span>
                        </label>
                        <input type="text" class="form-control" name="site_settings[phone]"   id="phone" value="{{site_settings('phone')}}" placeholder="0XXXXXX">
                    </div>
                    
                    <div class="col-xl-12">
                        <div>
                            <label for="address" class="form-label">
                                {{translate('Address')}} <span class="text-danger" >*</span>
                            </label>
                            <textarea class="form-control" name="site_settings[address]" id="address" cols="30" rows="1">{{site_settings('address')}}</textarea>
                        </div>
                    </div>


                    <div class="col-xl-6">
                        <div>
                            <label for="country" class="form-label">
                                {{translate('Country')}} <span class="text-danger" >*</span>
                            </label>

                            <select class="form-select country" name="site_settings[country]" id="country">
                          
                                @foreach(App\Enums\Settings\GlobalConfig::COUNTRIES as $country)
                                    <option {{site_settings('country') ==  $country['code'] ? 'selected' : ''}} value="{{$country['code']}}"  >
                                        {{$country['name']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div>
                            <label for="timezone" class="form-label">
                                {{translate('Timezone')}} <span class="text-danger" >*</span>
                            </label>

                            <select  name="site_settings[time_zone]" id="timezone" class="time-zone form-select">
                                    @foreach($timeZones as $timeZone)
                                        <option value="'{{@$timeZone}}'" @if(config('app.timezone') == $timeZone) selected @endif>{{$timeZone}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>


                    <div class="col-xl-6">
                        <div>
                            <label for="latitude" class="form-label">
                                {{translate('Latitude')}} <span class="text-danger" >*</span>
                            </label>
                            <input id="latitude"   type="text" name="site_settings[latitude]" class="form-control" value="{{site_settings('latitude','-33.8688')}}">
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div>
                            <label for="longitude" class="form-label">
                                {{translate('Longitude')}} <span class="text-danger" >*</span>
                            </label>
                            <input  id="longitude" type="text" name="site_settings[longitude]" class="form-control" value="{{site_settings('longitude','151.2195')}}">
                        </div>
                    </div>


                    <div class="col-12">
                       

                          <input id="map-input" class="form-control mt-1 map-search-input"
                            type="text"
                            placeholder="{{translate('Search your loaction here')}}"/>
                  


                            <div class="rounded w-100  mb-5 h-400"
                                 id="gmap-site-address"></div>
                    
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

