@extends('admin.layouts.app')
@push('style-include')
    <link rel="stylesheet" href="{{asset('assets/backend/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/backend/css/buttons.dataTables.min.css') }}">
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
                    <li class="breadcrumb-item active">
                        {{translate('Staffs')}}
                    </li>
                </ol>
            </div>
        </div>

		<div class="card">
			<div class="card-header border-bottom-dashed">
				<div class="row g-4 align-items-center">
					<div class="col-sm">
                        <h5 class="card-title mb-0">
                            {{translate('Staff List')}}
                        </h5>
					</div>
                    @if(permission_check('create_admin'))
                        <div class="col-sm-auto">
                            <div class="d-flex flex-wrap align-items-start gap-2">
                                <a href="{{route('admin.create')}}" class="btn btn-success btn-sm add-btn waves ripple-light">
                                    <i class="ri-add-line align-bottom me-1"></i>
                                        {{translate('Add New Staff')}}
                                </a>
                            </div>
                        </div>
                    @endif
				</div>
			</div>

			<div class="card-body">
			    <table id="adminTable" class="w-100 table table-bordered dt-responsive nowrap table-striped align-middle" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>
                                {{translate('Name')}}
                            </th>
                            <th>
                                {{translate('Username')}}
                            </th>
                            <th>
                                {{translate('Email')}}
                            </th>
                            <th>
                                {{translate('Status')}}
                            </th>
                            <th>
                                {{translate('Created By')}}
                            </th>
                            <th>
                                {{translate('Optinos')}}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                            <tr>
                                <td>
                                    {{$loop->iteration}}
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="{{show_image(file_path()['profile']['admin']['path'].'/'.$admin->image ,file_path()['profile']['admin']['size']) }}" alt="{{$admin->image}}" class="avatar-sm rounded img-thumbnail me-2">
                                    <div>
                                        <h5 class="fs-13 mb-0">
                                            {{ $admin->name}}
                                        </h5>
                                        <p class="fs-12 mb-0 text-muted">
                                            {{ $admin->role?$admin->role->name :'N/A' }}
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    {{$admin->user_name ?? translate('N/A')}}
                                </td>
                                <td>
                                    {{$admin->email}}
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="status-update form-check-input"
                                            data-column="status"
                                            data-route="{{ route('admin.status.update') }}"
                                            data-model="Admin"
                                            data-status='{{ $admin->status == "1" ? "0":"1"}}'
                                            data-id="{{$admin->id}}" {{$admin->status == "1" ? 'checked' : ''}}
                                        id="status-switch-{{$admin->id}}" >
                                        <label class="form-check-label" for="status-switch-{{$admin->id}}"></label>
                                    </div>
                                </td>
                                <td>
                                   {{$admin->createdBy?$admin->createdBy->name :'N/A'}}
                                </td>
                                <td>
                                    <div class="hstack gap-3">
                                        @if(permission_check('update_admin'))
                                           <a href="{{route('admin.edit',$admin->id)}}" title="{{translate('Update')}}" data-bs-toggle="tooltip" data-bs-placement="top" class=" fs-18 link-warning"><i class="ri-pencil-fill"></i></a>
                                        @endif
                                        @if(permission_check('delete_admin'))
                                            <a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.destroy',$admin->id)}}" class="delete-item fs-18 link-danger">
                                            <i class="ri-delete-bin-line"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
			</div>
		</div>
	</div>
</div>

@include('admin.modal.delete_modal')

@endsection

@push('script-include')
    <script src="{{asset('assets/backend/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{asset('assets/backend/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{asset('assets/backend/js/dataTables.responsive.min.js') }}"></script>
@endpush

@push('script-push')
<script>
	(function($){
       	"use strict";
        document.addEventListener("DOMContentLoaded", function () {
            new DataTable("#adminTable", {
                fixedHeader: !0
            })
        })

	})(jQuery);
</script>
@endpush





