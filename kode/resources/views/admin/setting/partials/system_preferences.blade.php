
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
                        <label for="frontend_preloader" class="form-label">
                            {{translate('Frontend preloader')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[preloader]" id="frontend_preloader"  class="form-select">
                            <option {{site_settings('preloader') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('preloader') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>


                <div class="col-xl-6">
                    <div>
                        <label for="maintenance_mode" class="form-label">
                            {{translate('Maintenance mode')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[maintenance_mode]" id="maintenance_mode"  class="form-select">
                             <option value="">
                                 {{translate("Select status")}}
                             </option>
                            <option {{site_settings('maintenance_mode' , App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('maintenance_mode', App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div>
                        <label for="guest_checkout" class="form-label">
                            {{translate('Guest checkout')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[guest_checkout]" id="guest_checkout"  class="form-select">
                            <option {{site_settings('guest_checkout') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('guest_checkout') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div>
                        <label for="strong_password" class="form-label">
                            {{translate('Strong password')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[strong_password]" id="strong_password"  class="form-select">
                            <option {{site_settings('strong_password') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('strong_password') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>



                <div class="col-xl-6">
                    <div>
                        <label for="club_point_system" class="form-label">
                            {{translate('Reward point configuration')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[club_point_system]" id="club_point_system"  class="form-select">
                            <option {{site_settings('club_point_system') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('club_point_system') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
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

