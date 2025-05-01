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
                    <li class="breadcrumb-item"><a href="{{ $paymentMethod->type == App\Models\PaymentMethod::MANUAL ? route('admin.gateway.payment.manual.method')   :  route('admin.gateway.payment.method')}}">
                        {{translate('Payment Methods')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Update')}}
                    </li>
                </ol>
            </div>
        </div>


        <div class="card">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <div>
                            <h5 class="card-title mb-0">
                                {{translate('Update')}}-{{$paymentMethod->name}}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

             <div class="card-body">
                <form action="{{route('admin.gateway.payment.update', $paymentMethod->id)}}" method="POST"  enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">

						@if($paymentMethod->type != App\Models\PaymentMethod::MANUAL)

						   <h6 class="fw-bold">{{translate('Payment Gateway Setting')}}</h6>

							@foreach($paymentMethod->payment_parameter as $key => $parameter)
								<div class="col-lg-6">
									<label for="{{$key}}" class="form-label">{{ucwords(str_replace('_', ' ', $key))}} <span class="text-danger">*</span></label>


									@if($key == 'is_sandbox')

										<select class="form-select"  name="method[{{$key}}]" id="{{$key}}" required>
											<option value="1" @if($parameter == 1) selected @endif>{{translate('Sandbox')}}</option>
											<option value="0" @if($parameter == 0) selected @endif>{{translate('Live')}}</option>
										</select>

									@else


									@if($key == "public_key")
													
												<textarea name="method[{{$key}}]" id="{{$key}}" class="form-control"   placeholder='{{"Enter ".ucwords(str_replace("_", " ", $key))}}' required>{{$parameter}}</textarea>
									@else
										
											<input type="text" name="method[{{$key}}]" id="{{$key}}" value="{{$parameter}}" class="form-control"   placeholder='{{"Enter ".ucwords(str_replace("_", " ", $key))}}' required>

										
									@endif


	
									@endif

								</div>
							@endforeach
						@else

						<div class="col-xl-6 col-lg-6">
							<div >
								<label for="name" class="form-label">  {{translate('Name')}}
									<span  class="text-danger"  >*</span>
								</label>
								<input required type="text" name="name" id="name" class="form-control" value="{{$paymentMethod->name}}" maxlength="70" placeholder="Enter  Name" >
							</div>
						</div>


						@endif

						<div class="col-lg-6">
							<label for="image" class="form-label">{{translate('Image')}} <span class="text-danger">*</span></label>
							<input type="file" name="image" id="image" class="form-control">

							<div id="image-preview-section">
                                <img alt='{{$paymentMethod->image}}' class="mt-2 rounded  d-block avatar-xl img-thumbnail"
                                    src="{{show_image(file_path()['payment_method']['path'].'/'.$paymentMethod->image,file_path()['payment_method']['size']) }}">
                            </div>
						</div>

						<div class="col-lg-6">
							<label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
							<select class="form-select" name="status" id="status" required>
								<option value="1" @if($paymentMethod->status == 1) selected @endif>{{translate('Active')}}</option>
								<option value="2" @if($paymentMethod->status == 2) selected @endif>{{translate('Inactive')}}</option>
							</select>
						</div>

						<div class="col-lg-6">
							<label for="currency_id" class="form-label">{{translate('Select Currency')}} <span class="text-danger">*</span></label>
							<select class="form-select" name="currency_id" id="currency_id" required>
								<option value="">{{translate('Select One')}}</option>
								@foreach($currencies as $currency)
									<option value="{{$currency->id}}" @if($paymentMethod->currency_id == $currency->id) selected @endif data-rate="{{$currency->rate}}">{{translate($currency->name)}}</option>
								@endforeach
							</select>
						</div>

						<div class="col-lg-6">
							<label for="percent_charge" class="form-label">{{translate('Percent Charge')}} <span class="text-danger">*</span></label>
							<div class="input-group">
								  <input type="text" class="form-control" id="percent_charge" name="percent_charge" value="{{round($paymentMethod->percent_charge)}}" placeholder="{{translate('Enter number')}}" >
								  <span class="input-group-text" >%</span>
							</div>
						</div>
						<div class="col-lg-6">
							<label for="rate" class="form-label">{{translate('Currency Rate')}} <span class="text-danger">*</span></label>
							<div class="input-group mb-3">
								  <span class="input-group-text">1 {{default_currency()->name}} = </span>
								  <input type="text" name="rate" value="{{round($paymentMethod->rate)}}" class="form-control" aria-label="Amount (to the nearest dollar)">
								  <span class="input-group-text limittext">{{(@$paymentMethod->currency->name)}}</span>
							</div>
						</div>

						@if($paymentMethod->type == App\Models\PaymentMethod::MANUAL)
							<div class="col-12">
								<div class="product-add-container border p-3">
									<div class="product-heading-container">
										<h6>{{translate('User Information')}}</h6>
									</div>
									<div>
										<a href="javascript:void(0)" class="btn btn-sm btn-danger  border-0 rounded newdata"><i class="las la-plus"></i> {{translate('Add New')}}</a>
										<div class="newdataadd mt-3">


											@forelse ($paymentMethod->payment_parameter as $input)
												<div class="row g-3 border-bottom pb-3 mb-3 newuserdata">
													<div class="col-lg-3">
														<input name="data_name[]" class="form-control" type="text" value="{{k2t($input->name)}}" required placeholder="{{translate('User Field Name')}}">
													</div>

													<div class="col-lg-3">
														<select name="type[]" class="form-select">
															<option value="text" {{$input->type == 'text' ? "selected" : ''}} > {{translate('Input Text')}} </option>
															<option value="textarea" {{$input->type == 'textarea' ? "selected" : ''}}  > {{translate('Textarea')}} </option>

															<option  {{$input->type == 'email' ? "selected" : ''}} value="email" > {{translate('Email')}} </option>
															<option  {{$input->type == 'date' ? "selected" : ''}} value="date" > {{translate('Date')}} </option>
														</select>
													</div>

													<div class="col-lg-3">
														<select name="required[]" class="form-select">
															<option value="required" {{$input->is_required  ? "selected" : ''}}  > {{translate('Required')}} </option>
															<option value="optional" {{ !$input->is_required  ? "selected" : ''}}  > {{translate('Optional')}} </option>
														</select>
													</div>


													<div class="col-lg-2 col-12 text-right">
														<span class="input-group-btn">
															<button class="btn btn-danger btn-md removeBtn" type="button">
																<i class="ri-delete-bin-line"></i>
															</button>
														</span>
													</div>
												</div>

											@empty

											@endforelse

										</div>
									</div>
								</div>
							</div>
						@endif

                        <div class="col-12">
                            <div class="text-start">
                                <button type="submit" class="btn btn-success">
                                    {{translate('Update')}}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
             </div>
        </div>

	</div>
</div>

@endsection

@push('script-push')
<script>
	'use strict'
	$("#currency_id").on('change', function(){
		var value = $(this).find("option:selected").text();
		$(".limittext").text(value);
		var currencyrate = $('select[name=currency_id] :selected').data('rate');
		$('input[name=rate]').val(currencyrate);
	});


	$('.newdata').on('click', function () {
	        var html = `
		        <div class="row g-3 border-bottom pb-3 mb-3 newuserdata">
		    		<div class="col-lg-3">
						<input name="data_name[]" class="form-control" type="text" required placeholder="{{translate('User Field Name')}}">
					</div>

					<div class="col-lg-3">
						<select name="type[]" class="form-select">
	                        <option value="text" > {{translate('Input Text')}} </option>
	                        <option value="textarea" > {{translate('Textarea')}} </option>
	                        <option value="email" > {{translate('Email')}} </option>
							<option value="date" > {{translate('Date')}} </option>
							
	                    </select>
					</div>

                    <div class="col-lg-3">
						<select name="required[]" class="form-select">
	                        <option value="required" > {{translate('Required')}} </option>
	                        <option value="optional" > {{translate('Optional')}} </option>
	                    </select>
					</div>


		    		<div class="col-lg-2 col-12 text-right">
		                <span class="input-group-btn">
		                    <button class="btn btn-danger btn-md removeBtn" type="button">
								<i class="ri-delete-bin-line"></i>
		                    </button>
		                </span>
		            </div>
		        </div>`;
	        $('.newdataadd').append(html);
	    });
	    $(document).on('click', '.removeBtn', function () {
	        $(this).closest('.newuserdata').remove();
	    });

</script>
@endpush
