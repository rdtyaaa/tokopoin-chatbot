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
                        <li class="breadcrumb-item"><a href="{{route('admin.index')}}">
                            {{translate('Staffs')}}
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
                                    {{translate('Create Staff')}}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{route('admin.store')}}" method="POST"  enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 pb-3">
                            <div class="col-xl-6 col-lg-6">
                                <div >
                                    <label for="name" class="form-label">
                                        {{translate('Name')}} <span  class="text-danger">*</span>
                                    </label>
                                    <input required type="text" name="name" value="{{old('name')}}"  class="form-control" placeholder="{{translate('Enter your name')}}" id="name">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div >
                                    <label for="username" class="form-label">  {{translate('User Name')}}
                                    <span  class="text-danger"  >*</span>
                                    </label>
                                    <input required type="text" name="user_name" value="{{old('user_name')}}" class="form-control" placeholder="{{translate('Enter your username')}}" id="username">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div>
                                    <label for="phone" class="form-label">
                                        {{translate('Phone Number')}}
                                    </label>
                                    <input type="text" name="phone" value="{{old('phone')}}" class="form-control" placeholder="0XXXXXXX" id="phone">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div>
                                    <label for="email" class="form-label">
                                        {{translate('Email')}} <span  class="text-danger"  >*</span>
                                    </label>
                                    <input required type="email" name="email" value="{{old('email')}}" class="form-control" placeholder="example@gamil.com" id="email">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div>
                                    <label for="password" class="form-label">
                                        {{translate('Password')}} <span  class="text-danger">* ({{translate('Minimum 5 Character Required!!')}})</span>
                                    </label>
                                    <input required type="password" name="password" value="{{old('password')}}" class="form-control" placeholder="*************" id="password">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div >
                                    <label for="confirmPassword" class="form-label">
                                        {{translate('Confirm Password')}} <span  class="text-danger"  >*</span>
                                    </label>
                                    <input required type="password" name="password_confirmation" value="{{old('confirm_password')}}" class="form-control" placeholder="*************" id="confirmPassword">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div >
                                    <label for="role" class="form-label">
                                        {{translate('Roles')}} <span  class="text-danger"  >*</span>
                                    </label>
                                    <select class="form-select" required name="role" id="role">
                                        <option value="">--{{translate('Select A Role')}}</option>

                                        @foreach($roles as $role)
                                            <option {{old('role') ==$role->id ?'seleted' :""  }}   value="{{$role->id}}">
                                                {{$role->name}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div>
                                    <label for="address" class="form-label">
                                        {{translate('Address')}}
                                    </label>
                                    <input type="text" name="address" value="{{old('address')}}" class="form-control" placeholder="{{translate('Enter your address')}}" id="address">
                                </div>
                            </div>

                            <div class="col-xl-6 col-lg-6">
                                <div>
                                    <label for="image" class="form-label">
                                        {{translate('Image')}}  <span class="text-danger">
                                            ({{file_path()['profile']['admin']['size'] }})
                                        </span>
                                    </label>
                                    <input data-size ="{{file_path()['profile']['admin']['size']}}" type="file" class="preview form-control w-100"
                                        name="image" id="image">
                                </div>
                                <div id="image-preview-section">

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








