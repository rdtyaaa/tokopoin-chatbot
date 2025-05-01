
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
                        <label for="multi_vendor" class="form-label">
                            {{translate('Multi Vendor')}}
                            <span class="text-danger" >* </span>
                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If you disable the multi-vendor option, the seller panel will also be disabled.')}}"></i>
                        </label>

                        <select name="site_settings[multi_vendor]" id="multi_vendor"  class="form-select">
                            <option {{site_settings('multi_vendor',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('multi_vendor',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>


                <div class="col-xl-6">
                    <div>
                        <label for="seller_commission_status" class="form-label">
                            {{translate('Seller Commission Activatation')}}
                            <span class="text-danger" >* </span>
                        
                        </label>

                        <select name="site_settings[seller_commission_status]" id="seller_commission_status"  class="form-select">
                            <option {{site_settings('seller_commission_status') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('seller_commission_status') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
                    </div>
                </div>


                <div class="col-xl-6">
                    <div>
                        <label for="seller_commission" class="form-label">
                            {{translate('Default Commission')}} %
                            <span class="text-danger" >* </span>
                        
                        </label>

                        <input type="number" class="form-control" id="seller_commission" name="site_settings[seller_commission]" value="{{site_settings('seller_commission')}}">

                       
                    </div>
                </div>



                <div class="col-xl-6">

                    <div>
                        <label for="seller_product_status_update_permission" class="form-label">
                            {{translate('Product Status Update permission')}}
                            <span class="text-danger" >* </span>
                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If you enable this , seller will able to update a published product status')}}"></i>

                        </label>

                        <select name="site_settings[seller_product_status_update_permission]" id="seller_product_status_update_permission"  class="form-select">
                            <option {{site_settings('seller_product_status_update_permission') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('seller_product_status_update_permission') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
            
                    </div>

                </div>



                <div class="col-xl-4">

                    <div>
                        <label for="seller_kyc_verification" class="form-label">
                            {{translate('KYC Verification')}}
                            <span class="text-danger" >* </span>
                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If you enable this ,Seller KYC Verification module wil be activated')}}"></i>

                        </label>

                        <select name="site_settings[seller_kyc_verification]" id="seller_kyc_verification"  class="form-select">
                            <option {{site_settings('seller_kyc_verification') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('seller_kyc_verification') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
            
                    </div>

                </div>


                <div class="col-xl-4">

                    <div>
                        <label for="seller_registration" class="form-label">
                            {{translate('Seller Registration')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[seller_registration]" id="seller_registration"  class="form-select">
                            <option {{site_settings('seller_registration') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('seller_registration') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
            
                    </div>

                </div>



                <div class="col-xl-4">

                    <div>
                        <label for="seller_order_delivery_permission" class="form-label">
                            {{translate('Order Delivery Permission')}}
                            <span class="text-danger" >* </span>
                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If you enable this ,Vendor will able to delivered order ')}}"></i>
                        </label>

                        <select name="site_settings[seller_order_delivery_permission]" id="seller_order_delivery_permission"  class="form-select">
                            <option {{site_settings('seller_order_delivery_permission') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('seller_order_delivery_permission') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>
            
                    </div>

                </div>


                <div class="col-xl-6 col-lg-6">
                    <div>
                        <label for="min_limit" class="form-label">{{translate('Minimum Deposit Amount')}} <span  class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="1" class="form-control" id="min_limit" name="site_settings[seller_min_deposit_amount]" value="{{site_settings('seller_min_deposit_amount',0)}}" placeholder="{{translate('Enter amount')}}">
                            <span class="input-group-text" >{{default_currency()->name}}</span>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-lg-6">
                    <label for="max_limit" class="form-label">{{translate('Maximum Deposit Amount')}} <span  class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="1" class="form-control" id="max_limit" name="site_settings[seller_max_deposit_amount]" value="{{site_settings('seller_max_deposit_amount',0)}}" placeholder="{{translate('Enter amount')}}">
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

