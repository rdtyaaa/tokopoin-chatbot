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
                            {{translate('Categories')}}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                                <h5 class="card-title mb-0">
                                    {{translate('Category List')}}
                                </h5>
                        </div>

                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a href="{{route('admin.item.category.create')}}" class="btn btn-success btn-sm w-100 add-btn"><i
                                    class="ri-add-line align-bottom me-1"></i>
                                   {{translate('create')}}
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{route('admin.item.category.search')}}" method='get'>
                        <div class="row g-3">
                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input value="{{@$search}}"  name="search" type="text" class="form-control search"
                                        placeholder="Search by name">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-3 col-6">
                                <div>
                                    <button type="submit" class="btn btn-primary w-100"> <i
                                            class="ri-equalizer-fill me-1 align-bottom"></i>
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-2 col-md-3 col-6">
                                <div>
                                    <a href="{{route('admin.item.category.index')}}" class="btn btn-danger w-100"> <i
                                            class="ri-refresh-line me-1 align-bottom"></i>
                                        {{translate('Reset')}}
                                    </a>
                                </div>
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
                                        {{translate('Parent Category')}}
                                    </th>

                                    <th scope="col">
                                        {{translate('Top Category')}}
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
                                @forelse($categories as $category)
                                    <tr>
                                        <td class="fw-medium">
                                            {{$loop->iteration}}
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['category']['path'].'/'.$category->banner ,file_path()['category']['size']) }}" alt="{{$category->banner}}">
                                                </div>

                                                <div class="flex-grow-1">
                                                    {{@get_translation($category->name)}}
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($category->parent_id)

                                                {{@get_translation($category->parentCategory->name)}}

                                            @else
                                                {{translate('N/A')}}
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{route('admin.item.category.top', $category->id)}}" class="
                                                fs-18 link-{{$category->top=='1' ? 'success' :'danger'}}
                                                " data-bs-toggle="tooltip" data-bs-placement="top" title="Top category">
                                            @if($category->top =='0')
                                                <i class="ri-close-circle-line"></i>
                                            @else
                                                <i class="ri-check-double-line"></i>
                                            @endif
                                            </a>
                                        </td>

                                        <td>
                                            @if($category->status == 1)
                                            <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                            @else
                                                <span class="badge badge-soft-danger">{{translate('Inactive')}}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="hstack justify-content-center gap-3">
                                                @if(permission_check('update_category'))
                                                    <a  title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top"  href="{{route('admin.item.category.edit', $category->id)}}"  class=" fs-18 link-warning"><i class="ri-pencil-fill"></i></a>
                                                @endif
                                                @if(permission_check('delete_category'))
                                                    <a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.item.category.delete',$category->id)}}" class="delete-item fs-18 link-danger">
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
                        {{$categories->appends(request()->all())->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>


@include('admin.modal.delete_modal')

@endsection







