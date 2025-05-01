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
							{{translate('System Information')}}
						</li>
					</ol>
				</div>

			</div>

			<div class="card">
				<div class="card-header border-bottom-dashed">
					<div class="d-flex align-items-center">
						<h5 class="card-title mb-0 flex-grow-1">
							{{translate('Server information')}}
						</h5>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive table-card ">
						<table class="table table-hover align-middle table-nowrap" >
							<thead class="text-muted table-light">
								<tr class="text-uppercase">
									<th>{{ translate('Name') }}</th>
									<th>{{ translate('Value') }}</th>
								</tr>
							</thead>

							<tbody class="list form-check-all">

								<tr>
									<td>
										{{translate("Php versions")}}
									</td>
									<td>{{ phpversion() }}</td>
								</tr>
								<tr>
									<td>
										{{translate("Larvel versions")}}
									</td>
									<td>
										{{Arr::get($systemInfo,'laravel_version')}}
									</td>
								</tr>

								<tr>
									<td>
										{{translate("Http host")}}
									</td>
									<td>
										{{Arr::get($systemInfo['server_detail'],'HTTP_HOST')}}
									</td>
								</tr>

							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-header border-bottom-dashed">
					<div class="d-flex align-items-center">
						<h5 class="card-title mb-0 flex-grow-1">
							{{ translate('php.ini Config') }}
						</h5>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive table-card">
						<table class="table table-hover align-middle table-nowrap" >
							<thead class="text-muted table-light">
								<tr class="text-uppercase">
									<th>{{ translate('Config Name') }}</th>
									<th>{{ translate('Current') }}</th>
									<th>{{ translate('Recommended') }}</th>
									<th>{{ translate('Status') }}</th>
								</tr>
							</thead>

							<tbody class="list form-check-all">

								<tr>
									<td>
										{{translate("File uploads")}}
									</td>
									<td>
										@if(ini_get('file_uploads') == 1)
										{{translate("On")}}
										@else
										{{translate("Off")}}

										@endif
									</td>
									<td> {{translate("On")}}</td>
									<td>
										@if (ini_get('file_uploads') == 1)
										<i class="las la-check text-success  fs-4"></i>
										@else
										<i class="las la-times text-danger  fs-4"></i>
										@endif
									</td>
								</tr>

								<tr>
									<td>
										{{translate("Max File Uploads")}}
									</td>
									<td>
										{{ ini_get('max_file_uploads') }}
									</td>
									<td>20+</td>
									<td>
										@if (ini_get('max_file_uploads') >= 20)
										<i class="las la-check text-success  fs-4"></i>
										@else
											<i class="las la-times text-danger fs-4"></i>
										@endif
									</td>
								</tr>

								<tr>
									<td>
										{{translate("Allow url Fopen")}}
									</td>
									<td>
										@if(ini_get('allow_url_fopen') == 1)
										{{translate("On")}}
										@else
											{{translate("Off")}}
										@endif
									</td>
									<td> {{translate("On")}}</td>
									<td>
										@if (ini_get('allow_url_fopen') == 1)
										<i class="las la-check text-success fs-4"></i>
										@else
										<i class="las la-times text-danger fs-4"></i>
										@endif
									</td>
								</tr>

								<tr>
									<td>
										{{translate("Max Execution time")}}
									</td>
									<td>
										@if(ini_get('max_execution_time') == '-1')
										{{translate("Unlimited")}}

										@else
										{{ ini_get('max_execution_time') }}
										@endif
									</td>
									<td>600+</td>
									<td>
										@if (ini_get('max_execution_time') == -1 || ini_get('max_execution_time') >= 600)
										<i class="las la-check text-success fs-4"></i>
										@else
										<i class="las la-times text-danger fs-4"></i>
										@endif
									</td>
								</tr>

								<tr>
									<td>
										{{translate("Max Input time")}}
									</td>
									<td>
										@if(ini_get('max_input_time') == '-1')
											{{translate("Unlimited")}}
										@else
											{{ ini_get('max_input_time') }}
										@endif
									</td>
									<td>120+</td>
									<td>
										@if (ini_get('max_input_time') == -1 || ini_get('max_input_time') >= 120)
										<i class="las la-check text-success fs-4"></i>
										@else
										<i class="las la-times text-danger fs-4"></i>
										@endif
									</td>
								</tr>

								<tr>
									<td>
										{{translate("Max Input vars")}}
									</td>
									<td>
										{{ ini_get('max_input_vars') }}
									</td>
									<td>1000+</td>
									<td>
										@if (ini_get('max_input_vars') >= 1000)
										<i class="las la-check text-success fs-4"></i>
										@else
										<i class="las la-times text-danger fs-4"></i>
										@endif
									</td>
								</tr>

								<tr>
									<td>
										{{translate("Memory limit")}}

									</td>
									<td>
										@if(ini_get('memory_limit') == '-1')
											{{translate("Unlimited")}}
										@else
											{{ ini_get('memory_limit') }}
										@endif
									</td>
									<td>256M+</td>
									<td>
										@php
											$memory_limit = ini_get('memory_limit');
											if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
												if ($matches[2] == 'G') {
													$memory_limit = $matches[1] * 1024 * 1024 * 1024;
												} else if ($matches[2] == 'M') {
													$memory_limit = $matches[1] * 1024 * 1024;
												} else if ($matches[2] == 'K') {
													$memory_limit = $matches[1] * 1024;
												}
											}
										@endphp
										@if (ini_get('memory_limit') == -1 || $memory_limit >= (256 * 1024 * 1024))
										<i class="las la-check text-success fs-4"></i>
										@else
										<i class="las la-times text-danger fs-4"></i>
										@endif
									</td>
								</tr>

							</tbody>
						</table>
					</div>

				</div>
			</div>

			<div class="card">
				<div class="card-header border-bottom-dashed">
					<div class="d-flex align-items-center">
						<h5 class="card-title mb-0 flex-grow-1">
							{{translate('Extensions')}}
						</h5>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive table-card ">
						<table class="table table-hover align-middle table-nowrap" >
							<thead class="text-muted table-light">
								<tr class="text-uppercase">
									<th>{{ translate('Extension Name') }}</th>
									<th>{{ translate('Status') }}</th>
								</tr>
							</thead>
								@php
									$loadedExtensions   = get_loaded_extensions();
									$requiredExtensions = Arr::get(config("installer.requirements"),'php',[]);


								@endphp
							<tbody class="list form-check-all">

								@foreach ($requiredExtensions as $extension)
									<tr>
										<td>{{ $extension }}</td>
										<td>
											@if(in_array($extension, $loadedExtensions))
											<i class="las la-check text-success fs-4"></i>
											@else
												<i class="las la-times text-danger fs-4"></i>
											@endif
										</td>
									</tr>
								@endforeach

							</tbody>
						</table>
					</div>


				</div>
			</div>

			<div class="card">
				<div class="card-header border-bottom-dashed">
					<div class="d-flex align-items-center">
						<h5 class="card-title mb-0 flex-grow-1">
							{{ translate('File & Folder Permissions') }}
						</h5>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive table-card ">
						<table class="table table-hover align-middle table-nowrap" >
							<thead class="text-muted table-light">
								<tr class="text-uppercase">
									<th>{{ translate('File or Folder') }}</th>
									<th>{{ translate('Status') }}</th>
								</tr>
							</thead>
							@php
								$requiredPermissions =  array_keys(config("installer.permissions"));
							@endphp
							<tbody class="list form-check-all">

								@foreach ($requiredPermissions as $filePath)
									<tr>
										<td>{{ $filePath }}</td>

										<td>
											@if(is_writable(base_path($filePath)))
											<i class="las la-check text-success fs-4"></i>
											@else
											<i class="las la-times text-danger fs-4"></i>
											@endif
										</td>
									</tr>
								@endforeach

							</tbody>
						</table>
					</div>


				</div>
			</div>

		</div>
	</div>
@endsection
