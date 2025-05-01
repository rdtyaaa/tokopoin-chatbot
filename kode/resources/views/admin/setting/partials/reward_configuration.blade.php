
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{$tab}}
        </h5>
    </div>

    @php
         $rewardPointConfigurations = !is_array(site_settings('order_amount_based_reward_point',[])) 
                                        ?  json_decode(site_settings('order_amount_based_reward_point',[]),true) 
                                        : [];



         $rewardPointConfigurations  = collect($rewardPointConfigurations);
         


    @endphp

    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf

            <div class="row g-4 mb-4">

                <div class="col-xl-4">
                    <div>
                        <label for="club_point_system" class="form-label">
                            {{translate('Reward point system')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[club_point_system]" id="club_point_system"  class="form-select">
                            <option {{site_settings('club_point_system' ,0) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('club_point_system',0) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Inactive')}}
                           </option>
                       </select>

                    </div>
                </div>

                <div class="col-xl-4">
                    <div>
                        <label for="reward_point_by" class="form-label">
                            {{translate('Reward point by')}}
                            <span class="text-danger" >* </span>

                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('If "Order Amount Based" is enabled, customers will earn reward points based on their order total. If "Product Based" is enabled, customers will earn reward points as specified for each product.')}}"></i>
                        </label>

                        <select name="site_settings[reward_point_by]" id="reward_point_by"  class="form-select">
                            <option {{site_settings('reward_point_by') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Order amount based')}}
                           </option>
                            <option {{site_settings('reward_point_by') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                               {{translate('Product based')}}
                           </option>
                       </select>

                    </div>
                </div>
    
          
                <div class="col-4">
                    <div>
                        <label for="customer_wallet_point_conversion_rate" class="form-label">{{translate('Point to wallet')}}
                            <span  class="text-danger">*</span>
                        </label>
                        <div class="input-group mb-3">
                            <span class="input-group-text">1  {{default_currency()->name}} = </span>
                            <input type="number"  step="1" min="0"  name="site_settings[customer_wallet_point_conversion_rate]" id="customer_wallet_point_conversion_rate" value="{{site_settings('customer_wallet_point_conversion_rate',0)}}" class="form-control" aria-label="Amount (to the nearest dollar)">
                            <span class="input-group-text limittext">
                                 {{translate('Point')}}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-6">
                    <div>
                        <label for="default_reward_point" class="form-label">{{translate('Default reward point amount')}} <span  class="text-danger">*</span>
                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Set default reward point that customer will get for based on order amount')}}"></i>
                        </label>


                        <input type="number" step="1" min="0" class="form-control" id="default_reward_point" name="site_settings[default_reward_point]" value="{{site_settings('default_reward_point',0)}}" placeholder="{{translate('Enter amount')}}">
                    </div>
                </div>



                <div class="col-6">
                    <div>
                        <label for="reward_point_expired_after" class="form-label">
                            {{translate('Reward point expired after')}} <span  class="text-danger">*</span>  
                        </label>

                        <div class="input-group mb-3">
                            <input type="number"  step="1" min="0"  name="site_settings[reward_point_expired_after]" id="reward_point_expired_after" value="{{site_settings('reward_point_expired_after',0)}}" 
                            class="form-control" >
                            <span class="input-group-text limittext">
                                 {{translate('Days')}}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 shipping-type">

                    <div class="card bg-light">

                        <div class="card-header border-bottom-dashed bg-light">

                            <div class="row g-4 align-items-center">
                                <div class="col-sm">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            {{translate('Order amount based reward point')}}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="tab-content text-muted">
                                <div  id="product-wise-shipping">

                                     <div class="text-start">
                                        <a href="javascript:void(0)" class="btn btn-sm text-end btn-success add-price-btn  waves ripple-light"><i
                                            class="ri-add-line align-bottom me-1"></i>
                                          {{translate('Add New')}}
                                       </a>
                                     </div>

                                    <div class="table--wrapper">

                                        <table class="mt-4">
                                            <thead>
                                                <th></th>
                                            </thead>
                                            <tbody  class="add-price-row">


                                                @if($rewardPointConfigurations->count() > 0)

                                                    @foreach ($rewardPointConfigurations as $rewardPointConfiguration )

                                                        <tr>
                                                            <td>
                                                                <p class="mb-0"> {{translate('Applicable if order amount is greter than')}}</p>
                                                                <div class="input-group ">
                                                                    <span class="input-group-text">
                                                                        {{default_currency()->symbol}}
                                                                    </span>
                                                                    <input placeholder="{{translate('Enter order amount')}}" type="number" name="order_wise[greater_than][]" value="{{ Arr::get($rewardPointConfiguration ,'min_amount',0 )}}" class="form-control">
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <p class="mb-0"> {{translate('Applicable if price is less than or equal')}}</p>
                                                                <div class="input-group ">
                                                                    <span class="input-group-text">{{default_currency()->symbol}}</span>
                                                                    <input placeholder="{{translate('Enter order amount')}}" type="number" name="order_wise[less_than_eq][]" value="{{ Arr::get($rewardPointConfiguration ,'less_than_eq',0 )}}" class="form-control">
                                                                </div>
                                                            </td>

                                                            <td>
                                                                <p class="mb-0">
                                                                    {{translate('Point')}}
                                                                </p>

                                                                <input step="1" min="0" placeholder="{{translate('Enter Point')}}"  name="order_wise[point][]" value="{{ Arr::get($rewardPointConfiguration ,'point',0 )}}"  type="number" class="form-control">
                                                            </td>

                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-danger delete-row-btn">Delete</button>
                                                            </td>


                                                        </tr>
                                                        
                                                    @endforeach

                                                @endif
                                          
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                             

                            </div>
                        </div>
                        
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

