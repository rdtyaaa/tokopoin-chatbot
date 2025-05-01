@extends('seller.layouts.app')
@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ translate('KYC Logs') }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">
                                {{ translate('Dashboard') }}
                            </a></li>
                        <li class="breadcrumb-item active">
                            {{ translate('KYC Logs') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card" id="orderList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">
                                {{ translate('Log List') }}
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="card-body  border border-dashed border-end-0 border-start-0">
                    <form action="{{ route(Route::currentRouteName(), Route::current()->parameters()) }}" method="get">
                        <div class="row g-3">
                           
                            <div class="col-xl-4 col-sm-6">
                                <div class="search-box">
                                    <input type="text" id="datePicker" name="date"
                                        value="{{ request()->input('date') }}" class="form-control search"
                                        placeholder="{{ translate('Search by date') }}">
                                    <i class="ri-time-line search-icon"></i>

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
                        <table class="table table-hover table-nowrap align-middle mb-0">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th>
                                        {{ translate('Date') }}
                                    </th>

                                    <th>
                                        {{ translate('Status') }}
                                    </th>
                                    <th>
                                        {{ translate('Action') }}
                                    </th>
                                    
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($reports as $report)
                                    <tr>

                                
                                        <td data-label="{{ translate('Time') }}">
                                            <span class="fw-bold">{{ diff_for_humans($report->created_at) }}</span><br>
                                            {{ get_date_time($report->created_at) }}
                                        </td>


                                        <td data-label="{{ translate('KYC Status') }}">
                                            @if ($report->status == App\Enums\KYCStatus::APPROVED->value)
                                                <span class="badge badge badge-soft-success">{{ translate('Approved') }}</span>
                                            @elseif($report->status == App\Enums\KYCStatus::REQUESTED->value)
                                                <span class="badge badge-soft-warning">{{ translate('Requested') }}</span>
                                            @elseif($report->status == App\Enums\KYCStatus::HOLD->value)
                                                <span
                                                    class="badge badge-soft-info">{{ translate('Hold') }}</span>
                                            @elseif($report->status == App\Enums\KYCStatus::REJECTED->value)
                                                <span class="badge badge-soft-danger">{{ translate('Rejected') }}</span> 
                                            @endif
                                        </td>

                                        <td data-label="{{ translate('Action') }}">

                                            <div class="hstack justify-content-center gap-3">
            
                                                <a title="Details" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    href="{{route('seller.kyc.log.show',$report->id)}}"
                                                    class="fs-18 link-info ms-1"><i class="ri-list-check"></i>
                                                </a>
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



@endsection

