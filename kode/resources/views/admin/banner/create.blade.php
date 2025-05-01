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
                        <li class="breadcrumb-item"><a href="{{route('admin.frontend.section.banner')}}">
                            {{translate('Banners')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate('Create')}}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{translate('Create Banner')}}
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="card-body">
					<form action="{{ route('admin.frontend.section.banner.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="row g-3">
							<div class="col-lg-6">
								<label for="serial_id" class="form-label">{{translate('Serial ID')}} <span class="text-danger" >*</span> </label>
								<input type="number" name="serial_id" id="serial_id" value="{{old('serial_id')}}" class="form-control" placeholder="{{translate('Enter serial id')}}" required="">
							</div>


							<div class="col-lg-6 details">
								<label for="btn_url" class="form-label">{{translate('Button URL')}}  </label>
								<input type="text" name="btn_url" id="btn_url" value="{{old('btn_url')}}" class="form-control" placeholder="button url">
							</div>

							<div class="col-lg-6">
								<label for="banner_image" class="form-label w-100">{{translate('Banner image') }}
								 <span  class="text-danger">* </span>
								</label>
								<input type="file" name="banner_image" id="banner_image" class="form-control">
								<div class="text-danger mt-2">{{translate('Supported File :jpg, png, jpeg and size')}} {{file_path()['banner_image']['size']}} {{translate('pixels')}}</div>
							</div>

							<div class="col-lg-6">
								<label for="status" class="form-label">{{translate('Status') }} <span class="text-danger" >*</span> </label>
								<select class="form-select" id="status" name="status" required>
									<option  value="">{{translate('Select One')}}</option>
									<option  {{old('status') == 1? 'selected': ''}}    value="0">{{translate('Inactive')}}</option>
									<option {{old('status') == 0? 'selected': ''}}     value="1">{{translate('Active')}}</option>
								</select>
							</div>
						</div>

						<div class="mt-3">
							<button type="submit" class="btn btn-success btn-xl fs-6 text-light">{{translate('Submit')}}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
</div>
@endsection

@push('scriptpush')
<script>
	(function($){

     "use strict";

	 $(".details").hide();

	 $('#banner_type').on('change', function(){

        $('#size-message').html(``)
		var value = $(this).val();
		if (value == 'banner') {
            $('#size-message').append(`
                <div id="emailHelp" class="form-text">{{translate('Image Size Should Be') <span id="size">810X420</span>
                </div>
            `)
			$(".details").show();
		} else{
            $('#size-message').append(`
            <div id="emailHelp" class="form-text">{{translate('Image Size Should Be') <span id="size">340X200</span>
            </div>
           `)
			$(".details").hide();
		}
	 })

	})(jQuery);
</script>
@endpush
