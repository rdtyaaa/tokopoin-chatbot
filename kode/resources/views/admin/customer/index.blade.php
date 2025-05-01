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
                        {{translate("Customers")}}
                    </li>
                </ol>
            </div>

        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0 flex-grow-1">
                            {{translate('Customer List')}}
                        </h5>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.customer.create')}}" class="btn btn-success btn-sm w-100 waves ripple-light"> <i
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
                                <input type="text" name="search" class="form-control search"
                                    placeholder="{{translate('Search by name , email ,username or phone')}}">
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
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}" class="btn btn-danger w-100 waves ripple-light"
                                    > <i
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
                        <a class="nav-link {{request()->routeIs('admin.customer.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.customer.index')}}" >
                            <i class="ri-group-fill me-1 align-bottom"></i>
                            {{translate('All
                            Customer')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.customer.active') ? 'active' :''}}   py-3"  id="Placed"
                            href="{{route('admin.customer.active')}}" >

                            <i class="ri-user-follow-line me-1 align-bottom"></i>
                            {{translate('Active Customer')}}

                        </a>
                    </li>
                    <li class="nav-item">
                        <a class='nav-link {{request()->routeIs("admin.customer.banned") ? "active" :""}} Confirmed py-3'  id="Confirmed"
                            href="{{route('admin.customer.banned')}}" >

                            <i class="ri-user-unfollow-line me-1 align-bottom"></i>
                            {{translate("Banned Customer")}}

                        </a>
                    </li>
                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>{{translate('Customer - Username')}}</th>
                                <th>{{translate('Email - Phone')}}</th>
                                <th>{{translate('Balance')}}
                                </th>
                                <th>{{translate('Point')}}
                                </th>
                        
                                <th>{{translate('Number of Orders')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Joined At')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($customers as $customer)
                                <tr>
                                    <td data-label='{{translate("Customer - Username")}}'>
                                        <span class="fw-bold">
                                            {{($customer->name ?? 'N/A')}}
                                        </span>
                                            <br>
                                        {{($customer->username ?? 'N/A')}}
                                    </td>

                                    <td data-label="{{translate('Email - Phone')}}">
                                        {{($customer->email)}}<br>
                                        {{($customer->phone ?? 'N/A')}}
                                    </td>

                                    <td data-label="{{translate('Balance')}}">
                                        <div>
                                            <a href="javascript:void(0)" data-id="{{$customer->id}}" class="update-balance"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Update Balance')}}"><span><i class="fs-4 lar la-edit"></i></span></a>
                                            <span>{{round(($customer->balance))}} {{default_currency()->name}}</span>
                          
                                        </div>
                                   </td>


                                   <td data-label="{{translate('Rewards')}}">
                                   
                                        <a   title="{{translate('View rewards')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.customer.rewards',['user_id' => $customer->id])}}">
                                            {{
                                                $customer->rewards->sum("point")
                                               }}
                                        </a>
                                   </td>


                                
                                    <td class="text-start" data-label="{{translate('Number of Orders')}}">
                                        {{$customer->order->count()}}
                                    </td>

                                    <td data-label="{{translate('Status')}}">
                       
                                        @if($customer->status == 1)
                                           <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{translate('Banned')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Joined At')}}">
                                        {{diff_for_humans($customer->created_at)}}<br>
                                        {{get_date_time($customer->created_at)}}
                                    </td>

                                    <td data-label="{{translate('Action')}}">
                                        <div class="hstack justify-content-center gap-3">

                                            <a target="_blank" class="link-success fs-18 " data-bs-toggle="tooltip" data-bs-placement="top" title="Login" href="{{route('admin.customer.login', $customer->id)}}"><i class="ri-login-box-line"></i></a>

                                            <a class="link-info fs-18 " data-bs-toggle="tooltip" data-bs-placement="top" title="Details"  href="{{route('admin.customer.details', $customer->id)}}"><i class="ri-list-check"></i></a>

                                                                     
                                            <a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.customer.delete',$customer->id)}}" class="delete-item fs-18 link-danger">
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

                <div class="pagination-wrapper d-flex justify-content-end mt-4 ">
                    {{$customers ->appends(request()->all())->links()}}
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
			<form action="{{route('admin.customer.balance.update')}}" method="POST">
				@csrf
				<input type="hidden" name="user_id" id="userID" value="">
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

			modal.find('#userID').val($(this).attr('data-id'));

			modal.modal('show');
		})
        
	})(jQuery);
</script>
@endpush

