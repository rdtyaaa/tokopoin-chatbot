@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">

	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box d-sm-flex align-items-center justify-content-between">
					<h4 class="mb-sm-0">
						{{translate(@$title)}}
					</h4>
					<div class="page-title-right">
						<ol class="breadcrumb m-0">
							<li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
								{{translate('Home')}}
							</a></li>
							<li class="breadcrumb-item active">
                                {{translate(@$title)}}
							</li>
						</ol>
					</div>
				</div>
			</div>
		</div>

        @php

            $openAi =   site_settings('open_ai_setting',null) ? json_decode(site_settings('open_ai_setting',null)) : [];

        @endphp

		<div class="row basic-setting">
			
			<div class="col-md-12">
				<div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">

                    <div class="tab-pane fade active show  " id="v-pills-chatgpt-settings" role="tabpanel" aria-labelledby="v-pills-chatgpt-settings">
                        <form action="{{route("admin.general.ai.configuration.update")}}" class="d-flex flex-column gap-4 settingsForm"    method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="card">
                                <div class="card-header border-bottom-dashed">
                                    <div class="d-flex align-items-center justify-content-start">
                                        <h5 class="d-inline-block card-title mb-0 ">
                                            {{translate('Chat Gpt Settings')}}
                                        </h5>

                                    </div>

                                </div>
                                <div class="card-body">


                                    <div class="row g-3">

                                        <div class="col-lg-6 e">

                                            <label for="key" class="form-label">
                                                {{translate('Open AI Api Key')}} <span class="text-danger" >* </span>
                                            </label>
                                            <input type="text" name="site_settings[key]" id="key" class="form-control" value="{{@$openAi->key}}" required placeholder="{{translate('Api Key')}}">

                                        </div>


                                        <div class="col-lg-6 e">

                                            <label for="status" class="form-label">
                                                {{translate('Status')}} <span class="text-danger" >* </span>
                                            </label>

                                            <select id="status" class="form-select" name="site_settings[status]" >
                                                <option value="">
                                                     {{translate('Select Status')}}
                                                </option>
                                                <option {{@$openAi->status == 1 ? 'selected' :''}} value="1">
                                                    {{translate('Active')}}
                                                </option>
                                                <option {{@$openAi->status == 2 ? 'selected' :''}}  value="2">
                                                    {{translate('Inactive')}}
                                                </option>
                                            </select>

                                        </div>
                                
                                        
                                        <div class="text-start">
                                            <button type="submit"
                                                class="btn btn-success waves ripple-light">
                                                {{translate('Submit')}}
                                            </button>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>

				</div>
			</div>
		</div>
	</div>

</div>

@endsection


