@extends('admin.layouts.app')
@section('main_content')
<div class="page-content">
    <div class="container-fluid">
        <div class="h-100">
            <div class="d-flex align-items-lg-center flex-lg-row flex-column mb-3 pb-1">
                <div class="flex-grow-1">
                    <h4 class="fs-3 mb-0">
                        {{translate('Wellcome back')}},
                        <span class="text-primary">
                            {{auth_user()->name}}
                        </span>
                    </h4>
                </div>
                <div class="mt-3 mt-lg-0">

                     <span class="fw-semibold">{{translate('Last Cron Run')}}</span> :
                      <span class="badge badge-soft-success fs-12">
                         @php  
                             $setting = App\Models\Setting::where("key", 'last_cron_run')->first();
                         @endphp
                         {{$setting && $setting->value   ?  $setting->value : 'N/A'}}
                      </span>

                </div>
            </div>

            @php
                $taskConfig = site_settings('task_config') 
                                   ? json_decode(site_settings('task_config'),true)  
                                   : [];
            @endphp

            @if(!in_array('mail_config',$taskConfig) ||  !in_array('ai_config',$taskConfig))

                <div class="mb-4">
                    <h4>
                        {{translate('Tasks to be done!')}}
                    </h4>

                    <div class="d-flex flex-column gap-3">
                        @if(!in_array('mail_config',$taskConfig))
                            <div class="alert alert-primary alert-dismissible alert-border-left  alert-label-icon fade show mb-xl-0 todo-alart material-shadow" role="alert">
                                <span class="me-2">
                                    <a href="{{route('admin.mail.configuration')}}" class="btn btn-secondary btn-sm py-1">
                                        {{translate('Setup')}}
                                    </a>
                                </span>

                                {{translate('Mail Configuration - used for sending emails')}}
                                <a  onclick="return confirm('Have you completed this task?')"  href="?task=mail_config" class="todo-alart-close"><i class="ri-close-line"></i></a>
                            </div>
                        @endif

                        @if(!in_array('ai_config',$taskConfig))
                            <div class="alert alert-primary alert-dismissible alert-border-left alert-label-icon fade show mb-xl-0 todo-alart material-shadow" role="alert">
                                <span class="alert-icon">
                                    <a href="{{route('admin.general.ai.configuration')}}" class="btn btn-secondary btn-sm py-1">
                                        {{translate('Setup')}}
                                    </a>
                                </span>

                                {{translate('AI Configuration - used for generating content using open AI')}}
                                <a onclick="return confirm('Have you completed this task?')"  href="?task=ai_config" class="todo-alart-close"><i class="ri-close-line"></i></a>

                            </div>
                        @endif

                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-xxl-3 col-xl-4 col-sm-6">
                    <div class="card card-animate">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-shrink-0">
                                    <span class="overview-icon">
                                        <i class="ri-money-dollar-circle-line text-success"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        {{short_amount(Arr::get($data,'total_payment',0))}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Total Earnings")}}
                                    </p>

                                    <a href="{{route('admin.report.user.transaction')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="ri-money-dollar-circle-fill text-info"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                       {{short_amount(Arr::get($data,'subscription_earning',0))}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Subscription Payment")}}
                                    </p>

                                    <a href="{{route('admin.plan.subscription')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="ri-money-pound-circle-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        {{short_amount(Arr::get($data,'order_payment',0))}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Order Payment")}}
                                    </p>

                                    <a href="{{route('admin.inhouse.order.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="ri-wallet-3-fill text-warning"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        {{short_amount(Arr::get($data,'total_withdraw',0))}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Withdraw Amount")}}
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
                                         <i class="bx bxl-product-hunt text-success"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        {{Arr::get($data,'physical_product',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Inhouse Products")}}
                                    </p>

                                    <a href="{{route('admin.item.product.inhouse.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        {{Arr::get($data,'digital_product',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Digital Products")}}
                                    </p>

                                    <a href="{{route('admin.digital.product.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="bx bxs-group text-info"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                        {{Arr::get($data,'total_customer',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Total Customers")}}
                                    </p>

                                    <a href="{{route('admin.customer.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="bx bx-user-circle text-warning"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                         {{Arr::get($data,'total_seller',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate("Total Sellers")}}
                                    </p>

                                    <a href="{{route('admin.seller.info.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="ri-home-gear-fill text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                          {{Arr::get($data,'inhouse_order',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate('Inhouse Orders')}}
                                    </p>

                                    <a href="{{route('admin.inhouse.order.index')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                        <i class="ri-check-double-line text-primary"></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                          {{Arr::get($data,'delivered_order',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Delivered Orders')}}
                                    </p>

                                    <a href="{{route('admin.inhouse.order.delivered')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                          {{Arr::get($data,'shipped_order',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                        {{translate('Shipped Orders')}}
                                    </p>

                                    <a href="{{route('admin.inhouse.order.shipped')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                                         <i class='bx bx-x-circle text-danger'></i>
                                    </span>
                                </div>

                                <div class="text-end">
                                    <h4 class="fs-22 fw-bold ff-secondary mb-2">
                                          {{Arr::get($data,'cancel_order',0)}}
                                    </h4>


                                    <p class="text-uppercase fw-medium text-muted mb-3">
                                         {{translate('Canceld Orders')}}
                                    </p>

                                    <a href="{{route('admin.inhouse.order.cancel')}}" class="d-flex align-items-center justify-content-end gap-1">
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
                <div class="col-xl-4">
                    <div class="card card-height-100">
                        <div class="card-header border-bottom-dashed align-items-center d-flex mb-2">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('Monthly Order Insight')}}
                            </h4>

                        </div>

                        <div class="card-body">
                            @if(count($data['monthly_order_report']))
                                <div id="monthlyOrderChart" class="apex-charts"  data-colors='["--ig-primary", "--ig-success", "--ig-secondary", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'>
                                </div>
                            @else
                                @include('admin.partials.not_found')
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card card-height-100">
                        <div class="card-header align-items-center d-flex border-bottom-dashed">
                            <h4 class="card-title mb-0 flex-grow-1">
                                    {{translate("Latest Orders")}}
                            </h4>

                            <div class="flex-shrink-0">
                                <a href="{{route('admin.inhouse.order.index')}}" class="text-decoration-underline">
                                    {{translate('View All')}}
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-hover align-middle table-nowrap " >
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">
                                            <th>
                                                {{translate(
                                                    "Order ID"
                                                )}}
                                            </th>
                                            <th>
                                                {{translate('Qty')}}
                                            </th>
                                            <th  >{{translate('Time')}}
                                            </th>
                                            <th>
                                                {{translate('Customer Info')}}
                                            </th>

                                            <th >
                                                {{translate('Amount')}}
                                            </th>
                                            <th >
                                                {{translate('Delivery')}}
                                            </th>
                                            <th >
                                                {{translate('Action')}}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="form-check-all">

                                        @forelse(Arr::get($data,'latest_orders') as $order)

                                            <tr>
                                                <td data-label="{{translate('Order Number')}}">
                                                    <span class="text-primary fw-bold" data-bs-toggle="tooltip" data-bs-placement="top" title="Order Number">
                                                        {{$order->order_id}}
                                                    </span>
                                                </td>

                                                <td data-label="{{translate('Qty')}}">
                                                    <span>{{$order->qty}}</span><br>
                                                </td>
                                                <td data-label="{{translate('Time')}}">
                                                    <span class="fw-bold">{{diff_for_humans($order->created_at)}}</span><br>
                                                    {{get_date_time($order->created_at)}}
                                                </td>
                                                <td data-label="{{translate('Customer')}}" class="text-align-left">

                                                    @if($order->customer_id)
                                                        <a href="{{route('admin.customer.details', $order->customer_id)}}" class="fw-bold text-dark">
                                                            {{@$order->customer->name ?? $order->customer->email}}
                                                        </a>
                                                    @else
                                      
                                                         {{@$order->billingAddress ? @$order->billingAddress->email :  @$order->billing_information->email ?? 'N/A'}}
                                                    @endif

                                                </td>


                                                <td data-label="{{translate('Amount')}}">
                                                    <span class="fw-bold">
                                                        {{(short_amount($order->amount))}}
                                                    </span><br>

                                                    @php echo order_payment_status($order->payment_status) @endphp

                                                </td>
                                                <td data-label="{{translate('Delivery Status')}}">

                                                    @php echo order_status_badge($order->status) @endphp

                                                </td>
                                                <td data-label="{{translate('Action')}}">
                                                    <div class="hstack gap-3">
                                                        @if(permission_check('view_order'))
                                                            <a title="{{translate('Print')}}" data-bs-toggle="tooltip" data-bs-placement="top" href="{{route('admin.inhouse.order.print', [$order->id, 'inhouse'])}}"
                                                                class="fs-18 link-success order_id">
                                                                <i class="ri-printer-line"></i>
                                                            </a>

                                                            <a title="{{translate('Details')}}" data-bs-toggle="tooltip" data-bs-placement="top"  href="{{route('admin.inhouse.order.details', $order->id)}}"
                                                                class="fs-18 link-info ms-1"><i class="ri-list-check"></i></a>
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
            </div>

            <div class="row">
                <div class="col-xxl-8 col-xl-7">
                    <div class="card card-height-100">
                        <div class="card-header border-bottom-dashed align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('Best Selling Products')}}
                            </h4>

                            <div class="flex-shrink-0">
                                <a href="{{route('admin.item.product.inhouse.index')}}" class="text-decoration-underline">
                                    {{translate('View All')}}
                                </a>
                            </div>

                        </div>

                        <div class="card-body card-height-100">
                            <div class="table-responsive table-card">
                                <table class="table table-hover align-middle table-nowrap ">
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">
                                            <th class="text-start">{{translate('Product')}}</th>
                                            <th>{{translate('Categories - Sold Item')}}</th>
                                            <th>{{translate('Info')}}</th>
                                            <th>{{translate('Top Item - Todays Deal')}}</th>
                                            <th>{{translate('Time - Status')}}</th>

                                        </tr>
                                    </thead>
                                    <tbody class="form-check-all">

                                        @forelse(Arr::get($data,'best_selling_product') as $inhouseProduct)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img class="rounded avatar-sm img-thumbnail" alt="{{@$inhouseProduct->featured_image}}" src="{{show_image(file_path()['product']['featured']['path'].'/'.$inhouseProduct->featured_image,file_path()['product']['featured']['size'])}}"
                                                            >
                                                        </div>
                                                        <div class="flex-grow-1">
                                                           {{limit_words($inhouseProduct->name,2)}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>

                                                    <span class="badge bg-info text-white fw-bold">{{(@get_translation($inhouseProduct->category->name))}}

                                                    </span>
                                                    <br>
                                                    <span>
                                                        {{translate('Total Sold')}} : {{$inhouseProduct->order->count()}}
                                                    </span>
                                                </td>

                                                <td data-label="{{translate('Info')}}">
                                                    <span>{{translate('Regular Price')}} : {{(short_amount($inhouseProduct->price))}} <br> {{translate('Discount Price')}} : {{(short_amount($inhouseProduct->discount))}}</span>

                                                </td>


                                                <td data-label="{{translate('Top Product')}}">
                                                    <a href="{{route('admin.item.product.inhouse.top', $inhouseProduct->id)}}" class=" fs-15 link-{{$inhouseProduct->top_status == 1 ? 'danger':'success'}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Top Product')}}">{{($inhouseProduct->top_status==1?'No':'Yes')}} <i class="las la-arrow-alt-circle-left"></i></a> <i class="las la-arrows-alt-v"></i>
                                                    <a href="{{route('admin.item.product.inhouse.featured.status', $inhouseProduct->id)}}" class="fs-15 link-{{$inhouseProduct->featured_status == 1 ? 'danger':'success'}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Todays Deal')}}">{{($inhouseProduct->featured_status==1?'No':'Yes')}} <i class="las la-arrow-alt-circle-left"></i></a>
                                                </td>

                                                <td data-label="{{translate('Time - Status')}}">
                                                    <span>{{get_date_time($inhouseProduct->created_at, 'd M Y')}}</span><br>
                                                    @php echo product_status($inhouseProduct->status) @endphp

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

                <div class="col-xxl-4 col-xl-5">
                    <div class="card card-height-100">
                        <div class="card-header border-bottom-dashed align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate("Top Category")}}
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="top-category" data-simplebar>
                                <ul class="list-group">
                                    @forelse (Arr::get($data,'top_categories') as $category )
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <img class="rounded avatar-sm img-thumbnail" src="{{show_image(file_path()['category']['path'].'/'.$category->banner,file_path()['category']['size']) }}" alt="{{$category->banner}}"
                                                            >
                                                        </div>

                                                        <div class="flex-shrink-0 ms-2">
                                                            <p class="fs-14 fw-semibold mb-0 text-break">
                                                                {{@get_translation($category->name)}}
                                                            </p>
                                                            <small class="text-muted">
                                                                    {{diff_for_humans($category->created_at)}}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <p class="text-info mb-0">
                                                            {{$category->product_count}}
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item">
                                                @include('admin.partials.not_found')
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-header border-0 align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('Orders Insight')}}
                            </h4>

                        </div>

                        <div class="card-header p-0 border-0 bg-light-subtle">
                            <div class="row g-0 text-center">

                                <div class="col-4 col-sm-4">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span>{{Arr::get($data,'inhouse_order',0) + Arr::get($data,'digital_order',0)  }}</span></h5>
                                        <p class="text-muted mb-0">
                                                {{translate("Total Orders")}}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-4 col-sm-4">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1">
                                            <span>
                                                {{Arr::get($data,'inhouse_order',0)}}
                                            </span>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            {{translate("Physical Orders")}}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-4 col-sm-4">
                                    <div class="p-3 border border-dashed border-start-0
                                    border-end-0">
                                        <h5 class="mb-1">
                                            {{Arr::get($data,'digital_order',0)}}
                                        </h5>
                                        <p class="text-muted mb-0">{{translate("Digital Orders")}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-2">
                            <div id="orderChart" class="apex-charts"  data-colors='["--ig-primary", "--ig-success", "--ig-secondary", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card card-height-100">
                        <div class="card-header border-0">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate("Product Insight")}}
                            </h4>
                        </div>


                        <div class="card-header p-0 border-0 bg-light-subtle">
                            <div class="row g-0 text-center">

                                <div class="col-4 col-sm-4">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1"><span>{{Arr::get($data,'physical_product',0) + Arr::get($data,'digital_product',0)  }}</span></h5>
                                        <p class="text-muted mb-0">
                                                {{translate("Total Product")}}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-4 col-sm-4">
                                    <div class="p-3 border border-dashed border-start-0">
                                        <h5 class="mb-1">
                                            <span>
                                                {{Arr::get($data,'physical_product',0)}}
                                            </span>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            {{translate("Inhouse Product")}}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-4 col-sm-4">
                                    <div class="p-3 border border-dashed border-start-0
                                    border-end-0">
                                        <h5 class="mb-1">
                                            {{Arr::get($data,'digital_product',0)}}
                                        </h5>
                                        <p class="text-muted mb-0">{{translate("Digital Product")}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-2">
                                <div id="productInsight" class="apex-charts"  data-colors='["--ig-primary", "--ig-success", "--ig-secondary", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'>
                                </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-6">
                    <div class="card card-height-100">
                        <div class="card-header border-bottom-dashed align-items-center  d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">
                                    {{translate("Latest Transactions")}}
                            </h4>
                            <div class="flex-shrink-0">
                                <a href="{{route('admin.report.user.transaction')}}" class="text-decoration-underline">
                                    {{translate('View All')}}
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table table-hover align-middle table-nowrap ">
                                    <thead class="text-muted table-light">
                                        <tr class="text-uppercase">

                                            <th>{{translate('Date')}}</th>

                                            <th>{{translate('Customer/Selller')}}</th>

                                            <th>
                                                {{translate('Transaction ID')}}
                                            </th>
                                            <th>
                                                {{translate('Amount')}}
                                            </th>

                                        </tr>
                                    </thead>

                                    <tbody>

                                        @forelse(Arr::get($data,'latest_transaction') as $transaction)
                                            <tr>

                                                <td data-label="{{translate('Date')}}">
                                                    <span class="fw-bold">{{diff_for_humans($transaction->created_at)}}</span>
                                                    <br>
                                                    {{get_date_time($transaction->created_at)}}
                                                </td>

                                                @if($transaction->user_id)
                                                    <td data-label="{{translate('User')}}">
                                                        <span>{{@$transaction->user->name}}</span><br>
                                                        <a href="{{route('admin.customer.details', $transaction->user_id)}}" class="fw-bold text-dark">{{(@$transaction->user->email)}}</a>
                                                    </td>
                                                @elseif($transaction->seller_id)
                                                    <td data-label="{{translate('Seller')}}">
                                                        <span>{{@$transaction->seller? @$transaction->seller->name : 'N/A'}}</span><br>
                                                        <a href="{{route('admin.seller.info.details', $transaction->seller_id)}}" class="fw-bold text-dark">{{(@$transaction->seller->email)}}</a>
                                                    </td>
                                                @else
                                                    <td data-label="{{translate('Guest')}}">
                                                        {{ @$transaction->guest_user->email ?? 'Guest User'}}
                                                    </td>
                                                @endif


                                                <td data-label="{{translate('TRX ID')}}">
                                                    {{(@$transaction->transaction_number)}}
                                                </td>

                                                <td data-label="{{translate('Amount')}}">
                                                    <span class="@if($transaction->transaction_type == '+') text-success @else text-danger @endif fw-bold">
                                                        {{$transaction->transaction_type == "+" ? '+' : '-'}}
                                                        {{show_amount($transaction->amount,default_currency()->symbol) }}
                                                    </span>
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

                <div class="col-xl-6">
                    <div class="card card-height-100">
                        <div class="card-header border-bottom-dashed align-items-center d-flex mb-2">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('Earning Insight')}}
                            </h4>

                        </div>


                        <div class="card-body p-2">
                            <div id="earningChart"  class="apex-charts"  data-colors='["--ig-primary", "--ig-success", "--ig-secondary", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card card-height-100">
                        <div class="card-header border-bottom-dashed align-items-center d-flex mb-2">
                            <h4 class="card-title mb-0 flex-grow-1">
                                {{translate('Web Visitors Insight')}}
                            </h4>

                            
                            <a href="{{route('admin.security.ip.list')}}" class="d-flex align-items-center justify-content-end gap-1">
                                {{translate('View All')}}
                                 <i class="ri-arrow-right-line"></i>
                            </a>

                        </div>

                        <div class="card-body">
                            <div id="vistorChart"  class="apex-charts"  data-colors='["--ig-primary", "--ig-success", "--ig-secondary", "--ig-danger", "--ig-info","--ig-primary","--ig-warning"]'>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@php

