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
                        {{translate("Inhouse Products")}}
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
                                {{translate('Product List')}}
							</h5>
						</div>
					</div>

					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							  <a href="{{route('admin.item.product.inhouse.create')}}" class="btn btn-success btn-sm w-100 waves ripple-light"> <i
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
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search  by product name , brand or category')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Search')}}
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
                        <a class="nav-link {{request()->routeIs('admin.item.product.inhouse.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.item.product.inhouse.index')}}" >
                            <i class="ri-store-2-line me-1 align-bottom"></i>
                            {{translate('All
                            Product')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.item.product.inhouse.trashed') ? 'active' :''}}  Placed py-3"  id="Placed"
                            href="{{route('admin.item.product.inhouse.trashed')}}" >
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
                                <th>{{translate('Categories - Sold Item')}}</th>
                                <th>{{translate('Info')}}</th>
                                <th>{{translate('Top Item - Todays Deal')}}</th>
                                <th>{{translate('Time - Status')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($inhouseProducts as $inhouseProduct)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img alt="{{$inhouseProduct->featured_image}}" class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['product']['featured']['path'].'/'.$inhouseProduct->featured_image,file_path()['product']['featured']['size'])}}">
                                            </div>

                                            <div class="flex-grow-1">
                                                {{$inhouseProduct->name}}
                                                <br>
                                                <span>{{translate('Brand')}}: {{@(get_translation($inhouseProduct->brand->name))}}
                                                </span>

                                            </div>
                                        </div>
                                    </td>

                                    <td>

                                        <span class="badge bg-info text-white fw-bold">{{@(get_translation($inhouseProduct->category->name))}}

                                        </span>
                                        <br>
                                        <span>{{translate('Total Sold')}} : {{$inhouseProduct->order->count()}}
                                        </span>
                                    </td>

                                    <td data-label="{{translate('Info')}}">
                                        <span>{{translate('Regular Price')}} : {{(short_amount($inhouseProduct->price))}} <br> {{translate('Discount Price')}} : {{(short_amount($inhouseProduct->discount))}}</span><br>
                                        <a href="{{route('admin.item.product.inhouse.bestsellingitem.status', $inhouseProduct->id)}}" class=" fs-15 link-{{$inhouseProduct->best_selling_item_status == '1' ? 'danger':'info'}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Best Selling Product')}}">{{translate($inhouseProduct->best_selling_item_status==1?'Best Selling Item - No':'Best Selling Item - Yes')}} <i class="las la-arrow-alt-circle-left"></i></a>
                                        <br>

                                        <a href="{{route('admin.item.product.inhouse.suggested.status', $inhouseProduct->id)}}" class=" fs-15 link-{{$inhouseProduct->is_suggested == 0 ? 'danger':'info'}}"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Suggested Product')}}">{{translate($inhouseProduct->is_suggested == 0?'Suggested Item - No':'Suggested Item  - Yes')}} <i class="las la-arrow-alt-circle-left"></i></a>

                                    </td>

                                    <td data-label="{{translate('Top Product')}}">
                                        <a href="{{route('admin.item.product.inhouse.top', $inhouseProduct->id)}}" class=" fs-15 link-{{$inhouseProduct->top_status == 1 ? 'danger':'success'}}"  data-bs-toggle="tooltip" data-bs-placement="top" title="Top Product">{{($inhouseProduct->top_status==1?'No':'Yes')}} <i class="las la-arrow-alt-circle-left"></i></a> <i class="las la-arrows-alt-v"></i>
                                        <a href="{{route('admin.item.product.inhouse.featured.status', $inhouseProduct->id)}}" class="fs-15 link-{{$inhouseProduct->featured_status == 1 ? 'danger':'success'}}" data-bs-toggle="tooltip" data-bs-placement="top" title="Todays Deal">{{($inhouseProduct->featured_status==1?'No':'Yes')}} <i class="las la-arrow-alt-circle-left"></i></a>
                                    </td>

                                    <td data-label="{{translate('Time - Status')}}">
                                        <span>{{get_date_time($inhouseProduct->created_at, 'd M Y')}}</span><br>
                                        @if($inhouseProduct->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Published')}}</span>
                                        @elseif($inhouseProduct->status == 2)
                                            <span class="badge badge-soft-warning">{{translate('Inactive')}}</span>
                                        @elseif($inhouseProduct->status == 3)
                                            <span class="badge badge-soft-danger">{{translate('Cancel')}}</span>
                                        @else
                                            <span class="badge badge-soft-primary">{{translate('New')}}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="hstack justify-content-center gap-3">
                                            @if(request()->routeIs('admin.item.product.inhouse.trashed'))
                                                <a href="javascript:void(0)"  title="Restore Product" data-bs-toggle="tooltip" data-bs-placement="top" class="link-success fs-18  productdeleterestore" data-bs-toggle="modal" data-id="{{$inhouseProduct->id}}" data-bs-target="#trashrestore"><i class="las la-undo-alt"></i></a>
                                                <a href="javascript:void(0)"  title="{{translate('Delete Product')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="link-danger fs-18  productparmanentdelete" data-bs-toggle="modal" data-id="{{$inhouseProduct->id}}" data-bs-target="#permanentDelete"><i class="las la-trash"></i></a>
                                            @else


                                            <a href="{{route('admin.item.product.inhouse.replicate', $inhouseProduct->id)}}" class="link-success fs-18"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Duplicate Product')}}"><i class="ri-file-copy-line"></i></a>


                                            <a href="{{route('admin.item.product.inhouse.details', $inhouseProduct->id)}}" class="link-info fs-18"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Details')}}"><i class="las la-redo"></i></a>
                                            <a href="{{route('admin.item.product.inhouse.edit', $inhouseProduct->id)}}" class="link-warning fs-18"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Update')}}"><i class="las la-pen"></i></a>
                                            <a href="javascript:void(0)" class="link-danger fs-18 productdelete" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Product Delete')}}" data-bs-toggle="modal" data-id="{{$inhouseProduct->id}}" data-bs-target="#delete">
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
                    {{ $inhouseProducts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('admin.item.product.inhouse.delete')}}" method="POST">
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
			<form action="{{route('admin.item.product.inhouse.restore')}}" method="POST">
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
			<form action="{{route('admin.item.product.inhouse.permanentDelete')}}" method="POST">
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

	})(jQuery);
</script>
@endpush
