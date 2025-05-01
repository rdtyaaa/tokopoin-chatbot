
@if(count($combinations[0]) > 0)
<div class="table-responsive">
    <table class="table table-bordered ">
        <thead>
            <tr>
                <th>
                    {{translate('Variant')}}
                </th>

                <th >
                    {{translate('Display name')}}
                </th>
    
                <th >
                    {{translate('Price')}}
                </th>
    
                <th>
                    {{translate('Quantity')}}
                </th>
    
            </tr>
        </thead>
        <tbody>
        @foreach ($combinations as $key => $combination)
            @php
                $varient_name = '';

                foreach ($combination as $key => $item){
                    if($key > 0 ){
                        $varient_name .= '-'.str_replace(' ', '', $item);
                    }
                    else{
                        $varient_name .= str_replace(' ', '', $item);
                    }
                }
            @endphp
            @if(strlen($varient_name) > 0)
                <tr class="variant">
                    <td>
                        <label  class="control-label mt-2 mb-0">{{ ($varient_name) }}</label>
                    </td>

                    <td>
                        <input type="text"  name="display_name_{{ $varient_name }}" value="{{ $varient_name }}" class="form-control" required>
                    </td>
                    <td>
                        <input type="number"  name="price_{{ $varient_name }}" value="{{ $unit_price }}" min="0" step="0.01" class="form-control" required>
                    </td>
    
                    <td>
                        <input type="number"  name="qty_{{ $varient_name }}" value="10" min="0" step="1" class="form-control" required>
                    </td>
    
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
@endif
