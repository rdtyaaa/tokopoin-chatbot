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
                    <li class="breadcrumb-item active">{{translate('Plugins')}}</li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="d-flex align-items-center">
					<h5 class="card-title mb-0 flex-grow-1">
						{{translate('Tawk To')}}

                   
				</div>
			</div>

            @php
               $tawks = site_settings('tawk_to',null) 
                           ? json_decode(site_settings('tawk_to'),true) :
                                    [
                                                'property_id' => '@@',
                                                'widget_id'   => '@@',
                                                'status'      => '1',
                                    ];

            @endphp

			<div class="card-body">
                <form action="{{route('admin.plugin.update')}}" method="POST" class="d-flex flex-column gap-4">
                    @csrf
                    <div>
                            <div class="row g-3">
                                @foreach($tawks as $key => $tawk)
                                    <div class="col-lg-6">
                                        <label for="{{$key}}" class="form-label">
                                            {{
                                                ucwords(str_replace("_"," ",$key))
                                            }} <span  class="text-danger"  >*</span>
                                        </label>
                                        @if($key == 'status')

                                            <select class="form-select" name="tawk[{{$key}}]" id="{{$key}}">
                                                    <option {{$tawk == '1' ? 'selected' :""}} value="1">
                                                        {{translate('Active')}}
                                                    </option>
                                                    <option {{$tawk == '0' ? 'selected' :""}} value="0">
                                                        {{translate('Inactive')}}
                                                    </option>
                                            </select>

                                        @else
                                            <input type="text" name="tawk[{{$key}}]" id="{{$key}}" class="form-control" value="{{$tawk}}" placeholder="{{translate('Enter Tawk').$key}}" required>
                                        @endif

                                    </div>
                                @endforeach

                            </div>

                            <div class="text-start mt-3">
                            <button type="submit"
                                class="btn btn-success waves ripple-light"
                                id="add-btn">
                                {{translate('Submit')}}
                            </button>
                        </div>
                    </div>

                </form>
			</div>
		</div>

	</div>

</div>

@endsection


