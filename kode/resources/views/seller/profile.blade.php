@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">
		<div class="position-relative mx-n4 mt-n4">
			<div class="profile-wid-bg profile-setting-img">
				<img src="{{show_image(file_path()['profile']['seller']['path']."/".$seller->image , file_path()['profile']['seller']['size']) }}" class="profile-wid-img" alt="{{$seller->image}}">
			</div>
		</div>

		<div class="row">
			<div class="col-xxl-3 col-xl-4">
				<div class="card mt-n5">
					<div class="card-body p-4">
						<div class="text-center">
							<div class="profile-user position-relative d-inline-block mx-auto  mb-4">
								<img src="{{show_image(file_path()['profile']['seller']['path']."/".$seller->image,file_path()['profile']['seller']['size']) }}"
									class="rounded-circle avatar-xl img-thumbnail user-profile-image"
									alt="{{$seller->image}}">
							</div>

							<h5 class="fs-16 mb-1">
								{{$seller->name ? $seller->name :'N/A'}}
							</h5>

							<p class="text-muted mb-0">
								{{(short_amount($seller->balance))}}
							</p>

							<ul class="ps-0 mt-3 mb-0">
								<li class="d-flex justify-content-between align-items-center  flex-wrap gap-2 border-bottom mb-2 pb-2 px-2">
                                    <span class="fw-bold">{{translate('Username')}}</span>
									<span class="font-weight-bold">{{$seller->username?? 'N/A'}}</span>
								</li>

								<li class=" d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom mb-2 pb-2 px-2">
                                        <span class="fw-bold">
                                            {{translate('Phone')}}
                                        </span>
                                    <span class="font-weight-bold">{{$seller->phone ? $seller->phone :"N/A"}}</span>
								</li>

								<li class=" d-flex justify-content-between align-items-center flex-wrap gap-2 px-2">
                                    <span class="fw-bold">
                                        {{translate('Email')}}
                                    </span>
                                    <span class="font-weight-bold">{{$seller->email}}</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xxl-9 col-xl-8">
				<div class="card mt-xxl-n5">
					<div class="card-header">
						<ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0"
							role="tablist">
							<li class="nav-item">
								<a class="nav-link text-body active" data-bs-toggle="tab"
									href="#personalDetails" role="tab">
									<i class="fas fa-home"></i>

									{{translate('Personal Details')}}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-body" data-bs-toggle="tab" href="#changePassword"
									role="tab">
									<i class="far fa-user"></i>

									{{translate('Change Password')}}
								</a>
							</li>

						</ul>
					</div>

					<div class="card-body p-4">
						<div class="tab-content">
							<div class="tab-pane active" id="personalDetails" role="tabpanel">
								<form action="{{route('seller.profile.update')}}"  method="POST"  enctype="multipart/form-data">
									@csrf
									<div class="row g-4">
										<div class="col-lg-6">
											<div>
												<label for="name" class="form-label">
													{{translate("Name")}} <span class="text-danger" >*</span>
												</label>
												<input value="{{$seller->name}}" name="name" type="text" class="form-control" id="name"
													placeholder="{{translate('Enter your name')}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="phone" class="form-label">
													{{translate("Phone")}} <span class="text-danger" >*</span>
												</label>
												<input type="text" name="phone" class="form-control" id="phone"
													placeholder="{{translate('Enter Phone')}}" value="{{$seller->phone}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="emailInput" class="form-label">
													{{translate('Email
													Address')}} <span class="text-danger" >*</span>
												</label>
												<input type="email" name="email" class="form-control" id="emailInput"
													placeholder="{{translate("Enter your email")}}"
													value="{{$seller->email}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="formFile" class="form-label">
													{{translate('Image')}}  <span class="text-danger">
														({{file_path()['profile']['admin']['size'] }})
													</span>
												</label>

												<input data-size ="{{file_path()['profile']['admin']['size']}}" type="file" id="formFile" class="preview form-control w-100"
													name="image">
											</div>

											<div id="image-preview-section">

											</div>
										</div>

										<div class="col-lg-6">
											 <div>
												<label for="address" class="form-label">{{translate('Address')}} <span class="text-danger">*</span></label>
												<input type="text" name="address" id="address" class="form-control" value="{{@$seller->address->address}}" placeholder="{{translate('Enter Address')}}" required>
											 </div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="city" class="form-label">{{translate('City')}} <span class="text-danger">*</span></label>
												<input type="text" name="city" id="city" class="form-control" value="{{@$seller->address->city}}" placeholder="{{translate('Enter City')}}" required>
											</div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="state" class="form-label">{{translate('State')}} <span class="text-danger">*</span></label>
												<input type="text" name="state" id="state" class="form-control" value="{{@$seller->address->state}}" placeholder="{{translate('Enter State')}}" required>
											</div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="zip" class="form-label">{{translate('Zip')}} <span class="text-danger">*</span></label>
												<input type="text" name="zip" id="zip" class="form-control" value="{{@$seller->address->zip}}" placeholder="{{translate('Enter Zip')}}" required>
											</div>
										</div>

										<div class="col-12">
											<div class="hstack gap-2 justify-content-start">
												<button type="submit"
													class="btn btn-success">
													{{translate('Update')}}
												</button>
											</div>
										</div>
									</div>
								</form>
							</div>

							<div class="tab-pane" id="changePassword" role="tabpanel">
								<form method="post"  action="{{route('seller.password.update')}}">
									@csrf
									<div class="row g-4">
										<div class="col-xl-12 col-lg-6">
											<div>
												<label for="oldpasswordInput" class="form-label">
													 {{translate("Old
													 Password")}}

													 <span class="text-danger" >*</span>

													</label>
												<input type="password" required value="{{old('current_password')}}" name="current_password" class="form-control"
													id="oldpasswordInput"
													placeholder='{{translate("Enter current password")}}'>
											</div>
										</div>

										<div class="col-xl-6 col-lg-6">
											<div>
												<label for="newpasswordInput" class="form-label">
													{{translate('New
													Password')}}  <span class="text-danger" >*</span>
												</label>
												<input type="password" name="password" value="{{old('password')}}" class="form-control"
													id="newpasswordInput" placeholder="{{translate('Enter new password')}}">
											</div>
										</div>

										<div class="col-xl-6 col-lg-6">
											<div>
												<label for="confirmpasswordInput" class="form-label">
													{{translate('Confirm
													Password')}}  <span  class="text-danger"  >*</span>
													</label>
												<input type="password" name="password_confirmation" class="form-control"
													id="confirmpasswordInput"
													placeholder='{{translate("Confirm password")}}'>
											</div>
										</div>

										<div class="col-12">
											<div class="text-start">
												<button type="submit" class="btn btn-success">
													{{translate('Change
													Password')}}
												</button>
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
	</div>
</div>
@endsection






