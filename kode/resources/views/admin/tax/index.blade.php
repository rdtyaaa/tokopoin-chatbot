@extends('admin.layouts.app')
@push('style-include')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/responsive.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/buttons.dataTables.min.css') }}">
@endpush
@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ $title }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                {{ translate('Home') }}

                            </a></li>
                        <li class="breadcrumb-item active">
                            {{ translate('Tax') }}
                        </li>
                    </ol>
                </div>
            </div>


            <div class="card">


                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{ translate('Tax List') }}
                            </h5>
                        </div>
                        @if (permission_check('manage_taxes'))
                            <div class="col-sm-auto">
                                <div class="d-flex flex-wrap align-items-start gap-2">
                                    <button type="button" class="btn btn-success add-btn w-100 waves ripple-light"
                                        data-bs-toggle="modal" id="create-btn" data-bs-target="#createTax"><i
                                            class="ri-add-line align-bottom me-1"></i>
                                        {{ translate('Add Tax') }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">

                    <table id="tax-table"
                        class="w-100 table table-bordered dt-responsive nowrap table-striped align-middle">
                        <thead class="text-muted table-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    {{ translate('Name') }}
                                </th>
                                <th scope="col">
                                    {{ translate('Status') }}
                                </th>
                                <th scope="col" class="text-center">
                                    {{ translate('Action') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($taxes as $tax)
                                <tr>
                                    <td class="fw-medium">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                {{ $tax->name }}
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="status-update form-check-input"
                                                data-column="status" data-route="{{ route('admin.tax.status.update') }}"
                                                data-model="Tax" data-status="{{ $tax->status == '1' ? '0' : '1' }}"
                                                data-id="{{ $tax->id }}" {{ $tax->status == '1' ? 'checked' : '' }}
                                                id="status-switch-{{ $tax->id }}">
                                            <label class="form-check-label"
                                                for="status-switch-{{ $tax->id }}"></label>

                                        </div>
                                    </td>

                                    <td>

                                        <div class="hstack justify-content-center gap-3">
                                            @if (permission_check('manage_taxes'))
                                                <a title="{{ translate('Update') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" class="link-warning fs-18  edit"
                                                    data-bs-toggle="modal" data-bs-target="#updateTax"
                                                    href="javascript:void(0)" id="{{ $tax->id }}"
                                                    name="{{ $tax->name }}" status="{{ $tax->status }}"><i
                                                        class="ri-pencil-fill"></i>
                                                </a>
                                                <a href="javascript:void(0);" title="{{ translate('Delete') }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-href="{{ route('admin.tax.delete', $tax->id) }}"
                                                    class="delete-item fs-18 link-danger">
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
            </div>
        </div>

    </div>
    <div class="modal fade" id="createTax" tabindex="-1" aria-labelledby="createTax" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ translate('Add New Tax') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.tax.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="p-2">
                            <div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ translate('Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input value="{{ old('name') }}" type="text" class="form-control"
                                        id="name" name="name" placeholder="{{ translate('Enter Name') }}"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="status" class="form-label">{{ translate('Status') }} <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" name="status" id="status" required>
                                        <option {{ old('status') == 1 ? 'selected' : '' }} value="1">
                                            {{ translate('Active') }}</option>
                                        <option {{ old('status') == 0 ? 'selected' : '' }} value="0">
                                            {{ translate('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-md btn-danger"
                            data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-md btn-primary">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateTax" tabindex="-1" aria-labelledby="updateTax" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ translate('Update Tax') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form action="{{ route('admin.tax.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="p-2">
                            <div class="mb-3">
                                <label for="update-name" class="form-label">{{ translate('Name') }} <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="update-name" name="name"
                                    placeholder="{{ translate('Enter name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="update-status" class="form-label">{{ translate('Status') }} <span
                                        class="text-danger">*</span></label>

                                <select class="form-select" name="status" id="update-status" required>
                                    <option value="1">{{ translate('Active') }}</option>
                                    <option value="0">{{ translate('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-md btn-danger border-0 text-light"
                            data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-md btn-primary">{{ translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @include('admin.modal.delete_modal')
@endsection

@push('script-push')
    <script src="{{ asset('assets/backend/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        (function($) {
            "use strict";
            $('.edit').on('click', function() {
                var modal = $('#updateTax');
                modal.find('input[name=id]').val($(this).attr('id'));
                modal.find('input[name=name]').val($(this).attr('name'));
                modal.find('select[name=status]').val($(this).attr('status'));
                modal.modal('show');
            });


            document.addEventListener("DOMContentLoaded", function() {
                new DataTable("#tax-table", {
                    fixedHeader: !0
                })
            })
        })(jQuery);
    </script>
@endpush
