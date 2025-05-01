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
                    <li class="breadcrumb-item"><a href="{{route('admin.notification.templates.index')}}">
                        {{translate('Notification Templates')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Update')}}
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
								<div>
									<h5 class="card-title mb-0">
										{{translate('Update Template')}}
									</h5>
								</div>
							</div>
							<div class="col-sm-auto">
							</div>
						</div>
					</div>

					<div class="card-body">
                        <form action="{{route('admin.notification.templates.update', $emailTemplate->id)}}" method="POST"  enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label for="subject" class="form-label">{{translate('Subject')}} <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" class="form-control" value="{{$emailTemplate->subject}}" placeholder="{{translate('Enter subject')}}" required>
                                </div>

                                <div class="col-lg-6">
                                    <label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option value="1" @if($emailTemplate->status == 1) selected @endif>{{translate('Active')}}</option>
                                        <option value="2" @if($emailTemplate->status == 2) selected @endif>{{translate('Inactive')}}</option>
                                    </select>
                                </div>

                                <div class="col-12">


                                    <label for="sms_body" class="form-label">{{translate('SMS Body')}} <span class="text-danger">*</span></label>
                                    <textarea placeholder="{{translate('Enter message')}}" class="form-control" name="sms_body" rows="5" id="sms_body" required>{{$emailTemplate->sms_body}}</textarea>


                                </div>

                                <div class="col-12">

                                    <div class="text-editor-area">

                                        <label for="body" class="form-label">{{translate('Email Body')}} <span class="text-danger">*</span></label>
                                        <textarea placeholder="{{translate('Enter message')}}" class="form-control text-editor" name="body" rows="5" id="body" required>@php echo ($emailTemplate->body) @endphp</textarea>


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


                            </div>

                            <div class="text-start mt-3">
                                <button type="submit" class="btn btn-success">
                                    {{translate('Update')}}
                                </button>
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
								<div>
									<h5 class="card-title mb-0">
										{{translate('Email template short code')}}
									</h5>
								</div>
							</div>
							<div class="col-sm-auto">
							</div>
						</div>
					</div>
					 <div class="card-body">
                        <div class="d-flex flex-column gap-2">
                            @foreach($emailTemplate->codes as $key => $value)
                                <div class="p-2 border d-flex align-items-center justify-content-between ">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{$value}}</h6>
                                    </div>
                                    <p class="mb-0">@php echo "{{". $key ."}}"  @endphp</p>
                                </div>
                            @endforeach
                        </div>
					 </div>
				</div>
			</div>
		</div>

	</div>
</div>
@endsection

@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>
@endpush


