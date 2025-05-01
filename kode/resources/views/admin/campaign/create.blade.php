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
                            {{translate("Home")}}
                    </a></li>
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.campaign.index')}}">
                            {{translate("Campaigns")}}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">

                        {{translate("Create Campaign")}}
                    </li>
                </ol>
            </div>

        </div>

        <form   autocomplete="off" class="needs-validation"  action="{{ route('admin.campaign.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row gy-4">
                <div class="col-xl-8 col-lg-7">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate('Campaign Information')}}
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-xl-4">
                                    <div>
                                        <label class="form-label" for="name">
                                            {{translate("Name")}} <span class="text-danger" >*</span>
                                        </label>

                                        <input  name="name" id="name" type="text" class="form-control"  value="{{old('name')}}"
                                            placeholder="{{translate('Enter name')}}" required>
                                        <div class="invalid-feedback">
                                            {{translate("Please Enter a Name")}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div>
                                        <label for="discount_type" class="form-label">{{translate('Discount Type')}} <span class="text-danger"></span></label>
                                        <select class="form-select" name="discount_type" id="discount_type" >
                                            <option value=""> {{translate('Select Discont Type')}}</option>
                                            <option {{old('discount_type') == '1' ? 'selected' : ''}} value="1">%</option>
                                            <option {{old('discount_type') == '0' ? 'selected' : ''}} value="0">{{default_currency()->symbol}}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-xl-4">
                                    <div>
                                        <label for="discount" class="form-label">{{translate('Discount ')}}  <span class="text-danger"></span></label>
                                        <input type="number" name="discount" id="discount" value="{{old('discount')}}" class="form-control" placeholder="discount">

                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div>
                                        <label for="strat_date" class="form-label">{{translate('Start Date')}}  <span class="text-danger">*</span></label>
                                        <input class="form-control" type="datetime-local" name="strat_date" id="strat_date" value="{{old('strat_date')}}">
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div>
                                        <label for="endtDate" class="form-label">{{translate('End Date')}}  <span class="text-danger">*</span></label>
                                        <input class="form-control" type="datetime-local" name="end_date" id="endtDate" value="{{old('end_date')}}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div>
                                        <label for="banner_image" class="form-label">{{translate('Banner image')}}  <span class="text-danger">*</span></label>
                                        <input type="file" name="banner_image" id="banner_image" class="form-control">
                                        <div id="size-message" class="mt-2">
                                            <div  class="text-danger">{{translate('Image Size Should Be')}}
                                                <span id="size">
                                                     {{file_path()['campaign_banner']['size']}}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate("Campaign Products Sections")}}
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-xxl-4 col-xl-5">
                                    <div class="border rounded p-2 position-relative">
                                        <div class="position-absolute top-0 start-0 w-100">
                                            <input id="searchProduct" class="form-control border-0 border-bottom" type="search" placeholder="{{translate('Search here')}}">
                                        </div>

                                        <ul id="productUl" class="product-scroll productUl mt-5">
                                            @foreach($categories as $category)
                                                @if(count($category->product) != 0)
                                                    <li class="mb-2">
                                                        <div class="d-flex justify-content-start align-items-center gap-1">
                                                            <h5 class="cate-title">{{get_translation($category->name)}}</h5>
                                                        </div>

                                                        <ul class="productUl-sub">
                                                            @foreach($category->product as $product)
                                                                <li class="pointer py-1 px-2 select-product"
                                                                id='selected-product-{{$product->id}}'
                                                                data-id ="{{$product->id}}"  data-name ="{{$product->name}}">

                                                                {{ $product->name}}</li>

                                                            @endforeach
                                                        </ul>

                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-xxl-8 col-xl-7">
                                   <ul class="append-product product-scroll-2">

                                   </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-start">
                        <button type="submit" class="btn btn-success w-sm waves ripple-light mb-xl-4">
                            {{translate("Submit")}}
                        </button>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-5">

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate("Status")}} <span
                                class="text-danger">*</span>
                            </h5>
                        </div>

                        <div class="card-body">
                            <select class="form-select" id="status" name="status" required>
                                <option  value="">{{translate('--Select One--')}}</option>
                                <option  {{old('status') == "1"? 'selected': ''}}    value="1">{{translate('Active')}}</option>
                                <option  {{old('status') == "0"? 'selected': ''}}    value="0">{{translate('Inactive')}}</option>

                            </select>
                        </div>

                    </div>

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                {{translate("Home Page")}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-check-success form-check-reverse">
                                <label class="form-check-label" for="home_page">
                                    {{translate("Show On Home Page")}}
                                </label>
                                <input class="form-check-input" type="checkbox" value="1" name="show_home_page" id="home_page">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

</div>


@endsection


@push('script-push')
<script>
	(function($){
     "use strict"

    $('#discount').on('keyup', function() {
        var discount = $(this).val();
        var type = $("#discount_type").val();

        if (type && type  ==  '1' &&  discount >100) {
            $(this).val('');
            toaster("{{translate('Discount Can Not Be Greater Than 100')}}",'danger');
        }
        if(type == ""){
            $(this).val('');
            toaster("{{translate('Select Discount Type First')}}",'danger');
        }

    });

    $('#discount_type').on('change', function() {
        var type = $(this).val();
        var discount = $("#discount").val();
        if ((type  ==  '1' &&  discount >100) || type =='') {
            $('#discount').val('');

        }

    });

    $('#searchProduct').keyup(function(){
        var value = $(this).val().toLowerCase();
        $('#productUl li').each(function(){
            var lcval = $(this).text().toLowerCase();
            if(lcval.indexOf(value)>-1){
              $(this).show();
            } else {
               $(this).hide();
            }
        });
    });

    $('.select-product').click(function(){
        let id = $(this).attr('data-id');
        let name = $(this).attr('data-name');

        if ($(`.append-product #product-${id}`).length ) {

            toaster( "{{translate('Already Added')}}" ,'danger');
        }
        else{
            $('.append-product').append(
                `
                <li class='mb-2 border p-2 rounded' id='product-${id}'>
                    <div class="row g-2 align-items-end">
                        <div class="col-xxl-5">
                            <div>
                                <label for="ptitle" class="form-label">{{translate("Product title")}}</label>
                                <input disabled   type="text" id="ptitle" class="input-disabled form-control" value="${name}">
                            </div>
                            <input hidden   type="text" name='product[${id}][product_id]' class="form-control" value="${id}">
                        </div>

                        <div class="col-xxl-4 col-md-6">
                                <div>
                                <label for="product_discount_type" class="form-label">{{translate("Discount")}}</label>
                                <select class="form-select" name="product[${id}][discount_type]" id="product_discount_type" >
                                    <option value =''> {{translate('Discont Type')}}</option>
                                    <option {{old('product_discount_type') == 'percentage' ? 'selected' : ''}} value="1">%</option>
                                    <option {{old('product_discount_type') == 'flat' ? 'selected' : ''}} value="0">&#2547;</option>
                                </select>
                            </div>

                        </div>

                        <div class="col-xxl-2 col-md-4 col-10">
                                <div>
                                <label for="quantity" class="form-label"> {{translate("price")}}</label>
                                <input type="number" id="quantity"  class="form-control" value='0' type="text" name="product[${id}][discount]">
                            </div>
                        </div>

                        <div class="col-xxl-1 col-md-2 col-2">
                            <div class ="h-100 d-flex align-items-center justify-content-center mt-4">
                                <button type="button" class="btn btn-sm btn-danger w-100 delete-li" data-id= '${id}'><i class="las la-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </li>
                `
           )
           $(this).addClass('disabled')
           $(this).removeClass('pointer')
        }
    });


    $(document).on('click','.delete-li',function(e){
        var id = $(this).attr('data-id');
        $(`#selected-product-${id}`).removeClass('disabled')
        $(`#selected-product-${id}`).addClass('pointer')

        $(`#product-${id}`).remove()
        e.preventDefault()
    })


	})(jQuery);
</script>
@endpush






