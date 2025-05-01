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
                    <li class="breadcrumb-item"><a href="{{route('admin.role.index')}}">
                        {{translate('Roles')}}
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
                                {{translate('Update Role')}}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{route('admin.role.update')}}" method="post">

                <input type="hidden" name="id" value="{{$role->id}}">
                @csrf
                <div class="card-body">
                    <div>
                        <label for="name" class="form-label">
                            {{translate('Name')}}  <span  class="text-danger">*</span>
                        </label>
                            <input name="name" id="name" required class="form-control" value="{{$role->name}}"    type="text">
                    </div>
                    <div class="mt-3">
                        <h5>
                            {{translate('Permissions')}}
                        </h5>
                        @php
                           $permission_values = [];
                            foreach ((json_decode($role->permissions,true)) as $features) {
                                foreach ($features as $feature) {
                                    $permission_values = array_merge($permission_values, $feature);
                                }
                            }
                        @endphp
                        <div class="row g-3">
                            @foreach ($permissions as $permission)
                                <div class="col-xl-6">
                                    <div class="border rounded p-3">
                                        @foreach(($permission) as $key=>$modules)
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6 class="mb-0">
                                                {{ucfirst($key)}}
                                            </h6>
                                        </div>
                                            <div class="row g-3">
                                                @foreach($modules as $module)
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center justify-content-between gap-3 form-control p-2">
                                                            <label class="mb-0">
                                                                {{
                                                                    ucwords(str_replace("_" ,' ',$module))
                                                                }}
                                                            </label>
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" value="{{$module}}" name="permission[{{$key}}][{{$module}}] "

                                                                {{in_array($module,$permission_values)?'checked':""  }}

                                                                class="form-check-input"
                                                                id="{{$module}}" >
                                                                <label class="form-check-label" for="{{$module}}"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3">
                            <button type="submit"
                        class="btn btn-success waves ripple-light"
                        id="add-btn">
                        {{translate('Update')}} </button>
                    </div>

                </div>
           </form>
        </div>

	</div>
</div>

@endsection







