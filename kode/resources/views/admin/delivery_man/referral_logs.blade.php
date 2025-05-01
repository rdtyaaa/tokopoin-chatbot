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
                    <li class="breadcrumb-item active">
                        {{translate("Referral Logs")}}
                    </li>
                </ol>
            </div>

        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm">
                        <h5 class="card-title mb-0 flex-grow-1">
                            {{translate('Referral List')}}
                        </h5>
                    </div>

                    
                </div>
            </div>

            <div class="card-body border border-dashed border-end-0 border-start-0">
                <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="get">
                    <div class="row g-3">
                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                 <input class="form-control" value="{{request()->input('search')}}" type="search" name="search" placeholder="{{ translate('Search by name , email') }}">
                            </div>
                        </div>

                        <div class="col-xl-4 col-sm-6">
                            <div class="search-box">
                                <input type="text" id="datePicker" name="date" value="{{request()->input('date')}}" class="form-control search"
                                    placeholder="{{translate('Search by date')}}">
                                <i class="ri-time-line search-icon"></i>

                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <button type="submit" class="btn btn-primary w-100 waves ripple-light"> <i
                                        class="ri-equalizer-fill me-1 align-bottom"></i>
                                    {{translate('Search')}}
                                </button>
                            </div>
                        </div>

                        <div class="col-xl-2 col-sm-3 col-6">
                            <div>
                                <a href="{{route(Route::currentRouteName(),Route::current()->parameters())}}" class="btn btn-danger w-100 waves ripple-light"
                                    > <i
                                        class="ri-refresh-line me-1 align-bottom"></i>
                                    {{translate('Reset')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body">
              

                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                               
                                <th scope="col" class="text-start">
                                    {{translate("Deliveryman")}}
                                </th>
                                
               
                                <th scope="col" class="text-center">
                                    {{translate("Referred by")}}
                                </th>
               
                                <th >
                                    {{translate("Register at")}}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @forelse($refferelLogs  as $refferelLog)
                               <tr class="fs-14 tr-item">

                                
                                <td >

                
                                    <a href="{{route('admin.delivery-man.overview',$refferelLog->id)}}">
                                        {{$refferelLog->username}}
                                    </a>
                                
                                </td>
                                <td class="text-center">

                                    @if($refferelLog->refferedBy)
                                        <a href="{{route('admin.delivery-man.overview',$refferelLog->refferedBy->id)}}">
                                            {{$refferelLog->refferedBy->username}}
                                        </a>
                                    @else
                                       -
                                    @endif

                                </td>

                               


                                <td class="text-center">
                                    {{$refferelLog->created_at 
                                       ? get_date_time($refferelLog->created_at) 
                                       : 'N/A'  }}
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

                <div class="pagination-wrapper d-flex justify-content-end mt-4 ">
                    {{$refferelLogs ->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>


@endsection



@push('script-push')
<script>
	(function($){
       	"use strict";

        $("#deliveryman_id").select2({
        })

	

	})(jQuery);
</script>
@endpush
