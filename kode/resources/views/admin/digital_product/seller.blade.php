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
                        {{translate("Digital Products")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" id="orderList">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Digital Product List')}}
                        </h5>
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
                        <a class='nav-link {{request()->routeIs("admin.digital.product.seller") ? "active" :"" }} All py-3'  id="All"
                            href="{{route('admin.digital.product.seller',request()->query())}}" >
                            <i class="ri-store-2-line me-1 align-bottom"></i>
                            {{translate('All
                            Product')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class='nav-link {{request()->routeIs("admin.digital.product.seller.trashed") ? "active" :""}}  Placed py-3'  id="Placed"
                            href="{{route('admin.digital.product.seller.trashed',request()->query())}}" >
                            <i class="ri-delete-bin-fill me-1 align-bottom"></i>
                            {{translate('Trashed Product')}}
                        </a>
                    </li>
                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">

                                <th class="text-start">{{translate('Product')}}</th>
                                <th>{{translate('Seller')}}</th>
                                <th>{{translate('Category - Subcategory')}}</th>
                                <th>{{translate('Time - Status')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($sellerDigitalProducts as $sellerDigitalProduct)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['product']['featured']['path'].'/'.$sellerDigitalProduct->featured_image ,file_path()['product']['featured']['size'])}}" alt="{{@$sellerDigitalProduct->featured_image}}">
                                            </div>
                                            <div class="flex-grow-1">
                                                {{$sellerDigitalProduct->name}}
                                            </div>
                                        </div>
                                    </td>

                                    <td data-label="{{translate('Seller')}}">
                                        <a href="{{route('admin.seller.info.details', $sellerDigitalProduct->seller_id)}}" class="fw-bold text-dark">{{($sellerDigitalProduct->seller->username)}}</a>
                                    </td>

                                    <td>

                                        <span class="badge bg-info text-white fw-bold">{{(get_translation($sellerDigitalProduct->category->name))}}</span> -

                                        @if($sellerDigitalProduct->subCategory)
                                        <span class="badge bg-info text-white fw-bold">{{(get_translation($sellerDigitalProduct->subCategory->name))}}</span>
                                        @else
                                          {{translate('N/A')}}
                                        @endif

                              
                                    </td>
                                    
                                    <td data-label="{{translate('Time - Status')}}">
                                            <span>{{get_date_time($sellerDigitalProduct->created_at, 'd M Y')}}</span><br>
                                        @if($sellerDigitalProduct->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Published')}}</span>
                                        @elseif($sellerDigitalProduct->status == 2)
                                            <span class="badge badge-soft-warning">{{translate('Inactive')}}</span>
                                        @elseif($sellerDigitalProduct->status == 3)
                                            <span class="badge badge-soft-danger">{{translate('Cancel')}}</span>
                                        @else
                                            <span class="badge badge-soft-primary">{{translate('New')}}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="hstack justify-content-center gap-3">
                                            @if(!request()->routeIs('admin.digital.product.seller.trashed'))

                                            <a href="{{route('admin.digital.product.seller.details', $sellerDigitalProduct->id)}}" class="link-info fs-18" data-bs-toggle="tooltip" data-bs-placement="top" title="Product Details"><i class="ri-eye-line"></i></a>


                                                @if($sellerDigitalProduct->status == 0)
                                                    <a href="javascript:void(0)" class="link-success fs-18  productapproved" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Approved')}}" data-bs-toggle="modal" data-id="{{$sellerDigitalProduct->id}}" >
                                                        <i class="ri-check-double-line"></i>
                                                    </a>
                                                    <a href="javascript:void(0)" class="link-danger fs-18 productcancel" data-bs-toggle="tooltip" data-bs-placement="top" title="Product Cancel" data-bs-toggle="modal" data-id="{{$sellerDigitalProduct->id}}" data-bs-target="#cancel">
                                                        <i class="ri-close-circle-line"></i>
                                                    </a>
                                                @elseif($sellerDigitalProduct->status == 1)
                                                    <a href="javascript:void(0)"  data-bs-toggle="tooltip" data-bs-placement="top" title="Inactive Product" class="link-danger fs-18 productcancel" data-bs-toggle="modal" data-id="{{$sellerDigitalProduct->id}}" data-bs-target="#cancel">
                                                        <i class="ri-close-circle-line"></i>
                                                    </a>
                                                @elseif($sellerDigitalProduct->status == 2)
                                                <a href="javascript:void(0)" class="link-success fs-18 productapproved" data-bs-toggle="tooltip" data-bs-placement="top" title="Product Approved" data-bs-toggle="modal" data-id="{{$sellerDigitalProduct->id}}" data-bs-target="#success">
                                                    <i class="ri-check-double-line"></i>
                                                </a>
                                                @endif

                                                <a href="javascript:void(0)" class="link-danger fs-18 productdelete"  data-bs-toggle="tooltip" data-bs-placement="top" title="Product Delete" data-id="{{$sellerDigitalProduct->id}}" data-bs-toggle="modal" data-bs-target="#delete">
                                                    <i class="ri-delete-bin-6-line"></i>
                                                </a>

                                                @else
                                                    <a href="javascript:void(0)" class="link-success fs-18 productdeleterestore" data-bs-toggle="tooltip" data-bs-placement="top" title="Product Restore" data-id="{{$sellerDigitalProduct->id}}" data-bs-toggle="modal" data-bs-target="#restore">
                                                        <i class="ri-recycle-line"></i>
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
                    {{ $sellerDigitalProducts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="success" tabindex="-1" aria-labelledby="success" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.digital.product.seller.approved')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
	            <div class="modal-body">
	                <div class="mt-2 text-center">
						<i class="las fs-18 link-success la-check"></i>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to
								Approved This Product?')}}
							</p>
						</div>
					</div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-primary">{{translate('Approved')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cancel" tabindex="-1" aria-labelledby="cancel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.digital.product.seller.inactive')}}" method="POST">
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
								{{translate('Are you sure you want to inactive this product??')}}
							</p>
						</div>
					</div>

	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-danger">{{translate('Inactive')}}</button>
	            </div>
	        </form>

        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.digital.product.seller.delete')}}" method="POST">
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
								{{translate('Are you sure you want to inactive this product??')}}
							</p>
						</div>
					</div>
	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-danger">{{translate('Delete')}}</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="trashrestore" tabindex="-1" aria-labelledby="trashrestore" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.digital.product.seller.restore')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
	            <div class="modal-body">
	                <div class="mt-2 text-center">
						<i class="las fs-18 link-success la-check"></i>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to
								Restore This Product?')}}
							</p>
						</div>
					</div>
	            </div>
				<div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
	                <button type="submit" class="btn btn-success">{{translate('Restore')}}</button>
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

	})(jQuery);
</script>
@endpush


