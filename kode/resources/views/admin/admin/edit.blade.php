@extends('admin.layouts.app')
@section('main_content')<div class="page-content">
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
                        {{translate('Update')}}
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
                                {{translate('Update Staff')}}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

             <div class="card-body">
                <form action="{{route('admin.update')}}" method="POST"  enctype="multipart/form-data">
                    @csrf
                    <input name="id" value="{{$admin->id}}" type="hidden">

                    <div class="row g-3">
                        <div class="col-xl-6 col-lg-6">
                            <div >
                                <label for="name" class="form-label">
                                    {{translate('Name')}} <span  class="text-danger">*</span>
                                </label>
                                <input required type="text" name="name" value="{{$admin->name}}"  class="form-control" placeholder="{{translate('Enter your name')}}" id="name">
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            <div >
                                <label for="username" class="form-label">  {{translate('Username')}}
                                   <span  class="text-danger"  >*</span>
                                </label>
                                <input required type="text" name="user_name" value="{{$admin->user_name}}" class="form-control" placeholder="{{translate('Enter your username')}}" id="username">
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            <div >
                                <label for="phone" class="form-label">
                                    {{translate('Phone Number')}}
                                </label>
                                <input type="text" name="phone" value="{{$admin->phone}}" class="form-control" placeholder="0XXXXXX" id="phone">
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            <div >
                                <label for="email" class="form-label">
                                    {{translate('Email')}} <span  class="text-danger"  >*</span>
                                </label>
                                <input required type="email" name="email" value="{{$admin->email}}" class="form-control" placeholder="example@gamil.com" id="email">
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
                                        <option {{$admin->role_id == $role->id ?'selected' :""  }}   value="{{$role->id}}">
                                            {{$role->name}}
                                        </option>
                                      @endforeach
                                 </select>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            <div >
                                <label for="address" class="form-label">
                                    {{translate('Address')}}
                                </label>
                                <input type="text" name="address" value="{{$admin->address}}" class="form-control" placeholder="{{translate('Enter your address')}}" id="address">
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6">
                            <div>
                                <label for="image" class="form-label">
                                    {{translate('Image')}}  <span class="text-danger">
                                        ({{file_path()['profile']['admin']['size'] }})
                                    </span>
                                </label>
                                <input data-size ="{{file_path()['profile']['admin']['size']}}" id="image" type="file" class="preview form-control w-100"
                                    name="image">
                            </div>
                            <div id="image-preview-section">
                                <img alt='{{$admin->image}}' class="mt-2 rounded  d-block avatar-xl img-thumbnail"
                                    src="{{show_image(file_path()['profile']['admin']['path'].'/'.$admin->image,file_path()['profile']['admin']['size']) }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="text-start">
                                <button type="submit" class="btn btn-success">
                                    {{translate('Update')}}
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







