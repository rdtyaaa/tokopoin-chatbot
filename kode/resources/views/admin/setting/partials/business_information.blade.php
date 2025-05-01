
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
                    <label for="currency_position" class="form-label">
                        {{translate('Currency Position')}} <span class="text-danger" >*</span>
                    </label>
                    <select name="site_settings[currency_position]" id="currency_position"  class="form-select">
                         <option {{site_settings('currency_position',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::true->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::true->status()}}">
                            ({{default_currency()->symbol}}){{translate('Left')}}
                        </option>
                         <option {{site_settings('currency_position') ==  App\Enums\StatusEnum::false->status() ? 'selected' : '' }} value="{{App\Enums\StatusEnum::false->status()}}">
                            ({{translate('Right')}}){{default_currency()->symbol}}
                        </option>
                    </select>
             
                </div>

                <div class="col-xl-6">
                    <label for="digit_after_decimal" class="form-label">
                        {{translate('Digit After Decimal Point( Ex:0.00)')}} <span class="text-danger" >*</span>
                    </label>
                    
                    <input id="digit_after_decimal"  type="number" name="site_settings[digit_after_decimal]"  class="form-control" value="{{site_settings('digit_after_decimal')}}">
                  
                </div>


                <div class="col-lg-6">
                    <label for="search_min" class="form-label">
                        {{translate('Filter Price Range (Min)')}} <span class="text-danger" >*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="search_min" name="site_settings[search_min]" value="{{site_settings('search_min')}}" placeholder="100">
                        <span class="input-group-text" >{{@session()->get('web_currency')->name}}</span>
                    </div>
                </div>

                <div class="col-lg-6">
                    <label for="search_max" class="form-label">
                        {{translate('Filter Price Range (Max)')}} <span class="text-danger" >*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="search_max" name="site_settings[search_max]" value="{{site_settings('search_max')}}" placeholder="200">
                        <span class="input-group-text" >{{@session()->get('web_currency')->name}}</span>
                    </div>
                </div>

                <div class="col-xl-12">

                    <label for="pagination_number" class="form-label">
                        {{translate('Pagination number')}} <span class="text-danger" >*</span>
                    </label>
                    
                    <input id="pagination_number"  type="number" name="site_settings[pagination_number]"  class="form-control" value="{{site_settings('pagination_number')}}">
                  
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

