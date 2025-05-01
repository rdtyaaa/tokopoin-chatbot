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
                            {{translate("Home")}}
                        </a></li>
                        <li class="breadcrumb-item">
                            <a href="{{route('admin.digital.product.index')}}">
                            {{translate("Digital Products")}}
                        </a>
                        </li>
                        <li class="breadcrumb-item active">

                            {{translate("Create Product")}}
                        </li>
                    </ol>
                </div>
            </div>

            <form  id="createproduct-form" autocomplete="off"  action="{{route('admin.digital.product.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <div class="row g-4 align-items-center">
                                    <div class="col-sm">
                                        <h5 class="card-title mb-0">
                                            {{translate('Product Basic Information')}}
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="name">
                                                {{translate("Title")}} <span class="text-danger" >*</span>
                                            </label>

                                            <input  name="name" id="name" type="text" class="form-control"  value="{{old('name')}}"
                                                placeholder="{{translate('Enter product title')}}" required>
                                            <div class="invalid-feedback">
                                                {{translate("Please Enter a product title")}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="slug">
                                                {{translate("Slug")}} <span class="text-danger" >*</span>
                                            </label>

                                            <input  name="slug" id="slug" type="text" class="form-control"  value="{{old('slug')}}"
                                                placeholder="{{translate('Enter product slug')}}" required>
                                            <div class="invalid-feedback">
                                                {{translate("Enter product slug")}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div>
                                            <label for="club_point" class="form-label">{{translate('Club point')}} <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="point"
                                                id="club_point" value="{{old('point') ?  old('point') : 0}}"
                                                placeholder="{{translate('Enter club point')}}" required>
                                        </div>
                                    </div>
    

                                    <div class="col-12 text-editor-area">
                                        <label for="p-description">
                                            {{translate("Product Description")}} <span class="text-danger" >*</span>
                                        </label>

                                        <textarea id="p-description" class="form-control text-editor" name="description" rows="5"
                                         placeholder="{{translate('Enter Description')}}" required>
                                        {{old('description')}}</textarea>

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
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                    {{translate("Product Gallery")}}
                                </h5>
                            </div>
                            <div class="card-body ">
                                <div>
                                    <div>
                                        <label for="featured_image" class="form-label">{{translate('Thumbnail Image')}} <span class="text-danger" >*</span></label>
                                        <input type="file" name="featured_image" id="featured_image"
                                            class="form-control"  required>
                                        <div id="emailHelp" class="text-danger">{{translate('Image Size Should Be')}}
                                            {{file_path()['digital_product']['featured']['size']}}
                                        </div>
                                    </div>

                                    <div class="digital_featured_img">

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                   {{translate("Product Stock")}}
                                </h5>
                            </div>

                            <div class="card-body pb-3">
                                <div class="p-3">
                                    <a href="javascript:void(0)" class="btn  btn-primary btn-sm  border-0 rounded newattribute"><i class="las la-plus"></i> {{translate('Add New Attribute')}}</a>
                                    <div class="newdataadd"></div>
                                </div>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                  {{translate("Product Taxes")}}
                                </h5>
                            </div>

                            <div class="card-body pb-3">
                                <div>
                                    <div class="row g-3">
                                        @foreach ($taxes as $tax)

                                            <div class="col-md-4">
                                                <input type="text" class="form-control" value="{{ $tax->name }}" disabled>
                                                <input type="hidden" name="tax_id[]" class="form-control" value="{{ $tax->id }}">
                                            </div>

                                            <div class="col-md-4">
                                                <input step="any" name="tax_amount[]" type="number" class="form-control" value="" placeholder="{{translate('Amount')}}" >
                                            </div>
                                            <div class="col-md-4">
                                                <select name="tax_type[]" class="form-select">
                                                    <option value="1">
                                                        {{translate("Flat")}}
                                                    </option>
                                                    <option value="0">
                                                        {{translate("Percentage")}}
                                                    </option>
                                                </select>
                                            </div>

                                        @endforeach

                                    </div>


                                </div>
                            </div>

                        </div>


                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                   {{translate("Customer Information")}}
                                </h5>
                            </div>

                            <div class="card-body pb-3">
                                <div class="p-3">
                                    <a href="javascript:void(0)" class="btn  btn-success btn-sm  border-0 rounded newinput"><i class="las la-plus"></i> {{translate('Add New')}}</a>
                                    <div class="customfields mt-4"></div>
                                </div>
                            </div>

                        </div>
                      
                      

                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                    {{translate("Meta Data")}}
                                </h5>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="meta_title">
                                                {{translate("Meta
                                                title")}}
                                            </label>
                                            <input type="text" name="meta_title" id="meta_title"  class="form-control"
                                            value="{{old('meta_title')}}"  placeholder="{{translate('Enter meta title')}}">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="meta_keyword" class="form-label">{{translate('Meta Keywords')}}</label>
                                            <select
                                                name="meta_keywords[]" id="meta_keyword" class="form-control keywords"
                                                multiple>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="meta_description" class="form-label">{{translate('Meta Description')}}</label>
                                            <textarea class="form-control" rows="3" name="meta_description" id="meta_description" placeholder="{{translate('Enter meta description')}}">{{old('meta_description')}}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="text-end mb-3">
                            <button type="submit" class="btn btn-success w-sm waves ripple-light">
                                {{translate("Submit")}}
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                    {{translate("Publish")}}
                                </h5>
                            </div>

                            <div class="card-body">
                                <div>
                                    <label for="status" class="form-label">{{translate('Product Status')}} <span
                                        class="text-danger">*</span></label>
                                    <select class="form-select selectItem" id="status" name="status" required>
                                        <option  value="">{{translate('--Select One--')}}</option>
                                        <option  {{old('status') == 0? 'selected': ''}}    value="0">{{translate('New')}}</option>
                                        <option {{old('status') == 1? 'selected': ''}}     value="1">{{translate('Published')}}</option>
                                        <option {{old('status') == 2? 'selected': ''}}    value="2">{{translate('Inactive')}}</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                    {{translate("Product Categories")}}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div>
                                    <label for="category_id" class="form-label">{{translate('Category')}} <span
                                            class="text-danger">*</span></label>
                                    <select name="category_id" id="category_id" class="form-select w-100 selectItem" required>
                                        <option value="">{{translate('--Select One--')}}</option>
                                        @foreach($categories as $category)
                                        <option  {{old('category_id') == $category->id ? "selected" : ''}}   value="{{$category->id}}" data-subcategory="{{$category->parent}}">
                                            {{(@get_translation($category->name))}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <label for="subcategory_id" class="form-label">{{translate('Sub Category')}}</label>
                                    <select name="subcategory_id" id="subcategory_id" class="form-select selectItem" >
                                        <option value="">{{translate('--Select One--')}}</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>


@endsection

@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>


@endpush

@push('script-push')
<script>
	"use strict";

    $(".select2").select2({
		placeholder:"{{translate('Select item')}}",
	})



    $('.newinput').on('click', function () {
	        var html = `
		        <div class="row g-3 border-bottom pb-3 mb-3 newuserdata">
		    		<div class="col-lg-2">
						<input name="data_name[]" class="form-control" type="text" required placeholder="{{translate('User Field Name')}}">
					</div>

                    <div class="col-lg-2">
						<input name="data_label[]" class="form-control" type="text" required placeholder="{{translate('Label Name')}}">
					</div>

					<div class="col-lg-2">

						<select name="type[]" class="form-control input-type">
	                        <option value="text" > {{translate('Input Text')}} </option>
	                        <option value="textarea" > {{translate('Textarea')}} </option>
                            <option value="select" > {{translate('Select')}} </option>
                            <option value="number" > {{translate('Number')}} </option>
	                    </select>
					</div>

					<div class="col-lg-3 option-value d-none">
						<input name="option_value[]" class="form-control" type="text"  placeholder="{{translate('Comma separated.. select values')}}">
					</div>

	                <div class="col-lg-2">
						<select name="required[]" class="form-control">
	                        <option value="1"> {{translate('Required')}} </option>
	                        <option value="0"> {{translate('Optional')}} </option>
	                    </select>
					</div>

		    		<div class="col-lg-1 col-12 text-right">
		                <span class="input-group-btn">
		                    <button class="btn btn-danger btn-md removeBtn" type="button">
								<i class="ri-delete-bin-line"></i>
		                    </button>
		                </span>
		            </div>
		        </div>`;
	        $('.customfields').append(html);
	    });
	    $(document).on('click', '.removeBtn', function () {
	        $(this).closest('.newuserdata').remove();
	    });
  
  
       $(document).on('change', '.input-type', function () {

            var selectedType = $(this).val();
            var optionValueDiv = $(this).closest('.newuserdata').find('.option-value');
            if(selectedType == 'select') {
                optionValueDiv.removeClass('d-none');
            } else {
                optionValueDiv.addClass('d-none');
            }
        });



    $(".selectItem").select2({
		placeholder:"{{translate('Select item')}}",
	})
    $('select[name=category_id]').on('change', function() {
        $('select[name=subcategory_id]').html('<option value="" selected="" disabled="">{{translate("--Select One--")}}</option>');
        var subcategorys = $('select[name=category_id] :selected').data('subcategory');
        var html = '';
        var lang_code = "{{session()->get('locale')}}"
        subcategorys.forEach(function myFunction(item, index) {

            const x =  `{{old('subcategory_id')}}`;
            html += `<option   value="${item.id}">${JSON.parse(item.name)[lang_code]}</option>`
        });
        $('select[name=subcategory_id]').append(html);
    });

    $('.keywords').select2({
        tags: true,
        tokenSeparators: [','],
        placeholder:"{{translate('Type keywords')}}",
    });

    $('#select_attributes').select2({
        placeholder:"{{translate('Choose Value')}}",
    });

	$("#featured_image").on("change", function(e) {
		var file = e.target.files[0];
		$(`.digital_featured_img`).html('')
		$(`.digital_featured_img`).append(
			`<img alt='${file.type}'class='mt-2' src='${URL.createObjectURL(file)}'>`
		);
	});

	$('.newattribute').on('click', function(){
        var html = `
        <div class="row newdata my-2">
            <div class="mb-3 col-lg-5">
                <input type="text" class="form-control" name="attribute_option[name][]" placeholder="Attribute name">
            </div>

            <div class="mb-3 col-lg-5">
                <input type="number" step="any" class="form-control" name="attribute_option[price][]" placeholder="Attribute price">
            </div>

            <div class="col-lg-2 col-md-12 mt-md-0 mt-2 text-right">
                <span class="input-group-btn">
                    <button class="btn btn-danger btn-md removeBtn " type="button">
                        <i class="ri-close-fill"></i>
                    </button>
                </span>
            </div>
        </div>`;
        $('.newdataadd').append(html);
        $(".removeBtn").on('click', function(){
            $(this).closest('.newdata').remove();
        });
    });

</script>
@endpush






