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
                    <li class="breadcrumb-item"><a href="{{route('admin.customer.index')}}">
                        {{translate('Customers')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Create")}}
                    </li>
                </ol>
            </div>

        </div>

		<div class="row">

			<div class="col-12">
				<div class="card">
					<div class="card-header border-bottom-dashed">
						<div class="d-flex align-items-center">
							<h5 class="card-title mb-0 flex-grow-1">
								{{translate('Create Customer')}}
							</h5>
						</div>
					</div>

				     <div class="card-body">
							<div>
								<form action="{{route('admin.customer.store')}}" method="POST" enctype="multipart/form-data">
									@csrf
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{old('name')}}" required>
                                        </div>
    
                                        <div class="col-6">
                                            <label for="email" class="form-label">{{translate('Email')}} <span class="text-danger">*</span></label>
                                            <input type="text" name="email" id="email" class="form-control" value="{{old('email')}}" required>
                                        </div>

                                        <div class="col-6">
											<label for="password" class="form-label">{{translate('Password')}} <span class="text-danger">*</span></label>
											<input type="text" required name="password" id="password" class="form-control" value="{{old('password')}}" placeholder="{{translate('Enter password')}}" >
										</div>

    
                                        <div class="col-6">
                                            <label for="address" class="form-label">{{translate('Address')}} <span class="text-danger">*</span></label>
                                            <input type="text" name="address" id="address" class="form-control" value="" placeholder="{{translate('Enter Address')}}" required>
                                        </div>
    
                                        <div class="col-6">
                                            <label for="city" class="form-label">{{translate('City')}} <span class="text-danger">*</span></label>
                                            <input type="text" name="city" id="city" class="form-control" value="" placeholder="{{translate('Enter City')}}" required>
                                        </div>
    
                                        <div class="col-6">
                                            <label for="country_id" class="form-label">{{translate('Country')}} <span class="text-danger">*</span></label>
    
    
                                            <select class="select2" name="country_id" id="country_id">
    
                                                @foreach ($countries as $country )
    
                                                  <option {{$country->id == old('country_id') ? "selected" :"" }} value="{{$country->id}}"> {{$country->name}}</option>
    
                                                @endforeach
                                            </select>
    
                                        </div>
    
                                        <div class="col-6">
                                            <label for="state" class="form-label">{{translate('State')}} <span class="text-danger">*</span></label>
                                            <input type="text" name="state" id="state" class="form-control" value="" placeholder="{{translate('Enter State')}}" required>
                                        </div>
    
                                        <div class="col-6">
                                            <label for="zip" class="form-label">{{translate('Zip')}} <span class="text-danger">*</span></label>
                                            <input type="text" name="zip" id="zip" class="form-control" value="" placeholder="{{translate('Enter Zip')}}" required>
                                        </div>

                                        <div class="col-6">
											<label for="image" class="form-label">{{translate('Image')}} </label>
											<input type="file" name="image" id="image" class="form-control" placeholder="{{translate('Enter Zip')}}" >
										</div>
    
                                    </div>

									<div class="mt-4">
										<button type="submit" class="btn btn-md btn-success btn-xl px-4 fs-6 text-light waves ripple-light">{{translate('Update')}}</button>
									</div>
								</form>
							</div>
					 </div>
				</div>
			</div>
		</div>
    </div>
</div>



@endsection
@push('script-push')
<script>
	"use strict";

    $(".select2").select2({
		placeholder:"{{translate('Select item')}}",
	})

</script>

@endpush