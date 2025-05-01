@if( $openAi->status == 1)
    <div class="modal fade" id="aiModal" tabindex="-1" aria-labelledby="aiModal" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-scrollable">
            <form id="AiForm"  method="post">
                @csrf

                @php

                    $countries = json_decode(file_get_contents(resource_path(config('constants.options.country_code')) . 'countries.json'),true);

                @endphp

                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title">{{translate('AI Assistance')}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body position-relative ai-modal-body">

                        <div class="result-section d-none">
                            <div class="d-flex gap-2">

                                <label>
                                    {{translate("Result")}}
                                </label>

                                <a href="javascript:void(0)" class="copy-content"  title="{{translate('Copy')}}" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="ri-file-copy-line fs-18 link-info cursor-pointer "></i>
                                </a>

                                <a href="javascript:void(0)" class="download-text" title="{{translate('Download')}}" data-bs-toggle="tooltip" data-bs-placement="top">
                                    <i class="ri-download-2-line fs-18 link-success cursor-pointer"></i>
                                </a>

                            </div>
                            <textarea readonly class="ai-result form-control w-100"   rows="10"></textarea>
                        </div>

                        <div class="ai-content-generate">

                            <div class="pb-4">

                                <div class="mt-3">

                                    <label class="form-label" for="custom_prompt">
                                        {{translate("Your Content")}} <span class="text-danger">*</span>
                                    </label>

                                     <textarea class="form-control ai-prompt-input custom-prompt" placeholder="{{translate("Your prompt goes here .... ")}}" name="custom_prompt" id="custom_prompt" cols="30" rows="3"></textarea>
                                </div>



                            </div>
                            <h5> {{ucfirst(strtolower(translate('What do you want to do')))}}?</h5>
                            <p class="mb-0">
                                {{ucfirst(strtolower(translate('Here are some ideas')))}}
                            </p>

                            <input  hidden type="text" class="ai-content-option" >

                            <div class="list-group list-group-flush fade-in default-section">

                                @foreach (collect(get_ai_option())->take(3) as $key => $get_ai_option )

                                    <button name="ai_option" value="{{$key}}" type="submit" class="list-group-item list-group-item-action option-btn">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="ri-share-forward-fill align-middle list-icon"></i>

                                            {{k2t($key)}}
                                        </span>
                                    </button>

                                @endforeach


                                <button type="button" id="more-option" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between w-100">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ri-grid-fill align-middle list-icon"></i>
                                            {{translate('More')}}
                                    </span>

                                    <span>
                                        <i class="ri-arrow-down-s-fill"></i>
                                    </span>
                                </button>

                                <button type="button" id="translate-option" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between w-100">
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="ri-translate align-middle list-icon"></i>

                                            {{translate('Translate')}}
                                    </span>

                                    <span>
                                        <i class="ri-arrow-down-s-fill"></i>
                                    </span>
                                </button>

                            </div>




                            <div class="ai-options fade-in d-none">
                                <div class="d-flex align-items-center justify-content-between w-100 pb-2 mt-2">
                                    <h6 class="mb-0 fs-14">
                                        {{translate("Back")}}
                                    </h6>
                                    <span class="ai-option-closer">

                                        <i class='bx bx-undo align-middle' ></i>

                                    </span>
                                </div>

                                <div class="ai-option-wrapper" data-simplebar>
                                    <div class="ai-option-list">
                                        <h6>
                                            {{translate("Rewrite It")}}
                                        </h6>
                                        <div class="list-group list-group-flush">

                                            @foreach (collect(get_ai_option()) as $key => $option )

                                                <button name="ai_option" value="{{$key}}" type="submit" class="list-group-item list-group-item-action  option-btn">
                                                    <span class="d-flex align-items-center gap-2">
                                                        <i class="ri-share-forward-fill align-middle list-icon"></i>

                                                        {{k2t($key)}}
                                                    </span>
                                                </button>

                                            @endforeach

                                        </div>
                                    </div>

                                    <div class="ai-option-list mt-2">
                                        <h6>
                                            {{translate('Adjust Tone')}}
                                        </h6>
                                        <div class="list-group list-group-flush">

                                            @foreach (collect(get_ai_tone()) as $key => $tone )

                                                <button name="ai_tone" value="{{$key}}" type="submit" class="list-group-item list-group-item-action option-btn">
                                                    <span class="d-flex align-items-center gap-2">
                                                        <i class="ri-share-forward-fill align-middle list-icon"></i>

                                                        {{Arr::get($tone,'display_name')}}
                                                    </span>
                                                </button>

                                            @endforeach


                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="translate-section fade-in d-none">

                                <div class="d-flex align-items-center justify-content-between w-100 pb-2 mt-2">
                                    <h6 class="mb-0 fs-14">
                                        {{translate("Back")}}
                                    </h6>
                                    <span class="ai-option-closer">

                                        <i class='bx bx-undo align-middle' ></i>

                                    </span>
                                </div>

                                <div>
                                    <label class="form-label" for="ai-language">
                                        {{translate("Choose Language")}}
                                    </label>
                                    <select class="form-select ai-lang" name="language" id="ai-language">

                                        <option value="">
                                            {{translate("Select Language")}}
                                        </option>

                                        @foreach ($countries as $code => $country)
                                            <option  value="{{$country['name']}}">
                                                {{limit_words($country['name'],2)}}
                                            </option>
                                        @endforeach

                                    </select>
                                 </div>

                            </div>

                            <div class="my-3 text-center d-flex align-items-center justify-content-center">
                                <span class="divider">
                                    {{translate('OR')}}
                                </span>
                            </div>


                            <div class="ai-prompt">
                                <label for="custom_prompt_option">
                                    {{translate("Write your custom prompt")}}
                                </label>
                                <div class="input-group">
                                    <input name="custom_prompt_option" id="custom_prompt_option" class="form-control custom-prompt-option" type="text"  placeholder="{{translate('Ex: Make It more friendly ')}}.....">
                                    <button class="input-group-text" ><i class="ri-send-plane-fill"></i></button>
                                </div>
                            </div>
                        </div>


                        <div class="ai-content-loader d-none">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                       </div>

                    </div>

                    <div class="modal-footer ai-modal-footer">

                        <button type="button" class="btn btn-success d-none insert-result" data-bs-dismiss="modal">{{translate('Insert')}}</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif
