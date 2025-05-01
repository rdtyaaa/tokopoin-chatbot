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
                        {{translate("Subscribers")}}
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
                                {{translate('Subscriber List')}}
                            </h5>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <div class="d-flex flex-wrap align-items-start gap-2">
                            <a href="{{route('admin.subscriber.send.mail')}}" class="btn btn-success btn-sm add-btn waves ripple-light"><i class="ri-add-line align-bottom me-1"></i>
                                {{translate('Send Mail')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table table-hover table-nowrap align-middle mb-0" >
                        <thead class="text-muted table-light">
                            <tr class="text-uppercase">
                                <th>{{translate('Email')}}</th>

                                <th>
                                    {{translate('Joined At')}}
                                </th>
                                <th>
                                    {{translate('Action')}}
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($subscribers as $subscriber)
                                <tr>

                                    <td data-label="{{translate('Email')}}">
                                        <span class="fw-bold">
                                            {{($subscriber->email)}}
                                        </span>
                                    </td>

                                    <td data-label="{{translate('Joined At')}}">
                                        <span class="fw-bold">
                                            {{diff_for_humans($subscriber->created_at)}}
                                        </span>
                                        <br>
                                        {{get_date_time($subscriber->created_at)}}
                                    </td>

                                    <td data-label="{{translate('Action')}}">
                                        <div class="hstack justify-content-center gap-3">

                                            <a href="javascript:void(0);"  title="{{translate('Delete')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-href="{{route('admin.subscriber.delete',$subscriber->id)}}" class="delete-item fs-18 link-danger">
                                            <i class="ri-delete-bin-line"></i></a>
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
                    {{$subscribers->appends(request()->all())->links()}}
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.modal.delete_modal')
@endsection
