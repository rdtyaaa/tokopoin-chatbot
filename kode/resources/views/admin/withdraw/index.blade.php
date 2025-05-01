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
                        {{translate("Withdraw logs")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex align-items-center">
                    <h5 class="card-title mb-0 flex-grow-1">
                        {{translate('Withdraw Log List')}}
                    </h5>
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input value="{{request()->input('search')}}" type="text" name="search" class="form-control search"
                                    placeholder="{{translate('Search by name ,username ,email')}}">
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
                                <select class="form-select" name="type" id="">

                                     <option {{request()->input('type') ==  0 ? "selected" :""}} value="0">
                                         {{translate("All")}}
                                     </option>

                                     <option {{request()->input('type') ==  1 ? "selected" :""}} value="1">
                                         {{translate("Seller")}}
                                     </option>
       
                                     <option {{request()->input('type') ==  3 ? "selected" :""}} value="3">
                                         {{translate("User")}}
                                     </option>

                                     <option {{request()->input('type') ==  2 ? "selected" :""}} value="2">
                                        {{translate("Deliveryman")}}
                                    </option>
       

                                </select>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light">
                                    <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Search')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}" class="btn btn-danger w-100 waves ripple-light">
                                    <i class="ri-refresh-line me-1 align-bottom"></i>
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
                        <a class="nav-link {{request()->routeIs('admin.withdraw.log.index') ? 'active' :'' }} All py-3"  id="All"
                            href="{{route('admin.withdraw.log.index',request()->query())}}" >
                            <i class="ri-currency-fill me-1 align-bottom"></i>
                            {{translate('All
                            Logs')}}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.withdraw.log.pending') ? 'active' :''}}   py-3"  id="Placed"
                            href="{{route('admin.withdraw.log.pending',request()->query())}}" >
                            <i class="ri-refresh-line me-1 align-bottom"></i>
                            {{translate('Pending Logs')}}
                            @if($withdraw_pending_log_count > 0 && !request()->query("delivery_man") && request()->query("type",0) == 0 )
                                <span class="badge bg-danger align-middle ms-1">{{$withdraw_pending_log_count}}</span>
                            @endif

                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{request()->routeIs('admin.withdraw.log.approved') ? 'active' :''}} Confirmed py-3"  id="Confirmed"
                            href="{{route('admin.withdraw.log.approved',request()->query())}}" >
                            <i class="ri-check-line me-1 align-bottom"></i>
                            {{translate("Approved Logs")}}

                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link Processing {{request()->routeIs('admin.withdraw.log.rejected') ? 'active' :''}}   py-3"  id="Processing"
                            href="{{route('admin.withdraw.log.rejected',request()->query())}}" >
                            <i class="ri-close-fill me-1 align-bottom"></i>
                            {{translate('Rejected Logs')}}
                        </a>
                    </li>

                </ul>

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>
                                    {{translate(
                                        "Time"
                                    )}}
                                </th>
                                <th>
                                    {{translate('Seller/Deliveryman/User')}}
                                </th>
                                <th >{{translate('Method')}}
                                </th>
                                <th>
                                    {{translate('Amount')}}
                                </th>
                                <th>
                                    {{translate('Charge')}}
                                </th>
                                <th >
                                    {{translate('Receivable')}}
                                </th>
                                <th >
                                    {{translate('Status')}}
                                </th>
                                <th >
                                    {{translate('Action')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($withdraws as $withdraw)
                                    <tr>
                                        <td data-label="{{translate('Time')}}">
                                            <span class="fw-bold">{{diff_for_humans($withdraw->created_at)}}</span><br>
                                            {{get_date_time($withdraw->created_at)}}
                                        </td>

                                        @if($withdraw->seller)
                                            <td data-label="{{translate('Seller')}}">
                                                <a href="{{route('admin.seller.info.details', $withdraw->seller_id)}}" class="fw-bold text-dark">{{(@$withdraw->seller->username)}}</a><br>
                                                {{(@$withdraw->seller->name)}}
                                            </td>

                                        @elseif($withdraw->user)
                                            <td data-label="{{translate('User')}}">
                                                <a href="{{route('admin.customer.details', $withdraw->user_id)}}" class="fw-bold text-dark">{{(@$withdraw->user->username)}}</a><br>
                                                {{(@$withdraw->user->name)}}
                                            </td>
                                        @elseif($withdraw->deliveryman_id)

                                            <td data-label="{{translate('Deliveryman')}}">
                                                <a href="{{route('admin.delivery-man.edit',$withdraw->deliveryman_id)}}" class="fw-bold text-dark">{{(@$withdraw->deliveryman->first_name)}}</a><br>
                                                {{(@$withdraw->deliveryman->email)}}
                                            </td>
                                    
                                        @else
                                            <td>
                                                N/A
                                            </td>
                                        @endif

                                        <td data-label="{{translate('Method')}}">
                                            {{(@$withdraw->method ? $withdraw->method->name : translate('N/A'))}}
                                        </td>

                                        <td data-label="{{translate('Amount')}}">
                                            {{round(($withdraw->amount))}} {{default_currency()->name}}

                                        </td>

                                        <td data-label="{{translate('Charge')}}">
                                            {{round(($withdraw->charge))}} {{default_currency()->name}}
                                        </td>

                                        <td data-label="{{translate('Receivable')}}">
                                                <span class="text-success">{{round(($withdraw->final_amount))}} {{@$withdraw->currency->name}}</span>
                                        </td>

                                        <td data-label="{{translate('Status')}}">
                                            @if($withdraw->status == "1")
                                            <span class="badge badge-soft-primary">{{translate('Received')}}</span>
                                            @elseif($withdraw->status == "2")
                                            <span class="badge badge-soft-warning">{{translate('Pending')}}</span>
                                            @elseif($withdraw->status == "3")
                                            <span class="badge badge-soft-danger ">{{translate('Rejected')}}</span>
                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Info')}}"  href="javascript:void(0)" class="text--dark feedbackinfo" data-bs-toggle="modal" data-bs-target="#feedback" data-feedback="{{$withdraw->feedback}}">
                                                <i class="las la-info fs-17"></i></a>
                                            @endif
                                        </td>

                                        <td data-label="{{translate('Action')}}">
                                            <div class="hstack justify-content-center gap-3">
                                                @if($withdraw->status == 2)
                                                    <a href="javascript:void(0)" class="link-danger fs-18  withdrawrejected" data-bs-toggle="tooltip" data-bs-placement="top" title="Withdraw Rejected" data-bs-toggle="modal" data-bs-target="#rejected" data-id="{{$withdraw->id}}"><i class="ri-close-circle-line"></i></a>

                                                    <a href="javascript:void(0)" class="link-success fs-18 withdrawapproved" data-bs-toggle="tooltip" data-bs-placement="top" title="Withdraw Approved"  data-bs-toggle="modal" data-bs-target="#approved" data-id="{{$withdraw->id}}"><i class="ri-check-double-line"></i></a>
                                                @endif
                                                <a href="{{route('admin.withdraw.log.details', $withdraw->id)}}" class="link-info fs-18 " data-bs-toggle="tooltip" data-bs-placement="top" title="Withdraw Details"><i class="ri-list-check "></i></a>
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
                    {{$withdraws->links()}}
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="rejected" tabindex="-1" aria-labelledby="rejected" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.withdraw.log.rejectedby')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
	            <div class="modal-body">
	                <div class="modal_text2  mt-3">
	                    <h6>{{translate('Are you sure to want rejected this withdraw?')}}</h6>
	                    <textarea class="form-control" name="details" placeholder="{{translate('Enter Details')}}" required></textarea>
	                </div>
	            </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approved" tabindex="-1" aria-labelledby="approved" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        	<form action="{{route('admin.withdraw.log.approvedby')}}" method="POST">
        		@csrf
        		<input type="hidden" name="id">
	            <div class="modal-body">
	                <div class="modal_icon2 text-center">
                        <i class="fs-18 link-info ri-check-double-line"></i>
	                </div>
	                <div class="modal_text2 text-center  mt-3">
	                    <h6>{{translate('Are you sure want to approved this withdraw?')}}</h6>
	                </div>
	            </div>
				<div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
	        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="feedback" tabindex="-1" aria-labelledby="feedback" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header bg-light  p-3">
                <h5 class="modal-title">
                    {{translate('Feedback')}}
                </h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <p class="feedbacktext"></p>
            </div>

        </div>
    </div>
</div>

@endsection

@push('script-push')
<script>
	(function($){
       	"use strict";

		$(".withdrawrejected").on("click", function(){
			var modal = $("#rejected");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".withdrawapproved").on("click", function(){
			var modal = $("#approved");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".feedbackinfo").on("click", function(){
			var modal = $("#feedback");
			var data = $(this).data('feedback');
			$(".feedbacktext").text(data);
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush
