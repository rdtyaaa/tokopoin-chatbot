@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{$title}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                            {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Currencies')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Currency List')}}
                        </h5>
					</div>

					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<button type="button" class="btn btn-success btn-sm add-btn waves ripple-light"
								data-bs-toggle="modal" data-bs-target="#createCurrency"><i
									class="ri-add-line align-bottom me-1"></i>
								{{translate('Add New Currency')}}
							</button>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="table-responsive table-card">
					<table class="table table-hover table-centered align-middle table-nowrap mb-0">
						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate('Name')}}
								</th>
								<th scope="col">
									{{translate('Symbol')}}
								</th>
								<th scope="col">
									{{translate('Rate')}}
								</th>
								<th scope="col">
									{{translate('Status')}}
								</th>
								<th scope="col">
									{{translate('Action')}}
								</th>
							</tr>
						</thead>

						<tbody>

							@forelse($currencies as $currency)
								<tr>
									<td class="fw-medium">
										{{$loop->iteration}}
									</td>

									<td>
										<div class="d-flex align-items-center">

											<div class="flex-grow-1">
												{{$currency->name}}

												@if($currency->default == '1')
													<span class="badge badge-soft-success">
														<i class="ri-star-s-fill"></i>  {{translate('Default')}}
													</span>
												@endif

											</div>
										</div>
									</td>

									<td>
										{{ucfirst($currency->symbol)}}
									</td>

									<td>
										<span class="badge badge-soft-dark">1 {{default_currency()->name}} = 
											{{ number_format($currency->rate,site_settings('digit_after_decimal'))}} {{($currency->name)}}</span>
									</td>


									<td>
										<div class="form-check form-switch">
											<input type="checkbox" class="status-update form-check-input"
												data-column="status"
												data-route="{{ route('admin.general.setting.currency.status.update') }}"
												data-model="Currency"
												data-status='{{ $currency->status == "1" ? "2":"1"}}'
												data-id="{{$currency->id}}" {{$currency->status == "1" ? 'checked' : ''}}
											id="status-switch-{{$currency->id}}" >
											<label class="form-check-label" for="status-switch-{{$currency->id}}"></label>
										</div>
									</td>


									<td>
										<div class="hstack justify-content-center gap-3">
											@if(permission_check('update_settings'))

												@if($currency->default != '1' && $currency->status =='1')
													<a href="{{route('admin.general.setting.currency.default',$currency->id)}}" title="{{translate('Default')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="pointer badge badge-soft-success">
														<i class="ri-star-s-fill"></i>  {{translate('Make Default')}}
													</a>
												@endif

												<a data-id="{{$currency->id}}" data-name="{{$currency->name}}"  data-symbol="{{$currency->symbol}}"  data-status="{{$currency->status}}" title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-rate="{{number_format($currency->rate,site_settings('digit_after_decimal'))}}" data-bs-toggle="modal" data-bs-target="#updatecurrency" href="javascript:void(0)" class="currencydata fs-18 link-warning"><i class="ri-pencil-fill"></i></a>

												@if($currency->default != '1' &&   session()->get('currency')  != $currency->id )
													<a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.general.setting.currency.delete',$currency->id)}}" class="delete-item fs-18 link-danger">
													<i class="ri-delete-bin-line"></i></a>
												@endif
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
						{{$currencies->links()}}
				</div>
			</div>
		</div>
	</div>

</div>

<div class="modal fade" id="createCurrency" tabindex="-1" aria-labelledby="createCurrency" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Add New Currency')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.general.setting.currency.store')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<div class="modal-body">
					<div class="mb-3">
						<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
						<input value ="{{old('name')}}"   type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter name')}}" required>
					</div>

					<div class="mb-3">
						<label for="symbol" class="form-label">{{translate('Symbol')}} <span class="text-danger">*</span></label>
						<input value ="{{old('symbol')}}"  type="text" class="form-control" id="symbol" name="symbol" placeholder="{{translate('Enter symbol')}}" required>
					</div>

					<div class="mb-3">
						<label for="rate" class="form-label">{{translate('Exchange Rate')}} <span class="text-danger">*</span></label>
						<div class="input-group">
							<span class="input-group-text" id="basic-addon1">1 {{default_currency()->name}} = </span>
							<input step="any" value ="{{old('rate')}}" type="text" id="rate" name="rate" class="form-control" placeholder="0.00" aria-label="Username" aria-describedby="basic-addon1">
						</div>
					</div>

					<div class="mb-3">
						<label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
						<select class="form-select" name="status" id="status" required>
							<option {{old('status') ==  1 ? 'selected' :""}}  value="1">{{translate('Active')}}</option>
							<option {{old('status') ==  2 ? 'selected' :""}}   value="2">{{translate('Inactive')}}</option>
						</select>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="updatecurrency" tabindex="-1" aria-labelledby="updatecurrency" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Update Currency')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.general.setting.currency.update')}}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="id">
				<div class="modal-body">
					<div class="mb-3">
						<label for="uname" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
						<input value="old('name')" type="text" class="form-control" id="uname" name="name" placeholder="{{translate('Enter name')}}" required>
					</div>

					<div class="mb-3">
						<label for="usymbol" class="form-label">{{translate('Symbol')}} <span class="text-danger">*</span></label>
						<input value="old('symbol')" type="text" class="form-control" id="usymbol" name="symbol" placeholder="{{translate('Enter symbol')}}" required>
					</div>

					<div class="mb-3">
						<label for="urate" class="form-label">{{translate('Exchange Rate')}} <span class="text-danger">*</span></label>
						<div class="input-group">
							<span class="input-group-text" id="basic-addon1">1 {{default_currency()->name}} = </span>
							<input step="any" value="old('rate')" type="text" id="urate" name="rate" class="form-control" placeholder="0.00" aria-label="Username" aria-describedby="basic-addon1">
						</div>
					</div>


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

@include('admin.modal.delete_modal')

@endsection
@push('script-push')
<script>
	(function($){
		"use strict";

		$('.currencydata').on('click', function(){
			var modal = $('#updatecurrency');
			modal.find('input[name=id]').val($(this).data('id'));
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('input[name=symbol]').val($(this).data('symbol'));
			modal.find('input[name=rate]').val($(this).data('rate'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

        $(".currencyDelete").on("click", function(){
			var modal = $("#delete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

	})(jQuery);
</script>
@endpush


