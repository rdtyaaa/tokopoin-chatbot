@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">
		<div class="position-relative mx-n4 mt-n4">
			<div class="profile-wid-bg profile-setting-img">
				<img src="{{show_image(file_path()['profile']['admin']['path'].'/'.$admin->image ,file_path()['profile']['admin']['size']) }}" class="profile-wid-img" alt="{{$admin->image}}">
			</div>
		</div>

		<div class="row">
			<div class="col-xxl-3">
				<div class="card mt-n5">
					<div class="card-body p-4">
						<div class="text-center">
							<div class="profile-user position-relative d-inline-block mx-auto  mb-4">
								<img src="{{show_image(file_path()['profile']['admin']['path'].'/'.$admin->image ,file_path()['profile']['admin']['size']) }}"
									class="rounded-circle avatar-xl img-thumbnail user-profile-image"
									alt="{{$admin->image}}">
							</div>
							<h5 class="fs-16 mb-1">
								{{$admin->name}}
							</h5>
							<p class="text-muted mb-0">
								{{$admin->role ? $admin->role->name : "N/A"}}
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xxl-9">
				<div class="card mt-xxl-n5">
					<div class="card-header">
						<ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0"
							role="tablist">
							<li class="nav-item">
								<a class="nav-link text-body active" data-bs-toggle="tab"
									href="#profile" role="tab">
									<i class="fas fa-home"></i>
									{{translate('Personal Details')}}
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link text-body" data-bs-toggle="tab" href="#password"
									role="tab">
									<i class="far fa-user"></i>
									{{translate('Change Password')}}
								</a>
							</li>

						</ul>
					</div>
					<div class="card-body p-4">
						<div class="tab-content">
							<div class="tab-pane active" id="profile" role="tabpanel">
								<form action="{{route('admin.profile.update')}}"  method="POST"  enctype="multipart/form-data">
									@csrf
									<div class="row">
										<div class="col-lg-6">
											<div class="mb-3">
												<label for="name" class="form-label">
													{{translate("Name")}} <span class="text-danger" >*</span>
												</label>
												<input value="{{$admin->name}}" name="name" type="text" class="form-control" id="name"
													placeholder="{{translate('Enter your name')}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div class="mb-3">
												<label for="username" class="form-label">
													{{translate("Username")}} <span class="text-danger" >*</span>
												</label>
												<input type="text" name="user_name" class="form-control" id="username"
													placeholder="{{translate('Enter your username')}}" value="{{$admin->user_name}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div class="mb-3">
												<label for="phone" class="form-label">
													{{translate('Phone
													Number')}}
												</label>
												<input type="text" class="form-control"
													id="phone" name="phone"
													placeholder="{{translate('Phone Number')}}"
													value="{{$admin->phone}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div class="mb-3">
												<label for="email" class="form-label">
													{{translate('Email
													Address')}} <span class="text-danger" >*</span>
												</label>
												<input type="email" name="email" class="form-control" id="email"
													placeholder="{{translate('Email')}}"
													value="{{$admin->email}}">
											</div>
										</div>

										<div class="col-lg-6 mb-2">
											<div class="mb-3">
												<label for="image" class="form-label">
													{{translate('Image')}}  <span class="text-danger">
														({{file_path()['profile']['admin']['size'] }})
													</span>
												</label>
												<input data-size ="{{file_path()['profile']['admin']['size']}}" type="file" class="preview form-control w-100"
													name="image" id="image">
											</div>

											<div id="image-preview-section">

											</div>
										</div>


										<div class="col-lg-12">
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

							<div class="tab-pane" id="password" role="tabpanel">
								<form method="post"  action="{{route('admin.password.update')}}">
									@csrf
									<div class="row g-4">
										<div class="col-lg-12">
											<div>
												<label for="oldPassword" class="form-label">
													 {{translate("Old
													 Password")}}

													 <span class="text-danger" >*</span>

													</label>
												<input type="password" required value="{{old('current_password')}}" name="current_password" class="form-control"
													id="oldPassword"
													placeholder="{{translate('Enter current password')}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="password" class="form-label">
													{{translate('New Password')}}  <span class="text-danger" >*</span>
												</label>
												<input type="password" name="password" value="{{old('password')}}" class="form-control"
													id="password" placeholder="{{translate('New Password')}}">
											</div>
										</div>

										<div class="col-lg-6">
											<div>
												<label for="comfirmPassword" class="form-label">
													{{translate('Confirm
													Password')}}  <span  class="text-danger"  >*</span>
													</label>
												<input type="password" name="password_confirmation" class="form-control"
													id="comfirmPassword"
													placeholder="{{translate('Confirm password')}}">
											</div>
										</div>

										<div class="col-lg-12">
											<div class="text-end">
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






