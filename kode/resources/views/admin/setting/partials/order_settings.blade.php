
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
                        <label for="order-prefix" class="form-label">
                            {{translate('Order Prefix')}} <span class="text-danger" >*</span>
                        </label>
                        <input id="order-prefix"  type="text" name="site_settings[order_prefix]"  class="form-control" value="{{site_settings('order_prefix')}}">
                    </div>
                </div>

                <div class="col-xl-6 minimum-order-amount-section" >
                    <div>
                        <label for="minimum_order_amount" class="form-label">
                            {{translate('Minimum order amount')}} ({{default_currency()->symbol}})
                            <span class="text-danger" >* </span>
                        </label>

                        <input id="minimum_order_amount" class="form-control" type="number" name="site_settings[minimum_order_amount]" value="{{site_settings('minimum_order_amount')}}">
                        
                    </div>
                </div>


                <div class="col-xl-6" >
                    <div>
                        <label for="default_order_status" class="form-label">
                            {{translate('Default order status')}} 
                            <span class="text-danger" >* </span>

                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Delivery status after a new order created')}}"></i>

                        </label>

                        <select name="site_settings[default_order_status]" id="default_order_status"  class="form-select">


                            @foreach (App\Models\Order::delevaryStatus() as $status => $value )

                            <option {{site_settings('default_order_status') ==  $value ? 'selected' : '' }} value="{{$value}}">
                                {{
                                   ucfirst( $status)
                                }}
                             </option>
                                
                            @endforeach

                       </select>

                        
                    </div>
                </div>

                <div class="col-xl-6" >
                    <div>
                        <label for="deliver" class="form-label">
                            {{translate('Order status after payment')}} 
                            <span class="text-danger" >* </span>

                            <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Order status after payment for physical order , digital order attribute values will get visable after payment so it will be delivered')}}"></i>

                        </label>

                        <select name="site_settings[order_status_after_payment]" id="order_status_after_payment"  class="form-select">


                            @foreach (App\Models\Order::delevaryStatus() as $status => $value )

                            <option {{site_settings('order_status_after_payment') ==  $value ? 'selected' : '' }} value="{{$value}}">
                                {{
                                   ucfirst( $status)
                                }}
                             </option>
                                
                            @endforeach

                       </select>

                        
                    </div>
                </div>


                <div class="col-xl-12">
                    <div>
                        <label for="minimum_order_amount_check" class="form-label">
                            {{translate('Minimum order amount check')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[minimum_order_amount_check]" id="minimum_order_amount_check"  class="form-select">
                            <option {{site_settings('minimum_order_amount_check') ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('minimum_order_amount_check',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
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
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{translate('WhatsApp Order')}}
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.store')}}">

            @csrf

            <div class="row g-4 mb-4">

                <div class="col-4">
                    <div class="order-variable">
                        <h6>
                                {{translate("Physical Item Variable")}}
                        </h6>
                        <hr>
    
                        <ul>
                            @foreach (App\Enums\Settings\GlobalConfig::WP_ORDER as  $templateKey => $templateValue)
                                <li> {{$templateValue}} : <span>{{$templateKey}}</span></li>    
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-4">
    
                    <div>
                        <label for="wp_order_message" class="form-label">
                            {{translate("Physical Order Message")}}
                            <span class="text-danger" >*</span>
                        </label>
                        <textarea class="form-control" placeholder="{{translate("Type here")}}" name="site_settings[wp_order_message]" id="wp_order_message" cols="30" rows="7">{{site_settings('wp_order_message')}}</textarea>
                    </div>
                    
                </div>
                <div class="col-4">
    
                    <div>
                        <label for="wp_digital_order_message" class="form-label">
                            {{translate("Digital Order Message")}}
                            <span class="text-danger" >*</span>
                        </label>
                        <textarea placeholder="{{translate("Type here")}}" class="form-control" name="site_settings[wp_digital_order_message]" id="wp_digital_order_message" cols="30" rows="7">{{site_settings('wp_digital_order_message')}}</textarea>
                    </div>
                    
                </div>
    
    
                <div class="col-xl-12">
                    <div>
                        <label for="whatsapp_order" class="form-label">
                            {{translate('Order VIA WhatsApp')}}
                            <span class="text-danger" >* </span>
                        </label>

                        <select name="site_settings[whatsapp_order]" id="whatsapp_order"  class="form-select">
                            <option {{site_settings('whatsapp_order',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                              {{translate('Active')}}
                           </option>
                            <option {{site_settings('whatsapp_order',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
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

