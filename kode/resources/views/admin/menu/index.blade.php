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
                        {{translate('Menus')}}
                    </li>
                </ol>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-bottom-dashed">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Menu List')}}
                        </h5>
                    </div>

                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.menu.create')}}" class="btn btn-success btn-sm add-btn waves ripple-light"
                                ><i class="ri-add-line align-bottom me-1"></i>
                                    {{translate('Add New ')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-centered align-middle table-nowrap">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    {{translate('Name')}}
                                </th>
                                <th scope="col">
                                    {{translate('URL')}}
                                </th>
                                <th scope="col">
                                    {{translate('Options')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($menus as $menu)
                                <tr>
                                    <td class="fw-medium">
                                    {{$loop->iteration}}
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['menu']['path'].'/'.$menu->image ,file_path()['menu']['size'])}}" alt="{{$menu->name}}"
                                                    >
                                            </div>
                                            <div class="flex-grow-1">
                                                {{$menu->name}}
                                            </div>
                                        </div>
                                    </td>

                                    <td>

                                        <a target="_blank" href="{{URL($menu->url)}}">
                                            {{URL($menu->url)}}
                                        </a>
                                    </td>

                                    <td>
                                        <div class="hstack justify-content-center gap-3">
                                            <a class="fs-18 link-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Menu Update')}}" href="{{route('admin.menu.edit', $menu->id)}}"><i class="las la-pen"></i></a>
                                            @if($menu->default != 1)
                                                <a href="javascript:void(0)"  data-href="{{route('admin.menu.delete',$menu->id)}}" class="fs-18 delete-item link-danger " data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Menu Delete')}}">
                                                    <i class="las la-trash"></i>
                                                </a>
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
            </div>
        </div>
    </div>
</div>

@include('admin.modal.delete_modal')
@endsection
