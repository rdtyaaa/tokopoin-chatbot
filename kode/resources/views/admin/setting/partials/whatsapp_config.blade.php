
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{translate("Cloud API Configuration")}}

            <a href="https://developers.facebook.com/docs/whatsapp/cloud-api/" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Cloud API Configuration')}}" class="wp-config-btn text-danger ms-1" >
                <i class="ri-settings-3-line"></i>
            </a>
        </h5>

    </div>
    
    <div class="p-3">

        @include('admin.setting.partials.wp_cloud_api')
     
    </div>

</div>

