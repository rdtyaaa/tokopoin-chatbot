
<div class="border rounded mb-4">
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{
                translate("Notification settings")
            }} <i class="cursor-pointer ri-information-line"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Notify System admin when a new order is placed or created')}}"></i>
        </h5>
    </div>
    
    <form class="settingsForm p-3" data-route ="{{route('admin.general.setting.store')}}">

        @csrf

        <div class="row g-4 mb-4">
            <div class="col-xl-6">
                <div>
                    <label for="whatsapp_order_notification" class="form-label">
                        {{translate('Notify VIA WhatsApp')}} 

                        <button  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('WhatsApp Settings')}}" type="button" class="wp-config-btn text-danger"  id="configWp">
                            <i class="ri-settings-2-line"></i>
                        </button>

                    </label>
            
                    <select name="site_settings[whatsapp_order_notification]" id="whatsapp_order_notification"  class="form-select">
                        <option {{site_settings('whatsapp_order_notification',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                            {{translate('Active')}}
                        </option>
                        <option {{site_settings('whatsapp_order_notification',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                            {{translate('Inactive')}}
                        </option>
                    </select>
                </div>
            </div>

            <div class="col-xl-6">
                <div>
                    <label for="email_order_notification" class="form-label">
                        {{translate('Notify VIA Email')}}
                        <span class="text-danger" >* </span>
                    </label>

                    <select name="site_settings[email_order_notification]" id="email_order_notification"  class="form-select">
                        <option {{site_settings('email_order_notification',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                            {{translate('Active')}}
                        </option>
                        <option {{site_settings('email_order_notification',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                            {{translate('Inactive')}}
                        </option>
                    </select>
                </div>
            
            </div>
            <div class="col-xl-6">
               
                <div>
                    <label for="sms_order_notification" class="form-label">
                        {{translate('Notify VIA SMS')}}
                        <span class="text-danger" >* </span>
                    </label>

                    <select name="site_settings[sms_order_notification]" id="sms_order_notification"  class="form-select">
                        <option {{site_settings('sms_order_notification',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                            {{translate('Active')}}
                        </option>
                        <option {{site_settings('sms_order_notification',App\Enums\StatusEnum::false->status()) ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
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

<div class="border rounded">
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{
                translate("Notification variables")
            }}
        </h5>
    </div>
    
    <form class="settingsForm p-3" data-route ="{{route('admin.general.setting.store')}}">
        @csrf

        <div class="row g-4 mb-4">
            <div class="col-4">
                <div class="order-variable">
                    <h6>
                            {{translate('Order Variable')}}
                    </h6>

                    <hr>
                    <ul>
                        @foreach (App\Enums\Settings\GlobalConfig::ORDER_VARIABLE as  $templateKey => $templateValue)
                            <li> {{$templateValue}} : <span>{{$templateKey}}</span></li>    
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-4">
                <div class="order-variable">
                    <h6>
                            {{translate("Item Variable")}}
                    </h6>
                    <hr>

                    <ul>
                        @foreach (App\Enums\Settings\GlobalConfig::ITEM_VARIABLE as  $templateKey => $templateValue)
                            <li> {{$templateValue}} : <span>{{$templateKey}}</span></li>    
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-4">

                <div>
                    <label for="item_variable" class="form-label">
                        [item_variable] <span class="text-danger" >*</span>
                    </label>
                    <textarea class="form-control" name="site_settings[item_variable]" id="item_variable" cols="30" rows="3">{{site_settings('item_variable','{quantity} x {product_name} - {variant_name}  = {item_total}')}}</textarea>
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



<div>
    <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#sms-email" role="tab" aria-selected="true">
                {{translate("SMS & Email")}}
            </a>
        </li>

        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#WhatsApp" role="tab" aria-selected="false" tabindex="-1">
                {{translate("WhatsApp")}}
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="sms-email" role="tabpanel">
            <form class="settingsForm p-3" data-route ="{{route('admin.general.setting.store')}}">
                @csrf
                <div class="row g-4 mb-4">
            
                        <div class="col-12">
                            <label for="order_message">
                                {{translate('SMS Message')}} <span class="text-danger">*</span>
                            </label>
                            <textarea placeholder="{{translate('Enter message')}}" class="form-control" required="required" name="site_settings[order_message]" id="order_message" cols="50" rows="8">{{site_settings('order_message')}}</textarea>
                        </div>

                        <div class="col-12">
                            <label for="order_email_message">
                                {{translate('Email Message')}}  <span class="text-danger">*</span>
                            </label>
                            <textarea placeholder="{{translate('Enter message')}}" class="form-control text-editor" required="required" name="site_settings[order_email_message]" id="order_email_message" cols="50" rows="8">{{site_settings('order_email_message')}}</textarea>
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

        <div class="tab-pane" id="WhatsApp" role="tabpanel">

            <form class="settingsForm p-3" data-route ="{{route('admin.store.template')}}">
                @csrf
                <div class="row g-2">
                    <div class="col-xl-4">
                        <div class="p-3 border">
                            <h5>
                                 {{translate('Templates')}}
                            </h5>
                            <hr>
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                                    <label for="whatsapp_order_notification" class="form-label mb-0">
                                        {{translate('Template')}} 
                                    </label>
        
                                    <button type="button" class="btn btn-primary btn-sm py-1 d-flex align-items-center gap-1 lh-1 load-wp-template">  
                                          <i class="ri-refresh-line fs-16"></i>
                                          {{translate("Load template")}}
                                    </button>
                                </div>

                                @php
                                   if($wp_templates){
                                      $templates = json_decode($wp_templates->value,true);
                                   }
                                @endphp
                                
                        
                                <select name="site_settings[wp_template]" id="wp_template"  class="form-select">

                                     <option value="">
                                        {{translate("Select a template")}}
                                     </option>
                                    @if($wp_templates)

                                        @foreach ($templates as $template )

                                            <option {{site_settings('wp_template') ==  Arr::get($template ,'name') ? "selected" : ""}} value="{{Arr::get($template ,'name')}}">
                                            
                                                {{Arr::get($template ,'name')}}

                                            </option>
                                            
                                        @endforeach
                                    @endif


   
                                </select>
                            </div>
                        </div>
                        <div class="text-start mt-2">
                            <button type="submit"
                                class="btn btn-success waves ripple-light"
                                id="add-btn">
                                {{translate('Submit')}}
                            </button>
                        </div>
                    </div>

                    <div class="col-xl-8 wp-preview-section">
                          @include('admin.setting.partials.wp_preview_section')

                        <div class="whatsapp-loader d-flex align-items-center justify-content-center h-100 d-none">
                            <div class="spinner-border text-dark" role="status">
                                
                            </div>
                        </div>
                    </div>

                </div>

                
            </form>
        </div>
    </div>
</div>