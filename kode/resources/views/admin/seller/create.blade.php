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
                    <li class="breadcrumb-item"><a href="{{route('admin.seller.info.index')}}">
                        {{translate('Sellers')}}
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
								{{translate('Create seller')}}
							</h5>
						</div>
					</div>

				     <div class="card-body">
							<div>
								<form action="{{route('admin.seller.info.store')}}" method="POST" enctype="multipart/form-data">
									@csrf
									<div class="row g-3">

                                        <div class="col-6">
											<label for="username" class="form-label">{{translate('Username')}} <span class="text-danger">*</span></label>
											<input type="text" name="username" id="username" class="form-control" value="{{old("username")}}" required>
										</div>
										<div class="col-6">
											<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
											<input type="text" name="name" id="name" class="form-control" value="{{old("name")}}" required>
										</div>

										<div class="col-6">
											<label for="email" class="form-label">{{translate('Email')}} <span class="text-danger">*</span></label>
											<input type="text" name="email" id="email" class="form-control" value="{{old("email")}}" required>
										</div>

										<div class="col-6">
											<label for="phone" class="form-label">{{translate('Phone')}} <span class="text-danger">*</span></label>
											<input type="text" name="phone" id="phone" class="form-control" value="{{old("phone")}}" required>
										</div>

										<div class="col-6">
											<label for="password" class="form-label">{{translate('Password')}} <span class="text-danger">*</span></label>
											<input type="text" required name="password" id="password" class="form-control" value="{{old('password')}}" placeholder="{{translate('Enter password')}}" >
										</div>

										<div class="col-6">
											<label for="address" class="form-label">{{translate('Address')}}</label>
											<input type="text" name="address" id="address" class="form-control" value="{{old('address')}}" placeholder="{{translate('Enter Address')}}">
										</div>

										<div class="col-6">
											<label for="city" class="form-label">{{translate('City')}}</label>
											<input type="text" name="city" id="city" class="form-control" value="{{old('city')}}" placeholder="{{translate('Enter City')}}">
										</div>

										<div class="col-6">
											<label for="state" class="form-label">{{translate('State')}}</label>
											<input type="text" name="state" id="state" class="form-control" value="{{old('state')}}" placeholder="{{translate('Enter State')}}" >
										</div>

										<div class="col-6">
											<label for="zip" class="form-label">{{translate('Zip')}} </label>
											<input type="text" name="zip" id="zip" class="form-control" value="{{old('zip')}}" placeholder="{{translate('Enter Zip')}}" >
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
