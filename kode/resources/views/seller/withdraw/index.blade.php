@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Withdraw History")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Withdraw  History")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-0">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
							{{translate("Withdraw History")}}
                        </h5>
					</div>
					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<a href='{{route("seller.withdraw.method")}}' class="btn btn-success btn-sm add-btn waves ripple-light">
								<i class="ri-add-line align-bottom me-1"></i> {{translate('Withdraw')}}
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
                                    placeholder="{{translate('Search By TRX ID')}}">
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
					<table class="table table-hover table-centered align-middle table-nowrap mb-0">
						<thead class="text-muted table-light">
							<tr>
								<th>{{translate('Time')}}</th>
								<th>{{translate('Transaction Number')}}</th>
								<th>{{translate('Method')}}</th>
								<th>{{translate('Amount')}}</th>
								<th>{{translate('Charge')}}</th>
								<th>{{translate('Receivable')}}</th>
								<th>{{translate('Status')}}</th>
								<th>{{translate('Action')}}</th>
							</tr>
						</thead>
						<tbody>
						   @forelse($withdraws as $withdraw)
								<tr>
									<td data-label="{{translate('Time')}}">
										<span class="fw-bold">{{diff_for_humans($withdraw->created_at)}}</span><br>
										{{get_date_time($withdraw->created_at)}}
									</td>

									<td data-label="{{translate('Method')}}">
										<span class="fw-bold">{{(@$withdraw->trx_number)}}</span>
									</td>
									<td data-label="{{translate('Method')}}">
										<span class="fw-bold">{{(@$withdraw->method ? $withdraw->method->name :"N/A")}}</span>
									</td>

									<td data-label="{{translate('Amount')}}">
										<span class="text-primary fw-bold">{{(show_amount($withdraw->amount,default_currency()->symbol))}} </span>
									</td>

									<td data-label="{{translate('Charge')}}">
										<span class="text-danger fw-bold">{{(show_amount($withdraw->charge,default_currency()->symbol))}} </span>
									</td>

									<td data-label="{{translate('Receivable')}}">
										<span class="fw-bold text-success">
											{{show_amount($withdraw->final_amount ,@$withdraw->currency->symbol)}}
										</span>
									</td>

									<td data-label="{{translate('Status')}}">
										@if($withdraw->status == 1)
											<span class="badge badge-soft-success">{{translate('Received')}}</span>
										@elseif($withdraw->status == 2)
											<span class="badge badge-soft-primary">{{translate('Pending')}}</span>
										@elseif($withdraw->status == 3)
											<span class="badge badge-soft-danger">{{translate('Rejected')}}</span>
											<a href="javascript:void(0)" class="link-warning fs-18 feedbackinfo" data-bs-toggle="modal" data-bs-target="#feedback" data-feedback="{{$withdraw->feedback}}"><i class="las la-info"></i></a>
										@endif
									</td>
									<td data-label="{{ translate('Action') }}">

										<div class="hstack justify-content-center gap-3">
						
											<a title="Details" data-bs-toggle="tooltip" data-bs-placement="top"
												href="{{ route('seller.withdraw.show', $withdraw->id) }}"
												class="fs-18 link-info ms-1"><i class="ri-list-check"></i>
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
				<div class="pagination-wrapper d-flex justify-content-end mt-4 ">
					{{$withdraws->links()}}
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="feedback" tabindex="-1" aria-labelledby="feedback" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('FeedBack')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<div class="modal-body">
				<div>
					<p class="feedbacktext"></p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
			</div>

        </div>
    </div>
</div>

@endsection
@push('script-push')
<script>
	(function($){
       	"use strict";
		$(".feedbackinfo").on("click", function(){
			var modal = $("#feedback");
			var data = $(this).data('feedback');
			$(".feedbacktext").text(data);
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
