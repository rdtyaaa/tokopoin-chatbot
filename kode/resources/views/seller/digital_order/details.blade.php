@extends('seller.layouts.app')
@section('main_content')
	<div class="page-content">
		<div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{translate('Order Details')}}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                            {{translate('Home')}}
                        </a></li>
                        <li class="breadcrumb-item"><a href="{{route('seller.digital.order.index')}}">
                            {{translate('Digital Orders')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate('Order Details')}}
                        </li>
                    </ol>
                </div>
            </div>

			<div class="row">
				<div class="col-xl-9">
					<div class="card">
						<div class="card-header border-bottom-dashed">
							<div class="d-flex gap-2 align-items-center"        >
								<h5 class="card-title mb-0">
									{{translate('Order')}} -
									{{$order->order_id}}</h5>
									@if($orderDetail->status == App\Models\Order::DELIVERED)
									  <span class="badge badge-soft-danger">{{translate('Received')}}</span>
									@endif
							</div>
						</div>

						<div class="card-body">
							<div class="table-responsive table-card">
								<table class="table table-hover table-nowrap align-middle">
									<thead class="table-light text-muted">
										<tr>
											<th scope="col">
												{{translate('Product')}}
											</th>
											<th scope="col">
												{{translate('Attribute')}}
											</th>

											<th scope="col">
												{{translate('Original amount')}}
											</th>
											<th scope="col">
												{{translate('Tax')}}
											</th>
											<th scope="col">
												{{translate('amount')}}
											</th>

											<th scope="col" >
												{{translate("Action")}}
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td data-label="{{translate('Product')}}">
												{{$orderDetail->product->name}}
											</td>
											<td data-label="{{translate('Attribute')}}">
												{{(@$digitalProductAttributes ?@$digitalProductAttributes->name :"N/A")}}
											</td>
										
											<td>
												{{(short_amount($orderDetail->original_price))}}
											</td>
											<td>
												{{(short_amount($orderDetail->total_taxes))}}
											</td>

											<td>
												{{(short_amount($orderDetail->total_price))}}
											</td>

											<td data-label="{{translate('Action')}}">
												@if($orderDetail->status == App\Models\Order::DELIVERED)
													<span class="badge badge-soft-success my-1">{{translate('Received')}}</span>
												@else
													<span class="badge badge-soft-danger my-1">{{translate('N/A')}}</span>
												@endif
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-xl-3">

					<div class="card">
						<div class="card-header border-bottom-dashed">
							<div class="d-flex align-items-center justify-content-between">
								<h5 class="card-title flex-grow-1 mb-0">
									{{translate('Customer Details')}}
								</h5>

								@if(@$order->customer)

									<a title="{{ translate('Chat with customer') }}"
									data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('seller.customer.chat.list' , ['user_id' => @$order->customer->id])}}">

								  	<i class=" link-success fs-18 ri-message-2-line"></i>

									</a>
						    	@endif

							</div>
						</div>
						<div class="card-body">
							<ul class="list-unstyled mb-0 vstack gap-3">
								<li>
									<div class="d-flex align-items-center">
										<div class="flex-shrink-0">
											<img src="{{show_image(file_path()['profile']['user']['path'].'/'.@$order->customer->image,file_path()['profile']['user']['size'])}}" alt="{{@$order->customer->image}}"
												class="avatar-sm rounded">
										</div>
										<div class="flex-grow-1 ms-3">
											<h6 class="fs-14 mb-1">{{(@$order->customer->name?? @$order->billing_information->email)}}</h6>
											<p class="text-muted mb-0">
												{{translate('Customer')}}
											</p>
										</div>
									</div>
								</li>
								<li>
									<i class="ri-mail-line me-2 align-middle text-muted fs-16"></i>{{(@$order->customer->email ?? @$order->billing_information->email)}}
								</li>
								<li><i class="ri-phone-line me-2 align-middle text-muted fs-16"></i>{{(@$order->customer->phone ?  $order->customer->phone :'N/A')}}</li>
							</ul>
						</div>
					</div>

					


					@if($order->custom_information)
						<div class="card">
							<div class="card-header border-bottom-dashed">
								<div class="d-flex">
									<h5 class="card-title flex-grow-1 mb-0">
										{{translate('Custom information')}}
									</h5>
								</div>
							</div>
							<div class="card-body">
								<ul class="list-unstyled mb-0 vstack gap-3">

									<li>
                                        <span class="font-weight-bold text-break"> {{translate('Payment status')}} :

                                            @if ($order->payment_status == App\Models\Order::UNPAID)
                                               <span class="badge badge-soft-danger">{{ translate('Unpaid') }}</span>
                                            @elseif($order->payment_status == App\Models\Order::PAID)
                                                <span class="badge badge-soft-success">{{ translate('Paid') }}</span>
                                            @endif
                                        </span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break"> {{translate('Payment VIA')}} :
                                            @if($order->wallet_payment == App\Models\Order::WALLET_PAYMENT)
                                                    
                                                {{ translate('Payment VIA Wallet')}}

                                            @else
                                                @if ($order->payment_type == '2')
                                                    {{ @$order->paymentMethod ? $order->paymentMethod->name : 'N/A' }}
                                                @else
                                                    {{ translate('Cash On Delivary') }}
                                                @endif
                                            @endif
                                        </span>
                                    </li>
									

									@foreach($order->custom_information as $key => $value)
										<li>
											{{$key}} :
										
											<span class="text-break">
													@if(is_array($value))
														{{ implode(",", $value)}}
													@else
													{{$value}}
													@endif
											</span>
										</li>
									@endforeach

									
								</ul>
							</div>
						</div>
				    @endif


					<div class="card">
						<div class="card-header border-bottom-dashed">
							<div class="d-flex">
								<h5 class="card-title flex-grow-1 mb-0">
									{{translate('Payment details')}}
								</h5>
							</div>
						</div>

						<div class="card-body">
							<ul class="list-unstyled mb-0 vstack gap-3">

								<li>
									<span class="font-weight-bold text-break"> {{translate('Payment status')}} :

										@if ($order->payment_status == App\Models\Order::UNPAID)
										   <span class="badge badge-soft-danger">{{ translate('Unpaid') }}</span>
										@elseif($order->payment_status == App\Models\Order::PAID)
											<span class="badge badge-soft-success">{{ translate('Paid') }}</span>
										@endif
									</span>
								</li>

								<li>
									<span class="font-weight-bold text-break"> {{translate('Payment VIA')}} :
										@if($order->wallet_payment == App\Models\Order::WALLET_PAYMENT)
												
											{{ translate('Payment VIA Wallet')}}

										@else
											@if ($order->payment_type == '2')
												{{ @$order->paymentMethod ? $order->paymentMethod->name : 'N/A' }}
											@else
												{{ translate('Cash On Delivary') }}
											@endif
										@endif
									</span>
								</li>
								
								@if($order->payment_details)
									@foreach ($order->payment_details as $key => $value )

									<li>
										<span class="font-weight-bold text-break">{{k2t($key)}} : {{$value}}</span>
									</li>

									@endforeach
								@endif



							</ul>
						</div>
					</div>


					<div class="card">
						<div class="card-header border-bottom-dashed">
							<h5 class="card-title mb-0">
                               {{translate('Attribute Value') }}
							</h5>
						</div>

						<div class="card-body">
							@if(@$digitalProductAttributes->digitalProductAttributeValueKey->count() > 0)

					
								<div class="d-flex gap-4 mb-4 fs-14">
									{{translate("Attribute Name")}} : <a title="{{translate('Attribute Details')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('seller.digital.product.attribute.edit', $digitalProductAttributes->id)}}">
										<span class="badge-soft-success fs-12 badge">{{@$digitalProductAttributes->name}}</span>
									</a>
								</div>

					

							@else
						       <p class="text-center">
								  {{translate("No Attribute Value Found")}}
							   </p>
							@endif
						</div>
					</div>



				</div>
			</div>
		</div>
	</div>
@endsection
