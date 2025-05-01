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
                            {{translate('Testimonials')}}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">

                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{translate('Testimonials List')}}
                            </h5>
                        </div>

                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                  <button type="button" class="btn btn-success btn-sm add-btn waves ripple-light"
                                                 data-bs-toggle="modal" id="create-btn" data-bs-target="#addTestimonial"><i
                                    class="ri-add-line align-bottom me-1"></i>{{translate("Create
                                    Testimonial")}}</button>


                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{route('admin.frontend.testimonial.index')}}" method="get">
                        <div class="row g-3">
                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" name="search" value="{{request()->input('search')}}" class="form-control search"
                                        placeholder="{{translate('Search by author or designation')}}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <a href="{{route('admin.frontend.testimonial.index')}}" class="btn btn-danger w-100 waves ripple-light">
                                        <i class="ri-refresh-line me-1 align-bottom"></i>
                                        {{translate('Reset')}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-hover table-centered align-middle table-nowrap">
                            <thead class="text-muted table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">
                                        {{translate("Author")}}
                                    </th>
                                    <th scope="col">
                                        {{translate("Designation")}}
                                    </th>
                                    <th scope="col">
                                        {{translate('Rating')}}
                                    </th>
                                    <th scope="col">
                                        {{translate("Status")}}
                                    </th>
                                    <th scope="col">
                                        {{translate("Action")}}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($testimonials as $testimonial)
                                  <tr>
                                    <td>
                                        {{$loop->iteration}}
                                    </td>
                                    <td class="d-flex align-items-center" data-label="{{translate('Author')}}">
                                        <img src="{{show_image(file_path()['testimonial']['path'].'/'.$testimonial->image,file_path()['testimonial']['size']) }}" alt="{{$testimonial->image}}" class="avatar-xs rounded me-2">

                                        <p class="mb-0">
                                            {{$testimonial->author}}
                                        </p>
                                    </td>

                                    <td data-label="{{translate('Designation')}}">
                                        {{$testimonial->designation}}
                                    </td>
                                    <td data-label="{{translate('Rating')}}">
                                        <span class="badge badge-soft-success d-inline-flex align-items-center gap-1">
                                            {{$testimonial->rating}}<i class="ri-star-s-fill"></i>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="status-update form-check-input"
                                                data-column="status"
                                                data-route="{{ route('admin.frontend.testimonial.status.update') }}"
                                                data-model="Testimonial"
                                                data-status="{{ $testimonial->status == '1' ? '0':'1'}}"
                                                data-id="{{$testimonial->id}}" {{$testimonial->status == "1" ? 'checked' : ''}}
                                            id="status-switch-{{$testimonial->id}}" >
                                            <label class="form-check-label" for="status-switch-{{$testimonial->id}}"></label>
                                        </div>
                                    </td>
                                    <td data-label="{{translate('Action')}}">
                                        <div class="hstack justify-content-center gap-3">

                                            <a href="javascript:void(0);" data-id="{{$testimonial->id}}" data-author='{{$testimonial->author}}' data-rating='{{$testimonial->rating}}' data-designation='{{$testimonial->designation}}'  data-quote='{{$testimonial->quote}}' title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top"  class=" fs-18 link-warning update-testimonial"><i class="ri-pencil-fill"></i></a>

                                            <a title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="javascript:void(0);" data-href="{{route('admin.frontend.testimonial.delete',$testimonial->id)}}" class="delete-item fs-18 link-danger">
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
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addTestimonial" tabindex="-1" aria-labelledby="addTestimonial" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{translate('Add Testimonial')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{route('admin.frontend.testimonial.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="p-2">
                            <div class="mb-3">
                                <label for="author" class="form-label">{{translate('Author')}} <span class="text-danger">*</span></label>

                                <input name="author" required id="author" placeholder="{{translate('Enter author name')}}" class="form-control" type="text" value="{{old('author')}}" >

                            </div>

                            <div class="mb-3">
                                <label for="designation" class="form-label">{{translate('Designation')}} <span class="text-danger">*</span></label>

                                <input required id="designation" placeholder="{{translate('Enter designation')}}" name="designation" class="form-control" type="text" value="{{old('designation')}}" >

                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">{{translate('Image')}} <span class="text-danger">*</span></label>

                                <input name="image" required id="image" class="form-control" type="file" >

                            </div>

                            <div class="mb-3">
                                <label for="quote"  class="form-label">{{translate('Quote')}} <span class="text-danger">*</span></label>

                                <textarea placeholder="{{translate('Enter quote')}}" class="form-control" name="quote" id="quote" cols="30" rows="5">{{old('quote')}}</textarea>

                            </div>

                            <div class="mb-3">
                                <label for="rating" class="form-label">{{translate('Rating')}} <span class="text-danger">* ({{translate('Max')}}:5)</span></label>

                                <input required placeholder="{{translate('Enter rating')}}" id="rating" class="form-control" type="number" name="rating" value="{{old('rating')}}">

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

    <div class="modal fade" id="updateTestimonial" tabindex="-1" aria-labelledby="updateTestimonial" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" >{{translate('Update Testimonial')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{route('admin.frontend.testimonial.update')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div>
                            <div id="testimonial-update" class="p-2">


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
        $('.update-testimonial').on('click', function(){
            var modal = $('#updateTestimonial');

            const id = $(this).attr('data-id');
            const author = $(this).attr('data-author');
            const rating = $(this).attr('data-rating');
            const designation = $(this).attr('data-designation');
            const quote = $(this).attr('data-quote');
            $('#testimonial-update').html('');
            $('#testimonial-update').append(`
                         <input type="hidden" value="${id}" name="id">

                           <div class="mb-3">
                                <label for="author" class="form-label">{{translate('Author')}} <span class="text-danger">*</span></label>

                                <input name="author" required id="author" placeholder="{{translate('Enter author name')}}" class="form-control" type="text" value="${author}" >

                            </div>

                            <div class="mb-3">
                                <label for="designation" class="form-label">{{translate('Designation')}} <span class="text-danger">*</span></label>

                                <input required id="designation" placeholder="{{translate('Enter designation')}}" name="designation" class="form-control" type="text" value="${designation}" >

                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">{{translate('Image')}}</label>

                                <input name="image"  id="image" class="form-control" type="file" >

                            </div>

                            <div class="mb-3">
                                <label for="quote" required class="form-label">{{translate('Quote')}} <span class="text-danger">*</span></label>

                                <textarea placeholder="{{translate('Enter quote')}}" class="form-control" name="quote" id="quote" cols="30" rows="5">${quote}</textarea>

                            </div>

                            <div class="mb-3">
                                <label for="rating" class="form-label">{{translate('Rating')}} <span class="text-danger">* ({{translate('Max')}}:5)</span></label>

                                <input required placeholder="{{translate('Enter rating')}}" id="rating" class="form-control" type="number" name="rating" value="${rating}" value="rating">

                            </div>
            `);
            modal.modal('show');
        });

    })(jQuery);
</script>
@endpush


