@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Withdraw Method")}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Withdraw  Method")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate("Withdraw Method")}}
                        </h5>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="row g-4">
					@forelse($withdrawMethods as $withdrawMethod)
						<div class="col-xl-6 ">
							<div class="border rounded">
								<div class="card-header border-bottom-dashed p-3">
									<div class="d-flex align-items-center justify-content-between">
										<h5 class="mb-0 fs-14">
											{{$withdrawMethod->name}}
										</h5>
									</div>
								</div>

								<div class="row g-0">
									<div class="col-lg-5">
										<div class="card-body h-100">
											<div class="cardImageContainer">
												<img src="{{show_image(file_path()['withdraw']['path'].'/'.$withdrawMethod->image,file_path()['withdraw']['size'])}}" class="card-img-top img-fluid img-thumbnail p-2" alt="{{$withdrawMethod->image}}">
											</div>

											<div class="text-center plan-btn mt-3">
												<button class="btn btn-md btn-success withdrawmethod" data-bs-toggle="modal" data-id="{{$withdrawMethod->id}}" data-bs-target="#methodModal">{{translate('Withdraw Now')}}</button>
											</div>
										</div>
									</div>

									<div class="col-lg-7">
										<div class="p-3">
											<div class="p-2 bg-light">
												<h5 class="fs-15 mb-0"> {{translate('Method Details')}} :</h5>
											</div>

											<div class="pt-3">
												<ul class="list-unstyled vstack gap-2 mb-0">
													<li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2 px-2">
														<div class="me-auto">{{translate('Withdraw Limit')}}</div>
														<span>{{round(($withdrawMethod->min_limit))}} - {{round(($withdrawMethod->max_limit))}} {{(default_currency()->name)}}</span>
													</li>
													<li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2 px-2">
														<div class="me-auto">{{translate('Charge')}}</div>
														<span>{{round(($withdrawMethod->fixed_charge))}} {{(default_currency()->name)}} + {{round(($withdrawMethod->percent_charge))}} %</span>
													</li>
													<li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2 px-2">
														<div class="me-auto">{{translate('Processing Time')}}</div>
														<span>{{$withdrawMethod->duration}} {{translate('Hour')}}</span>
													</li>
												</ul>
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
	</div>
</div>

<div class="modal fade" id="methodModal" tabindex="-1" aria-labelledby="methodModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Withdraw Now')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('seller.withdraw.money')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
				<div class="modal-body">
					<div class="input-group mb-3">
					  	<input type="text" class="form-control" value="{{old('amount')}}" name="amount" placeholder="{{translate('Enter amount')}}" aria-label="Recipient's username">
					  	<span class="input-group-text" >{{default_currency()->name}}</span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-md btn-danger " data-bs-dismiss="modal">{{translate('Close')}}</button>
					<button type="submit" class="btn btn-md btn-success ">{{translate('Submit')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection
@push('script-push')
<script>
	"use strict";
	$('.withdrawmethod').on('click', function(){
		var modal = $('#methodModal');
		modal.find('input[name=id]').val($(this).data('id'));
		modal.modal('show');
	});
</script>
@endpush
