@extends('admin.layouts.app')
@push('style-include')
<link href="{{asset('assets/backend/css/summnernote.css')}}" rel="stylesheet" type="text/css" />
@endpush
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
                    <li class="breadcrumb-item"><a href="{{route('admin.withdraw.method.index')}}">
                        {{translate('Withdraw Methods')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Create')}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Create Withdraw Method')}}
                        </h5>
                    </div>

                </div>
            </div>

			<div class="card-body">
				<form class="overflow-hidden" action="{{route('admin.withdraw.method.store')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="row g-3">
						<div class="col-xl-4 col-lg-6">
							<div >
								<label for="Image" class="form-label">
									{{translate('Image')}} <span  class="text-danger">*</span>
								</label>
								<input type="file" name="file" id="Image" class="form-control">
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<div >
								<label for="name" class="form-label">  {{translate('Name')}}
									<span  class="text-danger"  >*</span>
								</label>
								<input required type="text" name="name" id="name" class="form-control" value="{{old('name')}}" maxlength="70" placeholder="{{translate('Enter name')}}" >
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<div>
								<label for="currency_id" class="form-label">
									{{translate('Currency')}}  <span  class="text-danger">*</span>
								</label>
								<select class="form-select" name="currency_id" id="currency_id" required>
									<option value="">{{translate('Select One')}}</option>
									@foreach($currencies as $currency)
										<option value="{{$currency->id}}" @if(old('currency_id') == $currency->id) selected @endif>{{($currency->name)}}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<div >
								<label for="rate" class="form-label">{{translate('Currency Rate')}}
									<span  class="text-danger">*</span>
								</label>
								<div class="input-group mb-3">
									<span class="input-group-text">1  {{default_currency()->name}} = </span>
									<input type="text" name="rate" id="rate" value="{{old('rate')}}" class="form-control" aria-label="Amount (to the nearest dollar)">
									<span class="input-group-text limittext"></span>
								</div>
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<div>
								<label for="duration" class="form-label">{{translate('Duration')}} <span  class="text-danger">*</span></label>
								<div class="input-group mb-3">
									<input type="text" class="form-control" id="duration" name="duration" value="{{old('duration')}}" placeholder="{{translate('Enter processing time')}}">
									<span class="input-group-text" >{{translate('Houre')}}</span>
								</div>
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<div>
								<label for="min_limit" class="form-label">{{translate('Minimum Amount')}} <span  class="text-danger">*</span></label>
								<div class="input-group">
									<input type="text" class="form-control" id="min_limit" name="min_limit" value="{{old('min_limit')}}" placeholder="{{translate('Enter amount')}}">
									<span class="input-group-text" >{{default_currency()->name}}</span>
								</div>
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<label for="max_limit" class="form-label">{{translate('Maximum Amount')}} <span  class="text-danger">*</span></label>
							<div class="input-group">
								<input type="text" class="form-control" id="max_limit" name="max_limit" value="{{old('max_limit')}}" placeholder="{{translate('Enter amount')}}">
								<span class="input-group-text" >{{default_currency()->name}}</span>
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<label for="fixed_charge" class="form-label">{{translate('Fixed Charge')}} <span  class="text-danger">*</span></label>
							<div class="input-group">
								<input type="text" class="form-control" id="fixed_charge" value="{{old('fixed_charge')}}" name="fixed_charge" placeholder="{{translate('Enter amount')}}">
								<span class="input-group-text" >{{default_currency()->name}}</span>
							</div>
						</div>

						<div class="col-xl-4 col-lg-6">
							<label for="percent_charge" class="form-label">{{translate('Percent Charge')}} <span  class="text-danger">*</span></label>
							<div class="input-group">
								<input type="text" class="form-control" id="percent_charge" name="percent_charge" value="{{old('percent_charge')}}" placeholder="{{translate('Enter number')}}">
								<span class="input-group-text" >{{translate('%')}}</span>
							</div>
						</div>

						<div class="col-12">
							<div class="text-editor-area">
								<label for="description" class="form-label">{{translate('Description')}} <span  class="text-danger">*</span></label>

								<textarea class="form-control text-editor " name="description" rows="5" id="description" placeholder="{{translate('Enter Description')}}" required>{{old("description")}}</textarea>

								@if( $openAi->status == 1)
									<button type="button" class="ai-generator-btn mt-3 ai-modal-btn" >
										<span class="ai-icon btn-success waves ripple-light">
												<span class="spinner-border d-none" aria-hidden="true"></span>

												<i class="ri-robot-line"></i>
										</span>

										<span class="ai-text">
											{{translate('Generate With AI')}}
										</span>
									</button>
					         	@endif
							</div>
						</div>

						<div class="col-12">
							<div class="product-add-container border p-3">
								<div class="product-heading-container">
									<h6>{{translate('User Information')}}</h6>
								</div>
								<div>
									<a href="javascript:void(0)" class="btn btn-sm btn-danger  border-0 rounded newdata"><i class="las la-plus"></i> {{translate('Add New')}}</a>
									<div class="newdataadd mt-3">
									</div>
								</div>
							</div>
						</div>

						<div class="col-12">
							<div class="text-start">
								<button type="submit" class="btn btn-success">
									{{translate('Submit')}}
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

@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>
@endpush

@push('script-push')
<script>
	(function($){
      	"use strict";
		$('.newdata').on('click', function () {
	        var html = `
		        <div class="row g-3 border-bottom pb-3 mb-3 newuserdata">
		    		<div class="col-lg-4">
						<input name="data_name[]" class="form-control" type="text" required placeholder="{{translate('User Field Name')}}">
					</div>

					<div class="col-lg-4">
						<select name="type[]" class="form-control">
	                        <option value="text" > {{translate('Input Text')}} </option>
	                        <option value="textarea" > {{translate('Textarea')}} </option>
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


	    $("#currency_id").on('change', function(){
			var value = $(this).find("option:selected").text();
			$(".limittext").text(value);
		}).change();
	})(jQuery);
</script>
@endpush
