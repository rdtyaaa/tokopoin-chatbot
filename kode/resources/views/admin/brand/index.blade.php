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
                        {{translate('Brands')}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Brand List')}}
                        </h5>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.item.brand.create')}}" class="btn btn-success btn-sm w-100 add-btn waves ripple-light"><i
                                class="ri-add-line align-bottom me-1"></i>
                                {{translate('Create')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route('admin.item.brand.search')}}" method='get'>
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input value="{{@$search}}"  name="search" type="text" class="form-control search"
                                    placeholder="{{translate('Search by name')}}">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <button type="submit" class="btn btn-primary w-100  waves ripple-light"> <i
                                    class="ri-equalizer-fill me-1 align-bottom"></i>
                                {{translate('Search')}}
                            </button>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <a href="{{route('admin.item.brand.index')}}" class="btn btn-danger w-100 waves ripple-light"> <i
                                    class="ri-refresh-line me-1 align-bottom"></i>
                                {{translate('Reset')}}
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    {{translate('Name')}}
                                </th>
                                <th scope="col">
                                    {{translate('Top Brand')}}
                                </th>
                                <th scope="col">
                                    {{translate('Status')}}
                                </th>
                                <th scope="col">
                                    {{translate('Options')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($brands as $brand)
                                <tr>
                                    <td class="fw-medium">
                                        {{$loop->iteration}}
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['brand']['path'].'/'.$brand->logo ,file_path()['brand']['size']) }}" alt="{{$brand->logo}}"
                                                >
                                            </div>

                                            <div class="flex-grow-1">
                                                {{@get_translation($brand->name)}}
                                                    @if($brand->top == '2')
                                                        <span class="badge badge-soft-success">
                                                            <i class="ri-star-s-fill"></i>
                                                        </span>
                                                    @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <a href="{{route('admin.item.brand.top', $brand->id)}}" class="
                                            fs-18 link-{{$brand->top==1 ? 'danger' :'success'}}
                                            " data-bs-toggle="tooltip" data-bs-placement="top" title="Top Brand">
                                        @if($brand->top==2)
                                            <i class="ri-check-double-line"></i>

                                        @else
                                            <i class="ri-close-circle-line"></i>
                                        @endif
                                        </a>
                                    </td>

                                    <td>
                                        @if($brand->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                        @endif

                                    </td>

                                    <td>
                                        <div class="hstack justify-content-center gap-3">
                                            @if(permission_check('update_brand'))
                                                <a   title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-id="{{$brand->id}}" data-serial="{{$brand->serial}}" data-name="{{get_translation($brand->name)}}" data-slug="{{$brand->slug}}" data-status="{{$brand->status}}" href="javascript:void(0)"  class="brand fs-18 link-warning"><i class="ri-pencil-fill"></i></a>
                                            @endif
                                            @if(permission_check('delete_brand'))
                                                <a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.item.brand.delete',$brand->id)}}" class="delete-item fs-18 link-danger">
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
                     {{$brands->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updatebrand" tabindex="-1" aria-labelledby="updatebrand" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light pb-2">
                <h5 class="modal-title">{{translate('Update Brand')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{route('admin.item.brand.update')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div>
                        <div class="mb-3">
                            <label for="serial" class="form-label">{{translate('Serial Number')}}
                                <span class="text-danger" > *</span>
                            </label>
                            <input type="text" class="form-control" id="serial" name="serial" placeholder="{{translate('Enter serial number')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">{{translate('Name')}}({{session()->get("locale")}})  <span class="text-danger" > *</span></label>
                            <input type="text" class="form-control" id="name" name="title[{{session()->get('locale')}}]" placeholder="{{translate('Enter name')}}" required>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">{{translate('Slug')}} <span class="text-danger" > *</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" placeholder="{{translate('Enter slug')}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="logo" class="form-label">{{translate('Logo')}}</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                            <div class="form-text">{{translate('Supported File : jpg,png,jpeg and size')}} {{file_path()['brand']['size']}} {{translate('pixels')}}</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">{{translate('Status')}}  <span class="text-danger" > *</span></label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="1">{{translate('Active')}}</option>
                                <option value="2">{{translate('Inactive')}}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-md btn-danger" data-bs-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" class="btn btn-md btn-success">{{translate('Submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.modal.delete_modal')
@endsection
@push('script-push')
<script>
	(function($){
       	"use strict";
        $('.brand').on('click', function(){
            var modal = $('#updatebrand');
            modal.find('input[name=id]').val($(this).data('id'));
            modal.find('input[name=serial]').val($(this).data('serial'));
            $('#name').val($(this).data('name'));
            $('#slug').val($(this).data('slug'));
            modal.find('select[name=status]').val($(this).data('status'));
            modal.modal('show');
        });
	})(jQuery);
</script>
@endpush





