@extends('admin.layouts.app')

@section('main_content')

<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Update banner")}}
            </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.dashboard')}}">
                            {{translate("Home")}}
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{route('admin.frontend.section.banner')}}">
                            {{translate("Banners")}}
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{translate("Update banners")}}
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
								{{translate('Update Banner')}}
							</h5>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body">
				<form autocomplete="off" class="needs-validation"  action="{{ route('admin.frontend.section.banner.update') }}" method="POST" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="id" value="{{$banner->id}}">

					<div class="row g-3">
						<div class="col-xl-6 col-lg-6">
							<div>
								<label for="serial_id" class="form-label">  {{translate('Serial Id')}}
								<span  class="text-danger">*</span>
								</label>
								<input type="text" value="{{$banner->serial_id}}" name="serial_id" id="serial_id" placeholder="{{translate('Enter serial id')}}" class="form-control" required>
							</div>
						</div>

						<div class="col-xl-6 col-lg-6">
							<div >
								<label for="btn_url" class="form-label">  {{translate('Button URL')}}

								</label>
								<input type="text" value="{{$banner->btn_url}}" name="btn_url" id="btn_url" placeholder="Button Url" class="form-control" >
							</div>
						</div>

						<div class="col-lg-6">
							<div >
								<label for="banner_image" class="form-label">  {{translate('Banner Image')}}
									<span  class="text-danger">* </span>
								</label>
								<input type="file" name="banner_image" id="banner_image" class="form-control">
								<div class="text-danger mt-2">{{translate('Supported File :jpg, png, jpeg and size')}} {{file_path()['banner_image']['size']}} {{translate('pixels')}}</div>
								<div id="image-preview-section">
									<div id="banner-image" class="featured_img-item mt-2">
										<img class="w-50" src="{{show_image(file_path()['banner_image']['path'].'/'.$banner->bg_image ,file_path()['banner_image']['size'])}}" alt="{{$banner->bg_image}}">
									</div>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<label for="status" class="form-label">{{translate('Status') }}<span class="text-danger">*</span></label>
							<select class="form-select" id="status" name="status" required>
								<option  value="">{{translate('--Select One--')}}</option>
								<option  {{$banner->status== 1? 'selected': ''}}    value="1">{{translate('Active')}}</option>
								<option {{$banner->status== 0? 'selected': ''}}     value="0">{{translate('Inactive')}}</option>
							</select>
						</div>

						<div class="col-12">
							<div class="text-start">
								<button type="submit" class="btn btn-success">
									{{translate('Update')}}
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







