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
                        {{translate('Dashboard')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Seller Subscriptions")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card" id="orderList">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        {{translate('Subscriptions List')}}
                    </h5>
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route('admin.plan.subscription')}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search by name,username or email')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}" class="form-control search"
                                    placeholder="{{translate('Search by date')}}">
                                <i class="ri-time-line search-icon"></i>

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
                                <a href="{{route('admin.plan.subscription')}}" class="btn btn-danger w-100 waves ripple-light"> <i
                                        class="ri-refresh-line me-1 align-bottom"></i>
                                    {{translate('Reset')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>{{translate('Date')}}</th>
                                <th>{{translate('Seller')}}</th>
                                <th>{{translate('Plan')}}</th>
                                <th>{{translate('Amount')}}</th>
                                <th>{{translate('Total Product')}}</th>
                                <th>{{translate('Expired Date')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($subscriptions as $subscription)
                                <tr>
                                    <td data-label="{{translate('Time')}}">
                                        <span class="fw-bold">{{diff_for_humans($subscription->created_at)}}</span><br>
                                        {{get_date_time($subscription->created_at)}}
                                    </td>

                                    <td data-label="{{translate('Seller')}}">
                                        <a href="{{route('admin.seller.info.details', $subscription->seller_id)}}" class="fw-bold text-dark">{{(@$subscription->seller->username)}}
                                        </a><br>
                                        <span>{{@$subscription?->seller->name ?? 'N/A'}}</span>
                                    </td>

                                    <td data-label="{{translate('Plan')}}">
                                        {{@($subscription?->plan->name)}}
                                    </td>

                                    <td data-label="{{translate('Plan')}}">
                                       {{(short_amount($subscription->plan->amount))}}
                                    </td>

                                    <td data-label="{{translate('Product Available')}}">
                                        {{@($subscription->total_product)}}
                                    </td>

                                    <td data-label="{{translate('Expired Date')}}">
                                        {{get_date_time(Carbon\Carbon::parse($subscription->created_at)->addMonths($subscription->plan->duration))}}
                                    </td>

                                    <td data-label="{{translate('Status')}}">
                                        @if($subscription->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Running')}}</span>
                                        @elseif($subscription->status == 2)
                                            <span class="badge badge-soft-warning">{{translate('Expired')}}</span>
                                        @elseif($subscription->status == 3)
                                            <span class="badge badge-soft-primary">{{translate('Requested')}}</span>
                                        @elseif($subscription->status == 4)
                                            <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Action')}}">

                                        <div class="hstack justify-content-center gap-3">
                                            @if($subscription->status == 3)
                                                <a href="javascript:void(0)" class="link-success fs-18 ms-2 planapproved" data-bs-toggle="modal" data-id="{{$subscription->id}}" data-bs-target="#success">
                                                    <i class="ri-check-double-fill"></i>
                                                </a>
                                            @else
                                                {{translate('N/A')}}
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
                    {{$subscriptions->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="success" tabindex="-1" aria-labelledby="success" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.plan.subscription.approved')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <i class=" link-success fs-18 las la-check-double"></i>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>
                               {{translate('Are you sure ?')}}
                            </h4>
                            <p class="text-muted mx-4 mb-0">
                                {{translate('Are you sure want to approve this update plan?')}}
                            </p>
                        </div>
                    </div>
                </div>
				<div class="modal-footer">
					<div class="hstack gap-2 justify-content-end">
						<button type="button"
							class="btn btn-danger waves ripple-light"
							data-bs-dismiss="modal">

						   {{translate('Cancel')}}
						</button>
						<button type="submit"
							class="btn btn-success waves ripple-light"
							id="add-btn">

							 {{translate('Approved')}}
						 </button>
					</div>
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
		$(".planapproved").on("click", function(){
			var modal = $("#success");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush



