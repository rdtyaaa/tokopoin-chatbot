@extends('admin.layouts.app')
@push('style-include')
<link href="{{asset('assets/backend/css/summnernote.css')}}" rel="stylesheet" type="text/css" />

<style>

    .table--wrapper{
        overflow-x: auto;
        max-width: 100%;
    }

    table{
        width: 100%;
    }

    td{
        min-width: 160px;
        padding: 8px;
    }

</style>
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
                    <li class="breadcrumb-item"><a href="{{route('admin.shipping.delivery.index')}}">
                        {{translate('Shipping Delivery')}}
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
						<div>
							<h5 class="card-title mb-0">
								{{translate('Create Shipping Delivery')}}
							</h5>
						</div>
					</div>
				</div>
			</div>

			<div class="card-body">
                <form action="{{route('admin.shipping.delivery.store')}}" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="row g-3">
						<div class="col-lg-6">
							<div>
								<label for="name" class="form-label">
									{{translate('Name')}} <span  class="text-danger" >*</span>
								</label>
								<input value="{{old('name')}}" type="text" name="name" id="name" class="form-control" placeholder="Enter name" required="">
							</div>
						</div>


						<div class="col-lg-6">
							<label for="duration" class="form-label">
								{{translate('Duration')}} <span  class="text-danger" >*</span>
							</label>
							<div class="input-group">
								<input  type="text" class="form-control"  name="duration" id="duration" placeholder="{{translate('Enter duration')}}" value="{{old('duration')}}" >
								<span class="input-group-text" >{{translate('Days')}}</span>
							</div>
						</div>

						<div class="col-lg-4">
							<label for="free_shipping" class="form-label">
								{{translate('Free shipping')}} <span  class="text-danger" >*</span>
							</label>
							<select class="form-select" name="free_shipping" id="free_shipping" required>
								<option  value="">{{translate('Select One')}}</option>
								<option {{old('free_shipping') == 0 ? "selected" : "" }}  value="0">{{translate('Inactive')}}</option>
								<option {{old('free_shipping') == 1 ? "selected" : "" }}    value="1">{{translate('Active')}}</option>
							</select>
						</div>

						<div class="col-lg-4">
							<label for="status" class="form-label">
								{{translate('Status')}} <span  class="text-danger" >*</span>
							</label>
							<select class="form-select" name="status" id="status" required>
								<option  value="">{{translate('Select One')}}</option>
								<option {{old('status') == 0 ? "selected" : "" }}  value="0">{{translate('Inactive')}}</option>
								<option {{old('status') == 1 ? "selected" : "" }}    value="1">{{translate('Active')}}</option>
							</select>
						</div>

                        <div class="col-lg-4">
							<label for="image" class="form-label">
								{{translate('Image')}} <span  class="text-danger" >*</span>
							</label>

                            <input required data-size = "80x80" type="file" name="image" id="image" class="form-control img-preview">

                            <div class="mt-2 image-preview-section ">

                            </div>

						</div>


                         <div class="col-lg-12">

                            <div class="text-editor-area">
                                <label for="description" class="form-label">
                                    {{translate('Decription')}} <span class="text-danger"  >*</span>
                                </label>
                                <textarea id="description" class=" form-control text-editor" name="description" rows="10" placeholder="{{translate('Enter Description')}}" required="">{{old('description')}}</textarea>

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


                         <div class="col-lg-12 shipping-type">


                            <div class="card bg-light">

                                <div class="card-header border-bottom-dashed bg-light">

                                    <div class="row g-4 align-items-center">
                                        <div class="col-sm">
                                            <div>
                                                <h5 class="card-title mb-0">
                                                    {{translate('Shipping type')}}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">


                                     <div class="mb-4">
                                        <select class="form-select" name="shipping_type" id="shipping_type" required>

                                            <option selected  value="price_wise">
                                                {{translate('Price wise shipping')}}
                                            </option>
                                            <option value="weight_wise">
                                                {{translate('Weight wise shipping')}}
                                            </option>
                                        </select>
                                     </div>


                                    <div class="tab-content text-muted">
                                        <div  id="product-wise-shipping">

                                             <div class="text-start">
                                                <a href="javascript:void(0)" class="btn btn-sm text-end btn-success add-price-btn  waves ripple-light"><i
                                                    class="ri-add-line align-bottom me-1"></i>
                                                  {{translate('Add New')}}
                                               </a>
                                             </div>

                                            <div class="table--wrapper">

                                                <table class="mt-4">
                                                    <thead>
                                                        <th></th>
                                                    </thead>
                                                    <tbody  class="add-price-row">
                                                        <tr>
                                                            <td>
                                                                <p class="mb-0"> {{translate('Applicable if price is greter than')}}</p>
                                                                <div class="input-group ">
                                                                    <span class="input-group-text">
                                                                        {{default_currency()->symbol}}
                                                                    </span>
                                                                    <input placeholder="{{translate('Enter price')}}" type="number" name="price_wise[greater_than][]" class="form-control">
                                                                  </div>
                                                            </td>
                                                            <td>
                                                                <p class="mb-0"> {{translate('Applicable if price is less than or equal')}}</p>
                                                                <div class="input-group ">
                                                                    <span class="input-group-text">{{default_currency()->symbol}}</span>
                                                                    <input placeholder="{{translate('Enter price')}}" type="number" name="price_wise[less_than_eq][]" class="form-control">
                                                                  </div>
                                                            </td>

                                                            @foreach ($zones as $zone )

                                                                <td>
                                                                    <p class="mb-0"> {{$zone->name}}</p>

                                                                    <div class="input-group">
                                                                        <span class="input-group-text">{{default_currency()->symbol}}</span>
                                                                        <input placeholder="{{translate('Enter price')}}"  name="price_base_zone_wise_price[{{ $zone->id }}][]"  type="number" class="form-control">
                                                                    </div>
                                                                </td>

                                                            @endforeach


                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="d-none"  id="weight-wise-shipping">
                                            <div class="text-start">
                                                <a href="javascript:void(0)" class="btn btn-sm text-end btn-success add-weight-btn  waves ripple-light"><i
                                                    class="ri-add-line align-bottom me-1"></i>
                                                  {{translate('Add New')}}
                                               </a>
                                             </div>

                                            <div class="table--wrapper">

                                                <table class="mt-4">
                                                    <thead>
                                                        <th></th>
                                                    </thead>
                                                    <tbody class="add-weight-row">
                                                        <tr>
                                                            <td>
                                                                <p class="mb-0"> {{translate('Applicable if weight is greter than')}}</p>
                                                                <div class="input-group ">
                                                                    <span class="input-group-text">
                                                                        {{default_currency()->symbol}}
                                                                    </span>
                                                                    <input placeholder="{{translate('Enter weight')}}" type="number" name="weight_wise[greater_than][]" class="form-control">
                                                                  </div>
                                                            </td>
                                                            <td>
                                                                <p class="mb-0"> {{translate('Applicable if weight is less than or equal')}}</p>
                                                                <div class="input-group ">
                                                                    <span class="input-group-text">{{default_currency()->symbol}}</span>
                                                                    <input placeholder="{{translate('Enter weight')}}" type="number" name="weight_wise[less_than_eq][]" class="form-control">
                                                                  </div>
                                                            </td>

                                                            @foreach ($zones as $zone )

                                                                <td>
                                                                    <p class="mb-0"> {{$zone->name}}</p>
                                                                    <div class="input-group ">
                                                                        <span class="input-group-text">{{default_currency()->symbol}}</span>
                                                                        <input placeholder="{{translate('Enter price')}}"  name="weight_base_zone_wise_price[{{$zone->id}}][]"  type="text" class="form-control">
                                                                    </div>
                                                                </td>

                                                            @endforeach


                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                         </div>

                         <div class="col-12">
							<div class="text-end">
								<button type="submit" class="btn btn-success">
									{{translate('Save')}}
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
    $(document).ready(function() {

        $(document).on('change','#free_shipping',function(e) {


            if($(this).val() == 1){
                $('.shipping-type').addClass('d-none')

            }else{
                $('.shipping-type').removeClass('d-none')
            }

            e.preventDefault()

        });


        $(document).on('change','#shipping_type',function(e) {

            if($(this).val() == 'weight_wise'){

                $('#weight-wise-shipping').removeClass('d-none');
                $('#product-wise-shipping').addClass('d-none');

            }else{
                $('#weight-wise-shipping').addClass('d-none');
                $('#product-wise-shipping').removeClass('d-none');
            }

            e.preventDefault()

        });

        // Function to validate and add new row for price wise shipping
        $(document).on('click','.add-price-btn',function() {
            var isFilled = true;
            $('.add-price-row input[type="number"]').each(function(man,$v) {


                if ($(this).val().trim() === '') {
                    isFilled = false;
                }
            });

            if (!isFilled) {
                alert('Please fill all the fields before adding a new row.');
                return;
            }

            // Get the value of the last 'less than or equal' input and set the new 'greater than' input value accordingly
            var lastLessThanEqValue = parseInt($('input[name="price_wise[less_than_eq][]"]').last().val());
            var newGreaterThanValue = isNaN(lastLessThanEqValue) ? 0 : lastLessThanEqValue + 1;

            var newRow = `
                <tr>
                    <td>
                        <p class="mb-0">{{translate('Applicable if price is greater than')}}</p>
                        <div class="input-group">
                            <span class="input-group-text">{{default_currency()->symbol}}</span>
                            <input placeholder="{{translate('Enter price')}}" type="number" name="price_wise[greater_than][]" class="form-control" value="${newGreaterThanValue}">
                        </div>
                    </td>
                    <td>
                        <p class="mb-0">{{translate('Applicable if price is less than or equal')}}</p>
                        <div class="input-group">
                            <span class="input-group-text">{{default_currency()->symbol}}</span>
                            <input placeholder="{{translate('Enter price')}}" type="number" name="price_wise[less_than_eq][]" class="form-control">
                        </div>
                    </td>
                    ${generateZoneColumns('price_base_zone_wise_price')}
                    <td>
                        <button type="button" class="btn btn-sm btn-danger delete-row-btn">Delete</button>
                    </td>
                </tr>
            `;

            $('.add-price-row').append(newRow);
        });

        // Function to validate and add new row for weight wise shipping

            $(document).on('click','.add-weight-btn',function() {
            var isFilled = true;
            $('.add-weight-row input[type="number"]').each(function() {
                if ($(this).val().trim() === '') {
                    isFilled = false;
                }
            });

            if (!isFilled) {
                alert('Please fill all the fields before adding a new row.');
                return;
            }

            // Get the value of the last 'less than or equal' input and set the new 'greater than' input value accordingly
            var lastLessThanEqValue = parseInt($('input[name="weight_wise[less_than_eq][]"]').last().val());
            var newGreaterThanValue = isNaN(lastLessThanEqValue) ? 0 : lastLessThanEqValue + 1;

            var newRow = `
                <tr>
                    <td>
                        <p class="mb-0">{{translate('Applicable if weight is greater than')}}</p>
                        <div class="input-group">
                            <span class="input-group-text">{{default_currency()->symbol}}</span>
                            <input placeholder="{{translate('Enter weight')}}" type="number" name="weight_wise[greater_than][]" class="form-control" value="${newGreaterThanValue}">
                        </div>
                    </td>
                    <td>
                        <p class="mb-0">{{translate('Applicable if weight is less than or equal')}}</p>
                        <div class="input-group">
                            <span class="input-group-text">{{default_currency()->symbol}}</span>
                            <input placeholder="{{translate('Enter weight')}}" type="number" name="weight_wise[less_than_eq][]" class="form-control">
                        </div>
                    </td>
                     ${generateZoneColumns('weight_base_zone_wise_price')}
                    <td>
                        <button type="button" class="btn btn-sm btn-danger delete-row-btn">Delete</button>
                    </td>
                </tr>
            `;

            $('.add-weight-row').append(newRow);
        });

        // Function to delete a row
        $(document).on('click', '.delete-row-btn', function() {
            $(this).closest('tr').remove();
        });

        // Function to generate zone columns
        function generateZoneColumns(key) {
            var zoneColumns = '';

            @foreach ($zones as $zone)
                zoneColumns += `
                    <td>
                        <p class="mb-0"> {{$zone->name}}</p>
                        <div class="input-group">
                            <span class="input-group-text">{{default_currency()->symbol}}</span>
                                    <input placeholder="{{translate('Enter price')}}"  name="${key}[{{ $zone->id }}][]"  type="number" class="form-control">
                        </div>
                    </td>
                `;
            @endforeach

            return zoneColumns;
        }
    });
</script>

@endpush
