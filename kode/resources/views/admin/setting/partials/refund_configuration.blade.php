
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
                        <label for="refund_status" class="form-label">
                            {{translate('Refund')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[refund_status]" id="refund_status"  class="form-select">
                            <option {{site_settings('refund_status') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('refund_status') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>

                    </div>
                </div>

                <div class="col-xl-6" >
                    <div>
                        <label for="refund_validity" class="form-label">
                            {{translate('Refund Order Validity')}} ({{translate('Days')}})
                            <span class="text-danger" >* </span>
                        </label>

                        <input id="refund_validity" class="form-control" type="number" name="site_settings[refund_validity]" value="{{site_settings('refund_validity')}}">
                        
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

