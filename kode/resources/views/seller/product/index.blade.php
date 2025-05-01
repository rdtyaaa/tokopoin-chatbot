@extends('seller.layouts.app')
@section('main_content')

	@php
		$totalProducts = ((Auth::guard('seller')->user()->product))->count();
		$subscription = Auth::guard('seller')->user()->subscription->where('status',1)->first();
     
        $totalSubscriptionProducts = 0;
		if($subscription){
			$totalSubscriptionProducts = @$subscription->total_product ?? 0;
		}


	@endphp

	<div class="page-content">
		<div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{translate("Inhouse Product")}}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Dashboard')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate("Inhouse Product")}}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{translate('Product List')}}
                            </h5>
                        </div>

                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a id="product-create" href="javascript:void(0)" class="btn btn-success waves ripple-light"> <i
                                        class="ri-add-line me-1 align-bottom"></i>
                                    {{translate('Create')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                        <div class="row g-3">
                            <div class="col-xl-4 col-lg-6">
                                <div class="search-box">
                                    <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                        placeholder="{{translate('Search  by product name , brand or category')}}">
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
                            <a class='nav-link {{request()->routeIs("seller.product.index") ? "active" :"" }} All py-3'  id="All"
                                href="{{route('seller.product.index')}}" >
                                <i class="ri-store-2-line me-1 align-bottom"></i>
                                {{translate('All
                                Product')}}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class='nav-link {{request()->routeIs("seller.product.trashed") ? "active" :""}}  Placed py-3'  id="trashed"
                                href="{{route('seller.product.trashed')}}" >
                                <i class="ri-delete-bin-fill me-1 align-bottom"></i>
                                {{translate('Trashed Product')}}

                            </a>
                        </li>

                        <li class="nav-item">
                            <a class='nav-link {{request()->routeIs("seller.product.refuse") ? "active" :""}}  Placed py-3'  id="refuse"
                                href="{{route('seller.product.refuse')}}" >
                                <i class="ri-delete-bin-3-fill me-1 align-bottom"></i>
                                {{translate('Refuse
                                Product')}}
                            </a>
                        </li>
                    </ul>

                    <div class="table-responsive table-card">
                        <table class="table table-hover table-nowrap align-middle mb-0" id="orderTable">

                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th class="text-start">{{translate('Product')}}</th>
                                    <th>{{translate('Categories - Sold Item')}}</th>
                                    <th>{{translate('Info')}}</th>
                                    <th>{{translate('Time - Status')}}</th>
                                    <th>{{translate('Action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image)}}" alt="{{$product->featured_image}}"
                                                        >
                                                </div>
                                                <div class="flex-grow-1">
                                                  {{$product->name}}

                                                </div>
                                            </div>
                                        </td>

                                        <td>

                                            <span class="badge bg-info text-white fw-bold">{{(get_translation($product->category->name))}}</span><br>

                                            <span>{{translate('Total Sold')}} : {{$product->order->count()}}
                                            </span>
                                        </td>

                                        <td data-label="{{translate('Info')}}">
                                            <span>{{translate('Regular Price')}} :{{(short_amount($product->price))}} {{translate('Discount Price')}} : {{(short_amount($product->discount))}}</span>

                                        </td>

                                        <td data-label="{{translate('Time - Status')}}">
                                            <span>{{get_date_time($product->created_at, 'd M Y')}}</span><br>
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

                                        <td>
                                            <div class="hstack justify-content-center gap-3">
                                                
                                                @if(request()->routeIs('seller.product.trashed'))
                                                    <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top"  title="Restore Product"  class="link-success fs-18  productdeleterestore" data-bs-toggle="modal" data-id="{{$product->id}}" data-bs-target="#trashrestore"><i class="las la-undo-alt"></i></a>

                                                    <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top"  title="Delete Product" class="link-danger fs-18  productparmanentdelete" data-bs-toggle="modal" data-id="{{$product->id}}" data-bs-target="#permanentDelete"><i class="las la-trash"></i></a>
                                                @else

                                                   @if( site_settings('seller_product_status_update_permission') ==  App\Enums\StatusEnum::true->status()  &&  ($product->status == 1 || $product->status == 2))

                                                        <a href="javascript:void(0)" class="fs-18 link-success ms-2 seller_status_edit" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Status Update')}}" data-bs-toggle="modal" data-id="{{$product->id}}" data-status="{{$product->status}}"    data-bs-target="#seller_status_edit">
                                                            <i class="las la-pen"></i>
                                                        </a>

                                                    @endif

                                                    @php
                                                        $slug = $product->slug ? $product->slug : make_slug($product->name);
                                                    @endphp


                                                    <a href="{{route('seller.product.details', [$slug,$product->id])}}" class="link-success fs-18" data-bs-toggle="tooltip" data-bs-placement="top" title="Product Details"><i class="las la-redo"></i></a>

                                                    <a href="{{route('seller.product.edit',[$slug , $product->id])}}" class="link-warning fs-18" data-bs-toggle="tooltip" data-bs-placement="top" title="Product Update"><i class="las la-pen"></i></a>

                                                    <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="top" class="link-danger fs-18 productdelete" title="Product Delete" data-bs-toggle="modal" data-id="{{$product->id}}" data-bs-target="#delete">
                                                        <i class="las la-trash"></i>
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

	<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="{{route('seller.product.delete')}}" method="POST">
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
									{{translate('Are you sure you want to
									remove this record ?')}}
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
				<form action="{{route('seller.product.restore')}}" method="POST">
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
									{{translate('Are you sure you want to
									restore this record ?')}}
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

	<div class="modal fade" id="permanentDelete" tabindex="-1" aria-labelledby="permanentDelete" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="{{route('seller.product.permanentDelete')}}" method="POST">
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
									{{translate('Are you sure you want to
									remove this record ?')}}
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


     @if(site_settings('seller_product_status_update_permission') ==  App\Enums\StatusEnum::true->status() )
        <div class="modal fade" id="seller_status_edit" tabindex="-1" aria-labelledby="seller_status_edit" aria-hidden="true" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{route('seller.product.status.update')}}" method="POST">
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
     @endif
    
@endsection

@push('script-push')
<script>
	(function($){
       	"use strict";

		$(".productdelete").on("click", function(){
			var modal = $("#delete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".productdeleterestore").on("click", function(){
			var modal = $("#trashrestore");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".productparmanentdelete").on("click", function(){
			var modal = $("#permanentDelete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

        $("#product-create").click(function(){
    
            if({{$totalSubscriptionProducts }} < 1)
            {
                toaster("{{translate('Renew Your Subsription Plan Or Buy A New Plan')}}",'danger')
            } else{
                window.location = "{{route('seller.product.create')}}"
            }
        });


        $(".seller_status_edit").on("click", function(){
			var modal = $("#seller_status_edit");
			const status = $(this).attr('data-status');
			$('#status-update').html('');
			$('#status-update').append(`
				<select class="form-select" name="status" required="">
					<option value="">Select One</option>
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

