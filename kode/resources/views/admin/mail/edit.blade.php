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
                    <li class="breadcrumb-item"><a href="{{route('admin.mail.configuration')}}">
                        {{translate('Email Gateways')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Update')}}
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
                                {{translate('Update Gateway')}}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
				<form action="{{route('admin.mail.update', $mail->id)}}" method="POST">
					@csrf
						@if($mail->name === "SMTP")
							<div class="row g-3">
								<div class="col-lg-6">
									<label for="driver" class="form-label">{{translate('Driver')}} <span class="text-danger">*</span></label>
									<input type="text" name="driver" id="driver" class="form-control" value="{{@$mail->driver_information->driver}}" placeholder="{{translate('Enter Driver')}}" required>
								</div>

								<div class="col-lg-6">
									<label for="host" class="form-label">{{translate('Host')}} <span class="text-danger">*</span></label>
									<input type="text" name="host" id="host" class="form-control" value="{{@$mail->driver_information->host}}" placeholder="{{translate('Enter Host')}}" required>
								</div>

								<div class="col-lg-6">
									<label for="port" class="form-label">{{translate('Port')}} <span class="text-danger">*</span></label>
									<input type="text" name="port" id="port" class="form-control" value="{{@$mail->driver_information->port}}" placeholder="{{translate('Enter Port')}}" required>
								</div>

								<div class="col-lg-6">
									<label for="encryption" class="form-label">{{translate('Encryption')}} <span class="text-danger">*</span></label>
									<input type="text" name="encryption" id="encryption" class="form-control" value="{{@$mail->driver_information->encryption}}" placeholder="{{translate('Enter Encryption')}}" required>
								</div>

								<div class="col-lg-6">
									<label for="username" class="form-label">{{translate('Username')}} <span class="text-danger">*</span></label>
									<input type="text" name="username" id="username" class="form-control" value="{{@$mail->driver_information->username}}" placeholder="{{translate('Enter Mail Username')}}" required>
								</div>

								<div class="col-lg-6">
									<label for="password" class="form-label">{{translate('Password')}} <span class="text-danger">*</span></label>
									<input type="text" name="password" id="password" class="form-control" value="{{@$mail->driver_information->password}}" placeholder="{{translate('Enter Mail Password')}}" required>
								</div>

								<div class="col-lg-6">
									<label for="from_address" class="form-label">{{translate('From Address')}} <span class="text-danger">*</span></label>
									<input type="text" name="from_address" id="from_address" class="form-control" value="{{@$mail->driver_information->from->address}}" placeholder="{{translate('Enter From Address')}}" required>
								</div>

								<div class="col-lg-6">
									<label for="from_name" class="form-label">{{translate('From Name')}} <span class="text-danger">*</span></label>
									<input type="text" name="from_name" id="from_name" class="form-control" value="{{@$mail->driver_information->from->name}}" placeholder="{{translate('Enter From Name')}}" required>
								</div>

							</div>
						@elseif($mail->name === "SendGrid Api")
							<div class="row g-3">
								<div class="col-12">
									<label for="app_key" class="form-label">{{translate('App Key')}}<span class="text-danger">*</span></label>
									<input type="text" name="app_key" id="app_key" class="form-control" value="{{@$mail->driver_information->app_key}}" placeholder="{{translate('Enter App key')}}" required>
								</div>
							</div>
						@endif

						<div class="text-start mt-3">
							<button type="submit" class="btn btn-success">
								{{translate('Update')}}
							</button>
						</div>
                </form>
            </div>
        </div>
	</div>
</div>
@endsection

