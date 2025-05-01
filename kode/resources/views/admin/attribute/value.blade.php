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
                    <li class="breadcrumb-item"><a href="{{route('admin.item.attribute.index')}}">
                        {{translate('Attributes')}}
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
								{{$title}}
							</h5>
						</div>
					</div>

					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<button type="button" class="btn btn-primary add-btn waves ripple-light"
								data-bs-toggle="modal" id="create-btn" data-bs-target="#createattributevalue"><i
									class="ri-add-line align-bottom me-1"></i>
									{{translate("Add Attribute Value")}}
							</button>
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
									{{translate("Attribute Value")}}
								</th>

								<th scope="col">
									{{translate("Action")}}
								</th>
							</tr>
						</thead>

						<tbody>
							@forelse($attributeValues as $attributeValue)

							    @php
									 $displayName = $attributeValue->display_name 
														? $attributeValue->display_name 
														: unslug($attributeValue->name);
								@endphp
								<tr>
									<td class="fw-medium">
								     	{{$loop->iteration}}
									</td>
									 <td>
										{{ 
											$displayName 
										}}
									 </td>

									<td>
										<div class="hstack justify-content-center gap-3">
											@if(permission_check('update_product'))
												<a href="javascript:void(0)" title="{{translate('Attribute Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-id="{{$attributeValue->id}}" data-name="{{$displayName}}" class="attributevalue fs-18 link-warning"><i class="ri-pencil-fill"></i></a>
											@endif
											@if(permission_check('delete_product'))
												<a href="javascript:void(0);" title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.item.attribute.value.delete',$attributeValue->id)}}" class="delete-item fs-18 link-danger">
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

				<div class="pagination-wrapper d-flex justify-content-end mt-4">
					  {{$attributeValues->appends(request()->all())->links()}}
				</div>
			</div>
		</div>
	</div>

</div>

<div class="modal fade" id="createattributevalue" tabindex="-1" aria-labelledby="createattributevalue" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Add New Attribute Value')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" ></button>
			</div>

			<form action="{{route('admin.item.attribute.value.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="attribute_id" value="{{$attribute->id}}">
	            <div class="modal-body">

					<div class="mb-3">
						<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger" >*</span>
						</label>
						<input type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter name')}}" required>
					</div>

	            </div>

				<div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger border-0 text-light" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateattributevalue" tabindex="-1" aria-labelledby="updateattributevalue" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Add New Attribute Value')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.item.attribute.value.update')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id">
				<input type="hidden" name="attribute_id" value="{{$attribute->id}}">
	            <div class="modal-body">

				<div class="mb-3">
					<label for="uname" class="form-label">{{translate('Name')}} <span class="text-danger" >*</span></label>
					<input type="text" class="form-control" id="uname" name="name" placeholder="{{translate('Enter name')}}" required>
				</div>

	            </div>
				<div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger border-0 text-light" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
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
		$('.attributevalue').on('click', function(){
			var modal = $('#updateattributevalue');
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});


	})(jQuery);
</script>
@endpush
