@extends('seller.layouts.app')
@push('style-include')

   <link href="{{asset('assets/backend/css/summnernote.css')}}" rel="stylesheet" type="text/css" />

@endpush

@section('main_content')

<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Create Product")}}
            </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                            {{translate("Dashboard")}}
                    </a></li>
                    <li class="breadcrumb-item">
                        <a href="{{route('seller.product.index')}}">
                            {{translate("Products")}}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">

                        {{translate("Create Product")}}
                    </li>
                </ol>
            </div>

        </div>

        <form  id="createproduct-form" autocomplete="off" class="needs-validation"  action="{{route('seller.product.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate('Product Basic Information')}}
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label" for="name">
                                            {{translate("Product Title")}} <span class="text-danger" >*</span>
                                        </label>

                                        <input  name="name" id="name" type="text" class="form-control"  value="{{old('name')}}"
                                            placeholder="Enter product title" required>
                                        <div class="invalid-feedback">
                                            {{translate("Please Enter a product title")}}
                                            </div>
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-12">
                                    <div>
                                        <label class="form-label" for="slug">
                                            {{translate("Slug")}} <span class="text-danger" >*</span>
                                        </label>

                                        <input  name="slug" id="slug" type="text" class="form-control"  value="{{old('slug')}}"
                                            placeholder="{{translate('Enter slug')}}" required>

                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label" for="price">
                                            {{translate("Regular price")}} <span class="text-danger" >*</span>
                                        </label>

                                        <input step="any" required type="text" class="form-control" id="price" name="price"
                                            value="{{old('price')}}" placeholder="{{translate('Product price')}}">

                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label" for="discount_percentage">
                                            {{translate("Discount
                                            Percentage(%)")}}
                                        </label>

                                        <input type="number" class="form-control discount_percentage"
                                        id="discount_percentage" name="discount_percentage"
                                        value="{{old('discount_percentage') ? old('discount_percentage') :0 }}"
                                        placeholder="{{translate('Discount Percentage')}}" >

                                        <div class="text-danger" id="dicountAmount">
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div>
                                        <label for="minimum_purchase_qty" class="form-label">{{translate('Purchase
                                            Quantity (Min)')}} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="minimum_purchase_qty"
                                            id="minimum_purchase_qty" value="{{old('minimum_purchase_qty')}}"
                                            placeholder="{{translate('Min qty should be 1')}}" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div>
                                        <label for="maximum_purchase_qty" class="form-label">{{translate('Purchase
                                            Quantity (Max)')}} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="maximum_purchase_qty"
                                            id="maximum_purchase_qty" value="{{old('maximum_purchase_qty')}}"
                                            placeholder="{{translate('Max qty unlimited number')}}" required>
                                    </div>
                                </div>


                                <div class="col-12">
                                    <div>
                                        <label for="club_point" class="form-label">{{translate('Club point')}} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="point"
                                            id="club_point" value="{{old('point') ? old('point') : 0 }}"
                                            placeholder="{{translate('Enter club point')}}" required>
                                    </div>
                                </div>

                                

                                <div class="col-12">
                                    <label for="short_description">
                                        {{translate("Short Description")}}<span class="text-danger" >*</span>
                                    </label>

                                    <textarea required id="short_description" name="short_description" class="form-control  text-editor" placeholder="Must enter minimum of a 100 characters" rows="3"> {{old('short_description')}}</textarea>

                                </div>

                                <div class="col-12">
                                    <label for="mail-composer">
                                        {{translate("Product Description")}} <span class="text-danger" >*</span>
                                    </label>

                                    <textarea  id="mail-composer" class="form-control div_editor1 text-editor " name="description" rows="5"
                                     placeholder="{{translate('Enter Description')}}" required>
                                    {{old('description')}}
                                    </textarea>
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

                        <div class="card-body">
                            <div class="row gy-4">
                                <div class="col-xl-6">
                                    <div>
                                        <label for="featured_image" class="form-label">{{translate('Thumbnail Image')}} <span class="text-danger" >*</span></label>
                                        <input type="file" name="featured_image" id="featured_image"
                                            class="form-control" required>
                                        <div id="emailHelp" class="text-danger">{{translate('Image Size Should Be')}}
                                            {{file_path()['product']['featured']['size']}}
                                        </div>
                                    </div>
                                    <div class="featured_img">

                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div>
                                        <label for="product_gallery_image" class="form-label">{{translate('Gallery Image')}} <span class="text-danger" >*</span></label>
                                        <input type="file" name="gallery_image[]" id="product_gallery_image"
                                            class="form-control" multiple required>
                                        <div class="text-danger">{{translate('Image Size Should Be')}}
                                            {{file_path()['product']['gallery']['size']}}
                                        </div>
                                        <div class="d-flex flex-wrap gap-2 gallery_img"></div>
                                    </div>
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
                            <div class="form-group row g-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" value="{{translate('Attributes')}}" disabled>
                                </div>
                                <div class="col-md-9">
                                    <select name="choice_attributes[]" id="select_attributes" class="form-control" multiple >
                                        @foreach (\App\Models\Attribute::all() as $key => $attribute)
                                        <option value="{{ $attribute->id }}">{{ $attribute->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3">
                                <p>{{ translate('Choose the attributes') }}</p>
                            </div>

                            <div class="mb-3 attribute_options" id="attribute_options">

                            </div>

                            <div class="varient_combination" id="varient_combination">

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
                              {{translate("Meta Data")}}
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div>
                                        <label class="form-label" for="meta_title">
                                            {{translate("Meta
                                            title")}}
                                        </label>

                                        <input type="text" name="meta_title" id="meta_title"  class="form-control"
                                        value="{{old('meta_title')}}"    placeholder="Enter meta title">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div>
                                        <label for="meta_keyword" class="form-label">{{translate('Meta Keywords')}}</label>
                                        <select
                                            name="meta_keywords[]" id="meta_keyword" class="form-control keywords"
                                            multiple=multiple></select>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div>
                                        <label for="meta_description" class="form-label">{{translate('Meta Description')}}</label>
                                        <textarea class="form-control" rows="3" name="meta_description" id="meta_description" placeholder="{{translate('Enter meta description')}}">{{old('meta_description')}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-start mb-3">
                        <button type="submit" class="btn btn-success w-sm waves ripple-light">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate("Product Categories")}}
                            </h5>
                        </div>

                        <div class="card-body">
                            <div>
                                <label for="category_id" class="form-label">{{translate('Category')}} <span
                                        class="text-danger">*</span>
                                </label>

                                <select name="category_id" id="category_id" class="form-select w-100" required>
                                    <option value="">{{translate('--Select One--')}}</option>
                                    @foreach($categories as $category)
                                    <option  {{old('category_id') == $category->id ? "selected" : ''}}   value="{{$category->id}}" data-subcategory="{{$category->parent}}">
                                        {{(get_translation($category->name))}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-2">
                                <label for="subcategory_id" class="form-label">{{translate('Sub Category')}}</label>
                                <select name="subcategory_id" id="subcategory_id" class="form-select" >
                                    <option value="">{{translate('--Select One--')}}</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate("Product Brand")}}
                            </h5>
                        </div>

                        <div class="card-body">
                            <div>
                                <label for="brand_id" class="form-label">{{translate('Brand')}} </label>
                                <select name="brand_id" id="brand_id" class="form-select" >
                                    <option value="">{{translate('--Select One--')}}</option>
                                    @foreach($brands as $brand)
                                    <option  {{old('brand_id') == $brand->id ? "selected" : ''}} value="{{$brand->id}}">{{get_translation($brand->name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate("Product Warranty Policy")}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                {{translate("Add warranty policy of product")}}
                            </p>

                            <textarea class="form-control" name="warranty_policy" rows="5"
                                    placeholder="{{translate('Enter warranty policy')}}" >{{old('warranty_policy')}}</textarea>
                        </div>

                    </div>

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate("Product Shipping Configuration")}}
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                        
                                <div>
                                    <label class="form-label" for="weight">
                                        {{translate("Weight")}}<span class="text-danger" > ({{translate('In KG')}})</span>
                                    </label>

                                    <input step="any" type="number" class="form-control" id="weight" name="weight"
                                     value="{{old('weight') ? old('weight') :0 }}" placeholder="{{translate('Product Weight')}}">

                                </div>
                            
                            </div>

                            <div class="mb-3">
                                <label for="shipping_delivery_id" class="form-label">{{translate('Shipping Carrier/Delivery')}}
                                </label>

                                <select  class="form-select select2"
                                multiple  name="shipping_delivery_id[]" id="shipping_delivery_id">
                                    @if($shippingDeliveries->count() > 1)
                                      <option value="0">{{translate('All')}}</option>
                                    @endif
                                    @foreach($shippingDeliveries as $shipping)
                                    <option {{ (collect(old('shipping_delivery_id'))->contains($shipping->id)) ? 'selected':'' }}    value="{{$shipping->id}}">{{($shipping->name)}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">

                                <label class="form-label" for="shipping_fee">
                                    {{translate("Flat shipping fees")}} <span class="text-danger" >*</span>
                                </label>

                                <input step="any" placeholder="{{translate("Enter shipping fees")}}"  name="shipping_fee" id="shipping_fee" type="number" class="form-control"  value="{{old('shipping_fee')}}"
                                     required>

                            </div>

                            <div class="mb-3">

                                <div class="form-check form-switch">
                                    <input  type="checkbox" class="form-check-input"
                                        name="shipping_fee_multiply"
                                        value="1"
                                    id="status-switch" >
                                    <label class="form-check-label" for="status-switch"> {{ translate('Shipping fees multiply by product QTY') }}</label>
                                </div>
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
		placeholder:"{{translate('Select Keywords')}}",
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

    $('.discount_percentage').on('keyup', function() {
        var discount = $(this).val();
        var original_price = $("#price").val();
        if (discount > 100) {
            $(this).val('');
            $("#dicountAmount").text('');
            toaster( "{{translate('Discount Can Not Be Greater Than Original Price')}}", 'danger');
        } else {
            var discounted_price = original_price - (original_price * discount / 100);

            if(discount!=0){
                $("#dicountAmount").text(`Discount Price {{default_currency()->symbol}}`+discounted_price);
            }else{
                $("#dicountAmount").text('');
            }

        }
    });

    $('#price').on('keyup', function() {
        var price = $(this).val();
        var discount = $("#discount_percentage").val();
        if(price!=0 && discount!=0){
            var discounted_price = price - (price * discount / 100);
            $("#dicountAmount").text(`Discount Price{{default_currency()->symbol}}`+discounted_price);
        }else{
            $("#dicountAmount").text('');
        }
    });

    if (window.File && window.FileList && window.FileReader) {
        $("#product_gallery_image").on("change", function(e) {
            var files = e.target.files,
                filesLength = files.length;
                $(".gallery_img").html('')
            for (var i = 0; i < filesLength; i++) {
                var f = files[i];
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                    var file = e.target;
                    var galleryHtml = $(`<div class=" gallery_img-item">
								<img  src="${e.target.result}" alt="${file.name}">
								<div class="gallery_img-item_icon remove">
									<i class="las la-times-circle"></i>
								</div>
							</div>`)
                    $(".gallery_img").append(galleryHtml);
                    $(".remove").click(function() {
                        $(this).parent(".gallery_img-item").remove();
                        window.filesToUpload.splice(i, 1);
                    });
                });
                fileReader.readAsDataURL(f);
            }
        });
    }

    if(window.File && window.FileList && window.FileReader) {
        $("#featured_image").on("change", function(e) {
            let file = e.target.files[0];
            $(`.featured_img`).html('')
            $(`.featured_img`).append(
                `<img alt='${file.type}'class='mt-2' src='${URL.createObjectURL(file)}'>`
            );
        });
    }

    function selectInit(){
        $('.attribute_value').select2({
          placeholder:"{{translate('Select Value')}}",
        });
    }

    $('#select_attributes').on('change', function() {
        $('#attribute_options').html(null);
        $.each($("#select_attributes option:selected"), function(){
            attrVal($(this).val(), $(this).text());
        });

        combinations()

    });

    function attrVal(i, name){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"POST",
            url:'{{ route('seller.product.attribute.value') }}',
            data:{
               attribute_id: i
            },
            success: function(data) {
                var html = JSON.parse(data);
                $('#attribute_options').append('\
                <div class="form-group row mb-2">\
                    <div class="col-md-3">\
                        <input type="hidden" name="choice_no[]" value="'+i+'">\
                        <input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ translate('Choice Title') }}" readonly>\
                    </div>\
                    <div class="col-md-9">\
                        <select class="form-select  attribute_value" required name="choice_options_'+ i +'[]" multiple>\
                            '+html+'\
                        </select>\
                    </div>\
                </div>');
                selectInit()

           }
       });

    }

    $(document).on("change", ".attribute_value",function() {
        combinations();
    });

    function combinations(){
        $.ajax({
           type:"POST",
           url:'{{ route('seller.product.combination') }}',
           data:$('#createproduct-form').serialize(),
           success: function(data) {
                $('#varient_combination').html(data);
           }
       });
    }

</script>
@endpush






