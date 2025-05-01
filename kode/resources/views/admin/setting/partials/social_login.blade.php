

<form class="settingsForm" data-route ="{{route('admin.social.login.update')}}">

    @csrf
      {{-- google  --}}
    <div class="border rounded">

        <div class="border-bottom px-3 py-3">
            <h5 class="mb-0 fs-14">
                {{translate('Google Auth Credentials Setup')}}
            </h5>
        </div>

        <div class="p-3">


                @php
                   $google = json_decode(site_settings('s_login_google_info'),true);


                @endphp

                <div class="row g-4 mb-4">


                    <div class="col-lg-6">
                        <label for="clientId" class="form-label">
                            {{translate('Client Id')}} <span  class="text-danger"  >*</span>
                        </label>
                        <input type="text" name="g_client_id" id="clientId" class="form-control" value="{{ @$google['g_client_id']}}" placeholder="{{translate('Enter Google Client Id')}}" required>
                    </div>

                    <div class="col-lg-6">
                        <div>
                            <label for="g_client_secret" class="form-label">
                                {{translate('Client Secret')}} <span  class="text-danger"  >*</span>
                            </label>
                            <input type="text" name="g_client_secret" id="g_client_secret" class="form-control" value="{{@$google['g_client_secret']}}" placeholder="{{translate('Enter Google Secret Key')}}" required>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label for="g_status" class="form-label">
                            {{translate('Status')}} <span  class="text-danger"  >*</span>
                        </label>

                        <select name="g_status" id="g_status" class="form-select" required>
                            @if(array_key_exists('g_status',$google))

                            @php  $gstatus = $google['g_status']; @endphp
                                <option value="1" @if($gstatus == 1) selected @endif>{{translate('Active')}}</option>
                                <option value="2"  @if($gstatus == 2) selected @endif>{{translate('Inactive')}}</option>
                            @endif
                        </select>


                    </div>

                    <div class="col-lg-6">
                        <label for="callback_google_url" class="form-label">
                            {{translate('Authorized redirect URIs')}}
                        </label>
                        <div class="input-group">
                            <input type="text" id="callback_google_url" class="form-control" value="{{url('auth/google/callback')}}" readonly="" aria-label="Enter amount" aria-describedby="basic-addon2">
                            <span onclick="copyGoogleUrl()" class="input-group-text cursor-pointer" >
                                {{translate('Copy Url')}}
                            </span>
                        </div>
                    </div>


                </div>



        </div>

    </div>

    {{-- facebook --}}

    <div class="border rounded mt-4">

        <div class="border-bottom px-3 py-3">
            <h5 class="mb-0 fs-14">
                {{translate('Facebook Auth Credentials Setup')}}
            </h5>
        </div>

        <div class="p-3">


            @php $facebook = json_decode(site_settings('s_login_facebook_info'),true); @endphp

            <div class="row g-4 mb-4">


                <div class="col-lg-6">
                    <label for="f_client_id" class="form-label">
                        {{translate('Client Id')}} <span  class="text-danger"  >*</span>
                    </label>
                    <input type="text" name="f_client_id" id="f_client_id" class="form-control" value="{{ @$facebook['f_client_id']}}" placeholder="{{translate('Enter Facebook Client Id')}}" required>
                </div>

                <div class="col-lg-6">
                    <div>
                        <label for="f_client_secret" class="form-label">
                            {{translate('Client Secret')}} <span  class="text-danger"  >*</span>
                        </label>
                        <input type="text" name="f_client_secret" id="f_client_secret" class="form-control" value="{{@$facebook['f_client_secret']}}" placeholder="{{translate('Enter Facebook Secret Key')}}" required>
                    </div>
                </div>

                <div class="col-lg-6">
                    <label for="f_status" class="form-label">
                        {{translate('Status')}} <span  class="text-danger"  >*</span>
                    </label>
                    <select name="f_status" id="f_status" class="form-select" required>
                        @if(array_key_exists('f_status',$facebook))
                            @php
                                $fstatus = $facebook['f_status'];
                            @endphp
                            <option value="1" @if($fstatus == 1) selected @endif>{{translate('Active')}}</option>
                            <option value="2"  @if($fstatus == 2) selected @endif>{{translate('Inactive')}}</option>
                        @endif
                    </select>
                </div>

                <div class="col-lg-6">
                    <label for="callback_facebook_url" class="form-label">
                        {{translate('Authorized redirect URIs')}}
                    </label>
                    <div class="input-group">
                        <input   type="text" id="callback_facebook_url" class="form-control" value="{{url('auth/facebook/callback')}}" readonly="" aria-describedby="basic-addon2">
                        <span onclick="copyUrlFacebook()"  class="input-group-text cursor-pointer" >
                            {{translate('Copy Url')}}
                        </span>
                    </div>
                </div>


            </div>




        </div>

    </div>

    <div class="text-start mt-4">
        <button type="submit"
            class="btn btn-success waves ripple-light"
            id="add-btn">
            {{translate('Submit')}}
        </button>
    </div>

</form>

