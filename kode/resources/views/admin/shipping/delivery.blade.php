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
                        {{translate('Dashboard')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Shipping Delivary')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			

			<div class="card-header border-0">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
						<h5 class="card-title mb-0">
							{{translate('Shipping Delivary List')}}
						</h5>
					</div>
					<div class="col-sm-auto">
						<div class="d-flex flex-wrap align-items-start gap-2">
							<a href="{{route('admin.shipping.delivery.create')}}" class="btn btn-success add-btn w-100 waves ripple-light"><i
									class="ri-add-line align-bottom me-1"></i>
								  {{translate('Add New')}}
					      	</a>
						</div>
					</div>
				</div>
			</div>


			<div class="card-body border border-dashed border-end-0 border-start-0">
				<form action="{{route('admin.shipping.delivery.index')}}" method="get">
					<div class="row g-3">
						<div class="col-xl-4 col-lg-5">
							<div class="search-box">
								<input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
									placeholder="{{translate('Search by name')}}">
								<i class="ri-search-line search-icon"></i>
							</div>
						</div>

						<div class="col-xl-2 col-lg-2 col-sm-4 col-6">
							<div>
								<button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
										class="ri-equalizer-fill me-1 align-bottom"></i>
									{{translate('Search')}}
								</button>
							</div>
						</div>

						<div class="col-xl-2 col-lg-2 col-sm-4 col-6">
							<div>
								<a href="{{route('admin.shipping.delivery.index')}}" class="btn btn-danger w-100 waves ripple-light"> <i
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
					<table class="table table-hover table-centered align-middle table-nowrap">
						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate('Name')}}
								</th>
					
								<th scope="col">
									{{translate('Duration')}}
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
							@forelse($shippingDeliverys as $shippingDelivery)
								<tr>
									<td class="fw-medium">
										{{$loop->iteration}}
									</td>

									<td>
							
										<div class="d-flex align-items-center flex-wrap">
											<div class="flex-shrink-0 me-2">
												<img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['shipping_method']['path'].'/'.$shippingDelivery->image,file_path()['shipping_method']['size'])}}" alt="{{$shippingDelivery->name}}">
											</div>
											<div class="flex-grow-1">
												{{$shippingDelivery->name}}
											</div>
										</div>
									</td>

									<td>
										{{$shippingDelivery->duration}} {{translate('Days')}}
									</td>

									

									<td>
										@if($shippingDelivery->status == 1)
											<span class="badge badge-soft-success">{{translate('Active')}}</span>
										@else
											<span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
										@endif
									</td>

									<td>
										<div class="hstack justify-content-center gap-3">
											@if(permission_check('update_settings'))

												<a title="{{translate('Update')}}"   data-bs-toggle="tooltip" data-bs-placement="top"   class="link-warning fs-18 " href="{{route('admin.shipping.delivery.edit', $shippingDelivery->id)}}"><i class="ri-pencil-fill"></i>
												</a>
												<a href="javascript:void(0);"  title="{{translate('Delete')}}"   data-bs-toggle="tooltip" data-bs-placement="top"    data-href="{{route('admin.shipping.delivery.delete',$shippingDelivery->id)}}" class="delete-item fs-18 link-danger">
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
			</div>
		</div>
	</div>

</div>


@include('admin.modal.delete_modal')
@endsection


