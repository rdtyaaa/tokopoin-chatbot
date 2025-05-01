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
                            {{translate('Notification Templates')}}
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
									{{translate('Notification Template List')}}
								</h5>
							</div>
						</div>
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive table-card">
						<table class="table table-hover table-centered align-middle table-nowrap mb-0">
							<thead class="text-muted table-light">
								<tr>
									<th scope="col">#</th>
									<th scope="col">
										{{translate(' Name')}}
									</th>
									<th scope="col">
										{{translate('Subject')}}
									</th>
									<th scope="col">
										{{translate('Status')}}
									</th>
									<th scope="col">
										{{translate('Action')}}
									</th>
								</tr>
							</thead>

							<tbody>
								@forelse ($emailTemplates as $emailTemplate)
									<tr>
										<td class="fw-medium">
										{{$loop->iteration}}
										</td>

										<td>
											{{ucfirst($emailTemplate->name)}}
										</td>

										<td>
											{{ucfirst($emailTemplate->subject)}}
										</td>

										<td>
											@if($emailTemplate->status == 1)
											    <span class="badge badge-soft-success">{{translate('Active')}}</span>
											@else
												<span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
											@endif
										</td>

										<td>
											<div class="hstack justify-content-center gap-3">
												@if(permission_check('update_configuration'))
													<a href="{{route('admin.notification.templates.edit', $emailTemplate->id)}}" class="pointer
														link-warning fs-18
														">
														<i class="ri-pencil-fill"></i>
													</a>
												@endif
											</div>
										</td>
									</tr>
								@empty
								<tr>
									<td class="border-bottom-0" colspan="100">
                                            @include('admin.partials.not_found')
									</td>
								</tr>
								@endforelse
							</tbody>
						</table>
					</div>

					<div class="pagination-wrapper d-flex justify-content-end mt-4">
                        {{$emailTemplates->appends(request()->all())->links()}}
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection

