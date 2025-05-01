@extends('admin.layouts.app')

@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ translate($title) }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                {{ translate('Home') }}
                            </a></li>

                        <li class="breadcrumb-item active">
                            {{ translate('Shipping Cities') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">

                                @if(request()->routeIs("admin.shipping.city.edit"))
                                    {{ translate('Update City') }}

                                @else
                                      {{ translate('Add City') }}
                                @endif
                            </h5>
                        </div>
                        @if(request()->routeIs("admin.shipping.city.edit"))
                            <div class="col-xl-2 col-sm-3 col-6">

                                <div class="d-flex flex-wrap align-items-start gap-2">
                                    <a href="{{route("admin.shipping.city.index")}}" class="btn btn-success w-100 waves ripple-light"><i class="ri-add-line align-bottom me-1"></i>
                                        {{ translate('Add City') }}
                                </a>

                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                @php

                    $route = route('admin.shipping.city.store');
                    if(request()->routeIs("admin.shipping.city.edit")){
                        $updateableCity = $cities->where("id",request()->route('id'))->first();
                        $route  = route('admin.shipping.city.update');
                    }

                @endphp


                <div class="card-body">
                        <form action="{{ $route  }}" method="post">

                            @csrf

                            @if(request()->routeIs("admin.shipping.city.edit"))
                                <input type="hidden" name="id" value="{{ $updateableCity?->id}}">
                            @endif

                            <div>
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ translate('Name') }} <span
                                            class="text-danger">*</span></label>
                                    <input value="{{  request()->routeIs("admin.shipping.city.edit") ? $updateableCity?->name :  old('name')  }}" type="text" class="form-control" id="name"
                                        name="name" placeholder="{{ translate('Enter name') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="state_id_add" class="form-label">{{ translate('State') }} <span
                                            class="text-danger">*</span></label>


                                    <select class="form-control select2" name="state_id" id="state_id_add" required>
                                        @foreach ($states as $state)
                                            <option {{ request()->routeIs("admin.shipping.city.edit") &&  @$updateableCity?->state_id && @$updateableCity?->state_id ==  $state->id ? "selected" :"" }}    value="{{ $state->id }}"> {{ $state->name }}</option>
                                        @endforeach
                                    </select>


                                </div>
                                @if(!request()->routeIs("admin.shipping.city.edit"))
                                    <div class="mb-3">
                                        <label for="status" class="form-label">{{ translate('Status') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" name="status" id="status" required>
                                            <option {{ old('status') == 0 ? 'seleted' : '' }} value="1">
                                                {{ translate('Visible') }}</option>
                                            <option {{ old('status') == 1 ? 'seleted' : '' }} value="0">
                                                {{ translate('Hidden') }}</option>
                                        </select>
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="shipping_fee" class="form-label">{{ translate('Shipping Fee') }} <span
                                            class="text-danger">*</span></label>
                                    <input value="{{  request()->routeIs("admin.shipping.city.edit") ? $updateableCity?->shipping_fee :  old('shipping_fee')  }}" type="number" class="form-control" id="shipping_fee"
                                        name="shipping_fee" placeholder="{{ translate('Enter name') }}" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success waves ripple-light">
                                @if(!request()->routeIs("admin.shipping.city.edit"))
                                   {{ translate('Add City') }}
                                @else
                                     {{ translate('Update City') }}
                                @endif
                            </button>

                        </form>
                </div>
            </div>


            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{ translate('City List') }}
                            </h5>
                        </div>

                    </div>
                </div>

                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form action="{{ route(Route::currentRouteName(), Route::current()->parameters()) }}" method="get">
                        <div class="row g-3">
                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" name="search" value="{{ request()->input('search') }}"
                                        class="form-control search" placeholder="{{ translate('Search city, state or country name') }}">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>


                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
                                            class="ri-equalizer-fill me-1 align-bottom"></i>
                                        {{ translate('Search') }}
                                    </button>
                                </div>
                            </div>

                            <div class="col-xl-2 col-sm-3 col-6">
                                <div>
                                    <a href="{{ route(Route::currentRouteName(), Route::current()->parameters()) }}"
                                        class="btn btn-danger w-100 waves ripple-light"> <i
                                            class="ri-refresh-line me-1 align-bottom"></i>
                                        {{ translate('Reset') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>
                                        {{ translate('Name') }}
                                    </th>
                                    <th>
                                        {{ translate('Shipping Fee') }}
                                    </th>
                                    <th>
                                        {{ translate('State') }}
                                    </th>
                                    <th>
                                        {{ translate('Country') }}
                                    </th>
                                    <th>
                                        {{ translate('Visible/Hidden') }}
                                    </th>

                                    <th>
                                        {{ translate('Action') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($cities as $city)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            {{ $city->name }}
                                        </td>
                                        <td>
                                            {{round(($city->shipping_fee))}} {{default_currency()->name}}
                                        </td>
                                        <td>
                                            {{ $city->state->name }}
                                        </td>
                                        <td>
                                            {{ $city->state->country->name }}
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="status-update form-check-input"
                                                    data-column="status"
                                                    data-route="{{ route('admin.shipping.city.status') }}" data-model="City"
                                                    data-status="{{ $city->status == '1' ? '0' : '1' }}"
                                                    data-id="{{ $city->id }}" {{ $city->status == '1' ? 'checked' : '' }}
                                                    id="status-switch-{{ $city->id }}">
                                                <label class="form-check-label"
                                                    for="status-switch-{{ $city->id }}"></label>

                                            </div>
                                        </td>
                                        <td>
                                            <div class="hstack justify-content-center gap-3">
                                                @if (permission_check('manage_cities'))
                                                    <a href="{{route('admin.shipping.city.edit', $city->id)}}" title="{{ translate('Update') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top" id="{{ $city->id }}"
                                                        class="edit-item fs-18 link-warning">
                                                        <i class="ri-pencil-fill"></i></a>

                                                    <a href="javascript:void(0);" title="{{ translate('Delete') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-href="{{ route('admin.shipping.city.destroy', $city->id) }}"
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

                    <div class="pagination-wrapper d-flex justify-content-end mt-4">
                        {{ $cities->links() }}
                    </div>
                 
                </div>
            </div>
        </div>
    </div>


    @include('admin.modal.delete_modal')
@endsection

@push('script-push')
    <script>
        (function($) {
            "use strict";

            $(".select2").select2({
                placeholder: "{{ translate('Select Country') }}",
            })


        })(jQuery);
    </script>
@endpush
