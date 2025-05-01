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
                        {{translate("Coupons")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Coupon List')}}
                        </h5>
                    </div>
                    
                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.promote.coupon.create')}}" class="btn btn-success btn-sm add-btn waves ripple-light">
                                <i class="ri-add-line align-bottom me-1"></i>
                                    {{translate('Add New')}}
                            </a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">

                                <th>{{translate('Duration')}}</th>
                                <th>{{translate('Name')}}</th>
                                <th>{{translate('Code')}}</th>
                                <th>{{translate('Type')}}</th>
                                <th>{{translate('Value')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($coupons as $coupon)
                            <tr>
                                <td data-label="{{translate('Date')}}">
                                    {{get_date_time($coupon->start_date)}} - {{get_date_time($coupon->end_date)}}
                                </td>

                                <td data-label="{{translate('Name')}}">
                                    {{($coupon->name)}}
                                </td>

                                <td data-label="{{translate('Code')}}">
                                    {{($coupon->code)}}
                                </td>

                                <td data-label="{{translate('Type')}}">
                                    @if($coupon->type == 1)
                                        <span class="badge badge-soft-success">{{translate('Fixed ')}}</span>
                                    @else
                                        <span class="badge badge-soft-danger">{{translate('Percent ')}}</span>
                                    @endif
                                </td>

                                <td data-label="{{translate('Value')}}">
                                    {{round(($coupon->value))}}
                                </td>

                                <td data-label="{{translate('Status')}}">
                                    @if($coupon->status == 1)
                                        <span class="badge badge-soft-success">{{translate('Enable')}}</span>
                                    @else
                                        <span class="badge badge-soft-danger">{{translate('Disable')}}</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="hstack justify-content-center gap-3">
                                        <a class="fs-18 link-warning" title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.promote.coupon.edit', $coupon->id)}}"><i class="ri-pencil-line"></i></a>

                                        <a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0)" class="link-danger fs-18 ms-1 coupondelete" data-bs-toggle="modal" data-id="{{$coupon->id}}" data-bs-target="#coupondelete">
                                            <i class="ri-delete-bin-6-line"></i>
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

                <div class="pagination-wrapper d-flex justify-content-end mt-4">
                    {{ $coupons->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="coupondelete" tabindex="-1" aria-labelledby="coupondelete" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="{{route('admin.promote.coupon.delete')}}" method="post">
				@csrf
				<input type="hidden" name="id">
				<div class="modal-body">
					<div class="mt-2 text-center">
						<lord-icon src="{{asset('assets/global/gsqxdxog.json')}}" trigger="loop"
							colors="primary:#f7b84b,secondary:#f06548"
							class="loader-icon">
						</lord-icon>
						<div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
							<h4>
							{{translate('Are you sure ?')}}
							</h4>
							<p class="text-muted mx-4 mb-0">
								{{translate('Are you sure you want to
								remove this record ?')}}
							</p>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
					<button type="submit" class="btn btn-danger">{{translate('Delete')}}</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection

@push('script-push')
    <script>
         'use strict'
        $(".coupondelete").on("click", function(){
			var modal = $("#coupondelete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
    </script>
@endpush
