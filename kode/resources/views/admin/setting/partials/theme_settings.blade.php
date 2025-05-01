


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


                <div class="col-lg-4">
                    <label for="primary_color" class="form-label">
                        {{translate('Primary Color')}} <span class="text-danger" >*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" >
                            <input  type='text' class="color_picker_show"  value="{{site_settings('primary_color')}}">
                        </span>
                        <input type="text" class="form-control color_code" id="primary_color" name="site_settings[primary_color]"   value="{{site_settings('primary_color')}}">
                        <span id="reset-primary-color" class="input-group-text pointer"><i class="las la-redo-alt"></i></span>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label for="secondary_color " class="form-label">
                        {{translate('Secondary Color')}} <span class="text-danger" >*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" >
                            <input type='text' class="color_picker_show" value="{{site_settings('secondary_color')}}" />
                        </span>
                        <input type="text" class="form-control color_code" id="secondary_color" name="site_settings[secondary_color]" value="{{site_settings('secondary_color')}}">
                        <span id="reset-secondary-color" class="input-group-text pointer"><i class="las la-redo-alt"></i></span>
                    </div>
                </div>

                <div class="col-lg-4">
                    <label for="font_color" class="form-label">
                        {{translate('Font Color')}} <span class="text-danger" >*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text" >
                            <input type='text' class="color_picker_show"  value="{{site_settings('font_color')}}"/>
                        </span>
                        <input type="text" class="form-control color_code" id="font_color" name="site_settings[font_color]" value="{{site_settings('font_color')}}">
                        <span id="reset-font-color" class="input-group-text pointer"><i class="las la-redo-alt"></i></span>
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

