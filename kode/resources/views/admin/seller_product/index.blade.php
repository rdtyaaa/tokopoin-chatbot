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
                        {{translate("Seller Products")}}
                    </li>
                </ol>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <div>
                            <h5 class="card-title mb-0">
                                {{translate('Seller Product List')}}
                            </h5>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-3 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search by name or category')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="search-box">
                                  <select name="seller_id" id="seller_id">
                                       <option value="">
                                            {{translate('Select seller')}}
                                       </option>

                                       @foreach (getSeller() as $seller)

                                         <option {{ request()->input('seller_id') ==  $seller->id ? 'selected' :"" }} value="{{ $seller->id }}">
                                                {{ $seller->name }}
                                        </option>
                                                
                                       @endforeach
                                  </select>
                            </div>
                        </div>


                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light"
                                    > <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Filter')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}" class="btn btn-danger w-100 waves ripple-light"> <i
                                        class="ri-refresh-line me-1 align-bottom"></i>
                                    {{translate('Reset')}}
                                </a>
                            </div>
                        </div>

                    </div>

                </form>
            </div>


            <div class="card-body pt-0">
                <ul class="nav nav-tabs nav-tabs-custom nav-primary mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.product.seller.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.product.seller.index',request()->query())}}" >
                            <i class="ri-store-2-line me-1 align-bottom"></i>
                            {{translate('All
                            Product')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.product.seller.new') ? 'active' :''}}  Placed py-3"  id="Placed"
                            href="{{route('admin.product.seller.new',request()->query())}}" >
                            <i class="ri-product-hunt-line me-1 align-bottom"></i>
                            {{translate('New Product')}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.product.seller.approved') ? 'active' :''}} Published py-3"  id="Confirmed"
                            href="{{route('admin.product.seller.approved',request()->query())}}" >
                            <i class="ri-checkbox-multiple-fill me-1 align-bottom"></i>
                            {{translate("Published Products")}}

                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link Refuse {{request()->routeIs('admin.product.seller.refuse') ? 'active' :''}}   py-3"  id="Refuse"
                            href="{{route('admin.product.seller.refuse',request()->query())}}" >

                            <i class="ri-delete-bin-4-line me-1 align-bottom"></i>
                            {{translate('Refuse Products')}}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link Processing {{request()->routeIs('admin.product.seller.trashed') ? 'active' :''}}   py-3"  id="trashed"
                            href="{{route('admin.product.seller.trashed',request()->query())}}" >
                            <i class="ri-delete-bin-fill me-1 align-bottom"></i>
                            {{translate('Trashed Products')}}
                        </a>
                    </li>
                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">

                                <th class="text-start">{{translate('Product')}}</th>
                                <th>{{translate('Categories')}}</th>
                                <th>{{translate('Seller')}}</th>
                                <th>{{translate('Price')}}</th>
                                <th>{{translate('Top Item - Todays Deal')}}</th>
                                <th>{{translate('Date')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img alt="{{$product->featured_image }}" class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image , file_path()['product']['featured']['size'])}}">
                                            </div>
                                            <div class="flex-grow-1">
                                                {{$product->name}}
                                            </div>
                                        </div>
                                    </td>

                                    <td data-label="{{translate('Categories')}}">

                                        <span class="badge bg-info text-white fw-bold">{{(@get_translation($product->category->name))}}</span><br>

                                        <span>{{translate('Total Sold')}} : {{$product->order->count()}}</span>
                                    </td>

                                    <td data-label="{{translate('Seller')}}">

                                        @if($product->seller)
                                           <a class="fw-bold text-dark" href="{{route('admin.seller.info.details', $product->seller_id)}}">{{@($product->seller->username)}}</a>
                                        @else
                                            {{ translate('N/A') }}
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Price')}}">
                                        <span>{{translate('Regular Price')}} : {{(short_amount($product->price))}}</span><br>
                                        <span>{{translate('Discount Price') }}: {{(short_amount($product->discount))}}</span>
                                    </td>

                                    <td data-label="{{translate('Top Product')}}">
                                        <a href="{{route('admin.item.product.inhouse.top', $product->id)}}" class=" fs-15 link-{{$product->top_status == 1 ? 'danger':'success'}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Top Product')}}">{{($product->top_status==1?'No':'Yes')}} <i class="las la-arrow-alt-circle-left"></i></a> <i class="las la-arrows-alt-v"></i>
                                        <a href="{{route('admin.item.product.inhouse.featured.status', $product->id)}}" class="fs-15 link-{{$product->featured_status == 1 ? 'danger':'success'}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Todays Deal')}}">{{($product->featured_status==1?'No':'Yes')}} <i class="las la-arrow-alt-circle-left"></i></a>
                                    </td>


                                    <td data-label="{{translate('Time - Status')}}">
                                        <span>{{get_date_time($product->created_at)}}</span><br>
                                        @if($product->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Published')}}</span>
                                        @elseif($product->status == 2)
                                            <span class="badge badge-soft-warning">{{translate('Inactive')}}</span>
                                        @elseif($product->status == 3)
                                            <span class="badge badge-soft-danger">{{translate('Cancel')}}</span>
                                        @else
                                            <span class="badge badge-soft-primary">{{translate('New')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Action')}}">
                                        <div class="hstack justify-content-center gap-3">
                                            @if(!request()->routeIs('admin.product.seller.trashed'))
                                                <a href="{{route('admin.product.seller.details', $product->id)}}" class="fs-18 link-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Details')}}">
                                                    <i class="las la-redo"></i>
                                                </a>
                                            @endif

                                       

                                            @if(request()->routeIs('admin.product.seller.new'))
                                                <a href="javascript:void(0)" class="fs-18 link-success productapproved" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Approved')}}" data-bs-toggle="modal" data-id="{{$product->id}}" data-bs-target="#success">
                                                    <i class="las la-check-double"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="fs-18 link-danger productcancel" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Cancel')}}" data-bs-toggle="modal" data-id="{{$product->id}}" data-bs-target="#cancel">
                                                    <i class="las la-times-circle"></i>
                                                </a>
                                            @elseif(request()->routeIs('admin.product.seller.refuse'))
                                                <a href="javascript:void(0)" class="fs-18 link-success  productapproved" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Approved')}}" data-bs-toggle="modal" data-id="{{$product->id}}" data-bs-target="#success">
                                                    <i class="las la-check-double"></i>
                                                </a>
                                            @elseif(request()->routeIs('admin.product.seller.approved'))
                                                <a href="javascript:void(0)" class="fs-18 link-warning productcancel" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Cancel')}}" data-bs-toggle="modal" data-id="{{$product->id}}" data-bs-target="#cancel">
                                                    <i class="las la-times-circle"></i>
                                                </a>
                                            @endif
                                            @if(!request()->routeIs('admin.product.seller.trashed'))
                                        
                                                <a href="javascript:void(0)" class="fs-18 link-success ms-2 seller_status_edit" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Update')}}" data-bs-toggle="modal" data-id="{{$product->id}}" data-status="{{$product->status}}"    data-bs-target="#seller_status_edit">
                                                    <i class="las la-pen"></i>
                                                </a>
                                                <a href="javascript:void(0)" class="fs-18 link-danger productdelete" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Delete')}}" data-id="{{$product->id}}" data-bs-toggle="modal" data-bs-target="#delete">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            @else
                                                <a href="javascript:void(0)" class="fs-18 link-success productdeleterestore ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Restore')}}" data-id="{{$product->id}}" data-bs-toggle="modal" data-bs-target="#restore">
                                                    <i class="las la-undo-alt"></i>
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
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="seller_status_edit" tabindex="-1" aria-labelledby="seller_status_edit" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.product.seller.update.status')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
	            <div class="modal-body">
					<label class="form-label">
						{{translate("Status")}}
						<span class="text-danger">*</span></label>
					<div id='status-update'>

					</div>

	            </div>
	            <div class="modal-footer">
	                <button type="submit" class="btn btn-md btn-success">{{translate('Update')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>


<div class="modal fade" id="success" tabindex="-1" aria-labelledby="success" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.product.seller.approvedby')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">

				<div class="modal-body">
					<div class="mt-2 text-center">
						<i class=" fs-18 link-success las la-check"></i>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to approve this product?')}}
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-success">{{translate('Approved')}}</button>
				</div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel" tabindex="-1" aria-labelledby="cancel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.product.seller.cancelby')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">

				<div class="modal-body">
					<div class="mt-2 text-center">
						<i class=" fs-18 link-success las la-times"></i>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to inactive this product?')}}
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-danger">{{translate('Confirm!!')}}</button>
				</div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.product.seller.delete')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">

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
								{{translate('Are you sure you want to delete this product?')}}
							</p>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-danger">{{translate('Confirm!!')}}</button>
				</div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="trashrestore" tabindex="-1" aria-labelledby="trashrestore" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.product.seller.restore')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">

				<div class="modal-body">
					<div class="mt-2 text-center">
						<i class=" fs-18 link-success las la-check"></i>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to restore this product?')}}
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-success">{{translate('Restore!!')}}</button>
				</div>
	        </form>
        </div>
    </div>
</div>

@endsection

@push('script-push')
<script>
	(function($){
       	"use strict";

        $("#seller_id").select2({
        })

		$(".productdelete").on("click", function(){
			var modal = $("#delete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".productapproved").on("click", function(){
			var modal = $("#success");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".productcancel").on("click", function(){
			var modal = $("#cancel");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".productdeleterestore").on("click", function(){
			var modal = $("#trashrestore");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".seller_status_edit").on("click", function(){
			var modal = $("#seller_status_edit");
			const status = $(this).attr('data-status');
			$('#status-update').html('');
			$('#status-update').append(`
				<select class="form-select" name="status" required="">
					<option value="">Select One</option>
					<option ${status == 0 ? 'selected' : ''} value="0">New</option>
					<option ${status == 1 ? 'selected' : ''} value="1">Published</option>
					<option ${status == 2 ? 'selected' : ''} value="2">Inactive</option>
				</select>
					`)
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

	})(jQuery);
</script>
@endpush
