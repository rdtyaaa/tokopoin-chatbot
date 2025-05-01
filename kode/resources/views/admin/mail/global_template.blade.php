@extends('admin.layouts.app')
@push('style-include')
<link href="{{asset('assets/backend/css/summnernote.css')}}" rel="stylesheet" type="text/css" />
@endpush
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

                    <li class="breadcrumb-item active">
                        {{translate('Global Email Template')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="row">
			<div class="col-xl-9">
				<div class="card">
					<div class="card-header border-bottom-dashed">
						<div class="row g-4 align-items-center">
							<div class="col-sm">
                                <h5 class="card-title mb-0">
                                    {{translate('Email Template Short Code')}}
                                </h5>
							</div>
						</div>
					</div>

					<div class="card-body">
						<form action="{{route('admin.global.template.update')}}" method="POST">
							@csrf
								<div class="row g-3 mb-3">
									<div class=" col-12">
										<label for="mail_from" class="form-label">{{translate('Sent From Email')}}<span class="text-danger">*</span></label>
										<input type="text" id="mail_from" name="mail_from" class="form-control" value="{{site_settings('mail_from')}}" placeholder="{{translate('Enter subject')}}" required>
									</div>

									<div class=" col-12">
										<div class="text-editor-area">
												<label for="body" class="form-label">{{translate('Email Template')}}<span class="text-danger">*</span></label>
												<textarea class="form-control text-editor" name="body" rows="5" id="body" required> @php echo site_settings('email_template') @endphp</textarea>


												@if( $openAi->status == 1)
													<button type="button" class="ai-generator-btn mt-3 ai-modal-btn" >
														<span class="ai-icon btn-success waves ripple-light">
																<span class="spinner-border d-none" aria-hidden="true"></span>

																<i class="ri-robot-line"></i>
														</span>

														<span class="ai-text">
															{{translate('Generate With AI')}}
														</span>
													</button>
											    @endif
										</div>
									</div>

									<div class="col-12">
										<button type="submit" class="btn btn-success">{{translate('Submit')}}</button>
									</div>
								</div>
						</form>
					</div>
			    </div>
			</div>

			<div class="col-xl-3">
				<div class="card">
					<div class="card-header border-bottom-dashed">
						<div class="row g-4 align-items-center">
							<div class="col-sm">
                                <h5 class="card-title mb-0">
                                    {{translate('Template')}}
                                </h5>
							</div>
						</div>
					</div>

					<div class="card-body">
                        <h6 class="mb-0">{{translate('Email template short code')}}</h6>

                        <div class="mt-3">
                            <div class="text-center d-flex flex-column gap-2">
                                <div class="p-2 border d-flex gap-2 align-items-center justify-content-between">
                                    <div >
                                        <h6 class="mb-0">{{translate('Username')}}</h6>
                                    </div>
                                    <p class="mb-0">@{{username}}</p>
                                </div>

                                <div class="p-2 border d-flex gap-2 align-items-center justify-content-between">
                                    <div>
                                        <h6 class="mb-0">{{translate('Mail Body')}}</h6>
                                    </div>
                                    <p class="mb-0">@{{message}}</p>
                                </div>
                            </div>
                        </div>
				   </div>
				</div>
			</div>
		</div>

	<div>
</div>
@endsection


@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>
@endpush
