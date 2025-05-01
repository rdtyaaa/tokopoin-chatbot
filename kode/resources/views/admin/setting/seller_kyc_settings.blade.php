@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">

@php
	$kycSettings     = !is_array(site_settings('seller_kyc_settings',[])) ?  json_decode(site_settings('seller_kyc_settings',[]),true) : [];
@endphp

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
                        {{translate($title)}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate($title)}}
                        </h5>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="javascript:void(0)" id="add-kyc-option" class="btn btn-success btn-sm add-btn waves ripple-light"
                                ><i class="ri-add-line align-bottom me-1"></i>
                                {{translate('Add More')}}
                   
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <form data-route="{{route('admin.general.setting.kyc.store')}}"  class="settingsForm" >
                @csrf
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-hover table-centered align-middle table-nowrap">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">
                                        {{translate('Name')}}
                                    </th>

                                    <th scope="col">
                                        {{translate('Placeholder')}}
                                    </th>

                                    <th scope="col">
                                        {{translate('Type')}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Mandatory/Required')}}
                                    </th>

                                    <th scope="col">
                                        {{translate('Action')}}
                                    </th>
                                </tr>
                            </thead>

                            <tbody id="ticketField">
                                @foreach ($kycSettings as $input)
                                    <tr>
                                        <td data-label='{{translate("Label")}}'>
                                            <div class="form-inner mb-0">
                                                <input class="form-control" type="text" name="custom_inputs[{{$loop->index}}][labels]"  value="{{$input['labels']}}">
                                            </div>
                                        </td>

                                        <td  data-label='{{translate("Placeholder")}}'>
                                            <div class="form-inner mb-0">
                                                <input class="form-control" type="text" name="custom_inputs[{{$loop->index}}][placeholder]"  value="{{$input['placeholder']}}">
                                            </div>
                                            <input   type="hidden" name="custom_inputs[{{$loop->index}}][default]"  value="{{$input['default']}}">
                                            <input   type="hidden" name="custom_inputs[{{$loop->index}}][multiple]"  value="{{$input['multiple']}}">
                                            <input   type="hidden" name="custom_inputs[{{$loop->index}}][name]"  value="{{$input['name']}}">
                                        </td>
                                        <td data-label='{{translate("Type")}}'>
                                            <div class="form-inner mb-0">

                                                @if($input['default'] == App\Enums\StatusEnum::true->status())
                                                    <input disabled type="text" name="custom_inputs[type]"  value="{{$input['type']}}">
                                                    <input type="hidden" name="custom_inputs[{{$loop->index}}][type]"  value="{{$input['type']}}">
                                                @else
                                                <select  class="form-select" name="custom_inputs[{{$loop->index}}][type]" >
                                                    @foreach(['file','textarea','text','date','email'] as $type)
                                                        <option {{$input['type'] == $type ?'selected' :""}} value="{{$type}}">
                                                            {{ucfirst($type)}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @endif

                                            </div>
                                        </td>
                                        <td  data-label='{{translate("Required")}}' >
                                            <div class="form-inner mb-0">
                                                @if($input['default'] == App\Enums\StatusEnum::true->status() && $input['type'] != 'file' )
                                                    <input disabled  type="text" name="custom_inputs[required]"  value="{{$input['required'] == App\Enums\StatusEnum::true->status()? 'Yes' :'No'}}">
                                                    <input hidden  type="text" name="custom_inputs[{{$loop->index}}][required]"  value="{{$input['required']}}">
                                                @else
                                                    <select class="form-select" name="custom_inputs[{{$loop->index}}][required]" >
                                                        <option {{$input['required'] == App\Enums\StatusEnum::true->status() ?'selected' :""}} value="{{App\Enums\StatusEnum::true->status()}}">
                                                            {{translate('Yes')}}
                                                        </option>
                                                        <option {{$input['required'] == App\Enums\StatusEnum::false->status() ?'selected' :""}} value="{{App\Enums\StatusEnum::false->status()}}">
                                                            {{translate('No')}}
                                                        </option>
                                                    </select>
                                                @endif
                                            </div>
                                        </td>
        
                                        <td data-label='{{translate("Option")}}'>
                                            @if($input['default'] == App\Enums\StatusEnum::true->status())
                                                {{translate('N/A')}}
                                                @else
                                                <div>
                                                    <a href="javascript:void(0);" class="pointer link-danger fs-18 delete-option">
                                                        <i class="las la-trash-alt"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                    
                        </table>
                    </div>
                </div>

                <div class="text-start p-4">
                    <button id="add-btn" type="submit" class="btn btn-success waves ripple-light">
                        {{translate('Submit')}}
                </div>
            </form>
        </div>
    </div>
</div>


@endsection



@push('script-push')
<script>
  "use strict";



    $(document).on('submit','.settingsForm',function(e){
    
            e.preventDefault();
            var data =   new FormData(this)
            var route = $(this).attr('data-route')
            var submitButton = $(e.originalEvent.submitter);
            $.ajax({
            method:'post',
            url: route,
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            beforeSend: function() {
                    submitButton.find(".note-btn-spinner").remove();

                    submitButton.append(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
                            <span class="visually-hidden"></span>
                        </div>`);
                },
            success: function(response){
                var className = 'success';
                if(!response.status){
                    className = 'danger';
                }
                toaster( response.message,className)

                if(response.status && response.preview_html){
                    $('.wp-preview-section').html(response.preview_html)
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
                            toaster(error.responseJSON.message,'danger')
                        }
                        else{
                            toaster( error.responseJSON.error,'danger')
                        }
                    }
                }
                else{
                    toaster(error.message,'danger')
                }
            },
            complete: function() {
                submitButton.find(".note-btn-spinner").remove();
            },
            })

    });

        var count = "{{count($kycSettings)-1}}";
		// add more kyc option
		$(document).on('click','#add-kyc-option',function(e){
			count++
			var html = `<tr>
							<td data-label="{{translate("label")}}">
                                <div class="form-inner mb-0">
								  <input class="form-control" placeholder="{{translate("Enter name")}}" type="text" name="custom_inputs[${count}][labels]" >
                                </div>
							</td>

                            <td data-label="{{translate("placeholder")}}">
                                <div class="form-inner mb-0">
                                    <input  class="form-control" placeholder="{{translate("Enter Placeholder")}}"  type="text" name="custom_inputs[${count}][placeholder]" >
                                    <input  type="hidden" name="custom_inputs[${count}][default]"  value="0">
                                    <input  type="hidden" name="custom_inputs[${count}][multiple]"  value="0">
                                    <input  type="hidden" name="custom_inputs[${count}][name]"  value="">
                                </div>
							</td>

							<td data-label="{{translate("Type")}}">
                                <div class="form-inner mb-0">
                                    <select class="form-select" name="custom_inputs[${count}][type]" >
                                        <option value="text">Text</option>
                                        <option value="email">Email</option>
                                        <option value="date">Date</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="file">File</option>
                                    </select>
                                </div>
							</td>
							<td data-label="{{translate("Required")}}">
                                <div class="form-inner mb-0">
                                    <select class="form-select" name="custom_inputs[${count}][required]" >
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
							</td>
						
							<td data-label='{{translate("Option")}}'>
							   <div >
                                    <a href="javascript:void(0);"  class="pointer link-danger fs-18 delete-option">
                                         <i class="las la-trash-alt"></i>
                                    </a>
                                </div>
							</td>

						</tr>`;
				$('#ticketField').append(html)

			e.preventDefault()
		})
        //delete ticket options
		$(document).on('click','.delete-option',function(e){
			$(this).closest("tr").remove()
			count--
			e.preventDefault()
		})
</script>
@endpush