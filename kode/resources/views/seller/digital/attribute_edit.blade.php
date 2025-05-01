@extends('seller.layouts.app')
@section('main_content')
	<div class="page-content">
		<div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{translate("Digital Products")}}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Home')}}
                        </a></li>
                        <li class="breadcrumb-item"><a href="{{route('seller.digital.product.index')}}">
                        {{translate('Digital Products')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate("Attributes Values")}}
                        </li>
                    </ol>
                </div>
            </div>

			<div class="row">
				<div class="col-xl-8 col-lg-10 mx-auto">
					<div class="card">
						<div class="card-header border-bottom-dashed">
							<div class="row g-4 align-items-center">
								<div class="col-sm">
									<div>
										<h5 class="card-title mb-0">
											{{translate('Add Values')}}
										</h5>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="p-2">
								<form action="{{route('seller.digital.product.attribute.value.store', $digitalProductAttribute->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									<div>
										<div class="row">

											<div class="mb-3 col-lg-12 col-md-12">
												<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
	
												<input placeholder="{{translate('Enter name')}}" required type="text" name="name" id="name" value="{{old('name')}}" class="form-control">
								
											</div>
	
											<div class="mb-3 col-lg-12 col-md-12">
												<label for="value" class="form-label">{{translate('Value')}}</label>
	
												<input placeholder="{{translate('Enter value')}}"  type="text" name="value" id="value" value="{{old('value')}}" class="form-control">
								
											</div>
	
											<div class="mb-3 col-lg-12 col-md-12">
												<label for="file" class="form-label">{{translate('File')}} </label>
	
												<input type="file" name="file" id="file" class="form-control">
								
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
								</form>
							</div>
						</div>
					</div>

					<div class="card">
						<div class="card-header border-bottom-dashed">
							<div class="row g-4 align-items-center">
								<div class="col-sm">
									<div>
										<h5 class="card-title mb-0">
											{{translate('Attribute Values')}}
										</h5>
									</div>
								</div>

							</div>
						</div>

						<div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-nowrap align-middle mb-0" id="orderTable">
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">

											<th>{{translate('Name')}}</th>
											<th>{{translate('Value')}}</th>
											<th>{{translate('Status')}}</th>
											<th>{{translate('Action')}}</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody class="list form-check-all">
                                        @forelse($digitalProductAttributeValues as $digitalProductAttributeValue)
                                        <tr class="@if($loop->even) table-light @endif">
											<td data-label="{{translate('Name')}}">
                                                {{($digitalProductAttributeValue->name)}}
                                            </td>
                                            <td data-label="{{translate('Value')}}">
                                                {{($digitalProductAttributeValue->value)}}
                                            </td>
                                            <td data-label="{{translate('Status')}}">
                                                @if($digitalProductAttributeValue->status == '1')
                                                    <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                                @else
                                                    <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                                @endif
                                            </td>
                                            <td data-label="{[translate('Action')}}">

                                                <div class="hstack justify-content-center gap-3">

													@if($digitalProductAttributeValue->file)
													<a href="{{route('seller.digital.product.attribute.value.download',$digitalProductAttributeValue->id)}}" title="{{translate('Download')}}" data-bs-toggle="tooltip" data-bs-placement="top"  class="link-info fs-18 " data-bs-toggle="modal" data-id="{{$digitalProductAttributeValue->id}}"><i class="las la-download"></i>
													</a>
										        	@endif

												 <a
													title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top"
													href="javascript:void(0)"
													data-id="{{$digitalProductAttributeValue->id}}"
													data-name="{{$digitalProductAttributeValue->name}}"
													data-value="{{$digitalProductAttributeValue->value}}"
													data-status="{{($digitalProductAttributeValue->status)}}"
													class="link-warning fs-18 editAttribute"><i class="las la-pen"></i></a>
                                                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" href="javascript:void(0)" class="link-danger fs-18 attributedelete" data-bs-toggle="modal" data-id="{{$digitalProductAttributeValue->id}}" data-bs-target="#delete"><i class="las la-trash"></i></a>

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
                    
                                {{ $digitalProductAttributeValues->links() }}
                              
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="{{route('seller.digital.product.attribute.value.delete')}}" method="POST">
					@csrf
					<input type="hidden" name="id">
					<div class="modal-body">
						<div class="mt-2 text-center">
							<lord-icon src="{{asset('assets/global/gsqxdxog.json')}}" trigger="loop"
								colors="primary:#f7b84b,secondary:#f06548"
								class="loader-icon"></lord-icon>
							<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
								<h4>
								    {{translate('Are you sure ?')}}
								</h4>
								<p class="text-muted mx-4 mb-0">
									{{translate('Are you sure you want to
									remove this record ?')}}
								</p>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
						<button type="submit" class="btn btn-md btn-success">{{translate('Delete')}}</button>
					</div>
				</form>
			</div>
		</div>
	</div>



	<div class="modal fade" id="digitalattribute" tabindex="-1" aria-labelledby="digitalattribute" aria-hidden="true">
		<div class="modal-dialog">
			<div id="modal-content" class="modal-content">
	
			
			</div>
		</div>
	</div>
	
@endsection

@push('script-push')
<script>
	(function($){
       	"use strict";
		$(".attributedelete").on("click", function(){
			var modal = $("#delete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$('.editAttribute').on('click', function(){

			const id = $(this).attr('data-id');
			const name = $(this).attr('data-name');
			const value = $(this).attr('data-value');
			const status = $(this).attr('data-status');

			$('#modal-content').html('');
			$('#modal-content').append(`
				<form action="{{route('seller.digital.product.attribute.value.update')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="id" value="${id}">
					<div class="modal-body">

						<h5 class="m-0">{{translate('Update Digital Product Attribute Values')}}</h5><hr>

							<div class="mb-3">
								<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="name" value="${name}" name="name" placeholder="{{translate('Enter name')}}" required>
							</div>

							<div class="mb-3">
								<label for="value" class="form-label">{{translate('Value')}}</label>
								<input type="text" class="form-control" id="value" value="${value}" name="value" placeholder="{{translate('Enter Value')}}">
							</div>
							<div class="mb-3">
								<label for="file" class="form-label">{{translate('File')}} </label>
								<input type="file" name="file" id="file" class="form-control">
							</div>

				
							<div class="mb-3">
								<label for="status" class="form-label">{{translate('Status')}}</label>
								<select    name="status" id="status" class="form-select">
									<option ${ status == 1 ? 'selected' :''}  value="1">
										{{translate('Active')}}
									</option>
									<option ${ status == 0 ? 'selected' :''}   value="0">
										{{translate('Inactive')}}
									</option>
								</select> 
							</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
						<button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
					</div>
				</form>
			`)

			$('#digitalattribute').modal('show')

		})
	})(jQuery);
</script>
@endpush






