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
                        {{translate('SEO')}}
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
								{{translate('SEO Content')}}
							</h5>
						</div>
					</div>
				</div>
			</div>
			@php
			   $seo_section = json_decode($seo->value,true);
			@endphp
			<div class="card-body">
				<form action="{{route('admin.seo.update')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="d-flex flex-column gap-4">

						<div class="border rounded p-3">
							<h6 class="mb-3 fw-bold">
								{{translate('Seo Image & Meta Keywords')}}
							</h6>
							<div class="row g-3">
								<div class="col-lg-6">
									<label for="meta_keyword" class="form-label">
										{{translate('Meta Keywords')}} <span  class="text-danger"  >*</span>
									</label>
									<select class="form-select select2" name="meta_keywords[]" id="meta_keyword" multiple=multiple>
										@if(@$seo_section['meta_keywords'])
											@foreach($seo_section['meta_keywords'] as $data)
												<option value="{{$data}}" selected>{{($data)}}</option>
											@endforeach
										@endif
									</select>
								</div>

								<div class="col-lg-6">
									<label for="seo_image" class="form-label">
										{{translate('SEO Image')}}
									</label>
									<input  type="file" name="seo_image" id="seo_image" class="form-control">

									<div class="form-text">{{translate('Supported File : jpg,png,jpeg and size')}} {{file_path()['seo_image']['size']}} {{translate('pixels')}}.
										<a  href="{{@show_image(file_path()['seo_image']['path'].'/'.@$seo_section['seo_image'],file_path()['seo_image']['size'])}}" target="__blank">{{translate('View image')}}</a>
									</div>
								</div>

								<div class="col-12 text-editor-area">
									<div>
										<label for="meta_description" class="form-label">
											{{translate('Meta Description')}} <span class="text-danger"  >*</span>
										</label>
										<textarea class=" form-control text-editor" name="meta_description" rows="10" id="meta_description" placeholder="{{translate('Enter Description')}}" required="">
											@php echo @$seo_section['meta_description'] @endphp</textarea>


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
						</div>

						<div class="border rounded p-3">
							<h6 class="mb-3 fw-bold">
								{{translate('Social Title & Image')}}
							</h6>
							<div class="row g-3">
								<div class="col-lg-6">
									<div>
										<label for="social_title" class="form-label">
											{{translate('Social Title')}}
										</label>
										<input type="text" name="social_title" id="social_title"  value="{{@$seo_section['social_title']}}"  required class="form-control"  placeholder="Enter title">
									</div>
								</div>

								<div class="col-lg-6">
									<label for="social_image" class="form-label">
										{{translate('Social SEO Image')}}
									</label>
									<input type="file" name="social_image" id="social_image" class="form-control">

									<div class="form-text">{{translate('Supported File : jpg,png,jpeg and size')}} {{file_path()['seo_image']['size']}} {{translate('pixels')}} . <a href="{{show_image(file_path()['seo_image']['path'].'/'.@$seo_section['social_image'],file_path()['seo_image']['size'])}}" target="__blank">{{translate('View image')}}</a></div>
								</div>

								<div class="col-12">
									<div >
										<label for="social_description" class="form-label">
											{{translate('Social Description')}} <span class="text-danger"  >*</span>
										</label>
										<textarea rows="3" name="social_description" id="social_description" class="form-control" placeholder='Enter Social Description' required>{{@$seo_section['social_description']}}</textarea>
									</div>
								</div>
							</div>
						</div>

						<div class="text-start">
							<button type="submit"
							class="btn btn-success waves ripple-light">
								{{translate('Update')}}
							</button>
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
@push('script-push')
<script>
	(function($){
       	"use strict";
		$(".select2").select2({
			placeholder:"{{translate('Keyword')}}",
			tags:true,
			tokenSeparators: [',', ' ']
		})
	})(jQuery);
</script>
@endpush
