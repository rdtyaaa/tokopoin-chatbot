
<div class="border rounded">
    
    <div class="border-bottom px-3 py-3">
        <h5 class="mb-0 fs-14">
            {{$tab}}
        </h5>
    </div>
    
    <div class="p-3">
        <form class="settingsForm" data-route ="{{route('admin.general.setting.logo.store')}}">

            @csrf

            <div class="row g-4 mb-4">
    
               
                <div class="col-xl-3 col-lg-4">
                    <label for="site_logo" class="form-label">
                        {{translate("Site Logo")}} <span class="text-danger" >*</span>
                    </label>
                    <input data-size = "80x80" type="file" name="site_settings[site_logo]"  id="site_logo" class="form-control img-preview">
             
                    <div class="mt-2 image-preview-section">
                        <img src="{{ show_image(file_path()['site_logo']['path']."/".site_settings('site_logo'),file_path()['site_logo']['size']) }}" alt="{{site_settings('site_logo','logo.jpg')}}" class="logo-preview">
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4">
                    <label for="admin_logo_lg" class="form-label">
                        {{translate('Admin Site Logo')}} <span class="text-danger" >*</span>
                    </label>
                    <input data-size = "80x80" type="file" name="site_settings[admin_logo_lg]"  id="admin_logo_lg" class="form-control img-preview">

                    <div class="mt-2 image-preview-section ">
                        <img src="{{show_image(file_path()['site_logo']['path']."/".site_settings('admin_logo_lg') ,file_path()['admin_site_logo']['size'])}}" alt="{{site_settings('admin_logo_lg')}}" class="bg-dark logo-preview">
                    </div>

                </div>

                <div class="col-xl-3 col-lg-4">
                    <label for="admin_logo_sm" class="form-label">
                        {{translate('Admin Logo Icon')}} <span class="text-danger" >*</span>
                    </label>
                    <input type="file"  data-size = "80x80" name="site_settings[admin_logo_sm]" id="admin_logo_sm" class="form-control img-preview">

                    <div class="mt-2 image-preview-section">
                        <img src="{{show_image(file_path()['site_logo']['path']."/".site_settings('admin_logo_sm'),file_path()['loder_logo']['size'])}}" alt="{{site_settings('admin_logo_sm')}}" class="icon-preview">
                    </div>

                </div>

                <div class="col-xl-3 col-lg-4">
                    <label for="site_favicon" class="form-label">
                        {{translate('Site Favicon')}} <span class="text-danger" >*</span>
                    </label>
                    <input type="file" data-size = "50x50"  name="site_settings[site_favicon]"  id="site_favicon" class="form-control img-preview">

                    <div class="fav-preview-image mt-2 image-preview-section">
                        <img src="{{ show_image(file_path()['site_logo']['path']."/".site_settings('site_favicon') ,file_path()['favicon']['size']) }}" alt="{{site_settings('site_favicon')}}" class="icon-preview">
                    </div>
                </div>
                
                @foreach(json_decode(site_settings('invoice_logo')) as $key => $value)
                    <div class="col-xl-3 col-lg-4">
                        <label for="{{$key}}" class="form-label">{{(ucfirst($key))}} <span class="text-danger" >*</span></label>
                        <input data-size = "80x80" type="file" name="invoice_logo[{{ $key }}]" id="{{$key}}" class="form-control img-preview">
                        <div class="seal-preview-image mt-2 image-preview-section">
                            <img src="{{ show_image('assets/images/backend/invoiceLogo/'.$value) }}" alt="{{$value}}" class="logo-preview">
                        </div>
                    </div>
                @endforeach
              

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

