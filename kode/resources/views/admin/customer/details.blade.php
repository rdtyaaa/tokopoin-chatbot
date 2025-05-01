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
                    <li class="breadcrumb-item"><a href="{{route('admin.customer.index')}}">
                        {{translate('Customers')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Details")}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="row">

			<div class="col-xxl-3 col-xl-4 col-lg-5">
				<div class="card sticky-side-div">
					 <div class="card-header border-bottom-dashed ">
						<div class="d-flex align-items-center">
							<h5 class="card-title mb-0 flex-grow-1">
								{{translate('Customer Details')}}
							</h5>
						</div>
					 </div>

					 <div class="card-body">
                        <div class="px-3">
                            <div class="profile-section-image">
                                <img src="{{show_image(file_path()['profile']['user']['path'].'/'.$user->image,file_path()['profile']['user']['size'])}}" alt="{{$user->image}}" class=" w-100 img-thumbnail">
                            </div>
                            <div class="mt-3">
                                <h6 class="mb-0">{{$user->name}}</h6>
                                <p>{{translate('Joining Date')}} {{get_date_time($user->created_at,'d M, Y h:i A')}}</p>
                            </div>
                        </div>

                        <div class="p-3 bg-body rounded">
                            <h6 class="mb-3 fw-bold">{{translate('Customer information')}}</h6>

                            <ul class="list-group">
                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Username')}}
                                    </span>
                                    <span class="font-weight-bold">{{$user->username?? translate('N/A')}}</span>
                                </li>

                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Email')}}
                                    </span>
                                    <span class="font-weight-bold text-break">{{$user->email}}</span>
                                </li>

                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Phone')}}
                                    </span>
                                    <span class="font-weight-bold">{{$user->phone ?? translate('N/A')}}</span>
                                </li>

                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Status')}}
                                    </span>

                                    @if($user->status == 1)
                                        <span class="badge badge-pill bg-success">{{translate('Active')}}</span>
                                    @else
                                        <span class="badge badge-pill bg-danger">{{translate('Banned')}}</span>
                                    @endif
                                </li>
                            </ul>

                            <ul class="mt-4 list-group">
                                <li class=" d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Number Of Orders')}}
                                    </span>
                                    <span>{{$user->order->count()}}</span>
                                </li>
                                <li class=" d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Total Item Wishlist')}}
                                    </span>
                                    <span>{{$user->wishlist->count()}}</span>
                                </li>
                                <li class=" d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Total Products reviewed')}}
                                    </span>

                                    <span>{{$user->reviews->count()}}</span>
                                </li>
                            </ul>
                        </div>
					 </div>
				</div>
			</div>

			<div class="col-xxl-9 col-xl-8 col-lg-7">
				<div class="card">
					<div class="card-header border-bottom-dashed ">
						<div class="d-flex align-items-center">
							<h5 class="card-title mb-0 flex-grow-1">
								{{translate('Latest Orders')}}
							</h5>
						</div>
					</div>

				     <div class="card-body">
						<div class="table-responsive mb-4">
							<table class="table table-hover table-nowrap align-middle" >
								<thead class="text-muted table-light">
									<tr class="text-uppercase">
										<th>{{translate('Order Number - Time')}}</th>
										<th>{{translate('Status')}}</th>
									</tr>
								</thead>

								<tbody class="list form-check-all">
									@forelse($user->order->take(5) as $value)
										<tr>
											<td data-label="{{translate('Order Number - Time')}}">
												{{($value->order_id)}} -
												{{diff_for_humans($value->created_at)}}
											</td>

											<td data-label="{{translate('Status')}}">
												@if($value->status == App\Models\Order::PLACED)
													<span class="badge badge-soft-primary">{{translate('Placed')}}</span>
												@elseif($value->status == App\Models\Order::CONFIRMED)
													<span class="badge badge-soft-info">{{translate('Confirmed')}}</span>
												@elseif($value->status == App\Models\Order::PROCESSING)
													<span class="badge badge-soft-secondary">{{translate('Processing')}}</span>
												@elseif($value->status == App\Models\Order::SHIPPED)
													<span class="badge badge-soft-warning">{{translate('Shipped')}}</span>
												@elseif($value->status == App\Models\Order::DELIVERED)
													<span class="badge badge-soft-success">{{translate('Delivered')}}</span>
												@elseif($value->status == App\Models\Order::CANCEL)
													<span class="badge badge-soft-danger">{{translate('Cancel')}}</span>
												@endif
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

						<div>
							<h6 class="fw-bold mb-3">{{translate('Order & Transaction')}}</h6>
							<div class="row">
								<div class="col-xxl-4 col-xl-6">
									<div class="card card-animate bg-soft-gray">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                       <i class="ri-disc-line text-primary"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                       	<span data-target="{{count($user->transaction)}}"
															class="counter-value">{{count($user->transaction)}}
														</span>
                                                    </h4>


                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                       {{translate('Total transaction')}}
                                                    </p>

                                                    <a href="{{route('admin.customer.transaction', $user->id)}}" class="d-flex align-items-center justify-content-end gap-1">
                                                        {{translate('View All')}}
                                                        <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
									</div>
								</div>

								<div class="col-xxl-4 col-xl-6">
									<div class="card card-animate bg-soft-green">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                       <i class="las la-shopping-cart text-success"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                      	<span data-target="{{$user->physicalProductOrder->count()}}"
															class="counter-value">{{$user->physicalProductOrder->count()}}
														</span>
                                                    </h4>


                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                       {{translate('physical product')}}
                                                    </p>

                                                    <a href="{{route('admin.customer.physical.product.order', $user->id)}}" class="d-flex align-items-center justify-content-end gap-1">
                                                        {{translate('View All')}}
                                                        <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
									</div>
								</div>

								<div class="col-xxl-4 col-xl-6">
									<div class="card card-animate bg-soft-orange">
                                         <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                       <i class="las la-wallet text-warning"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                      	<span class="counter-value"  data-target="{{$user->digitalProductOrder->count()}}"  >{{$user->digitalProductOrder->count()}}</span>
                                                    </h4>

                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                       {{translate('Digital product')}}
                                                    </p>

                                                    <a href="{{route('admin.customer.digital.product.order', $user->id)}}" class="d-flex align-items-center justify-content-end gap-1">
                                                        {{translate('View All')}}
                                                        <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
									</div>
								</div>
							</div>
						</div>

						<div class="mt-3">
							<h6 class="fw-bold mb-3">{{translate('Customer Information Update')}}</h6>

                            <form action="{{route('admin.customer.update', $user->id)}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-xl-4">
                                        <label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{@$user->name}}" required>
                                    </div>

                                    <div class="col-xl-4">
                                        <label for="email" class="form-label">{{translate('Email')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" class="form-control" value="{{@$user->email}}" required>
                                    </div>

                                    <div class="col-xl-4">
                                        <label for="address" class="form-label">{{translate('Address')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="address" id="address" class="form-control" value="{{@$user->address->address}}" placeholder="{{translate('Enter Address')}}" required>
                                    </div>

                                    <div class="col-xl-4">
                                        <label for="city" class="form-label">{{translate('City')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="city" class="form-control" value="{{@$user->address->city}}" placeholder="{{translate('Enter City')}}" required>
                                    </div>

                                    <div class="col-xl-4">
                                        <label for="country_id" class="form-label">{{translate('Country')}} <span class="text-danger">*</span></label>


                                        <select class="select2" name="country_id" id="country_id">

                                            @foreach ($countries as $country )

                                              <option {{$country->id == $user->country_id ? "selected" :"" }} value="{{$country->id}}"> {{$country->name}}</option>

                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="col-xl-4">
                                        <label for="state" class="form-label">{{translate('State')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="state" id="state" class="form-control" value="{{@$user->address->state}}" placeholder="{{translate('Enter State')}}" required>
                                    </div>

                                    <div class="col-xl-4">
                                        <label for="zip" class="form-label">{{translate('Zip')}} <span class="text-danger">*</span></label>
                                        <input type="text" name="zip" id="zip" class="form-control" value="{{@$user->address->zip}}" placeholder="{{translate('Enter Zip')}}" required>
                                    </div>

                                    <div class="col-xl-4">
                                        <label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
                                        <select class="form-select py-2 px-4 d-flex" name="status" id="status">
                                            <option value="1" @if($user->status == 1) selected @endif>{{translate('Active')}}</option>
                                            <option value="0" @if($user->status == 0) selected @endif>{{translate('Banned')}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-md btn-success btn-xl px-4 fs-6 text-light">{{translate('Update')}}</button>
                                </div>
                            </form>
						</div>
					 </div>
				</div>
			</div>

		</div>

    </div>
</div>
@endsection
@push('script-push')
<script>
	"use strict";

    $(".select2").select2({
		placeholder:"{{translate('Select item')}}",
	})

</script>

@endpush
