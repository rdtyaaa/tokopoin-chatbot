
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
    
          
                <div class="col-xl-12">
                    <div>
                        <label for="wallet_system" class="form-label">
                            {{translate('Wallet system')}}
                            <span class="text-danger" >* </span>
                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If you enable this customer will able to recharge and make payment by their wallet')}}"></i>
                        </label>

                        <select name="site_settings[customer_wallet]" id="customer_wallet"  class="form-select">
                            <option {{site_settings('customer_wallet',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('customer_wallet',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>


                <div class="col-xl-6 col-lg-6">
                    <div>
                        <label for="min_limit" class="form-label">{{translate('Minimum Deposit Amount')}} <span  class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="1" class="form-control" id="min_limit" name="site_settings[customer_min_deposit_amount]" value="{{site_settings('customer_min_deposit_amount',0)}}" placeholder="{{translate('Enter amount')}}">
                            <span class="input-group-text" >{{default_currency()->name}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6">
                    <label for="max_limit" class="form-label">{{translate('Maximum Deposit Amount')}} <span  class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="1" class="form-control" id="max_limit" name="site_settings[customer_max_deposit_amount]" value="{{site_settings('customer_max_deposit_amount',0)}}" placeholder="{{translate('Enter amount')}}">
                        <span class="input-group-text" >{{default_currency()->name}}</span>
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

