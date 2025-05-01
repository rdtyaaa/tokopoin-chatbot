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
                        <li class="breadcrumb-item"><a href="{{route('admin.item.category.index')}}">
                            {{translate('Categories')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate('Create')}}
                        </li>
                    </ol>
                </div>
            </div>

            <form action="{{route('admin.item.category.store')}}" method="post"  enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <div>
                                    <h5 class="card-title mb-0">
                                        {{translate('Create category')}}
                                    </h5>
                                </div>
                            </div>

                        </div>
                    </div>

                    @php
                      $codes = translateable_locale($languages);
                      $getLanguages = (getLanguagesArr($languages));

                    @endphp


                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-xl-12">
                                <div>
                                    <div class="step-arrow-nav mb-4">
                                        <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                                            @foreach($codes as $code)
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link
                                                    {{$loop->index == 0 ? 'active' :''}}
                                                    " id="lang-tab-{{$code}}" data-bs-toggle="pill" data-bs-target="#lang-tab-content-{{$code}}" type="button" role="tab" aria-controls="lang-tab-content-{{$code}}" aria-selected="true">
                                                        {{Arr::get( $getLanguages,$code)}}({{ucfirst($code)}})
                                                    </button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @php
                                        $serial_id = 1;

                                        if(count($categories) > 0){
                                            $serial_id =   $serial_id + $categories->first()->serial  ;
                                        }

                                    @endphp

                                    <div class="tab-content">
                                        @foreach($codes as $code)
                                            <div class="tab-pane fade  {{$loop->index == 0 ? 'show active' :''}}   " id="lang-tab-content-{{$code}}" role="tabpanel" aria-labelledby="lang-tab-{{$code}}">
                                                <div>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label" for="{{$code}}-input">
                                                                    {{translate('Name')}} ({{$code}})

                                                                    @if(session()->get("locale") == strtolower($code))
                                                                    <span class="text-danger">*</span>
                                                                    @endif
                                                                </label>
                                                                @php
                                                                    $lang_code =  strtolower($code)
                                                                @endphp

                                                                <input id="{{$code}}-input" type="text" name="name[{{strtolower($code)}}]" class="form-control"  placeholder='{{translate("Enter name")}}'
                                                                    value='{{old("name.$lang_code")}}'
                                                                {{session()->get("locale") == strtolower($code) ? "required" :""}}>

                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-12">
                                <div class="row g-3">

                                    <div class="col-lg-6">
                                        <div>
                                            <label for="slug" class="form-label">{{translate('Slug')}}     <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="slug" value="{{old('slug')}}" name="slug" placeholder="{{translate('Enter slug')}}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="serial" class="form-label">{{translate('Serial Number')}}     <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="serial" value="{{old('serial') ?old('serial') :$serial_id }}" name="serial" placeholder="{{translate('Enter serial number')}}" required>
                                        </div>
                                    </div>

                                    

                                    <div class="col-lg-6" id="parent-cat">
                                        <div class="mb-2">
                                            <label for="parent_id" class="form-label">{{translate('Parent Category')}} </label>
                                            <select class="form-select" name="parent_id" id="parent_id" >
                                                <option value="">{{translate('Select One')}}</option>
                                                @foreach($categories as $category)
                                                    <option  {{ old('status') == $category->id? 'selected':'' }}   value="{{$category->id}}">{{(get_translation($category->name))}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="banner" class="form-label">{{translate('Feature Image')}} 	 <span class="text-danger" >*</span></label>

                                            <input type="file" class="form-control" id="banner" name="banner" required>
                                            <div class="text-danger mt-2">{{translate('Supported File : jpg,png,jpeg and size')}} {{file_path()['category']['size']}} {{translate('pixels')}}</div>
                                        </div>
                                    </div>



                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="image_icon" class="form-label">{{translate('Icon Image')}} 	 <span class="text-danger" >*</span></label>

                                            <input type="file" class="form-control" id="image_icon" name="image_icon" required>
                                            <div class="text-danger mt-2">{{translate('Supported File : jpg,png,jpeg and size')}} {{file_path()['category']['size']}} {{translate('pixels')}}</div>
                                        </div>
                                    </div>


                                    <div class="col-lg-6">
                                        <div class="mb-2">
                                            <label for="meta_title" class="form-label">{{translate('Meta Title')}} </label>
                                            <input value="{{old('meta_title')}}"   type="text" name="meta_title" id="meta_title" class="form-control" placeholder="{{translate('Enter meta title')}}" >
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="mb-2">
                                            <label for="meta_description" class="form-label">{{translate('Meta Description')}} </label>
                                            <textarea rows="3" name="meta_description" id="meta_description" class="form-control" placeholder="{{translate('Enter meta description')}}" >{{old('meta_description')}}</textarea>
                                        </div>
                                    </div>


                                    <div class="col-lg-12">
                                        <div class="mb-2">
                                            <label for="status" class="form-label">{{translate('Status')}}    <span class="text-danger">*</span></label>
                                            <select class="form-select" name="status" id="status" required>
                                                <option value="1">{{translate('Active')}}</option>
                                                <option value="0">{{translate('Inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-start mt-4">
                                    <button type="submit" class="btn btn-success">
                                        {{translate('Add')}}
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


@endsection







