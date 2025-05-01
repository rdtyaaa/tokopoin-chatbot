@extends('seller.layouts.app')
@section('main_content')
	<div class="page-content">
		<div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{translate('KYC Verification')}}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{route('seller.dashboard')}}">
                                {{translate('Home')}}
                            </a>
                       </li>
                        <li class="breadcrumb-item active">
                            {{translate('KYC Verification')}}
                        </li>
                    </ol>
                </div>
            </div>

			<div class="card">
				<div class="card-header border-bottom-dashed">
					<div class="d-flex align-items-center">
						<h5 class="card-title mb-0 flex-grow-1">
                            {{translate('KYC Form')}}
						</h5>
					</div>
				</div>

                @php
                    $custom_feild_counter = 0;
                    $custom_rules = [];
                    $kycFields =  json_decode(site_settings("seller_kyc_settings"),true);
                @endphp
        


				<div class="card-body">
                    <form action="{{route('seller.kyc.apply')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="border rounded p-3">
                           
                            <div class="row g-4">

                                @foreach($kycFields as $kycField)
                                    @php
                                        if(isset($kycField['name']))           $field_name = $kycField['name'];
                                    @endphp
                                    <div class="col-lg-{{$kycField['type'] == 'textarea'  ? 12 :6}}">
                                        <label for="{{$loop->index}}" class="form-label">
                                            {{$kycField['labels']}} @if($kycField['required'] == '1' || $kycField['type'] == 'file') <span class="text-danger">
                                                {{$kycField['required'] == '1' ?  "*" :""}}
                                              
                                            </span>@endif
                                        </label>
                                        @if($kycField['type'] == 'textarea')
                                        <textarea id="{{$loop->index}}" {{$kycField['required'] == '1' ? "required" :""}} class="form-control"  name="kyc_data[{{ $field_name }}]" cols="30" rows="10" placeholder="{{$kycField['placeholder']}}">{{old('kyc_data.'.$field_name)}}</textarea>
                                        @elseif($kycField['type'] == 'file')
                                            <input class="form-control" id="{{$loop->index}}"  {{$kycField['required'] == '1' ? "required" :""}}    type="file" name="kyc_data[files][{{ $field_name }}]" >
                                        @else
                                            <input class="form-control" id="{{$loop->index}}" {{$kycField['required'] == '1' ? "required" :""}} type="{{$kycField['type']}}"   name="kyc_data[{{ $field_name }}]" value="{{old('kyc_data.'.$field_name)}}"  placeholder="{{$kycField['placeholder']}}">
                                        @endif 
                                    </div>
                                @endforeach
                            </div>
                        </div>


                        <div class="text-start mt-4">
                            <button type="submit"
                                class="btn btn-success waves ripple-light"
                                id="add-btn">
                                {{translate('Submit')}}
                            </button>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>
@endsection


