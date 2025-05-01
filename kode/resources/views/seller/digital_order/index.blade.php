@extends('seller.layouts.app')
@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{translate("Inhouse Order")}}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('seller.dashboard')}}">
                        {{translate('Dashboard')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate("Digital Orders")}}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">
                                    {{translate('Digital Order List')}}
                                </h5>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table table-hover table-nowrap align-middle mb-0" id="orderTable">
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">

                                    <th>
                                        {{translate(
                                            "Time"
                                        )}}
                                    </th>
                                    <th>
                                        {{translate('Order Number')}}
                                    </th>

                                    <th>{{translate('Customer')}}
                                    </th>
                                    <th>{{translate('Status')}}
                                    </th>
                                    <th>
                                        {{translate('Amount')}}
                                    </th>
                                    <th>
                                        {{translate('Details')}}
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                @forelse($orders as $order)
                                    <tr>

                                        <td data-label="{{translate('Time')}}">
                                            <span data-bs-toggle="tooltip" data-bs-placement="top" title="Time">
                                                <span class="fw-bold">{{diff_for_humans($order->created_at)}}</span><br>
                                                {{get_date_time($order->created_at)}}
                                            </span>
                                        </td>

                                        <td data-label="{{translate('Order Number')}}">

                                            <a title="Order Number" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('seller.digital.order.details',$order->id)}}">
                                                {{$order->order_id}}
                                            </a>

                                        </td>

                                        <td data-label="{{translate('Customer')}}">
                                            @if($order->customer || @$order->billing_information->email )
                                              {{$order->customer ? $order->customer->name :@$order->billing_information->email}}
                                            @else
                                                {{translate('N/A')}}
                                            @endif

                                        </td>

                                        <td data-label="{{translate('Payment Status')}}">
                                            @if($order->payment_status == App\Models\Order::UNPAID)
                                                <span class="badge badge-soft-danger my-1">{{translate('Unpaid')}}</span>
                                            @elseif($order->payment_status == App\Models\Order::PAID)
                                                <span class="badge badge-soft-success my-1">{{translate('Paid')}}</span>
                                            @endif
                                        </td>

                                        <td data-label="{{translate('Amount')}}">
                                            <span class="fw-bold">{{short_amount($order->amount)}}</span>
                                        </td>

                                        <td data-label="{{translate('Action')}}">
                                            <div class="hstack justify-content-center gap-3">
                                                <a title="Details" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('seller.digital.order.details',$order->id)}}" class=" fs-18 link-info">
                                                    <i class="ri-list-check"> </i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="border-bottom-0" colspan='100'>
                                            @include('admin.partials.not_found')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper d-flex justify-content-end mt-4 ">
                        {{$orders->appends(request()->all())->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
