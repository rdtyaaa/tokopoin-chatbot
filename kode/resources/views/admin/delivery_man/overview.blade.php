@extends('admin.layouts.app')
@push('style-push')
    <style>
        .profile-section-image {
            width: 120px;
            height: 120px;
        }

        .img-thumbnail {
            padding: 0.25rem;
            background-color: var(--ig-body-bg);
            border: var(--ig-border-width) solid var(--ig-border-color);
            border-radius: var(--ig-border-radius);
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.delivery-man.list') }}">
                                {{ translate('Delivery man list') }}
                            </a></li>
                        <li class="breadcrumb-item active">
                            {{ translate('Delivery man Details') }}
                        </li>
                    </ol>
                </div>

            </div>

            <div class="row">
                <div class="col-xxl-3 col-xl-4 col-lg-5">
                    <div class="card">
                        <div class="card-header border-bottom-dashed ">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    {{ translate('Delivery man Details') }}
                                </h5>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="text-center">
                                <div class="profile-section-image mx-auto ">
                                    <img src="{{ show_image(file_path()['profile']['delivery_man']['path'] . '/' . $deliveryman->image, file_path()['profile']['delivery_man']['size']) }}"
                                        alt="{{ $deliveryman->image }}" class="w-100 rounded-circle img-thumbnail">
                                </div>
                                <div class="mt-3">
                                    <h6 class="mb-0">{{ $deliveryman->name }}</h6>
                                    <p>{{ translate('Joining Date') }}
                                        {{ get_date_time($deliveryman->created_at, 'd M, Y h:i A') }}</p>
                                </div>
                            </div>

                            <div class="p-3 bg-body rounded">
                                <h6 class="mb-3 fw-bold">{{ translate('deliveryman information') }}</h6>

                                <ul class="list-group">
                                    <li
                                        class="d-flex justify-content-between align-items-center flex-wrap gap-2 list-group-item">
                                        <span class="fw-semibold">
                                            {{ translate('Username') }}
                                        </span>
                                        <span class="font-weight-bold">{{ $deliveryman->username }}</span>
                                    </li>

                                    <li
                                        class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                        <span class="fw-semibold">
                                            {{ translate('Phone') }}
                                        </span>
                                        <span class="font-weight-bold">{{ $deliveryman->phone }}</span>
                                    </li>

                                    <li
                                        class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                        <span class="fw-semibold">
                                            {{ translate('Status') }}
                                        </span>

                                        @if ($deliveryman->status == 1)
                                            <span class="badge badge-pill bg-success">{{ translate('Active') }}</span>
                                        @else
                                            <span class="badge badge-pill bg-danger">{{ translate('Inactive') }}</span>
                                        @endif
                                    </li>

                                    <li
                                        class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                        <span class="fw-semibold">
                                            {{ translate('Number Of Orders') }}
                                        </span>
                                        <span class="font-weight-bold">{{ $deliveryman->orders_count }}</span>
                                    </li>


                                    <li
                                        class="d-flex justify-content-between align-items-center flex-wrap gap-2  list-group-item">
                                        <span class="fw-semibold">
                                            {{ translate('deliveryman Balance') }}
                                        </span>

                                        <span class="font-weight-bold">{{ short_amount($deliveryman->balance) }}</span>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-xxl-9 col-xl-8 col-lg-7">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    {{ translate('Other Information') }}
                                </h5>
                            </div>
                        </div>

                        @if(!request()->routeIs('admin.delivery-man.earning'))
                            <div class="card-body">
                                <div>
                                    <h6 class="fw-bold mb-3">{{ translate('Balance Information') }}</h6>
                                    <div class="row mb-3">
                                        <div class="col-xxl-6 col-xl-8">
                                            <div class="card card-animate bg-soft-green h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                                        <div class="flex-shrink-0">
                                                            <span class="overview-icon">
                                                                <i class="las la-money-bill-wave"></i>
                                                            </span>
                                                        </div>

                                                        <div class="text-end">
                                                            <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                                <span>{{ short_amount($deliveryman->order_balance) }}
                                                                </span>
                                                            </h4>


                                                            <p class="text-uppercase fw-medium text-muted mb-3">
                                                                {{ translate('Order Balance') }}
                                                            </p>
                                                        </div>

                                                    </div>
                                                    <div class="text-center mt-4">
                                                        <button class="btn btn-danger waves ripple-light" data-bs-toggle="modal"
                                                            data-bs-target="#cashCollect">
                                                            <span>{{ translate('Cash Collect') }}</span>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-6 col-xl-8">
                                            <div class="card card-animate bg-soft-gray h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                                        <div class="flex-shrink-0">
                                                            <span class="overview-icon">
                                                                <i class="las la-shopping-bag"></i>
                                                            </span>
                                                        </div>

                                                        <div class="text-end">
                                                            <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                                <span>
                                                                    {{ $deliveryman->orders_count }}
                                                                </span>
                                                            </h4>


                                                            <p class="text-uppercase fw-medium text-muted mb-3">
                                                                {{ translate('Total Order') }}
                                                            </p>
                                                        </div>

                                                    </div>
                                                    <div class="text-center mt-4">
                                                        <div class="text-center mt-4">
                                                            <a href="{{route('admin.delivery-man.order.list', $deliveryman->id)}}" class="btn btn-primary waves ripple-light">
                                                                {{ translate('View all') }}

                                                            </a>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div>
                                    <h6 class="fw-bold mb-3">{{ translate('Withdraw Information') }}</h6>
                                    <div class="row">
                                        <div class="col-xxl-4 col-xl-6">
                                            <div class="card card-animate bg-soft-green">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                                        <div class="flex-shrink-0">
                                                            <span class="overview-icon">
                                                                <i class="las la-check-double"></i>
                                                            </span>
                                                        </div>

                                                        <div class="text-end">
                                                            <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                                <span>{{ short_amount($overview['withdraw']['total_success_withdraw']) }}
                                                                </span>
                                                            </h4>


                                                            <p class="text-uppercase fw-medium text-muted mb-3">
                                                                {{ translate('Total Success Withdraw') }}
                                                            </p>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-xl-6">
                                            <div class="card card-animate bg-soft-gray">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                                        <div class="flex-shrink-0">
                                                            <span class="overview-icon">
                                                                <i class="las la-hourglass"></i>
                                                            </span>
                                                        </div>

                                                        <div class="text-end">
                                                            <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                                <span>{{ short_amount($overview['withdraw']['total_pending_withdraw']) }}
                                                                </span>
                                                            </h4>


                                                            <p class="text-uppercase fw-medium text-muted mb-3">
                                                                {{ translate('Total Pending Withdraw') }}
                                                            </p>


                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xxl-4 col-xl-6">
                                            <div class="card card-animate bg-soft-orange">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-start justify-content-between gap-2">
                                                        <div class="flex-shrink-0">
                                                            <span class="overview-icon">
                                                                <i class="las la-ban"></i>
                                                            </span>
                                                        </div>

                                                        <div class="text-end">
                                                            <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                                                <span>{{ short_amount($overview['withdraw']['total_rejected_withdraw']) }}
                                                                </span>
                                                            </h4>


                                                            <p class="text-uppercase fw-medium text-muted mb-3">
                                                                {{ translate('Total Rejected Withdraw') }}
                                                            </p>

                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">


                                    <div class="card">
                                        <div class="card-header border-0">
                                            <div class="d-flex align-items-center">


                                                <h4 class="card-title mb-0 flex-grow-1">

                                                    {{ translate('Latest Earning log') }}
                                                </h4>
                                                <div class="flex-shrink-0">
                                                    <a href="{{route('admin.delivery-man.earning',['id'=> $deliveryman->id])}}" class="text-decoration-underline">
                                                        {{translate('View All')}}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="card-body pt-0">

                                            <div class="table-responsive table-card mt-3">
                                                <table class="table table-hover table-nowrap align-middle mb-0">
                                                    <thead class="text-muted table-light">
                                                        <tr class="text-uppercase">

                                                            <th>
                                                                {{ translate('Date') }}
                                                            </th>
                                                            <th>
                                                                {{ translate('Order') }}
                                                            </th>

                                                            <th>
                                                                {{ translate('Amount') }}
                                                            </th>



                                                            <th>
                                                                {{ translate('Details') }}
                                                            </th>

                                                        </tr>
                                                    </thead>

                                                    <tbody class="list form-check-all">

                                                        @forelse($overview['earning_log'] as $earning)
                                                            <tr>
                                                                <td data-label="{{ translate('Date') }}">
                                                                    <span
                                                                        class="fw-bold">{{ diff_for_humans($earning->created_at) }}</span><br>
                                                                    {{ get_date_time($earning->created_at) }}
                                                                </td>

                                                                <td data-label="{{ translate('Order') }}">
                                                                    @php
                                                                        $sellerId = $earning->order->orderDetails->contains(function ($orderDetail){
                                                                            return $orderDetail->product->seller_id;
                                                                        });

                                                                        $route = $sellerId ?  'admin.seller.order.details' : 'admin.inhouse.order.details';
                                                                    @endphp
                                                                    <div class="justify-content-center">
                                                                        <a href="{{route($route ,$earning->order_id)}}" class="fw-bold text-dark">
                                                                            <span>{{@$earning->order->order_id ?? "N\A"}}</span>
                                                                        </a>
                                                                    </div>
                                                                </td>

                                                                <td data-label="{{ translate('Amount') }}">
                                                                    <span
                                                                        class=" text-success fw-bold">
                                                                    {{ round($earning->amount) }}
                                                                        {{ default_currency()->name }}</span>

                                                                </td>


                                                                <td data-label="{{ translate('Details') }}">
                                                                    {{ $earning->details }}
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


                                    <div class="card">
                                        <div class="card-header border-0">
                                            <div class="d-flex align-items-center">
                                                <h4 class="card-title mb-0 flex-grow-1">
                                                    {{ translate('Latest Withdraw Log') }}
                                                </h4>
                                                <div class="flex-shrink-0">
                                                    <a href="{{route('admin.withdraw.log.index',['type'=> 2,'delivery_man'=> $deliveryman->id])}}" class="text-decoration-underline">
                                                        {{translate('View All')}}
                                                    </a>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="card-body">

                                            <div class="table-responsive table-card">
                                                <table class="table table-hover table-nowrap align-middle mb-0">
                                                    <thead class="text-muted table-light">
                                                        <tr class="text-uppercase">
                                                            <th>
                                                                {{ translate('Time') }}
                                                            </th>
                                                            <th>{{ translate('Method') }}
                                                            </th>
                                                            <th>
                                                                {{ translate('Amount') }}
                                                            </th>
                                                            <th>
                                                                {{ translate('Charge') }}
                                                            </th>
                                                            <th>
                                                                {{ translate('Receivable') }}
                                                            </th>
                                                            <th>
                                                                {{ translate('Status') }}
                                                            </th>
                                                            <th>
                                                                {{ translate('Action') }}
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody class="list form-check-all">
                                                        @forelse($overview['withdraw']['log'] as $withdraw)
                                                            <tr>
                                                                <td data-label="{{ translate('Time') }}">
                                                                    <span
                                                                        class="fw-bold">{{ diff_for_humans($withdraw->created_at) }}</span><br>
                                                                    {{ get_date_time($withdraw->created_at) }}
                                                                </td>

                                                                <td data-label="{{ translate('Method') }}">
                                                                    {{ @$withdraw->method ? $withdraw->method->name : translate('N/A') }}
                                                                </td>

                                                                <td data-label="{{ translate('Amount') }}">
                                                                    {{ round($withdraw->amount) }}
                                                                    {{ default_currency()->name }}

                                                                </td>

                                                                <td data-label="{{ translate('Charge') }}">
                                                                    {{ round($withdraw->charge) }}
                                                                    {{ default_currency()->name }}
                                                                </td>

                                                                <td data-label="{{ translate('Receivable') }}">
                                                                    <span
                                                                        class="text-success">{{ round($withdraw->final_amount) }}
                                                                        {{ @$withdraw->currency->name }}</span>
                                                                </td>

                                                                <td data-label="{{ translate('Status') }}">
                                                                    @if ($withdraw->status == '1')
                                                                        <span
                                                                            class="badge badge-soft-primary">{{ translate('Received') }}</span>
                                                                    @elseif($withdraw->status == '2')
                                                                        <span
                                                                            class="badge badge-soft-warning">{{ translate('Pending') }}</span>
                                                                    @elseif($withdraw->status == '3')
                                                                        <span
                                                                            class="badge badge-soft-danger ">{{ translate('Rejected') }}</span>
                                                                        <a data-bs-toggle="tooltip" data-bs-placement="top"
                                                                            title="{{ translate('Info') }}"
                                                                            href="javascript:void(0)"
                                                                            class="text--dark feedbackinfo"
                                                                            data-bs-toggle="modal" data-bs-target="#feedback"
                                                                            data-feedback="{{ $withdraw->feedback }}">
                                                                            <i class="las la-info fs-17"></i></a>
                                                                    @endif
                                                                </td>

                                                                <td data-label="{{ translate('Action') }}">
                                                                    <div class="hstack justify-content-center gap-3">
                                                                        @if ($withdraw->status == 2)
                                                                            <a href="javascript:void(0)"
                                                                                class="link-danger fs-18  withdrawrejected"
                                                                                data-bs-toggle="tooltip"
                                                                                data-bs-placement="top"
                                                                                title="Withdraw Rejected"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#rejected"
                                                                                data-id="{{ $withdraw->id }}"><i
                                                                                    class="ri-close-circle-line"></i></a>

                                                                            <a href="javascript:void(0)"
                                                                                class="link-success fs-18 withdrawapproved"
                                                                                data-bs-toggle="tooltip"
                                                                                data-bs-placement="top"
                                                                                title="Withdraw Approved"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#approved"
                                                                                data-id="{{ $withdraw->id }}"><i
                                                                                    class="ri-check-double-line"></i></a>
                                                                        @endif
                                                                        <a href="{{ route('admin.withdraw.log.details', $withdraw->id) }}"
                                                                            class="link-info fs-18 " data-bs-toggle="tooltip"
                                                                            data-bs-placement="top"
                                                                            title="Withdraw Details"><i
                                                                                class="ri-list-check "></i></a>
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

                                    <div class="card">
                                        <div class="card-header border-0">
                                            <div class="d-flex align-items-center">


                                                <h4 class="card-title mb-0 flex-grow-1">

                                                    {{ translate('Latest Transaction log') }}
                                                </h4>
                                                <div class="flex-shrink-0">
                                                    <a href="{{route('admin.report.deliveryman.transaction',['delivery_man'=> $deliveryman->id])}}" class="text-decoration-underline">
                                                        {{translate('View All')}}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="card-body pt-0">

                                            <div class="table-responsive table-card mt-3">
                                                <table class="table table-hover table-nowrap align-middle mb-0">
                                                    <thead class="text-muted table-light">
                                                        <tr class="text-uppercase">

                                                            <th>
                                                                {{ translate('Date') }}
                                                            </th>

                                                            <th>
                                                                {{ translate('Transaction ID') }}
                                                            </th>
                                                            <th>
                                                                {{ translate('Amount') }}
                                                            </th>

                                                            <th>
                                                                {{ translate('Post Balance') }}
                                                            </th>

                                                            <th>
                                                                {{ translate('Details') }}
                                                            </th>

                                                        </tr>
                                                    </thead>

                                                    <tbody class="list form-check-all">

                                                        @forelse($overview['transaction_log'] as $transaction)
                                                            <tr>
                                                                <td data-label="{{ translate('Date') }}">
                                                                    <span
                                                                        class="fw-bold">{{ diff_for_humans($transaction->created_at) }}</span><br>
                                                                    {{ get_date_time($transaction->created_at) }}
                                                                </td>

                                                                <td data-label="{{ translate('TRX ID') }}">
                                                                    {{ @$transaction->transaction_number }}
                                                                </td>

                                                                <td data-label="{{ translate('Amount') }}">
                                                                    <span
                                                                        class="@if ($transaction->transaction_type == '+') text-success @else text-danger @endif fw-bold">
                                                                        {{ $transaction->transaction_type == '+' ? '+' : '-' }}{{ round($transaction->amount) }}
                                                                        {{ default_currency()->name }}</span>

                                                                </td>
                                                                <td data-label="{{ translate('Post Balance') }}">
                                                                    <span
                                                                        class="fw-bold text-primary">{{ round($transaction->post_balance) }}
                                                                        {{ default_currency()->name }}</span>
                                                                </td>

                                                                <td data-label="{{ translate('Details') }}">
                                                                    {{ $transaction->details }}
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

                                    <div class="card">
                                        <div class="card-header border-bottom-dashed">
                                            <div class="row g-4 align-items-center">
                                                <div class="col-sm">
                                                    <h5 class="card-title mb-0">
                                                        {{translate('Review list')}}
                                                    </h5>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="table-responsive table-card">
                                                <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                                    <thead class="text-muted table-light">
                                                        <tr>
                                                            <th scope="col">#</th>
                                                            <th scope="col">
                                                                {{translate("Customer name")}}
                                                            </th>

                                                            <th scope="col">
                                                                {{translate("Order Id")}}
                                                            </th>

                                                            <th scope="col">
                                                            {{translate('Rating')}}
                                                            </th>

                                                            <th scope="col">
                                                                {{translate("Action")}}
                                                            </th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @forelse($overview['customer_reviews'] as $review)
                                                            <tr>
                                                                <td class="fw-medium">
                                                                    {{$loop->iteration}}
                                                                </td>

                                                                <td data-label="{{translate('Customer Info')}}" class="text-align-left">
                                                                    <span>{{translate("Name")}}: {{@$review->user->name}}</span><br>

                                                                        <a href="{{route('admin.customer.details', $review->user->id)}}" class="fw-bold text-dark">
                                                                            <span>
                                                                                {{translate('Email')}}: {{@$review->user->email ?? "N\A"}}</span>
                                                                        </a>

                                                                </td>

                                                                <td>
                                                                    @php
                                                                        $sellerId = $review->order->orderDetails->contains(function ($orderDetail){
                                                                            return $orderDetail->product->seller_id;
                                                                        });

                                                                        $route = $sellerId ?  'admin.seller.order.details' : 'admin.inhouse.order.details';
                                                                    @endphp
                                                                    <div class="justify-content-center">
                                                                        <a href="{{route($route ,$review->order_id)}}" class="fw-bold text-dark">
                                                                            <span>{{@$review->order->order_id ?? "N\A"}}</span>
                                                                        </a>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <span class="badge badge-soft-success d-inline-flex align-items-center gap-1">
                                                                        {{round(@$review->rating)}}<i class="ri-star-s-fill"></i>
                                                                    </span>
                                                                </td>




                                                                <td>
                                                                    <div class="hstack justify-content-center gap-3">

                                                                        <a title="{{translate('Show')}}" data-bs-toggle="tooltip" data-bs-placement="top" data-review="{{$review->message}}"  href="javascript:void(0);" class="fs-18 link-success show-review">
                                                                            <i class="ri-eye-line"></i></a>
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
                                                {{$overview['customer_reviews']->appends(request()->all())->links()}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card">
                                <div class="card-header border-0">
                                    <div class="d-flex align-items-center">


                                        <h4 class="card-title mb-0 flex-grow-1">

                                            {{ translate('Earning logs') }}
                                        </h4>

                                    </div>
                                </div>


                                <div class="card-body pt-0">

                                    <div class="table-responsive table-card mt-3">
                                        <table class="table table-hover table-nowrap align-middle mb-0">
                                            <thead class="text-muted table-light">
                                                <tr class="text-uppercase">

                                                    <th>
                                                        {{ translate('Date') }}
                                                    </th>
                                                    <th>
                                                        {{ translate('Order') }}
                                                    </th>

                                                    <th>
                                                        {{ translate('Amount') }}
                                                    </th>



                                                    <th>
                                                        {{ translate('Details') }}
                                                    </th>

                                                </tr>
                                            </thead>

                                            <tbody class="list form-check-all">

                                                @forelse($earningLogs as $earning)
                                                    <tr>
                                                        <td data-label="{{ translate('Date') }}">
                                                            <span
                                                                class="fw-bold">{{ diff_for_humans($earning->created_at) }}</span><br>
                                                            {{ get_date_time($earning->created_at) }}
                                                        </td>

                                                        <td data-label="{{ translate('Order') }}">
                                                            @php
                                                                $sellerId = $earning->order->orderDetails->contains(function ($orderDetail){
                                                                    return $orderDetail->product->seller_id;
                                                                });

                                                                $route = $sellerId ?  'admin.seller.order.details' : 'admin.inhouse.order.details';
                                                            @endphp
                                                            <div class="justify-content-center">
                                                                <a href="{{route($route ,$earning->order_id)}}" class="fw-bold text-dark">
                                                                    <span>{{@$earning->order->order_id ?? "N\A"}}</span>
                                                                </a>
                                                            </div>
                                                        </td>

                                                        <td data-label="{{ translate('Amount') }}">
                                                            <span
                                                                class=" text-success fw-bold">
                                                            {{ round($earning->amount) }}
                                                                {{ default_currency()->name }}</span>

                                                        </td>


                                                        <td data-label="{{ translate('Details') }}">
                                                            {{ $earning->details }}
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="cashCollect" tabindex="-1" aria-labelledby="cashCollect" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">{{ translate('Cash Collect') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form action="{{ route('admin.delivery-man.cash.collect') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $deliveryman->id }}">
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="amount" class="form-label">{{ translate('Amount') }} <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="amount" name="amount"
                                    placeholder="{{ translate('Enter amount') }}">
                                <span class="input-group-text">{{ default_currency()->name }}</span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit"
                            class="btn btn-success waves ripple-light">{{ translate('Collect') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejected" tabindex="-1" aria-labelledby="rejected" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.withdraw.log.rejectedby') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="modal_text2  mt-3">
                            <h6>{{ translate('Are you sure to want rejected this withdraw?') }}</h6>
                            <textarea class="form-control" name="details" placeholder="{{ translate('Enter Details') }}" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="approved" tabindex="-1" aria-labelledby="approved" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.withdraw.log.approvedby') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div class="modal_icon2 text-center">
                            <i class="fs-18 link-info ri-check-double-line"></i>
                        </div>
                        <div class="modal_text2 text-center  mt-3">
                            <h6>{{ translate('Are you sure want to approved this withdraw?') }}</h6>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="feedback" tabindex="-1" aria-labelledby="feedback" aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header bg-light  p-3">
                    <h5 class="modal-title">
                        {{ translate('Feedback') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="feedbacktext"></p>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="deliveryManReview" tabindex="-1" aria-labelledby="deliveryManReview" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" >{{translate('Review')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-md btn-danger " data-bs-dismiss="modal">{{translate('Cancel')}}</button>

                </div>

            </div>
        </div>
    </div>

    @include('admin.modal.delete_modal')
@endsection

@push('script-push')
    <script>
        "use strict";

        flatpickr(".withdraw_log", {
            dateFormat: "Y-m-d",
            mode: "range",
        });

        flatpickr(".transaction_log", {
            dateFormat: "Y-m-d",
            mode: "range",
        });


        $(".withdrawrejected").on("click", function(){
			var modal = $("#rejected");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".withdrawapproved").on("click", function(){
			var modal = $("#approved");
			modal.find('input[name=id]').val($(this).data('id'));
			modal.modal('show');
		});

		$(".feedbackinfo").on("click", function(){
			var modal = $("#feedback");
			var data = $(this).data('feedback');
			$(".feedbacktext").text(data);
			modal.modal('show');
		});

        $('.show-review').on('click', function(){
			var modal = $('#deliveryManReview');

            var review = $(this).attr('data-review');
            modal.find('.modal-body').html(review)

			modal.modal('show');
		});
    </script>
@endpush
