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
                    <li class="breadcrumb-item"><a href="{{route('admin.digital.product.seller')}}">
                        {{translate('Digital Products')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate("Products Details")}}
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
								{{translate('Product Details')}}
							</h5>
						</div>
					</div>

					 <div class="card-body">
						<div >
							<div class="px-3">
								<div class="profile-section-image">
									<img src="{{show_image(file_path()['product']['featured']['path'].'/'.$sellerDigitalProduct->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$sellerDigitalProduct->featured_image}}" class="w-100 img-thumbnail">
								</div>

								<div class="mt-3">
                                    <h6>{{$sellerDigitalProduct->name}}</h6>
                                    <p>{{translate('Created Date')}} {{get_date_time($sellerDigitalProduct->created_at,'d M, Y h:i A')}}</p>
                                </div>
							</div>
							<div class="p-3 bg-body rounded">
								<div>
									<h6 class="mb-3 fw-bold">{{translate('Product information')}}</h6>
									<ul class="list-group">
										<li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                            <span class="fw-semibold">
                                                {{translate('Categories')}}
                                            </span>

                                            <div>
                                                <span class="badge bg-light text-dark">{{(get_translation($sellerDigitalProduct->category->name))}}</span>
                                                @if($sellerDigitalProduct->subCategory)
                                                    <span class="badge bg-light text-dark">{{(get_translation($sellerDigitalProduct->subCategory->name))}}</span>
                                                @endif
                                            </div>
                                        </li>

                                        <li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                            <span class="fw-semibold">
                                                {{translate('Status')}}
                                            </span>

                                            @if($sellerDigitalProduct->status == 1)
                                                <span class="badge badge-soft-success">{{translate('Published')}}</span>
                                            @elseif($sellerDigitalProduct->status == 2)
                                                <span class="badge badge-soft-warning">{{translate('Inactive')}}</span>
                                            @else
                                                <span class="badge badge-soft-primary">{{translate('New')}}</span>
                                            @endif
                                        </li>

                                        <li class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                            <span class="fw-semibold">
                                                {{translate('Number Of Orders')}}
                                            </span>
                                            <span class="font-weight-bold">{{$sellerDigitalProduct->order->count()}}</span>
                                        </li>
									</ul>
								</div>
							</div>
						</div>
					 </div>
				</div>
			</div>

			<div class="col-xxl-9 col-xl-8 col-lg-7">
				<div class="card">
					<div class="card-header border-bottom-dashed ">
						<div class="d-flex align-items-center">
							<h5 class="card-title mb-0 flex-grow-1">
								{{translate('Digital product attribute')}}
							</h5>
						</div>
					</div>

				     <div class="card-body">
						<div class="table-responsive  mb-4">
							<table class="table table-hover table-nowrap align-middle" >
								<thead class="text-muted table-light">
									<tr class="text-uppercase">
										<th>{{translate('Name')}}</th>
										<th>{{translate('Price')}}</th>
									</tr>
								</thead>

								<tbody class="form-check-all">
									@forelse($sellerDigitalProduct->digitalProductAttribute as $digitalProductAttribute)
										<tr>
											<td data-label="{{translate('Name')}}">
												<div>
                                                    <h6>{{($digitalProductAttribute->name)}}</h6>
                                                    <p class="mb-0"><a href="{{route('admin.digital.product.attribute', $sellerDigitalProduct->id)}}" class="link-success fs-12">{{translate('Attribute Value')}}</a></p>
                                                </div>
											</td>

											<td data-label="{{translate('Price')}}">
											   {{short_amount($digitalProductAttribute->price)}}
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
							<div class="text-muted border p-3 rounded">
								<h6 class="fs-14">{{translate('Product Description')}}:</h6>
								<div >
									@php echo $sellerDigitalProduct->description @endphp
								</div>
							</div>
						</div>

					 </div>
				</div>

			</div>
		</div>
    </div>
</div>


@endsection
