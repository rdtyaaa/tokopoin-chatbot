@extends('seller.layouts.app')
@section('main_content')
	<div class="page-content">
		<div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{translate("Withdraw Preview")}}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                            {{translate('Home')}}
                        </a></li>
                        <li class="breadcrumb-item"><a href="{{route('seller.withdraw.method')}}">
                            {{translate('Withdraw Methods')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate("Withdraw  Preview")}}
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
									{{translate("Preview")}}
								</h5>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-xl-4">
							 <ul class="list-group">

								<li class="list-group-item d-flex justify-content-between align-items-start">
									<div class="ms-2 me-auto">{{translate('Method')}}</div>
									<span>{{optional($withdraw->method)->name}}</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start">
									<div class="ms-2 me-auto">{{translate('Amount')}}</div>
									<span>
									  {{show_amount($withdraw->amount,default_currency()->symbol) }}
									</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start">
									<div class="ms-2 me-auto">{{translate('Withdraw Charge')}}</div>
									<span>
									
										{{show_amount($withdraw->charge,default_currency()->symbol) }}
									</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start">
									<div class="ms-2 me-auto">{{translate('Conversion Rate')}}</div>
									<span>1 {{default_currency()->name}} = {{round(($withdraw->rate))}} {{($withdraw->currency->name)}}</span>
								</li>
								<li class="list-group-item d-flex justify-content-between align-items-start">
									<div class="ms-2 me-auto">{{translate('Final Amount')}}</div>
									<span>
										 {{
											show_amount($withdraw->final_amount,$withdraw->currency->symbol)
											}}
									
									</span>
								</li>
							 </ul>
						</div>

						<div class="col-xl-8">
							<form action="{{route('seller.withdraw.preview.store', $withdraw->id)}}" method="POST" enctype="multipart/form-data">
								@csrf
								<div class="shadow-lg p-3 mb-5 bg-body rounded">
									<h6>{{translate('Withdraw Information')}}</h6><hr>
									<div class="row">
										@if($withdraw->method->user_information)
											@foreach($withdraw->method->user_information as $key => $value)
												@if($value->type == "text")
													<div class="mb-3">
														<label for="{{$key}}" class="form-label">{{($value->data_label)}} <span class="text-danger">*</span></label>
														<input type="text" name="{{$key}}" id="{{$key}}" class="form-control" value="{{old($key)}}" placeholder="{{($value->data_label)}}" required>
													</div>
												@elseif($value->type == "file")
													<div class="mb-3">
														<label for="{{$key}}" class="form-label">{{($value->data_label)}} <span class="text-danger">*</span></label>
														<input type="file" name="{{$key}}" id="{{$key}}" class="form-control" value="{{old($key)}}" placeholder="{{($value->data_label)}}" required>
													</div>
												@elseif($value->type == "textarea")
													<div class="mb-3">
														<label for="{{$key}}" class="form-label">{{($value->data_label)}} <span class="text-danger">*</span></label>
														<textarea name="{{$key}}" id="{{$key}}" class="form-control" placeholder="{{($value->data_label)}}" required></textarea>
													</div>
												@endif
											@endforeach
										@endif
									</div>
								</div>
								<div class="text-end">
								   <button type="submit" class="btn btn-sm btn-success ">{{translate('Submit')}}</button>
								</div>
							</form>
						</div>
					</div>
			
				</div>
			</div>
		</div>
	</div>
@endsection
