
@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Plan")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item"><a href="{{route('seller.plan.index')}}">
                        {{translate('Subscription History')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Subscriptions Plan")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="row">
			@forelse($plans as $plan)
				<div class="col-lg-6 col-xl-4">
					<div class="card pricing-box">
						<div class="row g-0">
							<div class="col-12">
								<div class="card-body p-4">
									<div class="d-flex  align-items-center justify-content-between gap-2">
										<h5 class="mb-1">{{ucfirst($plan->name)}}</h5>


										<h3>{{(short_amount($plan->amount))}}</h3>

									</div>


									<div class="mt-4">
										<div class="bg-light  p-3">
											<h5 class="fs-15 mb-0">
												{{translate("Plan Features")}} :
											</h5>
										</div>

										<ul class="list-group vstack mb-0">
											<li class="list-group-item">
												<div  class="d-flex align-items-center gap-2">
													<i class="ri-checkbox-line text-success fs-5"></i>
													<p  class="mb-0">
														{{translate('Total Product')}}
														<span class="text-success fw-semibold">{{($plan->total_product)}}</span>
													</p>
												</div>
											</li>
											<li class="list-group-item">
												<div class="d-flex align-items-center gap-2">
													<i class="ri-checkbox-line text-success fs-5"></i>
													<p class="mb-0">{{translate('Duration')}}
													<span class="text-success fw-semibold">{{($plan->duration)}} </span>
													{{translate('Days')}}</p>
												</div>
											</li>
										</ul>

									</div>


									<div class="text-center plan-btn mt-4">
										<a href="javascript:void(0)" class="btn btn-md btn-info bordered radius subscription w-100" data-bs-toggle="modal" data-bs-target="#purchase" data-id="{{$plan->id}}">{{translate('Purchase Now')}}</a>
									</div>
								</div>
							</div>


						</div>
					</div>
				</div>
			@empty
		     	<div class="col-12">
					<div class="card">
						 <div class="card-body">
							@include('admin.partials.not_found')
						 </div>
					</div>
				</div>
			@endforelse
		</div>
    </div>
</div>

<div class="modal fade" id="purchase" tabindex="-1" aria-labelledby="purchase" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('seller.plan.subscription')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
				<div class="modal-body">
					<div class="mt-2 text-center">
						<i class="fs-18 link-success las la-check"></i>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to subscrribe this plan?')}}
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
@endsection

@push('script-push')
<script>
	"use strict";
	$(".subscription").on('click', function(){
		var modal = $('#purchase');
		modal.find('input[name=id]').val($(this).data("id"));
		modal.modal('show');
	});
</script>
@endpush
