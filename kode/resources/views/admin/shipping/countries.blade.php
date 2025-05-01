@extends('admin.layouts.app')
@push('style-include')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/css/responsive.bootstrap.min.css') }}">
@endpush
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
                            {{ translate('Shipping Countries') }}
                        </li>
                    </ol>
                </div>
            </div>


            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{ translate('Country List') }}
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
                                        class="form-control search"
                                        placeholder="{{ translate('Search country name , code') }}">
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
                                        {{ translate('Code') }}
                                    </th>
                                    <th class="text-center"> 
                                        {{ translate('Visible/Hidden') }}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($countries as $country)
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            {{ $country->name }}
                                        </td>
                                        <td>
                                            {{ $country->code }}
                                        </td>
                                        <td>
                                            <div class="form-check form-switch text-center justify-content-center">
                                                <input type="checkbox" class="status-update form-check-input"
                                                    data-column="status"
                                                    data-route="{{ route('admin.shipping.country.status') }}"
                                                    data-model="Country"
                                                    data-status="{{ $country->status == '1' ? '0' : '1' }}"
                                                    data-id="{{ $country->id }}"
                                                    {{ $country->status == '1' ? 'checked' : '' }}
                                                    id="status-switch-{{ $country->id }}">
                                                <label class="form-check-label"
                                                    for="status-switch-{{ $country->id }}"></label>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <div class="pagination-wrapper d-flex justify-content-end mt-4">
                            {{ $countries->links() }}
                        </div>
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-include')
    <script src="{{ asset('assets/backend/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/backend/js/dataTables.bootstrap5.min.js') }}"></script>
@endpush
