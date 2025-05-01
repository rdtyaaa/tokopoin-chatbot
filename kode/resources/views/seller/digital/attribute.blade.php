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
                        <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                        </a></li>
                        <li class="breadcrumb-item"><a href="{{route('seller.digital.product.index')}}">
                        {{translate('Digital Products')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate("Digital Products Attributes")}}
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
                                    {{translate('Digital Product Attribute List')}}
                                </h5>
                            </div>
                        </div>
                        <div class=" col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <button type="button" class="btn btn-success add-btn waves ripple-light"
                                data-bs-toggle="modal" data-bs-target="#digitalattribute"><i
                                        class="ri-add-line align-bottom me-1"></i>
                                        {{translate('Add New ')}}
                                </button>
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
                                        <th>{{translate('Short Details')}}</th>
                                        <th>{{translate('Price')}}</th>
                                        <th>{{translate('Status')}}</th>
                                        <th>{{translate('Action')}}</th>
                                    </tr>
                                </thead>

                                <tbody class="list form-check-all">
                                    @forelse($digitalProductAttributes as $digitalProductAttribute)
                                        <tr>
                                            <td data-label="{{translate('Name')}}">
                                                <span>{{($digitalProductAttribute->name)}}</span>
                                            </td>

                                            <td data-label="{{translate('Short Details')}}">
                                                <span>{{($digitalProductAttribute->short_details ? $digitalProductAttribute->short_details : 'N/A')}}</span>
                                            </td>

                                            <td data-label="{{translate('Price')}}">
                                                <span>{{(short_amount($digitalProductAttribute->price))}} </span>
                                            </td>

                                            <td data-label="{{translate('Status')}}">
                                                @if($digitalProductAttribute->status == '1')
                                                    <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                                @else
                                                    <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="hstack justify-content-center gap-3">

                                                   
                                                    <a
                                                    title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    href="javascript:void(0)"
                                                    data-id="{{$digitalProductAttribute->id}}"
                                                    data-name="{{$digitalProductAttribute->name}}"
                                                    data-short_details="{{$digitalProductAttribute->short_details}}"
                                                    data-price="{{round($digitalProductAttribute->price)}}"
                                                    data-status="{{($digitalProductAttribute->status)}}"
                                                        class="link-warning fs-18 editAttribute"><i class="las la-pen"></i></a>


                                                    <a       title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    href="javascript:void(0)" class="link-danger fs-18 attributedelete" data-id="{{$digitalProductAttribute->id}}"><i class="las la-trash"></i></a>

                                                    <a  title="{{translate('Attribute Details')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('seller.digital.product.attribute.edit', $digitalProductAttribute->id)}}" class="link-suceess fs-18"><i class="las la-key"></i></a>
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

                        <div class="pagination-wrapper d-flex justify-content-end mt-4 ">

                                {{ $digitalProductAttributes->links() }}

                        </div>
                </div>
            </div>
		</div>
	</div>

	<div class="modal fade" id="digitalattribute" tabindex="-1" aria-labelledby="digitalattribute" aria-hidden="true">
		<div class="modal-dialog">
			<div id="modal-content" class="modal-content">
				<form action="{{route('seller.digital.product.attribute.store')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="product_id" value="{{$product->id}}">
					<div class="modal-body">
						<div class="p-2">
							<h5 class="m-0">{{translate('Add Digital Product Attribute')}}</h5><hr>
							<div>
								<div class="mb-3">
									<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
									<input type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter name')}}" required>
								</div>

								<div class="mb-3">
									<label for="short_details" class="form-label">{{translate('Short Details')}}</label>
									<input type="text" class="form-control" id="short_details" name="short_details" placeholder="{{translate('Enter Short Details')}}">
								</div>

								<div class="mb-3">
									<label for="price" class="form-label">{{translate('Price')}} <span class="text-danger">*</span></label>
									<input type="number" class="form-control" id="price" name="price" placeholder="{{translate('Enter Price')}}" required>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
						<button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection

@push('script-push')
<script>
    'use strict'

	$('.editAttribute').on('click', function(){

		const id = $(this).attr('data-id');
		const name = $(this).attr('data-name');
		const shortDetails = $(this).attr('data-short_details');
		const price = $(this).attr('data-price');
		const status = $(this).attr('data-status');

		$('#modal-content').html('');
		$('#modal-content').append(`

			<form action="{{route('seller.digital.product.attribute.update')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id" value="${id}">
	            <div class="modal-body">

                       <h5 class="m-0">{{translate('Update Digital Product Attribute')}}</h5><hr>

                       

                        <div class="mb-3">
                            <label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" value="${name}" name="name" placeholder="{{translate('Enter name')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="short_details" class="form-label">{{translate('Short Details')}}</label>
                            <input type="text" class="form-control" id="short_details" value="${shortDetails}" name="short_details" placeholder="{{translate('Enter Short Details')}}">
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">{{translate('Price')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="price" value="${price}" name="price" placeholder="{{translate('Enter Price')}}" required>
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


    $('.createAttr').on('click', function(){


        $('#modal-content').html('');
        $('#modal-content').append(`

        <form action="{{route('seller.digital.product.attribute.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="product_id" value="{{$product->id}}">
	            <div class="modal-body">
	            	<div class="p-2">
						<h5 class="m-0">{{translate('Add Digital Product Attribute')}}</h5><hr>
		                <div>
							<div class="mb-3">
								<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter name')}}" required>
							</div>
							<div class="mb-3">
								<label for="short_details" class="form-label">{{translate('Short Details')}}</label>
								<input type="text" class="form-control" id="short_details" name="short_details" placeholder="{{translate('Enter Short Details')}}">
							</div>
							<div class="mb-3">
								<label for="price" class="form-label">{{translate('Price')}} <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="price" name="price" placeholder="{{translate('Enter Price')}}" required>
							</div>
						</div>
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


	$(".attributedelete").on("click", function(){
		const id = $(this).attr('data-id');

		$('#modal-content').html('');
		$('#modal-content').append(`
			<form action="{{route('seller.digital.product.attribute.delete')}}" method="POST">
        		@csrf
        		<input type="hidden" value="${id}" name="id">
				<div class="modal-body">
					<div class="mt-2 text-center">
						<lord-icon src="{{asset('assets/global/gsqxdxog.json')}}" trigger="loop"
							colors="primary:#f7b84b,secondary:#f06548"
							class="loader-icon"

							></lord-icon>
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
		`)
        $('#digitalattribute').modal('show')
	});
</script>
@endpush