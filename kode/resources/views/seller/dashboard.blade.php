@extends('seller.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">
        <div class="h-100">

            <div class="d-flex align-items-lg-center flex-lg-row flex-column mb-3 pb-1">
                <div class="flex-grow-1">
                    <h4 class="fs-3 mb-0">
                        {{translate('Wellcome Back')}},
                        <span class="text-primary">
                            {{auth_user('seller')->name}}
                        </span>
                    </h4>
                </div>

                <div class="mt-3 mt-lg-0">
                    <a href="{{route('seller.product.create')}}"
                        class="btn btn-soft-success waves ripple-light">
                            <i class="ri-add-circle-line align-middle me-1"></i>
                            {{translate('Add Product')}}
                    </a>
                </div>
            </div>

            <div class="row">


                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="ri-money-euro-box-line text-info"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                      {{short_amount($seller->balance)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate("Total Balance")}}
                                    </p>

                                    <a href="{{route('seller.transaction.history')}}" class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="ri-wallet-2-fill text-danger"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                      {{short_amount($data['withdraw_amount'])}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate("Total Withdraw")}}
                                    </p>

                                    <a href="{{route('seller.transaction.history')}}" class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="bx bxl-product-hunt text-success"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                       {{$data['total_subscription']}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate("Total Subscription")}}
                                    </p>

                                    <a href="{{route('seller.plan.index')}}" class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                        <i class="ri-function-add-line"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="bx bxl-product-hunt text-success"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                       {{$data['physical']}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate("Total Product")}}
                                    </p>

                                    <a href="{{route('seller.product.index')}}" class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="ri-terminal-window-fill text-success"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                       <span
                                            class="counter-value" data-target="{{$data['digital']}}">{{$data['digital']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate('Digital Products')}}
                                    </p>

                                    <a href="{{route('seller.digital.product.index')}}"  class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="ri-device-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                       <span
                                            class="counter-value" data-target="{{$order['digital_order']}}">{{$order['digital_order']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Digital Orders')}}
                                    </p>

                                    <a href="{{route('seller.digital.order.index')}}"   class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="ri-swap-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                      <span
                                            class="counter-value" data-target="{{$order['order']}}">{{$order['order']}}
                                        </span>
                                    </h4>

                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Total Orders')}}
                                    </p>

                                    <a href="{{route('seller.order.index')}}" class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class='bx bxs-plane-land text-primary' ></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$order['placed']}}">{{$order['placed']}}
                                        </span>
                                    </h4>

                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate('Placed Orders')}}
                                    </p>

                                    <a href="{{route('seller.order.placed')}}" class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                         <i class="ri-check-double-line text-success"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$order['delivered']}}">{{$order['delivered']}}
                                        </span>
                                    </h4>

                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Delivered Orders')}}
                                    </p>

                                    <a href="{{route('seller.order.delivered')}}"  class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">

                                         <i class="ri-ship-2-line text-info"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$order['shipped']}}">{{$order['shipped']}}
                                        </span>
                                    </h4>

                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Shipped Orders')}}
                                    </p>

                                    <a href="{{route('seller.order.shipped')}}"  class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">

                                        <i class="ri-close-circle-fill text-danger"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$order['canceled']}}">{{$order['canceled']}}
                                        </span>
                                    </h4>

                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Canceled Orders')}}
                                    </p>

                                    <a href="{{route('seller.order.cancel')}}"  class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">

                                        <i class="ri-send-plane-fill text-info"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$data['total_ticket']}}">{{$data['total_ticket']}}
                                        </span>
                                    </h4>

                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Total Tickets')}}
                                    </p>

                                    <a href="{{route('seller.ticket.index')}}"  class="d-flex align-items-center justify-content-end gap-1">
                                        {{translate('View All')}}
                                         <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('All transaction log')}}
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table
                                    class="table table-hover table-centered align-middle table-nowrap">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th>{{translate('Date')}}</th>
                                            <th>{{translate('Transaction ID')}}</th>
                                            <th>{{translate('Amount')}}</th>
                                            <th>{{translate('Post Balance')}}</th>
                                            <th>{{translate('Detail')}}</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($transactions as $transaction)
                                            <tr>
                                                <td data-label="{{translate('Time')}}">
                                                    <span>{{diff_for_humans($transaction->created_at)}}</span><br>
                                                    {{get_date_time($transaction->created_at)}}
                                                </td>

                                                <td data-label="{{translate('Transaction ID')}}">
                                                    {{($transaction->transaction_number)}}
                                                </td>

                                                <td data-label="{{translate('Amount')}}">
                                                    <span class="@if($transaction->transaction_type == '+')text-success @else text-danger @endif fw-bold">{{ $transaction->transaction_type }} {{(short_amount($transaction->amount))}}
                                                    </span>
                                                </td>

                                                <td data-label="{{translate('Post Balance')}}">
                                                    <span class="fw-bold">{{(short_amount($transaction->post_balance))}} </span>
                                                </td>

                                                <td data-label="{{translate('Details')}}">
                                                    {{($transaction->details) }}
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

            <div class="row">
                <div class="col-xl-4">
                    <div class="card card-height-100">
                        <div class="card-header border-bottom-dashed">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('Monthly Order Overview')}}
                            </h4>
                        </div>
                        <div class="card-body">
                            <div id="store-visits-source"
                                data-colors='["--ig-primary", "--ig-success", "--ig-warning", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'
                                class="apex-charts"></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h4 class="card-title mb-0">
                                {{translate('All order overview')}}
                            </h4>
                        </div>

                        <div class="card-body">
                            <div id="chart30" data-colors='["--ig-primary", "--ig-success", "--ig-warning", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'
                                class="apex-charts"
                               ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-include')
  <script src="{{asset('assets/global/js/apexcharts.js')}}"></script>