$ordersArr      = $data['order_by_year'];
$productArr     = $data['product_by_year'];
$totalArray     = array_column($ordersArr , 'total');
$digitalArray   = array_column($ordersArr , 'digital');
$physicalArray  = array_column($ordersArr , 'physical');

$totalProductArr            = array_column($productArr , 'total');
$digitalProductArray        = array_column($productArr , 'digital');
$physicalPorductArray       = array_column($productArr , 'physical');
$productSell                = array_values($data['product_sell_by_month']);
$webVisitors                = array_values($data['web_visitors']);



@endphp

@endsection

@push('script-include')
<script src="{{asset('assets/global/js/apexcharts.js')}}"></script>
@endpush

@push('script-push')
<script type="text/javascript">
   "use strict";
    var chartDonutBasicColors = getChartColorsArray("store-visits-source");

    var monthlyLabel = @json(array_keys($data['order_by_year']));
    var options = {
        chart: {
        height: 350,
        type: "line",
        },
        dataLabels: {
        enabled: false,
        },


        colors: chartDonutBasicColors,
        series: [
        {
            name: "{{ translate('Total Order') }}",
            data:  @json($totalArray),
        },
        {
            name: "{{ translate('Digital Order') }}",
            data: @json($digitalArray),
        },
        {
            name: "{{ translate('Physical Order') }}",
            data: @json($physicalArray),
        },
        ],
        xaxis: {
        categories:  monthlyLabel,
        },

        tooltip: {
            shared: false,
            intersect: true,

        },

        markers: {
        size: 6,
        },
        stroke: {
        width: [4, 4],
        },
        legend: {
        horizontalAlign: "center",
        },
    };

    var chart = new ApexCharts(document.querySelector("#orderChart"), options);
    chart.render();


    var options = {
          series: [
          {
            name: "{{ translate('Physical Product') }}",
            data: @json($physicalPorductArray),
          },
          {
            name: "{{ translate('Digital Product') }}",
            data: @json($digitalProductArray),
          },
          {
            name: "{{ translate('Total Product') }}",
            data:  @json($totalProductArr),

          },

          {
            name: "{{ translate('Total Sell') }}",
            data:  @json($productSell),

          },

        ],
          chart: {
          type: 'bar',
          height: 350,
          stacked: true,
        },
        stroke: {
          width: 1,
          colors: ['#fff']
        },

        plotOptions: {
          bar: {
            horizontal: false
          }
        },
        xaxis: {
          categories: monthlyLabel
        },
        fill: {
          opacity: 1
        },
        colors: chartDonutBasicColors,

        legend: {
          position: 'top',
          horizontalAlign: 'left'
        }
        };

        var chart = new ApexCharts(document.querySelector("#productInsight"), options);
        chart.render();


        var payment = @json(array_values($data['earning_per_months']));
        var paymentCharge = @json(array_values($data['monthly_payment_charge']));
        var withdrawCharge = @json(array_values($data['monthly_withdraw_charge']));



    var options = {
        chart: {
        height: 350,
        type: "line",
        },
        dataLabels: {
        enabled: false,
        },

        colors: chartDonutBasicColors,
        series: [
        {
            name: "{{ translate('Order Payment') }}",
            data: payment,
        },
        {
            name: "{{ translate('Payment Charge') }}",
            data: paymentCharge,
        },
        {
            name: "{{ translate('Withdraw Charge') }}",
            data: withdrawCharge,
        },
        ],
        xaxis: {
        categories:  monthlyLabel,
        },

        tooltip: {
            shared: false,
            intersect: true,
            y: {
                formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                return formatCurrency(value);
                }
            }
            },
        markers: {
        size: 6,
        },
        stroke: {
        width: [4, 4],
        },
        legend: {
        horizontalAlign: "center",
        },
    };

    var chart = new ApexCharts(document.querySelector("#earningChart"), options);
    chart.render();


    var orderStatus = @json(array_keys($data['monthly_order_report']));
    var orderValues = @json(array_values($data['monthly_order_report']));

    var options = {
            series: orderValues,
            chart: {
            height: 380,
            width:"100%",
            type: "donut",
            dropShadow: {
            enabled: true,
            color: '#111',
            top: -1,
            left: 3,
            blur: 3,
            opacity: 0.2
            }
        },

        legend: {
        position: 'bottom'
        },
        stroke: {
            width: 0,
        },
        plotOptions: {
            pie: {
            donut: {
                labels: {
                show: true,
                total: {
                    showAlways: true,
                    show: true
                }
                }
            }
            }
        },
        labels: orderStatus,

        dataLabels: {
            dropShadow: {
            blur: 3,
            opacity: 0.8
            }
        },
        fill: {
            opacity: 1,
            pattern: {
            enabled: true,
            },


              colors: chartDonutBasicColors,

        },
        states: {
            hover: {
            filter: 'none'
            }
        },
        responsive: [{
            breakpoint: 991,
            options: {
                chart: {
                    width: "100%",
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#monthlyOrderChart"), options);
    chart.render();



var options = {
  chart: {
    height: 280,
    type: "area"
  },
  dataLabels: {
    enabled: false
  },
  series: [
    {
      name: "Series 1",
      data: @json($webVisitors),
    }
  ],
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.7,
      opacityTo: 0.9,
      stops: [0, 90, 100]
    }
  },
  xaxis: {
    categories:  monthlyLabel,
  }
};

var chart = new ApexCharts(document.querySelector("#vistorChart"), options);

chart.render();


    function formatCurrency(value) {
        var suffixes = ["", "K", "M", "B", "T"];
        var order = Math.floor(Math.log10(value) / 3);
        var suffix = suffixes[order];
        if(value < 1)
        {return "$"+value}
        var scaledValue = value / Math.pow(10, order * 3);
        return "$" + scaledValue.toFixed(2) + suffix;
    }

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
