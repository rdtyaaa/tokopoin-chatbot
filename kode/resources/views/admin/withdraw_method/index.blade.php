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
                        {{translate("Withdraw Methods")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate("Withdraw Method List")}}
                        </h5>
					</div>
					@if(permission_check('create_method'))
						<div class="col-sm-auto">
							<div class="d-flex flex-wrap align-items-start gap-2">
								<a href="{{route('admin.withdraw.method.create')}}" class="btn btn-success btn-sm add-btn waves ripple-light">
									<i class="ri-add-line align-bottom me-1"></i>
									{{translate("Create Withdraw
									Method")}}
								</a>
							</div>
						</div>
					@endif
				</div>
			</div>

			<div class="card-body">
				<div class="table-responsive table-card">
					<table class="table table-hover table-centered align-middle table-nowrap mb-0">

						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate("Method Name")}}
								</th>
								<th scope="col">
									{{translate("Currency Rate")}}
								</th>
								<th scope="col">
									{{translate("Charge")}}
								</th>
								<th scope="col">
									{{translate("Withdraw Limit")}}
								</th>
								<th scope="col">
									{{translate("Duration")}}
								</th>
								<th scope="col">
									{{translate("Status")}}
								</th>
								<th scope="col">
									{{translate("Action")}}
								</th>
							</tr>
						</thead>

						<tbody>
							@forelse($withdrawMethods as $withdrawMethod)
							<tr>
								<td class="fw-medium">
								    {{$loop->iteration}}
								</td>

								<td>
									<div class="d-flex align-items-center flex-wrap">
										<div class="flex-shrink-0 me-2">
											<img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['withdraw']['path'].'/'.$withdrawMethod->image,file_path()['withdraw']['size'])}}" alt="{{$withdrawMethod->name}}"
												>
										</div>
										<div class="flex-grow-1">
											{{$withdrawMethod->name}}
										</div>
									</div>
								</td>

								<td>
									<span>
									1 {{default_currency()->name}} = {{round(($withdrawMethod->rate))}} {{@$withdrawMethod->currency->name}}</span>
								</td>

								<td>
									{{round(($withdrawMethod->fixed_charge))}} + {{round(($withdrawMethod->percent_charge))}} %
								</td>

								<td>
									<span class="rounded p-2 badge-soft-info">{{round(($withdrawMethod->min_limit))}} - {{round(($withdrawMethod->max_limit))}} {{(default_currency()->name)}}</span>
								</td>

								<td>
									{{$withdrawMethod->duration}} {{translate('Hour')}}
								</td>

								<td>
									@if($withdrawMethod->status == 1)
										<span class="badge badge-soft-success">{{translate('Active')}}</span>
									@else
										<span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
									@endif
								</td>

								<td>
									<div class="hstack justify-content-center gap-3">
										@if(permission_check('update_method'))
											<a  title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.withdraw.method.edit', $withdrawMethod->id)}}" class="pointer fs-18 link-warning">
												<i class="ri-pencil-fill"></i>
											</a>
									    @endif
										@if(permission_check('delete_method'))
											<a  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.withdraw.method.delete',$withdrawMethod->id)}}" class="delete-item fs-18 link-danger">
											<i class="ri-delete-bin-line"></i></a>
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
					{{$withdrawMethods->links()}}
				</div>
			</div>
		</div>
	</div>
</div>

@include('admin.modal.delete_modal')
@endsection
@push('script-push')
<script>
	(function($){
		"use strict";

		$(".withdrawdelete").on("click", function(){
			var modal = $("#withdrawdelete");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});
	})(jQuery);
</script>
@endpush


