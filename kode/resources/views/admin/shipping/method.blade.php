@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{$title}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Dashboard')}}

                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Shipping Methods')}}
                    </li>
                </ol>
            </div>
        </div>


		<div class="card">


			<div class="card-header border-0">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
						<h5 class="card-title mb-0">
							{{translate('Shipping Method List')}}
						</h5>
					</div>
					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<button type="button" class="btn btn-success add-btn w-100 waves ripple-light"
							data-bs-toggle="modal" id="create-btn" data-bs-target="#createshippingmethod"><i
								class="ri-add-line align-bottom me-1"></i>
							  {{translate('Add Method')}}
						   </button>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body border border-dashed border-end-0 border-start-0">
				<form action="{{route('admin.shipping.method.index')}}" method="get">
					<div class="row g-3">
						<div class="col-xl-4 col-lg-5">
							<div class="search-box">
								<input type="text" value="{{request()->input('search')}}" name="search" class="form-control search"
									placeholder="{{translate('Search by name')}}">
								<i class="ri-search-line search-icon"></i>
							</div>
						</div>

						<div class="col-xl-2 col-lg-2 col-sm-4 col-6">
							<div>
								<button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
										class="ri-equalizer-fill me-1 align-bottom"></i>
									{{translate('Search')}}
								</button>
							</div>
						</div>

						<div class="col-xl-2 col-lg-2 col-sm-4 col-6">
							<div>
								<a href="{{route('admin.shipping.method.index')}}" class="btn btn-danger w-100 waves ripple-light">
									<i class="ri-refresh-line me-1 align-bottom"></i>
									{{translate('Reset')}}
								</a>
							</div>
						</div>

					</div>
				</form>
			</div>

			<div class="card-body">
				<div class="table-responsive table-card">
					<table class="table table-hover table-centered align-middle table-nowrap">
						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate('Name')}}
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
							@forelse($shippingMethods as $shippingMethod)
								<tr>
									<td class="fw-medium">
									      {{$loop->iteration}}
									</td>

									<td>
										<div class="d-flex align-items-center">
											<div class="flex-shrink-0 me-2">
												<img src="{{show_image(file_path()['shipping_method']['path'].'/'.$shippingMethod->image ,file_path()['shipping_method']['size'])}}" alt="{{$shippingMethod->name}}"
													class=" rounded avatar-sm img-thumbnail">
											</div>
											<div class="flex-grow-1">
												{{$shippingMethod->name}}
											</div>
										</div>
									</td>

									<td>
										@if($shippingMethod->status == 1)
									     	<span class="badge badge-soft-success">{{translate('Active')}}</span>
										@else
											<span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
										@endif
									</td>

									<td>
										<div class="hstack justify-content-center gap-3">
											@if(permission_check('update_settings'))
												<a title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top"  class="link-warning fs-18  method" data-bs-toggle="modal" data-bs-target="#updateshippingmethod" href="javascript:void(0)" data-id="{{$shippingMethod->id}}" data-name="{{$shippingMethod->name}}" data-status="{{$shippingMethod->status}}"><i class="ri-pencil-fill"></i>
												</a>
												<a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top"  data-href="{{route('admin.shipping.method.delete',$shippingMethod->id)}}" class="delete-item fs-18 link-danger">
												<i class="ri-delete-bin-line"></i></a>
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
			</div>
		</div>
	</div>

</div>
<div class="modal fade" id="createshippingmethod" tabindex="-1" aria-labelledby="createshippingmethod" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Add New Shipping Method')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form action="{{route('admin.shipping.method.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
	            <div class="modal-body">
	            	<div class="p-2">
		                <div>
							<div class="mb-3">
								<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
								<input value="{{old('name')}}" type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter name')}}" required>
							</div>

							<div class="mb-3">
								<label for="image" class="form-label">{{translate('Image')}} <span class="text-danger">*</span></label>
								<input type="file" class="form-control" id="image" name="image" required>
							</div>

							<div class="mb-3">
								<label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
								<select class="form-select" name="status" id="status" required>
									<option {{old('status') == 1 ? "selected" : "" }}    value="1">{{translate('Active')}}</option>
									<option {{old('status') == 2 ? "selected" : "" }}    value="2">{{translate('Inactive')}}</option>
								</select>
							</div>
						</div>
	            	</div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-md btn-primary">{{translate('Submit')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateshippingmethod" tabindex="-1" aria-labelledby="updateshippingmethod" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Update Shipping Method')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.shipping.method.update')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
					<div class="p-2">
						<div class="mb-3">
							<label for="uname" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="uname" name="name" placeholder="{{translate('Enter name')}}" required>
						</div>

						<div class="mb-3">
							<label for="uimage" class="form-label">{{translate('Image')}} <span class="text-danger">*</span></label>
							<input type="file" class="form-control" id="uimage" name="image">
						</div>
						<div class="mb-3">
							<label for="ustatus" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
							<select class="form-select" name="status" id="ustatus" required>
								<option value="1">{{translate('Active')}}</option>
								<option value="2">{{translate('Inactive')}}</option>
							</select>
						</div>
	            	</div>
	            </div>

	            <div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger border-0 text-light" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-md btn-primary">{{translate('Submit')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>
@include('admin.modal.delete_modal')
@endsection

@push('script-push')
<script>
	(function($){
		"use strict";
		$('.method').on('click', function(){
			var modal = $('#updateshippingmethod');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
