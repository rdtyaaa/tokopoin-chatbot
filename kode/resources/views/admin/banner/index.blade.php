@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate("Banner")}}
            </h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.dashboard')}}">
                            {{translate("Home")}}
                        </a>
                    </li>

                    <li class="breadcrumb-item active">
                        {{translate("Banners")}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom-dashed">
                <div class="row g-2 align-items-center">
                    <div class="col-sm">
                        <div>
                            <h5 class="card-title mb-0">
                                {{translate('Banner List')}}
                            </h5>
                        </div>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-center justify-content-sm-end gap-3">
                         
                            <a href="{{route('admin.frontend.section.banner.create')}}" class="btn btn-success btn-sm add-btn waves ripple-light"><i
                                class="ri-add-line align-bottom me-1"></i>
                                    {{translate('Create Banner')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body ">
                <div class="table-responsive table-card">
                    <table class="table table-hover align-middle table-nowrap" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>#</th>
                                <th class="text-start">{{translate('Banner')}}</th>
                                <th>{{translate('Button URL')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th>{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($banners as $banner)
                                <tr>
                                    <td> {{$loop->iteration}}</td>

                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <img class="rounded avatar-sm object-fit-cover" alt="{{$banner->bg_image}}" src="{{show_image(file_path()['banner_image']['path'].'/'.$banner->bg_image ,file_path()['banner_image']['size'] )}}">
                                            </div>
                               
                                        </div>
                                    </td>

                                    <td>
                                        <a target="_blank" href="{{$banner->btn_url}}">{{$banner->btn_url ? $banner->btn_url :"N/A"}}</a>
                                 
                                    </td>

                                    <td>
                                        @if($banner->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                        @else
                                            <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="hstack justify-content-center gap-3">
                                            <a href="{{route('admin.frontend.section.banner.edit',$banner->id)}}" class="link-warning fs-18" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Update')}}"><i class="las la-pen"></i></a>
                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Delete')}}" href="javascript:void(0);" data-href="{{route('admin.frontend.section.banner.delete',$banner->id)}}" class="delete-item fs-18 link-danger">
                                                <i class="las la-trash"></i>
                                            </a>
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
            </div>
        </div>
    </div>
</div>





@include('admin.modal.delete_modal')
@endsection

