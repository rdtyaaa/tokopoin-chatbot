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
                        {{translate("Plans")}}
                    </li>
                </ol>
            </div>

        </div>

        <div class="card" id="orderList">
			<div class="card-header border-0">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Plan List')}}
                        </h5>
					</div>

					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<button  class="btn btn-success btn-sm w-100 add-btn waves ripple-light"
							data-bs-toggle="modal" data-bs-target="#addPlan"><i
								class="ri-add-line align-bottom me-1"></i>
								{{translate('Add Plan')}}
							</button>

						</div>
					</div>

				</div>
			</div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route('admin.plan.index')}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" value="{{request()->input('search')}}" name="search" class="form-control search"
                                    placeholder="{{translate('Search by name')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i  class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Search')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <a href="{{route('admin.plan.index')}}" class="btn btn-danger w-100 waves ripple-light">
                                        <i class="ri-refresh-line me-1 align-bottom"></i>
                                    {{translate('Reset')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card ">
                    <table class="table table-hover align-middle table-nowrap mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">

                                <th>{{translate('Name')}}</th>

                                <th>
                                    {{translate('Amount')}}
                                </th>
                                <th>
                                    {{translate('Total Product')}}
                                </th>
                                <th>
                                    {{translate('Duration')}}
                                </th>
                                <th>
                                    {{translate('Status')}}
                                </th>

                                <th >
                                    {{translate('Action')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($plans as $plan)
                                <tr>
                                    <td data-label="{{translate('Name')}}">
                                        <span class="fw-bold">{{($plan->name)}}</span>
                                    </td>

                                    <td data-label="{{translate('Amount')}}">
                                        {{round(($plan->amount))}} {{default_currency()->name}}
                                    </td>

                                    <td data-label="{{translate('Total Product')}}">
                                        {{($plan->total_product)}}
                                    </td>

                                    <td data-label="{{translate('Duration')}}">
                                        {{($plan->duration)}} {{translate('Days')}}
                                    </td>

                                    <td data-label="{{translate('status')}}">
                                        @if($plan->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                        @endif
                                    </td>

                                    <td data-label="{{translate('Action')}}">
                                        <div class="hstack justify-content-center gap-3">
                                            @if($plan->name !='Free' ||  round(($plan->amount))!= 0 )
                                            <a  title="{{translate('Update')}}"  href="javascript:void(0)"
                                            data-id="{{$plan->id}}"
                                            data-name="{{$plan->name}}"
                                            data-amount="{{round($plan->amount)}}"
                                            data-total_product="{{$plan->total_product}}"
                                            data-duration="{{$plan->duration}}"
                                            data-status="{{$plan->status}}" class="plan fs-18 link-warning"><i class="ri-pencil-fill"></i></a>

                                            <a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.plan.delete',$plan->id)}}" class="delete-item fs-18 link-danger">
                                            <i class="ri-delete-bin-line"></i></a>

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
                    {{$plans->appends(request()->all())->links()}}
                </div>
            </div>
        </div>

    </div>
</div>

@include('admin.modal.delete_modal')

<div class="modal fade" id="addPlan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addPlan" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Add New Pricing Plan')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form action="{{route('admin.plan.store')}}" method="post">
				@csrf
				<div class="modal-body">
					<div>
						<div class="mb-3">
							<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
							<input value="{{old('name')}}" type="text" class="form-control" id="name" name="name" placeholder="{{translate('Enter name')}}" required>
						</div>

						<div class="mb-3">
							<label for="amount" class="form-label">{{translate('Amount')}} <span class="text-danger">*</span></label>
							<div class="input-group mb-3">
								<input required value="{{old('amount')}}" type="number" class="form-control" id="amount" name="amount" placeholder="{{translate('Enter amount')}}" >
								<span class="input-group-text" >{{default_currency()->name}}</span>
							</div>
						</div>

						<div class="mb-3">
							<label for="total_product" class="form-label">{{translate('Total Product')}} <span class="text-danger">*</span></label>
							<div class="input-group mb-3">
								<input value="{{old('total_product')}}"  required  type="number" class="form-control" id="total_product" name="total_product" placeholder="{{translate('Enter number of product')}}" >
								<span class="input-group-text" >{{translate('Product')}}</span>
							</div>
						</div>

						<div class="mb-3">
							<label for="duration" class="form-label">{{translate('Duration')}} <span class="text-danger">*</span></label>
							<div class="input-group mb-3">
								<input required value="{{old('duration')}}"   type="number" class="form-control" id="duration" name="duration" placeholder="{{translate('Enter duration')}}" >
								<span class="input-group-text" >{{translate('Day')}}</span>
							</div>
						</div>

						<div class="mb-3">
							<label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
							<select class="form-select" name="status" id="status" required>
								<option {{ old('status')==  0 ? "seleted" : "" }} value="1">{{translate('Active')}}</option>
								<option  {{ old('status')==  1 ? "seleted" : "" }}   value="2">{{translate('Inactive')}}</option>
							</select>
						</div>
					</div>
					<div class="modal-footer px-0 pb-0 pt-3">
						<div class="hstack gap-2 justify-content-end">
							<button type="button"
								class="btn btn-danger waves ripple-light"
								data-bs-dismiss="modal">
								{{translate('Close
								')}}
							</button>
							<button type="submit"
								class="btn btn-success waves ripple-light">
								{{translate('Add Plan')}}
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="updateDataPlan" tabindex="-1" aria-labelledby="updateDataPlan" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title">{{translate('Update Pricing Plan')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<form action="{{route('admin.plan.update')}}" method="POST">
				@csrf
				<input type="hidden" name="id">
	            <div class="modal-body">
	            	<div>
						<div class="mb-3">
							<label for="uname" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="uname" name="name" placeholder="{{translate('Enter name')}}" required>
						</div>

						<div class="mb-3">
							<label for="uamount" class="form-label">{{translate('Amount')}} <span class="text-danger">*</span></label>
							<div class="input-group mb-3">
								<input required type="number" class="form-control" id="uamount" name="amount" placeholder="{{translate('Enter amount')}}" >
								<span class="input-group-text" >{{default_currency()->name}}</span>
							</div>
						</div>

						<div class="mb-3">
							<label for="utotal_product" class="form-label">{{translate('Total Product')}} <span class="text-danger">*</span></label>
							<div class="input-group mb-3">
								<input required type="number" class="form-control" id="utotal_product" name="total_product" placeholder="{{translate('Enter number of product')}}" >
								<span class="input-group-text" >{{translate('Product')}}</span>
							</div>
						</div>

						<div class="mb-3">
							<label for="uduration" class="form-label">{{translate('Duration')}}
									<span class="text-danger">*</span></label>
							<div class="input-group mb-3">
								<input required type="number" class="form-control" id="uduration" name="duration" placeholder="{{translate('Enter duration')}}" >
								<span class="input-group-text" >{{translate('Day')}}</span>
							</div>
						</div>

						<div class="mb-3">
							<label for="ustatus" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
							<select class="form-select" name="status" id="ustatus" required>
								<option value="1">{{translate('Active')}}</option>
								<option value="2">{{translate('Inactive')}}</option>
							</select>
						</div>
	            	</div>
	            </div>

				<div class="modal-footer">
					<div class="hstack gap-2 justify-content-end">
						<button type="button"
							class="btn btn-danger waves ripple-light"
							data-bs-dismiss="modal">
							{{translate('Close
							')}}
						</button>
						<button type="submit"
							class="btn btn-success waves ripple-light">
							{{translate('Update Plan')}}
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
		$('.plan').on('click', function(){
			var modal = $('#updateDataPlan');
			modal.find('input[name=id]').val($(this).data('id'));
			if($(this).data('name') == 'Free')
			{
				modal.find('input[name=name]').prop('disabled', true);
			}
			modal.find('input[name=name]').val($(this).data('name'));
			modal.find('input[name=amount]').val($(this).data('amount'));
			modal.find('input[name=total_product]').val($(this).data('total_product'));
			modal.find('input[name=duration]').val($(this).data('duration'));
			modal.find('select[name=status]').val($(this).data('status'));
			modal.modal('show');
		});

		$(".plandelete").on("click", function(){
			var modal = $("#plandelete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);

</script>
@endpush

