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
                        {{translate("Seller")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0 flex-grow-1">
                            {{translate('Seller List')}}
                        </h5>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.seller.info.create')}}" class="btn btn-success btn-sm w-100 waves ripple-light"> <i
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
                                    placeholder="{{translate('Search name,email,phone')}}">
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
                        <a class="nav-link {{request()->routeIs('admin.seller.info.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.seller.info.index')}}" >
                            <i class="ri-group-fill me-1 align-bottom"></i>
                            {{translate('All
                            Seller')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.seller.info.active') ? 'active' :''}}   py-3"  id="Placed"
                            href="{{route('admin.seller.info.active')}}" >
                            <i class="ri-user-star-line me-1 align-bottom"></i>
                            {{translate('Active Seller')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.seller.info.banned') ? 'active' :''}} Confirmed py-3"  id="Confirmed"
                            href="{{route('admin.seller.info.banned')}}" >
                            <i class="ri-forbid-fill me-1 align-bottom"></i>
                            {{translate("Banned Seller")}}

                        </a>
                    </li>
                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>
                                    {{translate(
                                        "Name - Username"
                                    )}}
                                </th>

                                <th>
                                    {{translate('Email - Phone')}}
                                </th>

                                <th>
                                    {{translate('Best Seller')}}
                                </th>

                                <th>{{translate('Balance')}}
                                </th>


                                <th>{{translate('Products')}}
                                </th>

                        

                                <th>
                                    {{translate('Status -Shop Status')}}
                                </th>

                                <th >
                                    {{translate('Action')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($sellers as $seller)
                                <tr>
                                    <td data-label="{{translate('Name - Username')}}">
                                        <span class="fw-bold">{{@($seller->name)}}</span> <br>
                                        {{@($seller->username)}}
                                    </td>

                                    <td data-label="{{translate('Email - Phone')}}">
                                        {{@($seller->email)}}<br>
                                        {{@($seller->phone)}}
                                    </td>

                                    <td class="text-start" data-label="{{translate('Best Seller')}}">
                                        <a data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Best Seller')}}" href="{{route('admin.seller.info.best.status', $seller->id)}}" class="
                                            link-{{$seller->best_seller_status==1 ? 'danger' :'success'}}
                                            fs-19">
                                            @if($seller->best_seller_status==1)
                                                <i class="fs-18 ri-close-circle-line"></i>
                                            @else
                                                <i class="fs-18 ri-check-fill"></i>
                                            @endif
                                        </a>
                                    </td>


                                    <td data-label="{{translate('Balance')}}">
                                            <div>
                                                <a href="javascript:void(0)" data-id="{{$seller->id}}" class="update-balance"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Update Balance')}}"><span><i class="fs-4 lar la-edit"></i></span></a>
                                                <span>{{round(($seller->balance))}} {{default_currency()->name}}</span>
                              
                                            </div>
                                    </td>
                                
                                    <td data-label="{{translate('Products')}}">
                                        <a  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('View physical product')}}" class="ms-2 badge text-bg-primary custom-toggle active"  href="{{route('admin.product.seller.index', ['seller_id' => $seller->id])}}" >{{translate('Physical')}} ({{$seller->product->wherenull('deleted_at')->where('product_type', 102)->count()}})</a>
                                        -
                                        <a  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('View digital product ')}}" class="ms-2 badge text-bg-primary custom-toggle active"  href="{{route('admin.digital.product.seller', ['seller_id' => $seller->id])}}" >{{translate('Digital')}} ({{$seller->product->wherenull('deleted_at')->where('product_type', 101)->count()}})</a>
                                    </td>

                                 
                                    <td data-label="{{translate('Status')}}">

                                        @if($seller->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{translate('Banned')}}</span>
                                        @endif
                                        -
                                        @if(@$seller->sellerShop->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Enable')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{translate('Disable')}}</span>
                                        @endif
                                    </td>
                                    
                                    <td data-label="{{translate('Action')}}">
                                        <div class="hstack justify-content-center gap-3">
                                            <a target="_blank" class="link-success fs-18 " data-bs-toggle="tooltip" data-bs-placement="top" title="Login" href="{{route('admin.seller.info.login', $seller->id)}}"><i class="ri-login-box-line"></i></a>

                                            <a class="link-info fs-18 " data-bs-toggle="tooltip" data-bs-placement="top" title="Details" href="{{route('admin.seller.info.details', $seller->id)}}"><i class="ri-list-check"></i></a>
                                            <a class="link-success  fs-18 " data-bs-toggle="tooltip" data-bs-placement="top" title="Shop"  href="{{route('admin.seller.info.shop', $seller->id)}}"><i class="las la-store-alt"></i></a>

                                            
                                            <a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.seller.info.delete',$seller->id)}}" class="delete-item fs-18 link-danger">
                                                <i class="ri-delete-bin-line"></i></a>
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
                    {{$sellers ->appends(request()->all())->links()}}
                </div>
            </div>
        </div>

    </div>
</div>

@include('admin.modal.delete_modal')


<div class="modal fade" id="balanceupdate" tabindex="-1" aria-labelledby="balanceupdate" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Update Blance')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.seller.balance.update')}}" method="POST">
				@csrf
				<input type="hidden" name="seller_id" id="sellerID" value="">
				<div class="modal-body">

					<div class="mb-3">
						<label for="balance_type" class="form-label">{{translate('Balance Type')}} <span class="text-danger">*</span></label>
						<select class="form-select" name="balance_type" id="balance_type" required>
							<option value="1">{{translate('Add Balance')}}</option>
							<option value="2">{{translate('Subtract Balance')}}</option>
						</select>
					</div>

					<div class="mb-3">
						<label for="amount" class="form-label">{{translate('Amount')}} <span class="text-danger">*</span></label>
						<div class="input-group">
							<input type="text" class="form-control" id="amount" name="amount" placeholder="{{translate('Enter amount')}}" >
							<span class="input-group-text" >{{default_currency()->name}}</span>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-success waves ripple-light">{{translate('Update')}}</button>
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

		$(document).on('click','.update-balance', function(e){

			e.preventDefault();


			var modal = $('#balanceupdate');

			modal.find('#sellerID').val($(this).attr('data-id'));

			modal.modal('show');
		})
        
	})(jQuery);
</script>
@endpush
