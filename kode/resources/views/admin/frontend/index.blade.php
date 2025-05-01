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
                    <li class="breadcrumb-item active">
                        {{translate('Frontends')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Section List')}}
                        </h5>
					</div>

				</div>
			</div>

			<div class="card-body">


				<div class="row gy-4">
					<div class="col-lg-4 col-xl-3">
						<div class="bg-light p-2">

							<div class="d-flex align-items-center gap-2">
								<input placeholder="{{translate('Search here')}}" class="form-control" id='search' type="search">

								<button type="submit" class="btn btn-success waves ripple-light section-list-btn d-lg-none">
									<i class="ri-equalizer-fill align-bottom"></i>
								</button>
							</div>

							<div class="section-list-wrapper is-open mt-2">
								<div class="nav flex-column nav-pills section-list " id="v-pills-tab" role="tablist" aria-orientation="vertical">
									@foreach($frontends as $frontend)
										 <a class="nav-link mb-2 frontend-tab-list {{$loop->index == 0 ? 'active' :''}}" id="v-pills-{{$frontend->slug}}-tab" data-bs-toggle="pill" href="#v-pills-{{$frontend->slug}}" role="tab" aria-controls="v-pills-{{$frontend->slug}}" aria-selected="true">{{$frontend->name}}</a>
									@endforeach

								</div>
							</div>


						</div>
					</div>

					<div class="col-lg-8 col-xl-9">
						<div class="tab-content text-muted sticky-side-div
						" id="v-pills-tabContent">
							@foreach($frontends as $frontend)
								<div class="tab-pane fade {{$loop->index == 0 ? 'active show' :''}}" id="v-pills-{{$frontend->slug}}" role="tabpanel" aria-labelledby="v-pills-{{$frontend->slug}}-tab">

									<form class="search-sections" enctype="multipart/form-data" method="post" data-route='{{route("admin.frontend.section.update",$frontend->id)}}' >
										@csrf
										<div class="border rounded">
											<div class=" border-bottom px-3 py-2">
												<div class="d-flex align-items-center justify-content-between">
													<h5 class="mb-0 fs-14">
														{{$frontend->name}}

														@if($frontend->slug == 'social-icon')
														 <a class="fs-16 link-info"  title="{{translate('View icons')}}" data-bs-toggle="tooltip"  data-bs-placement="top" target="_blank" href="https://fontawesome.com/v6/search?o=r&m=free"><i class="ri-information-line"></i> </a>
													    @endif
													</h5>

													<div class="d-flex align-items-center gap-2">
														<div class="form-check form-switch">
															<input type="checkbox" class=" form-check-input"
																name="status"
																value="1" {{$frontend->status == "1" ? 'checked' : ''}}
															id="status-switch-{{$frontend->id}}" >
															<label class="form-check-label" for="status-switch-{{$frontend->id}}"></label>
														</div>
													</div>
												</div>
											</div>
											<div class="p-3 frontend-scrollbar" data-simplebar="init">
												<div class="row g-4">

													@if($frontend->slug == 'service-section')

													  <div class="col-12">

															<div class="table-responsive table-card">
																<table class="table table-hover table-nowrap align-middle mb-0" >
																	<thead class="text-muted table-light">
																		<tr class="text-uppercase">
																			<th>{{translate('Heading')}}</th>
																			<th>{{translate('Sub Heading')}}</th>
																			<th>{{translate('Icon')}} 	   <a class="fs-16 link-info"  title="{{translate('View icons')}}" data-bs-toggle="tooltip" data-bs-placement="top" target="_blank" href="https://fontawesome.com/v6/search?o=r&m=free"><i class="ri-information-line"></i> </a></th>

																		</tr>
																	</thead>

																	<tbody class="list form-check-all">
																		@foreach(@json_decode($frontend->value,true) ?? [] as $key => $data)

																		    <tr>

																				<td data-label='{{$key}}'>
																					<input required  name="frontend[{{$key}}][heading]" type="text"  value="{{Arr::get($data ,'heading', 'N/A')}}" class="form-control file-input">
																				</td>
																				<td data-label='{{$key}}'>

																					<input required  name="frontend[{{$key}}][sub_heading]" type="text"  value="{{Arr::get($data ,'sub_heading', 'N/A')}}" class="form-control file-input">
																				</td>
																				<td data-label='{{$key}}'>

																					<input required  name="frontend[{{$key}}][icon]" type="text"  value="{{Arr::get($data ,'icon', 'N/A')}}" class="form-control file-input">
																				</td>


																			</tr>

																		@endforeach
																	</tbody>
																</table>
															</div>

													  </div>


													@else

														@foreach( json_decode($frontend->value,true ) as $keys => $section_data)
															@foreach( $section_data as $key=> $data)
																@if($key == 'value')
																	<div class="col-md-{{$frontend->slug=='promotional-offer' || $frontend->slug=='promotional-offer-2' ? 12 :12}}">
																		<label  class="form-label">
																			{{ucwords(str_replace("_"," ",$keys))}}
																			@if($section_data['type'] == 'file')
																				<span class="text-danger" >({{$section_data['size']}})</span>
																				@else
																				<span class="text-danger" > {{($keys =='position' ? "(After)" : "") }} *</span>
																			@endif
																		</label>
																		@if($section_data['type'] == 'textarea')
																		<textarea  @if($section_data['type'] !='file') required @endif class="form-control" name="frontend[{{ $keys}}][{{$key}}]"  cols="30" rows="10">{{$section_data['value']}}</textarea>
																		@elseif($section_data['type'] == 'select')

																		<select name="frontend[{{ $keys}}][{{$key}}]"  @if($section_data['type'] !='file') required @endif   class="form-select selectbox"  >

																			@foreach(frontend_section()->where('status',1)->whereNotIn('slug',['promotional-offer','promotional-offer-2','social-icon','app-section','footer-text','news-latter','floating-header','footer-text','payment-image','support','seo-section','login','breadcrumb','cookie','contact']) as $selectBox)

																				<option {{$selectBox->slug == $section_data['value'] ? 'selected' :"" }} value="{{$selectBox->slug}}"> {{$selectBox->name}} {{translate('Section')}}</option>

																			@endforeach

																		</select>

																		@else
																		<input @if($section_data['type'] !='file') required @endif name="frontend[{{ $keys}}][{{$key}}]" type="{{$section_data['type']}}" @if($section_data['type'] !='file') value="{{$section_data['value']}}" @endif class="form-control file-input">
																		@endif

																		@if($section_data['type'] == 'file')
																			<div class="mt-2 preview">
																				<img src="{{show_image(file_path()['frontend']['path'].'/'.@$section_data['value'],$section_data['size'])}}" alt="{{@$section_data['value']}}">
																			</div>
																		@endif
																	</div>
																@else

																@if($key  == 'url' || $key  == 'icon' || $key  == 'sub_heading' || $key  == 'heading'  )
																	<div class="col-md-{{$key  == 'heading' || ($key  == 'url' && $frontend->slug != 'social-icon' )   ? 12 :6}}">
																		<label  class="form-label">
																		{{ucwords(str_replace("_"," ",$keys))}}
																		({{ucwords(str_replace("_"," ",ucwords($key)))}})
																				<span class="text-danger" >*</span>
																		</label>
																		<input required name="frontend[{{ $keys}}][{{$key}}]"  type="text" value="{{$section_data[$key]}}" class="form-control" >
																	</div>
																@else
																	<input hidden name="frontend[{{ $keys}}][{{$key}}]" type="text" value="{{$section_data[$key]}}" class="form-control" >
																	@endif

																@endif

															@endforeach
														@endforeach

													@endif


													<div class="text-end">
														<button type="submit"
															class="btn btn-success waves ripple-light">
															{{translate('submit')}}
														</button>
													</div>
												</div>
											</div>

										</div>
									</form>

								</div>
							@endforeach

						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>


@endsection



@push('script-push')
<script>
	(function($){
       	"use strict";

		$('#search').keyup(function(){

			  var found = false;

		    	var value = $(this).val().toLowerCase();
				$('.frontend-tab-list').each(function(){
					var lcval = $(this).text().toLowerCase();
					if(lcval != 'Promotional Offer'){
						if(lcval.indexOf(value)>-1){
							$('.section-list-wrapper').removeClass('is-open')
							$(this).show();
							found = true;
						} else {
							$(this).hide();
						}
					}
				});

				if (!found) {
					$('.no-data-found').remove();
					$('.section-list-wrapper').append(`<div class="mb-2 no-data-found py-4 text-center bg-white " id="v-pills-messages-tab" data-bs-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false" tabindex="-1">
							<b class="fw-bold">
								{{translate('No Section Founded')}}
							</b>
					 </div>`);
				} else {
					$('.no-data-found').remove();
				}
		});


		$(document).on('submit', '.search-sections', function (e) {


			var route = $(this).attr('data-route');
			var formData = new FormData(this);
			var submitButton = $(e.originalEvent.submitter);

			$.ajax({
            url: route,
            beforeSend: function() {

				submitButton.find(".note-btn-spinner").remove();

				submitButton.append(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
						<span class="visually-hidden"></span>
					</div>`);

            },
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function (response) {
				if (response.status){

                        toaster(response.message,'success')

                    }else if(response.error){
                        toaster(response.message,'danger')
                    }
            },
            error: function (error){
                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            if((error.responseJSON.message)){

                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                            }
                            else{
                                toaster(error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
            },
            complete: function () {
				submitButton.find(".note-btn-spinner").remove();

            },

        });
			e.preventDefault();
		});



		$(document).on('change', '.file-input', function (e) {

			var file = e.target.files[0];

			var previewSection = $(this).parent().find('.preview')


			$(previewSection).html(
				`<img alt='${file.type}' class="mt-2 rounded  d-block"
				src='${URL.createObjectURL(file)}'>`
			);
			e.preventDefault();
       })






	})(jQuery);
</script>
@endpush

