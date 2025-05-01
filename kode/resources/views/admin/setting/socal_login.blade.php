@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate($title)}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">{{translate('Social Login')}}</li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header  border-bottom-dashed">
				<div class="d-flex align-items-center">
					<h5 class="card-title mb-0 flex-grow-1">
						{{translate('Social Login Settings')}}
					</h5>
				</div>
			</div>

			<div class="card-body">
					<form action="{{route('admin.social.login.update')}}" method="POST" class="d-flex flex-column gap-4">
						@csrf
						<div class="border rounded p-3">
							 <h6 class="mb-3 fw-bold">
								  {{translate('Google Auth Credentials Setup')}}
							 </h6>
								@php
								  $google = json_decode(site_settings('s_login_google_info'),true);

								@endphp
							 <div class="row g-3">
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

										  @php
										    $gstatus = $google['g_status']
										  @endphp
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
										<span onclick="copyUrl()" class="input-group-text cursor-pointer" >
											{{translate('Copy Url')}}
										</span>
									</div>
								</div>
							 </div>
						</div>
						@php
						  $facebook = json_decode(site_settings('s_login_facebook_info'),true);
					    @endphp

						<div class="border rounded p-3">
							 <h6 class="mb-3">
								{{translate('Facebook Auth Credentials Setup')}}
							 </h6>
							 <div class="row g-4">
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
										    $fstatus = $facebook['f_status']
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

	</div>
</div>

@endsection

@push('script-push')
<script>
	"use strict";
	function copyUrlFacebook() {
        var copyText = document.getElementById("callback_facebook_url");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        toaster('Copied the text : ' + copyText.value,'success');
    }
    function copyUrl() {
        var copyText = document.getElementById("callback_google_url");
        copyText.select();
        copyText.setSelectionRange(0, 99999)
        document.execCommand("copy");
        toaster('Copied the text : ' + copyText.value,'success');
    }
</script>
@endpush
