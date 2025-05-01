
@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">
        <div class="h-100">
            <div class="row">
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
                                        {{$product['physical']}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                       {{translate("Total Products")}}
                                    </p>

                                    <a href="{{route('admin.product.seller.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="ri-disc-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$product['digital']}}">{{$product['digital']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                       {{translate('Digital Products')}}
                                    </p>

                                    <a href="{{route('admin.digital.product.seller')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                         <i class=" ri-airplay-line text-info"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$orders['digital_order']}}">
                                            {{$orders['digital_order']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                       {{translate('Digital Orders')}}
                                    </p>

                                    <a href="{{route('admin.digital.order.product.seller')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="ri-money-dollar-circle-fill text-warning"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value">{{(short_amount($withdrawAmount))}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                       {{translate('Withdraws Amount')}}
                                    </p>

                                    <a href="{{route('admin.withdraw.log.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                         <i class="ri-flight-land-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$orders['placed']}}">{{$orders['placed']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Placed Orders')}}
                                    </p>

                                    <a href="{{route('admin.seller.order.placed')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                         <i class="ri-refresh-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$orders['processing']}}">{{$orders['processing']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Processing Orders')}}
                                    </p>

                                    <a href="{{route('admin.seller.order.processing')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                         <i class="ri-ship-2-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        <span
                                            class="counter-value" data-target="{{$orders['shipped']}}">{{$orders['shipped']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                          {{translate('Shipped Orders')}}
                                    </p>

                                    <a href="{{route('admin.seller.order.shipped')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                            class="counter-value" data-target="{{$orders['delivered']}}">{{$orders['delivered']}}
                                        </span>
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                          {{translate('Delivered Orders')}}
                                    </p>

                                    <a href="{{route('admin.seller.order.delivered')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                {{translate('Top selling store')}}
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive table-card mb-1">
                                <table
                                    class="table table-hover table-centered align-middle table-nowrap">
                                    <thead class="text-muted table-light">
                                        <tr>
                                            <th scope="col">
                                                {{translate('Name')}}
                                            </th>
                                            <th scope="col">
                                                {{translate('Username')}}
                                            </th>
                                            <th scope="col">{{translate('Phone')}}</th>
                                            <th scope="col">{{translate('Email')}}</th>
                                            <th scope="col">{{translate('Status')}}</th>
                                            <th scope="col">{{translate('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bestSellers as $bestSeller)
                                            <tr>
                                                <td data-label="{{translate('Name')}}">{{($bestSeller->name)}}</td>
                                                <td data-label="{{translate('Username')}}">{{($bestSeller->username ?? 'N/A')}}</td>
                                                <td data-label="{{translate('Phone')}}">{{($bestSeller->phone ? $bestSeller->phone  : 'N/A' )}}</td>
                                                <td data-label="{{translate('Email')}}">{{($bestSeller->email ? $bestSeller->email : 'N/A')}}</td>
                                                <td data-label="{{translate('Status')}}">
                                                    @if($bestSeller->status == 1)
                                                        <span class="badge badge-soft-success">{{translate('Active')}}</span>
                                                    @else
                                                        <span class="badge badge-soft-danger">{{translate('Banned')}}</span>
                                                    @endif
                                                </td>
                                                <td data-label="{{translate('Action')}}">
                                                    <a title="Details" data-bs-toggle="tooltip" data-bs-placement="top"  class="fs-18 link-success" href="{{route('admin.seller.info.details', $bestSeller->id)}}"><i class=" fs-3 las la-list"></i></a>
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
                        <div class="card-header border-bottom-dashed align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('Monthly Order Overview')}}
                            </h4>
                        </div>
                        <div class="card-body">
                            <div id="store-visits-source"
                                data-colors='["--ig-primary", "--ig-success", "--ig-secondary", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'
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
                            <div id="chart30"   data-colors='["--ig-primary", "--ig-success", "--ig-secondary", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]' class="apex-charts"
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
