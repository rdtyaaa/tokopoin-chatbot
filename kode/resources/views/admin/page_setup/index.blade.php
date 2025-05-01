
@extends('admin.layouts.app')
@push('style-include')
<link href="{{asset('assets/backend/css/summnernote.css')}}" rel="stylesheet" type="text/css" />
@endpush
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
                            {{translate('Dashboard')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate('Page Setup')}}
                        </li>
                    </ol>
                </div>
            </div>

			<div class="card">
				<div class="card-header border-bottom-dashed">
					<div class="row g-4 align-items-center">
						<div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{translate('Pages List')}}
                            </h5>
						</div>
						@if(permission_check('view_settings'))
							<div class="col-sm-auto">
								<div class="d-flex flex-wrap align-items-start gap-2">
									<button type="button" class="btn btn-success btn-sm add-btn waves ripple-light"
										data-bs-toggle="modal" data-bs-target="#addPage"><i
											class="ri-add-line align-bottom me-1"></i>

											{{translate('Add New Page')}}
									</button>


								</div>
							</div>
						@endif
					</div>
				</div>

				<div class="card-body">
					<div class="table-responsive table-card">
						<table class="table table-hover table-centered align-middle table-nowrap mb-0">
							<thead class="text-muted table-light">
								<tr>
									<th scope="col">#</th>
									<th scope="col">
										{{translate('Name')}}
									</th>
									<th>
										{{translate('Status')}}
									</th>
									<th scope="col">
										{{translate('Action')}}
									</th>
								</tr>
							</thead>

							<tbody>
								@forelse ($pageSetups as $pageSetup)
									<tr>
										<td class="fw-medium">
										   {{$loop->iteration}}
										</td>

										<td>
											{{ucfirst($pageSetup->name)}}
										</td>

										<td>
											<div class="form-check form-switch">
												<input type="checkbox" class="status-update form-check-input"
													data-column="status"
													data-route="{{ route('admin.page.status.update') }}"

													data-status="{{ $pageSetup->status == '1' ? '0':'1'}}"
													data-id="{{$pageSetup->id}}" {{$pageSetup->status == "1" ? 'checked' : ''}}
												id="status-switch-{{$pageSetup->id}}" >
												<label class="form-check-label" for="status-switch-{{$pageSetup->id}}"></label>

											</div>
										</td>

										<td>
											<div class="hstack justify-content-center gap-3">
												@if(permission_check('update_settings'))
													<a  title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.page.edit', [make_slug($pageSetup->name), $pageSetup->id])}}" class="pointer
														link-warning fs-18
														">
														<i class="ri-pencil-fill"></i>
													</a>

													@if($pageSetup->uid != '1dRR-7BkgK045-kV4k')
														<a  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.page.delete',$pageSetup->id)}}" class="delete-item fs-18 link-danger">
															<i class="ri-delete-bin-line"></i></a>
													@endif
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
						{{$pageSetups->links()}}
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="addPage" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPage" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header bg-light p-3">
					<h5 class="modal-title" >{{translate('Add Page')}}
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"
						aria-label="Close" id="close-modal"></button>
				</div>

				<form action="{{route('admin.page.store')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="modal-body">
						<div class="row g-3">
							<div>
								<label for="name" class="form-label">
									{{translate('Name')}} <span class="text-danger"  >*</span>
								</label>
								<input type="text" value="{{old('name')}}" name="name" id="name"   class="form-control" placeholder="Enter name">
							</div>

							<div class="text-editor-area">
								<label for="mail-composer" class="form-label">
									{{translate('Decription')}} <span class="text-danger"  >*</span>
								</label>

								<textarea id="mail-composer" class=" form-control text-editor" name="description" rows="10" placeholder="{{translate('Enter Description')}}" required="">{{old('description')}}</textarea>

								@if( $openAi->status == 1)
                                    <button type="button" class="ai-generator-btn mt-3 ai-modal-btn" >
                                        <span class="ai-icon btn-success waves ripple-light">
                                                <span class="spinner-border d-none" aria-hidden="true"></span>

                                                <i class="ri-robot-line"></i>
                                        </span>

                                        <span class="ai-text">
                                            {{translate('Generate With AI')}}
                                        </span>
                                    </button>
                                @endif
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<div class="hstack gap-2 justify-content-end">
							<button type="button"
								class="btn btn-danger waves ripple-light"
								data-bs-dismiss="modal">

							   {{translate('Close')}}
							</button>
							<button type="submit"
								class="btn btn-success waves ripple-light"
								id="add-btn">

								 {{translate('Add Page')}}
							 </button>
						</div>
					</div>
				</form>
			</div>
		</div>
    </div>
	@include('admin.modal.delete_modal')



@endsection
@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>
@endpush



