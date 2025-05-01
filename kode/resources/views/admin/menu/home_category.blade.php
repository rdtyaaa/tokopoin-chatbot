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
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.dashboard')}}">
                            {{translate("Home")}}
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{translate("Frontend Categories")}}
                    </li>
                </ol>
            </div>
        </div>

        <form  autocomplete="off" class="needs-validation"  action="{{ route('admin.home.category.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card pb-4">
                <div class="card-header border-bottom-dashed">
                    <h5 class="card-title mb-0">
                        {{translate("Home category list")}}
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-xl-5 col-lg-5">
                            <div class="border rounded p-3 position-relative">
                                <div class="position-absolute p-3 top-0 start-0 w-100">
                                    <input id="searchProduct" class="form-control  " type="search" placeholder="{{translate('Search here')}}">
                                </div>

                                <ul id="productUl" class="product-scroll productUl mt-5 list-group pe-1">
                                        @php
                                             $prevCate = $menucategories->pluck('category_id')->toArray();
                                        @endphp
                                        @forelse($categories as $category)
                                            <li  class="{{ in_array($category->id,  $prevCate) ? 'disabled' :'pointer'  }} select-product py-2 px-3"
                                                id='selected-product-{{$category->id}}'
                                                data-id ="{{$category->id}}"  data-name ="{{get_translation($category->name)}}" > {{get_translation($category->name)}}
                                            </li>
                                        @empty
                                            <li>
                                                @include('admin.partials.not_found')
                                            </li>
                                        @endforelse
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-7 col-lg-7">
                            <ul class="append-product product-scroll-2 " data-simplebar="init">
                                @forelse($menucategories as $menuCat)
                                    <li class='mb-2 border p-2 rounded' id='product-{{$menuCat->category_id}}'>
                                        <div class="row align-items-center g-2">
                                            <div class="col-xl-4 col-md-6">
                                                <div>
                                                    <label for="title{{$loop->index}}"  class="form-label">{{translate("Title")}}</label>
                                                    <input type="text" placeholder="{{translate('Enter title')}}" required class="form-control" id="title{{$loop->index}}" value='{{$menuCat->title}}' name="category[{{$menuCat->category_id}}][title]">
                                                </div>
                                            </div>

                                            <div class="col-xl-4 col-md-6">
                                                <div>
                                                    <label for="ptitle{{$loop->index}}"  class="form-label">{{translate("Category title")}}</label>
                                                    <input disabled   type="text" class="form-control" id="ptitle{{$loop->index}}" value="{{@get_translation($menuCat->category->name)}}">
                                                </div>
                                                <input hidden   type="text" name='category[{{$menuCat->category_id}}][cat_id]' class="form-control" value="{{$menuCat->category_id}}">
                                            </div>

                                            <div class="col-xl-3 col-md-10 col-9">
                                                <div>
                                                    <label for="serial-{{$loop->index}}"  class="form-label">{{translate("Serial Number")}}</label>

                                                    <input type="number" required class="form-control" id="serial-{{$loop->index}}" value='{{$menuCat->serial}}' name="category[{{$menuCat->category_id}}][serial]">
                                                </div>
                                            </div>

                                            <div class="col-xl-1 col-md-2 col-3">
                                                <div class="h-100 d-flex align-items-center justify-content-center mt-4">
                                                    <button type="button" class="btn btn-sm btn-danger delete-li mt-1" data-id='{{$menuCat->category_id}}'><i class="las la-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <li>
                                        @include('admin.partials.not_found')
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success w-sm waves ripple-light">
                            {{translate("Submit")}}
                        </button>
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
        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        $('#productnav-draft').remove();

        if ($(`.append-product #product-${id}`).length ) {
            toaster( "{{translate('Already Added')}}" ,'danger');
        }
        else{
            $('.append-product').append(
                `
                <li class='mb-2 border p-2 rounded' id='product-${id}'>
                    <div class="row align-items-center  g-2">
                        <div class="col-xl-4 col-md-6 col-12">
                            <div>
                                <label for="title"  class="form-label">{{translate("Title")}}</label>
                                <input type="text" placeholder="Enter title" required class="form-control" id="title" value='' name="category[${id}][title]">
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6 col-12">
                            <label for="ptitle"  class="form-label">{{translate("Category title")}}</label>
                            <input disabled id="ptitle" type="text" class="input-disabled form-control" value="${name}">
                            <input hidden   type="text" name='category[${id}][cat_id]' class="form-control" value="${id}">
                        </div>

                        <div class="col-xl-3 col-md-10 col-9">
                            <label for="ptitle"  class="form-label">{{translate("Serial Number")}}</label>
                            <input type="number" required  class="form-control" placeholder='Serial Number' name="category[${id}][serial]">
                        </div>

                        <div class="col-xl-1 col-md-2 col-3">
                            <div class="h-100 d-flex align-items-center justify-content-center mt-4">
                                <button type="button" class="btn btn-sm btn-danger  delete-li mt-1" data-id= '${id}'>
                                    <i class="las la-trash"></i>
                                </button>
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

