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
                    <li class="breadcrumb-item active">{{translate('App Settings')}}</li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
					<div class="col-sm">
						<div>
							<h5 class="card-title mb-0">
								{{translate('Onboard Page Content')}}
							</h5>
						</div>
					</div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="javascript:void(0)" class=" add-more btn btn-success btn-sm add-btn waves ripple-light"
                            ><i class="ri-add-line align-bottom me-1"></i>
                                    {{translate('Add More')}}
                            </a>
                        </div>
                    </div>
				</div>
			</div>

            @php

               $app_settings  = site_settings('app_settings',null) ? json_decode(site_settings('app_settings',null),true) : [];

            @endphp

			<div class="card-body">
                <form action="{{route('admin.general.app.setting.update')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4 mb-4" id="appendChild">

                        @foreach($app_settings as $key=>$settings)
                        <div class="col-xl-6 options" >
                            <div class="border rounded">
                                    <div class="card-header border-bottom-dashed px-3 py-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="mb-0 fs-14 key">
                                                {{ucfirst(str_replace('_'," ",$key))}}
                                            </h5>

                                            <div class="d-flex align-items-center gap-2">
                                                    <a href="javascript:void(0)" class="link-danger delete-option">
                                                        <i class="fs-20 ri-delete-bin-6-line"></i>
                                                    </a>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="p-3 frontend-scrollbar" data-simplebar="init">
                                        <div class="row g-4">
                                            <div class="col-xxl-6">
                                                <label for="{{$key}}-heading" class="form-label">
                                                        {{translate("heading")}}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input id="{{$key}}-heading" required placeholder ="{{translate('Enter heading')}}"  type="text" name="setting[{{$key}}][heading]" value="{{$settings['heading']}}" class="form-control">
                                            </div>

                                            <div class="col-xxl-6">
                                                <label for="{{$key}}-image" class="form-label">
                                                        {{translate('Image')}}
                                                    <span class="text-danger"> ({{file_path()['onboarding_image']['size']}}) </span>
                                                </label>

                                                <input   name="setting[{{$key}}][image]" type="file" id="{{$key}}-image"  class="form-control">

                                                <div class="mt-2 onboarding-image">

                                                    <img src="{{show_image(file_path()['onboarding_image']['path'].'/'.@$settings['image'],file_path()['onboarding_image']['size'])}}" alt="{{$settings['image']}}">
                                                </div>

                                            </div>

                                            <div class="col-12">
                                                <label  for="{{$key}}-description" class="form-label">
                                                        {{translate('Description')}}
                                                    <span class="text-danger">*</span>
                                                </label>

                                                <textarea required  name="setting[{{$key}}][description]"  placeholder="{{translate('Type Here')}}" class="form-control" id="{{$key}}-description" cols="30" rows="10">{{$settings ['description']}}</textarea>

                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        @endforeach

                    </div>

                    <div class="text-start">
                        <button type="submit"
                            class="btn btn-success waves ripple-light"
                            id="add-btn">
                            {{translate('submit')}}
                        </button>
                    </div>
                </form>
			</div>
		</div>

	</div>

    @php

      $required =  'required' ;
      if(site_settings('app_settings',null)){
        $required = '';
      }

    @endphp

</div>

@endsection


@push('script-push')
<script>
	(function($){
		"use strict";

        var required = "{{$required}}"

        var i = 0;
        $(document).on('click', '.add-more',function(e){

           var keyV = $('.key:last').html()
           if(keyV){
             i = parseInt(keyV.match(/\d+/)[0]);
           }
           i++;
            $('#appendChild').append(
                    `
                    <div class="col-lg-6 options" >
                        <div class="border rounded">
                                <div class="card-header border-bottom-dashed px-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5 class="mb-0 fs-14 key">
                                           Page ${i}
                                        </h5>

                                        <div class="d-flex align-items-center gap-2">
                                                <a href="javascript:void(0)" class="link-danger delete-option">
                                                   <i class="fs-20 ri-delete-bin-6-line"></i>
                                                </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 frontend-scrollbar" data-simplebar="init">
                                    <div class="row g-4">

                                        <div class="col-md-6">
                                            <label for="heading" class="form-label">
                                                    {{translate("heading")}}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input id="heading" required placeholder ="{{translate('Enter heading')}}" type="text" name="setting[page_${i}][heading]" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="image" class="form-label">
                                                    {{translate('Image')}}
                                                <span class="text-danger">({{file_path()['onboarding_image']['size']}}) </span>
                                            </label>

                                            <input ${required}  name="setting[page_${i}][image]" type="file" id="image"  class="form-control">

                                        </div>
                                        <div class="col-md-12">
                                            <label for="description" class="form-label">
                                                    {{translate('Description')}}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <textarea id="description" required  name="setting[page_${i}][description]"  placeholder="{{translate('Type Here')}}" class="form-control"  cols="30" rows="10"></textarea>

                                        </div>

                                    </div>
                                </div>
                        </div>
                    </div>

                    `
            )
            e.preventDefault()
        })

        //delete element


        $(document).on('click', '.delete-option',function(e){
            i--
            $(this).closest('.options').remove();
        });



	})(jQuery);
</script>
@endpush


