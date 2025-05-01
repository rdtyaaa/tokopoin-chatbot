@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Subscribers")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Subscriptions History")}}
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
								{{translate('Subscription History')}}
							</h5>
						</div>
					</div>
					@if($subscriptions->isEmpty())
						<div class="col-sm-auto">
							<div class="d-flex flex-wrap align-items-start gap-2">
								<a href='{{route("seller.plan.subscribe")}}' class="btn btn-success btn-sm add-btn waves ripple-light">
									<i class="ri-add-line align-bottom me-1"></i> {{translate('Subscribe now')}}
							    </a>
							</div>
						</div>
					@else
						<div class="col-sm-auto">
							<div class="d-flex flex-wrap align-items-start gap-2">
								<a  href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#planupdate" class="btn btn-success btn-sm add-btn waves ripple-light">
									<i class="ri-add-line align-bottom me-1"></i> {{translate('Plan Update Request')}}
								</a>
							</div>
						</div>
					@endif
				</div>
			</div>

			<div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">

                        <div class="col-xxl-4 col-sm-6">
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
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}" class="btn btn-danger w-100 waves ripple-light"> <i
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
					<table class="table table-hover table-nowrap align-middle mb-0" id="orderTable">
						<thead class="text-muted table-light">
							<tr class="text-uppercase">
								<th>{{translate('Date')}}</th>
								<th>{{translate('Plan')}}</th>
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

									<td data-label="{{translate('Plan')}}">
										{{($subscription->plan->name)}}
									</td>

									<td data-label="{{translate('Product Available')}}">
										{{($subscription->total_product)}}
									</td>

									<td data-label="{{translate('Expired Date')}}">
										{{get_date_time($subscription->expired_date)}}
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
											@if($subscription->status == 1 || $subscription->status == 2)
												<a data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Renew')}}" href="javascript:void(0)" class="fs-18 link-info subscription" data-bs-toggle="modal" data-bs-target="#renew" data-id="{{$subscription->id}}">
													<i class="ri-add-circle-line"></i>
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

				<div class="pagination-wrapper d-flex justify-content-end mt-4 ">
                    {{$subscriptions->appends(request()->all())->links()}}
				</div>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="renew" tabindex="-1" aria-labelledby="renew" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('seller.plan.renew')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
				<div class="modal-body">
					<div class="mt-2 text-center">
						<i class="las fs-1 link-success la-check"></i>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to renew this plan?')}}
							</p>
						</div>
					</div>
					<div class="d-flex gap-2 justify-content-center mt-4 mb-2">
						<button type="button" class="btn w-sm btn-danger"
							data-bs-dismiss="modal">
							{{translate('Cancel')}}

						</button>
						<button type="submit" class="btn w-sm btn-success "
							id="delete-href">
							{{translate('Confirm!')}}
						</button>
					</div>
				</div>

	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="planupdate" tabindex="-1" aria-labelledby="planupdate" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Plan Update Request')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('seller.plan.update.request')}}" method="POST">
				@csrf
	            <div class="modal-body">
					<div class="mb-3">
						<label for="id" class="form-label">{{translate('Select Plan')}} <span class="text-danger">*</span></label>
						<select class="form-select" name="id" id="id" required>
							<option value="">{{translate('Select One')}}</option>
							@foreach($plans as $plan)
								<option value="{{$plan->id}}">{{ucfirst($plan->name)}} -- {{(short_amount($plan->amount))}} </option>
							@endforeach
						</select>
					</div>
	            </div>

	            <div class="modal-footer">
					<button type="button" class="btn w-sm btn-danger"
						data-bs-dismiss="modal">
						{{translate('Cancel')}}

					</button>
					<button type="submit" class="btn w-sm btn-success "
						>
						{{translate('Confirm!')}}
					</button>
	            </div>
	        </form>
        </div>
    </div>
</div>

@endsection

@push('script-push')
	<script>
		"use strict";
		$(".subscription").on('click', function(){
			var modal = $('#renew');
			modal.find('input[name=id]').val($(this).data("id"));
			modal.modal('show');
		});
	</script>
@endpush


