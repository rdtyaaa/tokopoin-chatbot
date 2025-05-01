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
                        {{translate("Delivery man list")}}
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
                                {{translate('Delivery man')}}
							</h5>
						</div>
					</div>

					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							  <a href="{{route('admin.delivery-man.create')}}" class="btn btn-success btn-sm w-100 waves ripple-light"> <i
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
                                <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                    placeholder="{{translate('Search  by name, email or phone')}}">
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
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>#</th>
                                <th >{{translate('Name')}}</th>
                                <th>{{translate('Email - Phone')}}</th>
                                <th>{{translate('Balance')}}
                                </th>
                                <th>{{translate('KYC')}}</th>
                                <th>{{translate('Order - Ratings')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">

                            @forelse ($deliverymanlist as $deliveryman)
                            <tr>
                                <td>
                                    {{$loop->iteration}}
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="{{show_image(file_path()['profile']['delivery_man']['path'].'/'.$deliveryman->image ,file_path()['profile']['delivery_man']['size']) }}" alt="{{$deliveryman->image}}" class="avatar-sm rounded img-thumbnail me-2">
                                    <div>
                                        <h5 class="fs-13 mb-0">
                                            {{ $deliveryman->username}}
                                        </h5>

                                    </div>
                                </td>
                                <td>
                                    {{$deliveryman->email}} - {{$deliveryman->phone}}
                                </td>

                                <td data-label="{{translate('Balance')}}">
                                    <div>
                                        <a href="javascript:void(0)" data-id="{{$deliveryman->id}}" class="update-balance"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Update Balance')}}"><span><i class="fs-4 lar la-edit"></i></span></a>
                                        <span>{{round(($deliveryman->balance))}} {{default_currency()->name}}</span>
                      
                                    </div>
                                </td>


                                <td>

                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="status-update form-check-input"
                                            data-column="is_kyc_verified"
                                            data-route="{{ route('admin.delivery-man.kyc.status.update') }}"
                                            data-model="Admin"
                                            data-status='{{ $deliveryman->is_kyc_verified == "1" ? "0":"1"}}'
                                            data-id="{{$deliveryman->id}}" {{$deliveryman->is_kyc_verified == "1" ? 'checked' : ''}}
                                        id="status-switch-is_kyc_verified-{{$deliveryman->id}}" >
                                        <label class="form-check-label" for="status-switch-is_kyc_verified-{{$deliveryman->id}}"></label>
                                        -
                                        @if($deliveryman->is_kyc_verified == 1)
                                            <span class="badge badge-soft-success">
                                                {{translate('Verified')}}
                                            </span>
                                        @else
                                            <span  class="badge badge-soft-danger">
                                                {{translate('unverified')}}
                                            </span>
                                        @endif
                                    </div>

                                </td>

                                <td>
                                    <a href="{{route('admin.delivery-man.order.list', $deliveryman->id)}}" class="fs-16" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Orders')}}">
                                        {{$deliveryman->orders->count()}}
                                    </a>

                                    -

                                    <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Ratings')}}" class="badge badge-soft-success d-inline-flex align-items-center gap-1 fs-16">
                                        {{$deliveryman->ratings->count() > 0 ? short_amount($deliveryman->ratings->avg('rating'),false,true) : 0}} <i class="ri-star-s-fill"></i>
                                    </span>

                                </td>

                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.delivery-man.status.update') }}"
                                            data-model="Admin"
                                            data-status='{{ $deliveryman->status == "1" ? "0":"1"}}'
                                            data-id="{{$deliveryman->id}}" {{$deliveryman->status == "1" ? 'checked' : ''}}
                                        id="status-switch-{{$deliveryman->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$deliveryman->id}}"></label>
                                    </div>
                                </td>

                                <td>
                                    <div class="hstack gap-3">

                                        <a href="{{route('admin.delivery-man.overview',$deliveryman->id)}}"  title="{{translate('Information')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="information fs-18 link-info">
                                            <i class="ri-information-line"></i></a>


                                        <a data-id="{{$deliveryman->id}}" href="javascript:void(0);"  title="{{translate('Password update')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="password-update fs-18 link-warning">
                                            <i class="ri-key-2-line"></i></a>


                                        <a href="{{route('admin.delivery-man.edit',$deliveryman->id)}}" title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" class=" fs-18 link-info"><i class="ri-pencil-fill"></i></a>


                                        <a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.delivery-man.delete',$deliveryman->id)}}" class="delete-item fs-18 link-danger">
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

                <div class="pagination-wrapper d-flex justify-content-end mt-4">
                     {{$deliverymanlist->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>



@include('admin.modal.delete_modal')


<div class="modal fade" id="updatePassword" tabindex="-1" aria-labelledby="updatePassword" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light pb-2">
                <h5 class="modal-title">{{translate('Update Password')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{route('admin.delivery-man.password.update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                {{translate('New Password')}}  <span class="text-danger" >*</span>
                            </label>
                            <input type="password" name="password" value="{{old('password')}}" class="form-control"
                                id="password" placeholder="{{translate('New Password')}}">
                        </div>

                        <div class="mb-3">
							<label for="comfirmPassword" class="form-label">
                                {{translate('Confirm
                                Password')}}  <span  class="text-danger"  >*</span>
                                </label>
                            <input type="password" name="password_confirmation" class="form-control"
                                id="comfirmPassword"
                                placeholder="{{translate('Confirm password')}}">
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


<div class="modal fade" id="balanceupdate" tabindex="-1" aria-labelledby="balanceupdate" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-light p-3">
				<h5 class="modal-title" >{{translate('Update Blance')}}
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close" id="close-modal"></button>
			</div>
			<form action="{{route('admin.delivery-man.balance.update')}}" method="POST">
				@csrf
				<input type="hidden" name="id" id="userID" value="">
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

           $('.country-code').select2({ });

           $('.password-update').on('click', function(){
                var modal = $('#updatePassword');
                modal.find('input[name=id]').val($(this).data('id'));

                modal.modal('show');
            });

            $(document).on('click','.update-balance', function(e){

                e.preventDefault();


                var modal = $('#balanceupdate');

                modal.find('#userID').val($(this).attr('data-id'));

                modal.modal('show');
            })

	})(jQuery);
</script>
@endpush
