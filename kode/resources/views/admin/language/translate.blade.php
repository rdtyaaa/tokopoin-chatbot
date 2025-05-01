@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
	<div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{$title}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.language.index')}}">
                        {{translate('Languages')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Translate')}}
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
								{{translate('Translate Language')}}
							</h5>
						</div>
					</div>

				   <div class="col-sm-auto">
					  <input type="text" placeholder="Search Here" id="search-key"  class="form-control"  >
				  </div>
				</div>
			</div>

			<div class="card-body">
				<div class="table-responsive table-card">
					<table id='transaltionTable' class="table table-hover table-centered align-middle table-nowrap mb-1">
						<thead class="text-muted table-light">
							<tr>
								<th scope="col">#</th>
								<th scope="col">
									{{translate('key')}}
								</th>
								<th scope="col">
									{{translate('value')}}
								</th>

								<th scope="col">
									{{translate('Options')}}
								</th>
							</tr>
						</thead>

						<tbody>
							@forelse ($translations as $translate)
								<tr >
									<td class="fw-medium">
									{{$loop->iteration}}
									</td>

									<td>
									   {{limit_words($translate->value,10)}}
									</td>

									<td>
										<input id="lang-key-value-{{ $loop->iteration }}" name='translate[{{$translate->key }}]' value="{{ $translate->value }}" class="form-control lang-value w-100" type="text">
									</td>

									<td>
										<div class="hstack justify-content-center gap-3">
											@if(permission_check('create_languages'))
											<a href="javascript:void(0)" data-translate-id ="{{$translate->id}}" data-id ="{{$loop->iteration}}"  title="save" class="translate fs-18 link-info"><i class="ri-save-line"></i></a>
											@endif
											@if(permission_check('delete_languages'))
												<a href="javascript:void(0);" data-href="{{route('admin.language.destroy.key',$translate->id)}}" class="delete-item fs-18 link-danger">
													<i class="ri-delete-bin-line"></i></a>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td class="border-bottom-0" colspan="100">
										@include('admin.partials.not_found')
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

                <div class="pagination-wrapper d-flex justify-content-end mt-4">
                    {{ $translations->links() }}
                </div>

			</div>

		</div>
	</div>
</div>

@include('admin.modal.delete_modal')
@endsection

@push('script-push')
<script>
	(function($){
       	"use strict";


        $(document).on('keyup','#search-key',function(e){
            e.preventDefault()
            var value = $(this).val().toLowerCase();
            if(value){
                $('.pagination').addClass('d-none')

            }
            else{
                $('.pagination').removeClass('d-none')
            }
            $("#transaltionTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        })

        // save translation
        $(document).on('click','.translate',function(e){
            e.preventDefault()
            var id  = $(this).attr('data-id')
            var tranId  = $(this).attr('data-translate-id')
            var value = $(`#lang-key-value-${id}`).val()
            updateLangKeyValue(tranId,value)
        })

        function updateLangKeyValue(tranId,value){
          const data = {
            "id":tranId,
            "value":value,
          }
          $.ajax({
            method:'post',
            url: "{{ route('admin.language.tranlateKey') }}",
            data:{
                data,
				"_token" :"{{csrf_token()}}",
            },
            dataType: 'json'
          }).then(response => {
                if(response.success){
                    toaster("{{translate('Successfully Translated')}}",'success')
                }
                else{
                    toaster("{{translate('Translation Failed')}}",'danger')
                }
          })
        }
	})(jQuery);
</script>
@endpush