@endpush

@push('script-push')
<script type="text/javascript">
"use strict";
var chartDonutBasicColors = getChartColorsArray("store-visits-source");
if (chartDonutBasicColors) {
    var options = {
        series: @json($monthlyOrderReport),
        labels: ["All", "Pending", "Placed", "Confirmed", "Processing", "Shipped", "Delivered"],

        chart: {
            height: 380,
            width:"100%",
            type: "donut",
        },
        legend: {
            position: "bottom",
        },
        stroke: {
            show: false
        },
        dataLabels: {
            dropShadow: {
                enabled: false,
            },
        },
        colors: chartDonutBasicColors,
    };

    var chart = new ApexCharts(
        document.querySelector("#store-visits-source"),
        options
    );
    chart.render();
}

var options = {
    series: [{
        name: 'Total Order',
        type: 'column',
        data: [{{implode(",", $salesReport['order_count']->toArray())}}]
    }],
    chart: {
        height: 400,
        type: 'line',
        stacked: false,
    },
    stroke: {
        width: [0, 2, 5],
        colors: ['#ffb800', '#cecece'],
        curve: 'smooth'
    },
    plotOptions: {
        bar: {
            columnWidth: '50%'
        }
    },
    fill: {
        opacity: [0.85, 0.25, 1],
        colors: ['#8b0dfd', '#c9b6ff'],
        gradient: {
            inverseColors: false,
            shade: 'light',
            type: "vertical",
            opacityFrom: 0.85,
            opacityTo: 1,
            stops: [0, 100, 100, 100]
        }
    },
    labels: @json($salesReport['month']->toArray()),
    markers: {
        size: 0
    },
    xaxis: {
        type: 'month'
    },
    yaxis: {
        title: {
            text: 'Orders report',
        },
        min: 0
    },
    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function(y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(0);
                }
                return y;

            }
        }
    }
};
var chart = new ApexCharts(document.querySelector("#chart30"), options);
chart.render();

function getChartColorsArray(e) {
    if (null !== document.getElementById(e)) {
        e = document.getElementById(e).getAttribute("data-colors");
        if (e)
        return (e = JSON.parse(e)).map(function (e) {
            var t = e.replace(" ", "");
            return -1 === t.indexOf(",")
                ? getComputedStyle(document.documentElement).getPropertyValue(t) || t
                : 2 == (e = e.split(",")).length
                    ? "rgba(" +
                    getComputedStyle(document.documentElement).getPropertyValue(e[0]) +
                    "," +
                    e[1] +
                    ")"
                    : t;
        });
    }
}

</script>
@endpush
