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

                    <li class="breadcrumb-item active">
                        {{translate('Global Template')}}
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
										{{translate('SMS Template Short Code')}}
									</h5>
								</div>
							</div>
						</div>
					</div>

					<div class="card-body">
						<form action="{{route('admin.sms.global.template.store')}}" method="POST">
							@csrf
								<div class="row g-3 mb-3 ">
									<div class="col-lg-12">
										<label for="message" class="form-label"> {{translate('Message')}} <span class="text-danger">*</span></label>
										<textarea class="form-control" name="sms_template" rows="5" id="sms_template" required>{{site_settings('sms_template')}}</textarea>
									</div>
									<div class="col-lg-12">
										<button type="submit" class="btn btn-success fs-6">{{translate('Submit')}}</button>
									</div>
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
										{{translate('Template')}}
									</h5>
								</div>
							</div>
						</div>
					</div>
					<div class="card-body">
						<div>
							<div >
								<h6>{{translate('SMS template short code')}}</h6>
							</div>
							<div class="mt-3">
								<div class="text-center d-flex flex-column gap-2">
									<div class="p-2 border d-flex align-items-center justify-content-between  ">
										<div class="me-2">
											<h6 class="mb-0">{{translate('Username')}}</h6>
										</div>
										<p class="mb-0">@{{username}}</p>
									</div>

									<div class="p-2 border d-flex align-items-center justify-content-between ">
										<div class="me-2">
											<h6 class="mb-0">{{translate('Mail Body')}}</h6>
										</div>
										<p class="mb-0">@{{message}}</p>
									</div>
								</div>
							</div>
						</div>
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

