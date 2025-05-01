<div class="row g-2  preview-content-section">

    <div class="col-xl-6">

        @php
                  $variablesValues = json_decode(site_settings('wp_paramvalues'),true);
                  $variables_match = json_decode(site_settings('wp_parammatch'),true);
        @endphp


            <div class="p-3 border">
                <h5>
                    {{translate('Variables')}} 
                </h5>
                <hr>
                @if (@$variables!=null)
   
                    @foreach ($variables as $key => $itemBox)

                        <div class="d-grid gap-3 border-bottom pb-3 mb-3">
                            <h6 class="fs-15 mb-0">{{ __(ucfirst ($key)    ) }}</h6>

                            @if ($key=="header"||$key=="body")

                                @foreach ($itemBox as $index =>  $item)
                                
                                    @php
                                        $id            = "paramvalues[".$key."][".$item['id']."]";
                                        $variable      = "parammatch[".$key."][".$item['id']."]";
                                        $name          =  __('Variable')."{{".$item['id']."}}";
                                        $placeholder   =  $item['exampleValue'];

                                        if(isset($variablesValues[$key] [$item['id']]) ){
                                            $value         =  $variablesValues[$key] [$item['id']];
                                        }
                                        if(!isset($variablesValues[$key][$item['id']]) && isset($variables_match[$key] [$item['id']])){
                                            $value         =  $variables_match[$key] [$item['id']]; 
                                        }else{
                                            $value         =  $item['exampleValue'];
                                        }

                                    @endphp


                                    <div>
                                
                                        <div class="mb-3">
                                            
                                            <label for="{{ $id }}" class="form-label">
                                                {{ $name }}
                                            </label>

                                           <input  type="text" name="{{ $id }}" id="{{ $id }}" class="form-control" placeholder="{{ __($placeholder) }}" value="{{  $value }}"  >

                                    

                                        </div>

                                        <div>
                                            
                                            <label for="{{ $loop->index }}" class="form-label">
                                                {{ translate('Order variable') }}
                                            </label>

                                            <select name="{{ $variable }}" class="form-select" id="{{ $loop->index }}">
                                        

                                                <option value="">
                                                     {{translate('Select variable')}}
                                                </option>

                                               @foreach (App\Enums\Settings\GlobalConfig::ORDER_VARIABLE as  $templateKey => $templateValue)

                                                    <option {{ isset($variables_match[$key] [$item['id']]) && $variables_match[$key] [$item['id']] ==  $templateKey ? 'selected' : "" }} value="{{ $templateKey }}">
                                                          {{ $templateKey }}
                                                    </option>
                                               @endforeach
                                                

                                            </select>

                                        </div>
 
                                    </div>
                
                                @endforeach

                              @elseif ($key=="buttons")

                                @foreach ($itemBox as $button)

                                    @foreach ($button as $keybtn => $item)


                                        @php
                                            $id            = "paramvalues[".$key."][".$keybtn."][".$item['id']."]";
                                            $name          =  $item['text'];
                                            $variable      = "parammatch[".$key."][".$keybtn."][".$item['id']."]";

                                            
                                        @endphp
                                        <label for="{{ $id }}" class="form-label">
                                            {{ $name }}
                                        </label>
                                        @if ($item['type']=="URL")
                                        
                                            <input  type="text" name="{{ $id }}" id="{{ $name  }}" class="form-control" placeholder="{{@$item['exampleValue']}}" value="" >

                                        @elseif($item['type']=="COPY_CODE")

                          
                                           <input  type="text" name="{{ $id }}" id="{{ $name  }}" class="form-control" placeholder="{{$item['exampleValue']}}" value="" >

                                        @endif

                                        <div>
                                            
                                            <label for="{{ $loop->index }}" class="form-label">
                                                {{ translate('Order variable') }}
                                            </label>

                                            <select name="{{$variable}}" class="form-select" id="{{ $loop->index }}">
                                    

                                               @foreach (App\Enums\Settings\GlobalConfig::ORDER_VARIABLE as  $templateKey => $templateValue)

                                                    <option  {{ isset($variables_match[$key][$keybtn][$item['id']]) && $variables_match[$key][$keybtn][$item['id']] ==  $templateKey ? 'selected' : '' }} value="{{ $templateKey }}">
                                                          {{ $templateKey }}
                                                    </option>
                                               @endforeach
                                                

                                            </select>

                                        </div>
                                    @endforeach
                                @endforeach
                            
                            @endif
                    
                        </div>
                    @endforeach

                @else
                    <div class="text-center">
                        {{translate("No variables found !!")}}
                    </div>
               @endif

            </div>

    </div>

    <div class="col-xl-6">
        <div class="p-3 border position-relative">
            <h5>
                {{translate('Preview')}} 
            </h5>
            <hr>
        
            <div class="preview-message rounded overflow-hidden">
                <div class="bg-white p-2">

                    @if(!@$loaded)
             
                        @if(site_settings('wp_header_text'))
                            <h6>
                                {{site_settings('wp_header_text')}}
                            </h6>
                        @endif
                            <p>
                                {{site_settings('wp_full_message')}}
                            </p>
                        @if(site_settings('wp_footer_text'))
                            <p>
                                {{site_settings('wp_footer_text')}}
                            </p>
                        @endif
                        @if(site_settings('wp_buttons',null))
                                @foreach (@json_decode(site_settings('wp_buttons'),true) as $button )
                                        <div class="text-center text-info border-bottom py-2">
                                            <span>{{ $button['text'] }}</span>
                                        </div>
                           
                               @endforeach
                        @endif
                    @else
                           @foreach (@$template['components'] as $component )


                            @if ($component['type']=="HEADER" && $component['format']=="TEXT")
                                <h6 class="card-title mb-2">{{ $component['text']  }}</h6>
                            @elseif ($component['type']=="FOOTER")
                                    <span class="text-muted text-xs">{{ $component['text']  }}</span>
                                @endif
                                @if ($component['type']=="BODY")
                                    
                                    <p class="card-text">{{ $component['text'] }}</p>
                                @endif
                           @endforeach

                            @foreach (@$template['components'] as $component )
                                    @if ($component['type']=="BUTTONS")
                                        @foreach ($component['buttons'] as $button)
                                            <div class="text-center text-info border-bottom py-2">
                                                <span>{{ $button['text'] }}</span>
                                            </div>
                                        @endforeach
                                    @endif
                            @endforeach
                    @endif
                </div>
                <div class="preview-bg">
                    <img src="{{asset('assets/images/whatsapp-bg.png')}}" alt="whatsapp-bg.png">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="whatsapp-loader d-flex align-items-center justify-content-center h-100 d-none">
    <div class="spinner-border text-dark" role="status">
        
    </div>
</div>