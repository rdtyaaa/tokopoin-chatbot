
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
    
                <div class="col-xl-4">
                    <div>
                        <label for="digital_payment" class="form-label">
                            {{translate('Digital Payment')}} <span class="text-danger" >*</span>
                        </label>

                        <select class="form-select" name="site_settings[digital_payment]" id="digital_payment">
                            <option value="">
                                {{translate('Select status')}}
                             </option>
                            <option {{site_settings('digital_payment') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                {{translate("Active")}}
                            </option>
                            <option {{site_settings('digital_payment') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                    {{translate("Inactive")}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div>
                        <label for="offline_payment" class="form-label">
                            {{translate('Offiline Payment')}} <span class="text-danger" >*</span>
                        </label>

                        <select class="form-select" name="site_settings[offline_payment]" id="offline_payment">
                             <option value="">
                                {{translate('Select status')}}
                             </option>
                            <option {{site_settings('offline_payment') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                {{translate("Active")}}
                            </option>
                            <option {{site_settings('offline_payment') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                    {{translate("Inactive")}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div>
                        <label for="cash_on_delivery" class="form-label">
                            {{translate('Cash on delivery')}} <span class="text-danger" >*</span>
                        </label>
                    

                        <select class="form-select" name="site_settings[cash_on_delivery]" id="cash_on_delivery">
                            <option value="">
                                {{translate('Select status')}}
                             </option>
                            <option {{site_settings('cash_on_delivery',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                                {{translate("Active")}}
                            </option>
                            <option {{site_settings('cash_on_delivery',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                                    {{translate("Inactive")}}
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

