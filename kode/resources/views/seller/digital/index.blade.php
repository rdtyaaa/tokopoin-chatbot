@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    @php
  		$totalProducts = ((Auth::guard('seller')->user()->product))->count();
		$subscription = Auth::guard('seller')->user()->subscription->where('status',1)->first();
        $totalSubscriptionProducts = 0;
		if($subscription){
            $totalSubscriptionProducts = @$subscription->total_product ?? 0;
		}

    @endphp
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
                    <li class="breadcrumb-item active">
                        {{translate("Digital Products")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" >
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <div>
                            <h5 class="card-title mb-0">
                                {{translate('Digital Product List')}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="javascript:void(0)" id="product-create" class="btn btn-success btn-sm add-btn waves ripple-light">
                                <i class="ri-add-line align-bottom me-1"></i>
                                    {{translate('Add New Product')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                <ul class="nav nav-tabs nav-tabs-custom nav-primary mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('seller.digital.product.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('seller.digital.product.index')}}" >
                            <i class="ri-store-2-line me-1 align-bottom"></i>
                            {{translate('All
                            Product')}}
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('seller.digital.product.new') ? 'active' :''}}  Placed py-3"
                            href="{{route('seller.digital.product.new')}}" >
                            <i class="ri-product-hunt-line me-1 align-bottom"></i>
                            {{translate('New Product')}}

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('seller.digital.product.approved') ? 'active' :''}}  Placed py-3"
                            href="{{route('seller.digital.product.approved')}}" >
                            <i class="ri-checkbox-multiple-fill me-1 align-bottom"></i>
                            {{translate('Approved Product')}}
                        </a>
                    </li>
                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" id="orderTable">
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th class="text-start">{{translate('Product')}}</th>
                                <th>{{translate('Categories')}}</th>
                                <th>{{translate('Time - Status')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($digitalProducts as $inhouseDigitalProduct)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['product']['featured']['path'].'/'.$inhouseDigitalProduct->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$inhouseDigitalProduct->featured_image}}">
                                            </div>

                                            <div class="flex-grow-1">
                                                {{$inhouseDigitalProduct->name}}
                                            </div>
                                        </div>
                                    </td>
                                    <td>

                                        <span class="badge bg-info text-white fw-bold">{{(get_translation($inhouseDigitalProduct->category->name))}}</span><br>

                                        <span>@if($inhouseDigitalProduct->subCategory) {{@(get_translation    ($inhouseDigitalProduct->subCategory->name))}}@endif
                                        </span>
                                    </td>
                                    <td data-label="{{translate('Time - Status')}}">
                                        <span>{{get_date_time($inhouseDigitalProduct->created_at, 'd M Y')}}</span><br>
                                        @if($inhouseDigitalProduct->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Published')}}</span>
                                        @elseif($inhouseDigitalProduct->status == 2)
                                            <span class="badge badge-soft-warning">{{translate('Inactive')}}</span>
                                        @elseif($inhouseDigitalProduct->status == 3)
                                            <span class="badge badge-soft-danger">{{translate('Cancel')}}</span>
                                        @else
                                            <span class="badge badge-soft-primary">{{translate('New')}}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack justify-content-center gap-3">


                                            @if( site_settings('seller_product_status_update_permission') ==  App\Enums\StatusEnum::true->status()  &&  ($inhouseDigitalProduct->status == 1 || $inhouseDigitalProduct->status == 2))

                                                <a href="javascript:void(0)" class="fs-18 link-success ms-2 seller_status_edit" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Status Update')}}" data-bs-toggle="modal" data-id="{{$inhouseDigitalProduct->id}}" data-status="{{$inhouseDigitalProduct->status}}"    data-bs-target="#seller_status_edit">
                                                    <i class="las la-pen"></i>
                                                </a>

                                            @endif


                                            <a href="{{route('seller.digital.product.attribute', $inhouseDigitalProduct->id)}}" class="link-info fs-18" data-bs-toggle="tooltip" data-bs-placement="top" title="Digital Product Attribute"><i class="ri-store-3-line"></i></a>

                                            <a href="{{route('seller.digital.product.edit', $inhouseDigitalProduct->id)}}" class="link-warning fs-18"  data-bs-toggle="tooltip" data-bs-placement="top" title="Product Update"><i class="ri-pencil-line"></i></a>

                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="Product Delete" href="javascript:void(0)" class="link-danger fs-18
                                            productdelete" data-bs-toggle="modal" data-id="{{$inhouseDigitalProduct->id}}" data-bs-target="#delete">
                                                <i class="ri-delete-bin-6-line"></i>
                                            </a>
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
                        {{ $digitalProducts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('seller.digital.product.delete')}}" method="POST">
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
				toaster('Renew Your Subsription Plan Or Buy A New Plan','danger')
            } else{
                window.location = "{{route('seller.digital.product.create')}}"
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
