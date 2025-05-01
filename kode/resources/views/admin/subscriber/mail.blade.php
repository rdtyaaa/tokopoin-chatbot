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
                        <li class="breadcrumb-item"><a href="{{route('admin.subscriber.index')}}">
                            {{translate('Subscribers')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate('Send Mail')}}
                        </li>
                    </ol>
                </div>
            </div>


            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">
                                    {{translate('Send Mail')}}
                                </h5>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <form action="{{route('admin.subscriber.send.mail.submit')}}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">

                            <div class="col-12">
                                <div >
                                    <label for="Subject" class="form-label">  {{translate('Subject')}}
                                        <span  class="text-danger">*</span>
                                    </label>
                                    <input type="text" value="{{old('Subject')}}" name="subject" id="Subject" placeholder="{{translate('Enter subject')}}" class="form-control" required>

                                </div>
                            </div>
                            <div class="col-12">
                                <div  class="text-editor-area">
                                    <label for="mail-composer" class="form-label">  {{translate('Body')}}
                                          <span  class="text-danger">*</span>
                                    </label>

                                    <textarea id="mail-composer" class="form-control text-editor" name="details" rows="5"  placeholder="{{translate('Enter Description')}}">{{old('details')}}</textarea>

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

                            <div class="col-lg-12">
                                <div class="text-start">
                                    <button  class="btn btn-success">
                                        {{ucfirst(translate('Submit'))}}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>
@endpush









