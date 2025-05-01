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
                            {{translate("Home")}}
                    </a></li>
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.promote.flash.deals.index')}}">
                            {{translate("Flash Deals")}}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{translate("Create")}}
                    </li>
                </ol>
            </div>

        </div>

        <form   autocomplete="off" class="needs-validation"  action="{{ route('admin.promote.flash.deals.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="id" value="{{$flashDeal->id}}">
            <div>
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="card-title mb-0">
                            {{translate('Basic Information')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xl-12">
                                <div>
                                    <label class="form-label" for="name">
                                        {{translate("Name")}} <span class="text-danger" >*</span>
                                    </label>

                                    <input  name="name" id="name" type="text" class="form-control"  value="{{$flashDeal->name}}"
                                        placeholder="{{translate('Enter name')}}" required>
                                    <div class="invalid-feedback">
                                        {{translate("Please Enter a Name")}}
                                        </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div>
                                    <label for="startDate" class="form-label">{{translate('Start Date')}}  <span class="text-danger">*</span></label>
                                    <input class="form-control" type="datetime-local" name="start_date" value="{{$flashDeal->start_date}}" id="startDate">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div>
                                    <label for="endtDate" class="form-label">{{translate('End Date')}}  <span class="text-danger">*</span></label>
                                    <input class="form-control" type="datetime-local" name="end_date" id="endtDate" value="{{$flashDeal->end_date}}">
                                </div>
                            </div>

                            <div class="col-md-12">

                                <div>
                                    <label for="banner_image" class="form-label">{{translate('Banner image')}}  <span class="text-danger">*</span></label>
                                    <input type="file" name="banner_image" id="banner_image" class="form-control">
                                    <div id="size-message" class="mt-2">
                                        <div  class="text-danger">{{translate('Image Size Should Be')}}
                                            <span id="size">
                                            {{file_path()['flash_deal']['size']}}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="preview">
                                    <img  src="{{show_image(file_path()['flash_deal']['path'].'/'.$flashDeal->banner_image,file_path()['flash_deal']['size'])}}" alt="{{$flashDeal->banner_image}}">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="card-title mb-0">
                            {{translate("Product Sections")}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xl-6">
                                <div class="border rounded p-2 position-relative">
                                    <div class="position-absolute top-0 start-0 w-100">
                                        <input id="searchProduct" class="form-control border-0 border-bottom" type="search" placeholder="{{translate('Search here')}}">
                                    </div>

                                    <ul class="productUl productUl-sub product-scroll">


                                    </ul>

                                    <div class="text-center m-4  load-more-section d-none">
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm add-btn waves ripple-light load-more">
                                            <i class="ri-refresh-line align-bottom me-1"></i>
                                            {{translate('Load more')}}
                                        </a>
                                    </div>


                                </div>
                            </div>

                            <div class="col-xl-6">
                                <ul class="append-product product-scroll-2" data-simplebar>
                                    @foreach($products as $product )
                                        <li class='mb-2 border p-2 rounded' id='product-{{$product->id}}'>
                                            <div class="row g-2 align-items-end">

                                                <div class="col-lg-11 col-sm-10">
                                                    <div>
                                                        <label for="ep-title-{{$loop->index}}" class="form-label">{{translate("Product title")}}</label>
                                                        <input disabled id="ep-title-{{$loop->index}}"  type="text" class="form-control" value="{{$product->name}}">
                                                    </div>
                                                    <input hidden   type="text" name='products[]' class="form-control" value="{{$product->id}}">
                                                </div>

                                                <div class="col-lg-1 col-sm-2 col-3">
                                                    <div class ="d-flex align-items-center justify-content-sm-center justify-content-start">
                                                        <button type="button" class="btn btn-sm btn-danger w-100 delete-li" data-id='{{$product->id}}'><i class="las la-trash"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
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
        </form>
    </div>
</div>
@endsection


@push('script-push')
<script>
	(function($){
     "use strict"

        $('#searchProduct').keyup(function(){
            var value = $(this).val().toLowerCase();
            $('.productUl li').each(function(){
                var lcval = $(this).text().toLowerCase();
                if(lcval.indexOf(value)>-1){
                   $(this).show();
                } else {
                   $(this).hide();
                }
            });
        });

        $('body').on('click', '.select-product', function() {

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
                            <div class="col-lg-11 col-sm-10">
                                <div>
                                    <label for="ptitle" class="form-label">{{translate("Product title")}}</label>
                                    <input disabled   type="text" id="ptitle" class="input-disabled form-control" value="${name}">
                                </div>
                                <input hidden   type="text" name='products[]' class="form-control" value="${id}">
                            </div>

                            <div class="col-lg-1 col-sm-2 col-3">
                                <div class ="d-flex align-items-center justify-content-sm-center justify-content-start">
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

        var page = 1;

        $(document).on('click','.load-more',function(e){
            page++;
            loadMore(page);
        })


        $(document).ready(function() {

            loadMore(page);
        });


        function loadMore(page) {

            $.ajax({
                url: "{{ route('admin.promote.flash.deals.index') }}",
                method: "GET",
                data: {
                    page: page
                },
                dataType: "json",

                success: function(response) {

                    var html = '';
                    var product;

                    for (var i = 0; i <response.data.data.length; i++) {

                        product =  response.data.data;
                        html += ` <li class="pointer p-2 select-product border-bottom"
                                            id='selected-product-${product[i].id}'
                                            data-id ="${product[i].id}"  data-name ="${product[i].name}">

                                            ${product[i].name}</li>`;
                    }
                    $('.load-more-section').removeClass('d-none');
                    if(response.total_data  == response.current_page_data){
                        $('.load-more-section').addClass('d-none');
                    }
                    else{
                        $('.load-more-section').removeClass('d-none');
                    }

                    $('.product-scroll').append(html)

                }
            });

        }


	})(jQuery);
</script>
@endpush






