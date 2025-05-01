@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Deposit History")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Deposit  History")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-0">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
							{{translate("Deposit History")}}
                        </h5>
					</div>
					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<a href='{{route("seller.deposit.method")}}' class="btn btn-success btn-sm add-btn waves ripple-light">
								<i class="ri-add-line align-bottom me-1"></i> {{translate('Deposit')}}
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
                                <input type="text" name="trx_code" value="{{request()->input('trx_code')}}" class="form-control search"
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
								<th>{{translate('Receivable')}}</th>
								<th>{{translate('Charge')}}</th>
								<th>{{translate('Paid')}}</th>
								<th>{{translate('Status')}}</th>
								<th>{{translate('Action')}}</th>
							</tr>
						</thead>
						<tbody>
						   @forelse($reports as $report)
								<tr>
									<td data-label="{{translate('Time')}}">
										<span class="fw-bold">{{diff_for_humans($report->created_at)}}</span><br>
										{{get_date_time($report->created_at)}}
									</td>

									<td data-label="{{translate('Method')}}">
										<span class="fw-bold">{{(@$report->trx_number)}}</span>
									</td>
									<td data-label="{{translate('Method')}}">
										<span class="fw-bold">{{(@$report->paymentGateway ? $report->paymentGateway->name :"N/A")}}</span>
									</td>

									<td data-label="{{translate('Receivable')}}">
										{{round(($report->amount))}}{{default_currency()->name}}
									</td>

									<td data-label="{{translate('Charge')}}">
										<span class="text-danger fw-bold">	{{round(($report->charge),2)}} {{default_currency()->name}} </span>
									</td>

									<td data-label="{{translate('Paid')}}">
										<span class="fw-bold text-success">
											{{round(($report->final_amount))}} {{@$report->paymentGateway->currency->name ?? default_currency()->name}}										</span>
									</td>

									<td data-label="{{translate('Status')}}">
										@if($report->status == 1)
											<span class="badge badge-soft-warning">{{translate('Pending')}}</span>
										@elseif($report->status == 2)
											<span class="badge badge-soft-primary">{{translate('Received')}}</span>
										@elseif($report->status == 3)
											<span class="badge badge-soft-danger">{{translate('Rejected')}}</span>
											<a href="javascript:void(0)" class="link-warning fs-18 feedbackinfo" data-bs-toggle="modal" data-bs-target="#feedback" data-feedback="{{$report->feedback}}"><i class="las la-info"></i></a>
										@endif
									</td>

									<td data-label="{{ translate('Action') }}">

										<div class="hstack justify-content-center gap-3">
						
											<a title="Details" data-bs-toggle="tooltip" data-bs-placement="top"
												href="{{ route('seller.deposit.show', $report->id) }}"
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
					{{$reports->links()}}
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
