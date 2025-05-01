@extends('admin.layouts.app')
@push('style-include')
<link href="{{asset('assets/backend/css/summnernote.css')}}" rel="stylesheet" type="text/css" />
@endpush
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
                        <li class="breadcrumb-item"><a href="{{route('admin.blog.index')}}">
                            {{translate('Blogs')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate('Create')}}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">
                                    {{translate('Create Blog')}}
                                </h5>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <form action="{{route('admin.blog.store')}}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div >
                                    <label for="category_id" class="form-label">
                                        {{translate('Category')}} <span  class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value=""> {{translate('Select One')}}</option>
                                        @foreach($categories as $category)
                                            <option {{old('category_id') == $category->id ? 'selected' : ' '}} value="{{$category->id}}">{{
                                                get_translation($category->name)
                                            }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div >
                                    <label for="image" class="form-label">  {{translate('image')}}
                                    <span  class="text-danger">*( {{file_path()['blog']['size']}} {{translate('pixels')}}). </span>
                                    </label>
                                    <input type="file" name="image" id="image" class="form-control" required>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div >
                                    <label for="post" class="form-label">  {{translate('Post Title')}}
                                       <span  class="text-danger">*   </span>
                                    </label>
                                    <input type="text" value="{{old('post')}}" name="post" id="post" placeholder="{{translate('Enter post title')}}" class="form-control" required>

                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="status" class="form-label">{{translate('Status')}}    <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="status" required>
                                    <option value="1">{{translate('Active')}}</option>
                                    <option value="0">{{translate('Inactive')}}</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <div class="text-editor-area">
                                    <label for="mail-composer" class="form-label">  {{translate('Body')}}
                                    <span  class="text-danger">*   </span>
                                    </label>
                                    <textarea id="mail-composer" class="form-control text-editor" name="body" rows="5" placeholder="{{translate('Enter Description')}}" required>
                                        {{old('body')}}
                                    </textarea>

                                    @if( $openAi->status == 1)
                                        <button type="button" class="ai-generator-btn mt-3 ai-modal-btn" >
                                            <span class="ai-icon btn-success waves ripple-light">
                                                    <span class="spinner-border d-none" aria-hidden="true"></span>

                                                    <i class="ri-robot-line"></i>
                                            </span>

                                            <span class="ai-text">
                                                {{translate('Generate With AI')}}
                                            </span>
                                        </button>
                                    @endif

                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-start">
                                    <button type="submit" class="btn btn-success">
                                        {{translate('Add')}}
                                    </button>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script-include')
	<script src="{{asset('assets/backend/js/summnernote.js')}}"></script>
	<script src="{{asset('assets/backend/js/editor.init.js')}}"></script>
@endpush









