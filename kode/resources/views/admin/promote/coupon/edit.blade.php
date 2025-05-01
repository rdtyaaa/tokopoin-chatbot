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
                    <li class="breadcrumb-item"><a href="{{route('admin.promote.coupon.index')}}">
                        {{translate('Coupons')}}
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
                            {{translate('Edit Coupon')}}
                        </h5>
                    </div>
                </div>
            </div>

             <div class="card-body">
				<form action="{{route('admin.promote.coupon.update', $coupon->id)}}" method="POST" enctype="multipart/form-data">
					@csrf
                    <div class="row g-3">
						<div class="col-lg-6">
							<label for="name" class="form-label">{{translate('Name')}} <span class="text-danger">*</span></label>
							<input  value="{{$coupon->name}}"  type="text" name="name" id="name"  class="form-control" placeholder="{{translate('Enter coupon name')}}" required>
						</div>

						<div class="col-lg-6">
							<label for="code" class="form-label">{{translate('Code')}} <span class="text-danger">*</span></label>
							<input    type="text" name="code" id="code" value="{{$coupon->code}}" class="form-control" placeholder="{{translate('Enter coupon code')}}" required>
						</div>

						<div class="col-lg-6">
							<label for="type" class="form-label">{{translate('Type')}} <span class="text-danger">*</span></label>
							<select class="form-select" name="type"  id="type" required>
								<option  value="">{{translate('Select One')}}</option>
								<option {{$coupon->type ==  1 ? "selected" : ""}}    value="1">{{translate('Fixed')}}</option>
								<option {{$coupon->type ==  2 ? "selected" : ""}}    value="2">{{translate('Percent')}}</option>
							</select>
						</div>

						<div class="col-lg-6">
							<label for="value" class="form-label">{{translate('Value')}} <span class="text-danger">*</span></label>
							<input  value="{{round(($coupon->value))}}"  type="text" name="value" id="value" value="{{old('value')}}" class="form-control" placeholder="{{translate('Enter coupon value')}}" required>
						</div>

						<div class="col-lg-6">
							<label for="start_date" class="form-label">{{translate('Start Date')}} <span class="text-danger">*</span></label>
							<input    type="date" name="start_date" id="start_date" value="{{ date('Y-m-d', strtotime($coupon->start_date))}}" class="form-control"   required>
						</div>

						<div class="col-lg-6">
							<label for="end_date" class="form-label">{{translate('End Date')}} <span class="text-danger">*</span></label>
							<input type="date" name="end_date" id="end_date" value="{{ date('Y-m-d', strtotime($coupon->end_date))}}" class="form-control "  required>
						</div>

						<div class="col-12">
							<label for="status" class="form-label">{{translate('Status')}} <span class="text-danger">*</span></label>
							<select class="form-select" name="status" id="status" required>
								<option {{$coupon->status ==  1 ? "selected" : ""}}  value="1">{{translate('Enable')}}</option>
								<option  {{$coupon->status ==  2 ? "selected" : ""}}   value="2">{{translate('Disable')}}</option>
							</select>
						</div>
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








