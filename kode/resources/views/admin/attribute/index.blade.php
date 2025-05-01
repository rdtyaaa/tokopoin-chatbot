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
                        {{translate('Atrributes')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Attribite List')}}
                        </h5>
					</div>
					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<button type="button" class="btn btn-success btn-sm add-btn waves ripple-light"
								data-bs-toggle="modal"  data-bs-target="#createattribute"><i
									class="ri-add-line align-bottom me-1"></i>
									{{translate("Create
									Attribute")}}
							</button>

							<button type="button" class="btn btn-primary btn-sm add-btn waves ripple-light"
								data-bs-toggle="modal"  data-bs-target="#createattributevalue"><i
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
									{{translate("Name")}}
								</th>
						
								<th scope="col">
									{{translate("Status")}}
								</th>
								<th scope="col">
									{{translate("Action")}}
								</th>
							</tr>
						</thead>

						<tbody>
							@forelse($attributes as $attribute)
								<tr>
									<td class="fw-medium">
								     	{{$loop->iteration}}
									</td>

									 <td>
										{{$attribute->name}}
									 </td>

								
									<td>
										@if($attribute->status == '1')
										   <span class="badge badge-soft-success">{{translate('Active')}}</span>
										@else
											<span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
										@endif
									</td>

									<td>
										<div class="hstack justify-content-center gap-3">
											@if(permission_check('view_product'))
												<a  title="{{translate('Show Attribute value')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.item.attribute.value.get', $attribute->id)}}"  class=" fs-18 link-info">
													<i class="ri-eye-line"></i>
												</a>
											@endif
											@if(permission_check('update_product'))
												<a href="javascript:void(0)" title="{{translate('Attribute Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-id="{{$attribute->id}}" data-name="{{$attribute->name}}" data-status="{{$attribute->status}}" class="attribute fs-18 link-warning"><i class="ri-pencil-fill"></i></a>
											@endif
											@if(permission_check('delete_product'))
											<a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top"  href="javascript:void(0);" data-href="{{route('admin.item.attribute.delete',$attribute->id)}}" class="delete-item fs-18 link-danger">
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
					{{$attributes->appends(request()->all())->links()}}
				</div>
			</div>
		</div>
	</div>

</div>

<div class="modal fade" id="createattributevalue" tabindex="-1" aria-labelledby="createattributevalue" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Add New Attribute Value')}}
				</h5>

				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" ></button>
			</div>

			<form action="{{route('admin.item.attribute.value.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
	            <div class="modal-body">
					<div class="p-2">
						<div class="mb-3">
							<label for="attribute_id" class="form-label">{{translate('Attribute')}}  <span class="text-danger">*</span> </label>
							<select name="attribute_id" id="attribute_id" class="form-select" required>
								<option value="">{{translate('Select One')}}</option>
								@foreach($attributeForValues as $attributeForValue)
									<option {{old('attribute_id') ==  $attributeForValue->id ? 'selected' :''}}   value="{{($attributeForValue->id)}}">{{($attributeForValue->name)}}</option>
								@endforeach
							</select>
						</div>

						<div class="mb-3">
							<label for="nameOne" class="form-label">{{translate('Attribute Value')}} <span class="text-danger">*</span> </label>
							<input type="text" value="{{old('name')}}" class="form-control" id="nameOne" name="name" placeholder="{{translate('Attribute Value')}}" required>
						</div>
					</div>
	            </div>

				<div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger  " data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="createattribute" tabindex="-1" aria-labelledby="createattribute" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Add New Attribute')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form action="{{route('admin.item.attribute.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
	            <div class="modal-body">
					<div class="p-2">
						<div class="mb-3">
							<label for="uname" class="form-label">{{translate('Name')}} <span class="text-danger">*</span>   </label>
							<input type="text" value="{{old('name')}}" class="form-control" id="uname" name="name" placeholder="{{translate('Enter name')}}" required>
						</div>

						<div class="mb-3">
							<label for="ustatus" class="form-label">{{translate('Status')}} <span class="text-danger">*</span> </label>
							<select class="form-select" name="status" id="ustatus" required>
								<option {{old('status') == 1 ? 'selected' : ''}} value="1">{{translate('Active')}}</option>
								<option {{old('status') == 2 ? 'selected' : ''}}  value="2">{{translate('Inactive')}}</option>
							</select>
						</div>
					</div>
				</div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger " data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-md btn-success">{{translate('Add Attribue')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateattribute" tabindex="-1" aria-labelledby="updateattribute" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Update Attribute')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.item.attribute.update')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
	            	<div class="p-2">
						<div class="mb-3">
							<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span> </label>
							<input type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter name')}}" required>
						</div>

						<div class="mb-3">
							<label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span> </label>
							<select class="form-select" name="status" id="status" required>
								<option value="1">{{translate('Active')}}</option>
								<option value="2">{{translate('Inactive')}}</option>
							</select>
						</div>
	            	</div>
	            </div>

				<div class="modal-footer">
	                <button type="button" class="btn btn-md btn-danger " data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-md btn-success">{{translate('Update Attribue')}}</button>
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
		$('.attribute').on('click', function(){
			var modal = $('#updateattribute');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

        $(".attributedelete").on("click", function(){
			var modal = $("#attributedelete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
