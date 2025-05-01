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
                            {{translate("Blogs")}}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">
                                    {{translate('Blog List')}}
                                </h5>
                            </div>
                        </div>

                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a href="{{route('admin.blog.create')}}" class="btn btn-success btn-sm add-btn w-100 waves ripple-light"><i
                                    class="ri-add-line align-bottom me-1"></i>
                                        {{translate('Create Blog')}}
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{route('admin.blog.index')}}" method="get">
                        <div class="row g-3">
                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input value="{{request()->input('search')}}" type="text" name="search" class="form-control search"
                                        placeholder="{{translate('Search by category or title')}}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
                                            class="ri-equalizer-fill me-1 align-bottom"></i>
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <a href="{{route('admin.blog.index')}}" class="btn btn-danger w-100 waves ripple-light"> <i
                                            class="ri-refresh-line me-1 align-bottom"></i>
                                        {{translate('Reset')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card ">
                        <table class="table table-hover table-centered table-nowrap align-middle mb-0" >
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th>
                                        #
                                    </th>

                                    <th>
                                        {{translate('Post Title')}}
                                    </th>

                                    <th>{{translate('Category')}}</th>


                                    <th>
                                        {{translate('Status')}}
                                    </th>

                                    <th >
                                        {{translate('Action')}}
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="list form-check-all">
                                @forelse($blogs as $blog)
                                    <tr>
                                        <td>
                                            {{$loop->iteration}}
                                        </td>
                                        <td class="d-flex align-items-center" data-label="{{translate('post')}}">
                                            <img src="{{show_image(file_path()['blog']['path'].'/'.$blog->image,file_path()['blog']['size']) }}" alt="{{$blog->image}}" class="avatar-xs rounded me-2">

                                            <p class="mb-0">
                                                {{limit_words($blog->post,15)}}
                                            </p>
                                        </td>
                                        <td data-label="{{translate('Category')}}">
                                            {{(@get_translation($blog->category->name) ?? 'N/A')}}
                                        </td>

                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="status-update form-check-input"
                                                    data-column="status"
                                                    data-route="{{ route('admin.blog.status.update') }}"
                                                    data-model="Role"
                                                    data-status="{{ $blog->status == '1' ? '0':'1'}}"
                                                    data-id="{{$blog->id}}" {{$blog->status == "1" ? 'checked' : ''}}
                                                id="status-switch-{{$blog->id}}" >
                                                <label class="form-check-label" for="status-switch-{{$blog->id}}"></label>
                                            </div>
                                        </td>

                                        <td data-label="{{translate('Action')}}">
                                            <div class="hstack justify-content-center gap-3">

                                                <a  title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.blog.edit', [make_slug($blog->post), $blog->id])}}" class=" fs-18 link-warning"><i class="ri-pencil-fill"></i></a>

                                                <a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.blog.delete',$blog->id)}}" class="delete-item fs-18 link-danger">
                                                <i class="ri-delete-bin-line"></i></a>
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
                        {{$blogs->appends(request()->all())->links()}}
                    </div>

                </div>
            </div>
        </div>
    </div>
@include('admin.modal.delete_modal')
@endsection


