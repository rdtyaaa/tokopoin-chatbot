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
                    <li class="breadcrumb-item"><a href="{{route('admin.seller.info.index')}}">
                        {{translate('Sellers')}}
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
								{{translate('Seller Details')}}
							</h5>
						</div>
					</div>

					<div class="card-body">
                        <div class="text-center">
                            <div class="profile-section-image mx-auto ">
                                <img src="{{show_image(file_path()['profile']['seller']['path'].'/'.$seller->image,file_path()['profile']['seller']['size'])}}" alt="{{$seller->image}}" class="w-100 rounded-circle img-thumbnail">
                            </div>
                            <div class="mt-3">
                                <h6 class="mb-0">{{$seller->name}}</h6>
                                <p>{{translate('Joining Date')}} {{get_date_time($seller->created_at,'d M, Y h:i A')}}</p>
                            </div>
                        </div>

                        <div class="p-3 bg-body rounded">
                            <h6 class="mb-3 fw-bold">{{translate('Seller information')}}</h6>

                            <ul class="list-group">
                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Username')}}
                                    </span>
                                    <span class="font-weight-bold">{{$seller->username}}</span>
                                </li>

                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Phone')}}
                                    </span>
                                    <span class="font-weight-bold">{{$seller->phone}}</span>
                                </li>

                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Status')}}
                                    </span>

                                    @if($seller->status == 1)
                                        <span class="badge badge-pill bg-success">{{translate('Active')}}</span>
                                    @else
                                        <span class="badge badge-pill bg-danger">{{translate('Banned')}}</span>
                                    @endif
                                </li>

                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Number Of Orders')}}
                                    </span>
                                    <span class="font-weight-bold">{{$orders['count']}}</span>
                                </li>


                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                    <span class="fw-semibold">
                                        {{translate('Seller Balance')}}
                                    </span>

                                    <span class="font-weight-bold">{{(short_amount($seller->balance))}}</span>
                                </li>

                                <li class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                    <span class="badge bg-danger">{{translate('Balance Update')}}</span>
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#balanceupdate" ><span><i class="fs-4 lar la-edit"></i></span></a>
                                </li>

                            </ul>
                        </div>
				    </div>
			    </div>
            </div>

			<div class="col-xxl-9 col-xl-8 col-lg-7">
				<div class="card">
					<div class="card-header border-bottom-dashed">
						<div class="d-flex align-items-center">
							<h5 class="card-title mb-0 flex-grow-1">
								{{translate('latest product order')}}
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

								<tbody class="form-check-all">
									@forelse($orders['physical']->take(5)  as $value)
										<tr>
											<td data-label="{{translate('Order Number - Time')}}">
												{{($value->order_id)}}-
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
							<h6 class="fw-bold mb-3">{{translate('New order list')}}</h6>
							<div class="row">
								<div class="col-xxl-4 col-xl-6">
									<div class="card card-animate bg-soft-green">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                        <i class="ri-disc-line text-primary"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                        <span>{{$seller->product->where('product_type', 102)->count()}}
														</span>
                                                    </h4>


                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                       {{translate('Physical product')}}
                                                    </p>

                                                    <a href="{{route('admin.seller.info.physical.product', $seller->id)}}" class="d-flex align-items-center justify-content-end gap-1">
                                                        {{translate('View All')}}
                                                        <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
									</div>
								</div>

								<div class="col-xxl-4 col-xl-6">
									<div class="card card-animate bg-soft-gray">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                       <i class="las la-shopping-cart text-primary"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                      	<span>{{$orders['physical']->count()}}
														</span>
                                                    </h4>


                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                       {{translate('physical product order')}}
                                                    </p>

                                                    <a href="{{route('admin.seller.info.physical.product.order', $seller->id)}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                       <i class="las la-shopping-bag text-warning"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                       	<span>{{$orders['digital']}}
														</span>
                                                    </h4>


                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                       {{translate('Digital product order')}}
                                                    </p>

                                                    <a href="{{route('admin.seller.info.digital.product.order', $seller->id)}}" class="d-flex align-items-center justify-content-end gap-1">
                                                        {{translate('View All')}}
                                                        <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
									</div>
								</div>

								<div class="col-xxl-4 col-xl-6">
									<div class="card card-animate bg-soft-blue">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                      <i class="las la-wallet text-primary"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                       <span>
                                                          {{$seller->transaction->count()}}
                                                       </span>
                                                    </h4>


                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                      {{translate('Total transaction')}}
                                                    </p>

                                                    <a href="{{route('admin.seller.info.transaction.log', $seller->id)}}" class="d-flex align-items-center justify-content-end gap-1">
                                                        {{translate('View All')}}
                                                        <i class="ri-arrow-right-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                        </div>
									</div>
								</div>

								<div class="col-xxl-4 col-xl-6">
									<div class="card card-animate bg-soft-purple">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <div class="flex-shrink-0">
                                                    <span class="overview-icon">
                                                      <i class="las la-credit-card text-primary"></i>
                                                    </span>
                                                </div>

                                                <div class="text-end">
                                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                        <span>{{round(($seller->withdraw->sum('amount')))}} {{default_currency()->name}}
                                                        </span>
                                                    </h4>


                                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                                       {{translate('Total withdraw amount')}}
                                                    </p>

                                                    <a href="{{route('admin.seller.info.withdraw.log', $seller->id)}}" class="d-flex align-items-center justify-content-end gap-1">
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
							<h6 class="fw-bold mb-3">{{translate('Seller Information Update')}}</h6>
							<div>
								<form action="{{route('admin.seller.info.details.update', $seller->id)}}" method="POST" enctype="multipart/form-data">
									@csrf
									<div class="row g-3">
										<div class="col-xxl-4 col-xl-6">
											<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
											<input type="text" name="name" id="name" class="form-control" value="{{@$seller->name}}" required>
										</div>

										<div class="col-xxl-4 col-xl-6">
											<label for="email" class="form-label">{{translate('Email')}} <span class="text-danger">*</span></label>
											<input type="text" name="email" id="email" class="form-control" value="{{@$seller->email}}" required>
										</div>

										<div class="col-xxl-4 col-xl-6">
											<label for="phone" class="form-label">{{translate('Phone')}} <span class="text-danger">*</span></label>
											<input type="text" name="phone" id="phone" class="form-control" value="{{@$seller->phone}}" required>
										</div>

										<div class="col-xxl-4 col-xl-6">
											<label for="address" class="form-label">{{translate('Address')}} <span class="text-danger">*</span></label>
											<input type="text" name="address" id="address" class="form-control" value="{{@$seller->address->address}}" placeholder="{{translate('Enter Address')}}" required>
										</div>

										<div class="col-xxl-4 col-xl-6">
											<label for="city" class="form-label">{{translate('City')}} <span class="text-danger">*</span></label>
											<input type="text" name="city" id="city" class="form-control" value="{{@$seller->address->city}}" placeholder="{{translate('Enter City')}}" required>
										</div>

										<div class="col-xxl-4 col-xl-6">
											<label for="state" class="form-label">{{translate('State')}} <span class="text-danger">*</span></label>
											<input type="text" name="state" id="state" class="form-control" value="{{@$seller->address->state}}" placeholder="{{translate('Enter State')}}" required>
										</div>

										<div class="col-xxl-4 col-xl-6">
											<label for="zip" class="form-label">{{translate('Zip')}} <span class="text-danger">*</span></label>
											<input type="text" name="zip" id="zip" class="form-control" value="{{@$seller->address->zip}}" placeholder="{{translate('Enter Zip')}}" required>
										</div>

										<div class="col-xxl-4 col-xl-6">
											<label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
											<select class="form-select" name="status" id="status">
												<option value="1" @if($seller->status == 1) selected @endif>{{translate('Active')}}</option>
												<option value="2" @if($seller->status == 2) selected @endif>{{translate('Banned')}}</option>
											</select>
										</div>

										
										<div class="col-xxl-4 col-xl-6">
											<label for="kycVerification" class="form-label">{{translate('KYC Verification')}} <span class="text-danger">*</span></label>
											<select class="form-select" name="kyc_status" id="kycVerification">
												<option value="1" @if($seller->kyc_status == 1) selected @endif>{{translate('Verified')}}</option>
												<option value="0" @if(!$seller->kyc_status || $seller->kyc_status == 0) selected @endif>{{translate('Unverified')}}</option>
											</select>
										</div>


										<div class="col-xxl-4 col-xl-6">
											<label for="rating" class="form-label">{{translate('Rating')}} (1 - 5) </label>
											<input type="number" max="5" name="rating" id="rating" class="form-control" value="{{@$seller->rating}}" placeholder="{{translate('Enter Rating')}}" >
										</div>
									</div>

									<div class="mt-4">
										<button type="submit" class="btn btn-md btn-success btn-xl px-4 fs-6 text-light waves ripple-light">{{translate('Update')}}</button>
									</div>
								</form>
							</div>
						</div>
					 </div>
				</div>
			</div>
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
			<form action="{{route('admin.seller.balance.update')}}" method="POST">
				@csrf
				<input type="hidden" name="seller_id" id="sellerID" value="{{$seller->id}}">
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
