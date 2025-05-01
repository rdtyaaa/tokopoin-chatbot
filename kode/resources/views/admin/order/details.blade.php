@extends('admin.layouts.app')

@push('style-push')

   <style>
        .timeline-log{
            max-height: 440px;
            height: 100%;
            margin-top: 20px;
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
                        <li class="breadcrumb-item"><a href="{{ route('admin.inhouse.order.index') }}">
                                {{ translate('Orders') }}
                            </a></li>


                        <li class="breadcrumb-item active">
                            {{ translate('Order Details') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header border-bottom-dashed d-flex align-items-center justify-content-between">
                            <div class="d-flex gap-2 align-items-center">
                                <h5 class="card-title mb-0">
                                    {{ translate('Order') }} -
                                    {{ $order->order_id }}
                                </h5>

                                @if ($order->payment_status == App\Models\Order::UNPAID)
                                    <span class="badge badge-soft-danger">{{ translate('Unpaid') }}</span>
                                @elseif($order->payment_status == App\Models\Order::PAID)
                                    <span class="badge badge-soft-success">{{ translate('Paid') }}</span>
                                @endif
                                &

                                @php echo order_status_badge($order->status)  @endphp


                                @if($order->verification_code)
                                   -
                                    <div>
                                        <span class="text-success" title="{{translate('Order verification code')}}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" >
                                            {{$order->verification_code}}
                                          
                                        </span>
                                    </div>
                                @endif
                               
                            </div>

                     
                          
                            <div>
                                <button type="button" class="btn btn-primary btn-md add-btn waves ripple-light" data-bs-toggle="offcanvas" data-bs-target="#deliveryOffcanvas" aria-controls="deliveryOffcanvas">
                                    @if(!$order->deliveryManOrder)
                                      <i class="ri-add-line align-bottom me-1 fs-16"></i>
                                        {{translate("Deliveryman")}}
                                    @else
                                              {{translate("View Deliveryman")}}
                                    @endif
                                    
                                </button>
                            </div>

                    
                        </div>
                        
                        @if(@$order->deliveryManOrder && 
                            @$order->deliveryManOrder->status == App\Models\DeliveryManOrder::DELIVERED &&
                            $order->status  != App\Models\Order::DELIVERED)
                            <div class="card-body">
                                <div class="alert alert-success material-shadow m-2 mb-2" role="alert">
                                    <strong>  {{translate('This order has been delivered by delivery man') }}  
                                    </strong> 
                                </div>
                            </div>
                        @endif

                        @if(@$order->deliveryManOrder && 
                        @$order->deliveryManOrder->status == App\Models\DeliveryManOrder::RETURN &&
                        $order->status  != App\Models\Order::RETURN)
                        <div class="card-body">
                            <div class="alert alert-success material-shadow m-2 mb-2" role="alert">
                                <strong>  {{translate('This order has been returned by delivery man') }}  
                                </strong> 
                            </div>
                        </div>
                    @endif


                  

                        <div class="card-body">
                           
                            <div class="table-responsive table-card">
                                <table class="table table-nowrap align-middle table-borderless mb-0">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th scope="col">
                                                {{ translate('Product Name') }}
                                            </th>
                                            <th scope="col">
                                                {{ translate('Item Price') }}
                                            </th>
                                            <th scope="col">
                                                {{ translate('Qty') }}
                                            </th>
                                            <th scope="col">
                                                {{ translate('Total') }}
                                            </th>
                                            <th scope="col">
                                                {{ translate('Status') }}
                                            </th>
                                            <th scope="col">
                                                {{ translate('Action') }}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @php
                         
                                            $originalPrice = 0;
                                            $discount = 0;
                                            $tax = 0;
                                            $totalAmount = 0;
                                            
                                        @endphp
                                        @foreach ($orderDeatils as $orderDetail)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0">
                                                            <img alt="{{ @$orderDetail->product->featured_image }}"
                                                                class="avatar-md rounded img-thumbnail"
                                                                src="{{ show_image(file_path()['product']['featured']['path'] . '/' . @$orderDetail->product->featured_image, file_path()['product']['featured']['size']) }}">
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="fs-14">
                                                                <a href="{{ route('admin.item.product.inhouse.details', $orderDetail->product_id) }}"
                                                                    class="text-body">{{ $orderDetail->product->name }}
                                                                </a>
                                                            </h5>
                                                            <div class="d-flex align-items-center">
                                                                <span
                                                                    class="btn btn-outline-primary btn-sm rounded py-0 me-2">{{ $orderDetail->attribute }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ short_amount($orderDetail->original_price / $orderDetail->quantity) }}
                                                </td>
                                                <td>
                                                    {{ $orderDetail->quantity }}
                                                </td>

                                                <td>
                                                    {{ short_amount($orderDetail->original_price / $orderDetail->quantity*$orderDetail->quantity) }}
                                                </td>
                                                <td data-label="{{ translate('Status') }}">
                                                    @php echo order_status_badge($orderDetail->status)  @endphp
                                                </td>
                                                <td data-label="{{ translate('Action') }}">
                                                    <a class="btn-soft-primary fs-18 link-warning p-1 rounded orderstatus"
                                                        title="Delivery Status update" data-bs-toggle="tooltip"
                                                        data-bs-placement="top" data-bs-toggle="modal"
                                                        data-bs-target="#updateorderstatus" href="javascript:void(0)"
                                                        data-id="{{ $orderDetail->id }}"
                                                        data-status="{{ $orderDetail->status }}"><i
                                                            class="ri-pencil-line"></i></a>
                                                </td>
                                            </tr>
                                            @php
                                                $originalPrice += $orderDetail->original_price;
                                                $tax += $orderDetail->total_taxes;
                                                $discount += $orderDetail->discount;
                                                $totalAmount += $orderDetail->total_price;
                                           @endphp
                                        @endforeach
                                        <tr class="border-top border-top-dashed">
                                            <td colspan="4"></td>
                                            <td colspan="3" class="fw-medium p-0">
                                                <table class="table table-borderless mb-0">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-start">
                                                                {{ translate('Original Amount') }}
                                                                :</td>
                                                            <td class="text-end">
                                                                {{ short_amount($originalPrice) }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="text-start">
                                                                {{ translate('Tax Amount') }}
                                                                :</td>
                                                            <td class="text-end">
                                                                {{ short_amount($tax) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-start">
                                                                {{ translate('Shipping Cost') }}
                                                            </td>
                                                            <td class="text-end">

                                                                 {{ short_amount($order->shipping_charge) }}

                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td class="text-start">
                                                                {{ translate('All Discount') }}

                                                                :</td>
                                                            <td class="text-end">
                                                                {{ short_amount($order->discount) }}
                                                            </td>
                                                        </tr>

                                                        <tr class="border-top border-top-dashed">
                                                            <th scope="row" class="fw-bold text-start">
                                                                {{ translate('Total') }}:</th>

                                                            <th class="text-end">
                                                                {{ short_amount($order->amount) }}
                                                            </th>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                <i class="ri-map-pin-line align-middle me-1 text-muted"></i>
                                {{ translate('Product Status Update') }}
                            </h5>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.inhouse.order.status.update', $order->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-lg-6">
                                        <label for="payment_status" class="form-label">{{ translate('Payment Status') }}
                                            <span class="text-danger">*</span></label>
                                        <select class="form-select" name="payment_status" id="payment_status">


                                            <option value="2" @if ($order->payment_status == 2) selected @endif>
                                                {{ translate('Paid') }}</option>
                                            <option value="1" @if ($order->payment_status == 1) selected @endif>
                                                {{ translate('Unpaid') }}</option>
                                        </select>
                                        <div class="form-group mt-2">
                                            <textarea name="payment_note" placeholder="Write short note" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-lg-6">
                                        <label for="status" class="form-label">{{ translate('Delivery Status') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" name="status" id="status">


                                            @foreach (App\Models\Order::delevaryStatus() as $status => $value )

                                            <option {{$order->status ==  $value ? 'selected' : '' }} value="{{$value}}">
                                                {{
                                                   ucfirst( $status)
                                                }}
                                             </option>
                                                
                                            @endforeach

                                        </select>
                                        <div class="form-group mt-2">
                                            <textarea name="delivery_note" placeholder="Write short note" class="form-control"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit"
                                    class="btn btn-success btn-xl fs-6 px-4 text-light mb-4">{{ translate('Save') }}</button>
                            </form>

                            @foreach ($orderStatus as $status)

                                <div class="row">
                                    <div class="col-lg-6">
                                        <ul class="list-group">
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <p class="d-block pmd-list-subtitle mb-0">{{ translate('Note') }} :
                                                        {{ $status->payment_note }}</p>
                                                    <span
                                                        class="text-muted fs-12">{{ $status->created_at->format('d-m-Y') }}</span>
                                                </div>
                                                <span
                                                    class="badge  bg-{{ $status->payment_status == 1 || !$status->payment_status ? 'danger' : 'success' }}">
                                                    {{ $status->payment_status == 1 || !$status->payment_status ? 'Unpaid' : 'paid' }}

                                                </span>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-lg-6">
                                        <ul class="list-group">
                                            <li
                                                class="list-group-item d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <p class="d-block pmd-list-subtitle mb-0">{{ translate('Note') }} :
                                                        {{ $status->delivery_note }}</p>
                                                    <span
                                                        class="text-muted fs-12">{{ $status->created_at->format('d-m-Y') }}</span>
                                                </div>


                                                @php echo order_status_badge($status->delivery_status)  @endphp
                                             
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-xl-3">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex">
                                <h5 class="card-title flex-grow-1 mb-0">
                                    {{ translate('Customer Details') }}
                                </h5>
                            </div>
                        </div>

                        <div class="card-body">

                             @php
                                   $customerName = @$order->customer->name ?? @$order->billing_information->first_name;

                                   $email = @$order->customer->email ?? @$order->billing_information->email;

                                   $phone = @$order->customer->phone ?? @$order->billing_information->phone ;
                                   if(@$order->billingAddress){
                                      $email = @$order->billingAddress->email;
                                      $phone = @$order->billingAddress->phone;
                                      $customerName  = @$order->billingAddress->first_name;
                                   }
                             @endphp
                            <ul class="list-unstyled mb-0 vstack gap-3">
                                <li>
                                    <div class="d-flex align-items-center">
                                        @if($order->customer)
                                            <div class="flex-shrink-0">
                                                <img src="{{ show_image(file_path()['profile']['user']['path'] . '/' . @$order->customer->image, file_path()['profile']['user']['size']) }}"
                                                    alt="{{ @$order->customer->name }}" class="avatar-sm rounded">
                                            </div>
            
                                        @endif
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="fs-14 mb-1">
                                                {{ @$customerName }}
                                            </h6>
                                            <p class="text-muted mb-0">
                                                {{ translate('Customer') }}
                                            </p>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <i class="ri-mail-line me-2 align-middle text-muted fs-16 text-break"></i>
                                    <span class="text-break">
                                        {{ @$email }}
                                    </span>
                                </li>

                                <li>
                                    <i class="ri-phone-line me-2 align-middle text-muted fs-16"></i>
                                    <span
                                        class="text-break">{{ @$phone }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="d-flex">
                                <h5 class="card-title flex-grow-1 mb-0">
                                    {{ translate('Payment Information') }}
                                </h5>
                            </div>
                        </div>

                        <div class="card-body">
                            <ul class="list-unstyled mb-0 vstack gap-3">

                                <li>
                                    <span class="font-weight-bold text-break"> {{translate('Payment status')}} :

                                        @if ($order->payment_status == App\Models\Order::UNPAID)
                                            <span class="badge badge-soft-danger">{{ translate('Unpaid') }}</span>
                                        @elseif($order->payment_status == App\Models\Order::PAID)
                                            <span class="badge badge-soft-success">{{ translate('Paid') }}</span>
                                        @endif
                                    </span>
                                </li>

                                <li>
                                    <span class="font-weight-bold text-break"> {{translate('Payment VIA')}} :
                                        @if($order->wallet_payment == App\Models\Order::WALLET_PAYMENT)
                                                
                                            {{ translate('Payment VIA Wallet')}}

                                        @else
                                            @if ($order->payment_type == '2')
                                                {{ @$order->paymentMethod ? $order->paymentMethod->name : 'N/A' }}
                                            @else
                                                {{ translate('Cash On Delivary') }}
                                            @endif
                                        @endif
                                    </span>
                                </li>


                                @if ($order->payment_details)
                                    @foreach ($order->payment_details as $key => $value)
                                        <li>
                                            <span class="font-weight-bold text-break">{{ k2t($key) }} :
                                                {{ $value }}</span>
                                        </li>
                                    @endforeach
                                    @endif
                            </ul>
                        </div>
                    </div>

                 
                    @if (@$order->billingAddress)
                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                    <i class="ri-map-pin-line align-middle me-1 text-muted"></i>
                                    {{ translate('Billing Address') }}
                                </h5>
                            </div>

                            <div class="card-body">
                                <ul class="list-unstyled vstack gap-2 fs-13 mb-0">

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('Address') }} :
                                            {{ $order->billingAddress->address->address }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('City') }} :
                                            {{ $order->billingAddress->city->name }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('Email') }} :
                                            {{ $order->billingAddress->email }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('First Name') }} :
                                            {{ $order->billingAddress->first_name }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('Last Name') }} :
                                            {{ $order->billingAddress->last_name }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('Phone') }} :
                                            {{ $order->billingAddress->phone }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('State') }} :
                                            {{ $order->billingAddress->state->name }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('Zip') }} :
                                            {{ $order->billingAddress->zip }}</span>
                                    </li>

                                    <li>
                                        <span class="font-weight-bold text-break">{{ translate('Country') }} :
                                            {{ $order->billingAddress->country->name }}</span>
                                    </li>

                                </ul>
                                @if (@$order->billingAddress->address->latitude && @$order->billingAddress->address->longitude)
                                    <div class="rounded w-100 h-200 mt-4" id="gmap-site-address"></div>
                                @endif
                            </div>
                        </div>
                    @elseif(@$order->billing_information)
                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                    <i class="ri-map-pin-line align-middle me-1 text-muted"></i>
                                    {{ translate('Billing Address') }}
                                </h5>
                            </div>

                            <div class="card-body">
                                <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                                    @foreach (@$order->billing_information as $key => $value)
                                        <li>
                                            <span class="font-weight-bold text-break">{{ k2t($key) }} :
                                                {{ $value }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if(@$order->shipping)
                        <div class="card">
                            <div class="card-header border-bottom-dashed">
                                <h5 class="card-title mb-0">
                                    <i class="ri-map-pin-line align-middle me-1 text-muted">
                                    </i>
                                    {{ translate('Shipping Address') }}
                                </h5>
                            </div>

                            <div class="card-body">
                                <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                                    <li>
                                      {{translate("Carrier")}} :  <span>{{ @$order->shipping->name }}</span>
                                    </li>
                                
                                    <li>
                                        {{translate("Duration")}} :  <span class="font-weight-bold">{{ @$order->shipping->duration }}
                                            {{ translate('Days') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateorderstatus" tabindex="-1" aria-labelledby="updateorderstatus"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.inhouse.order.product.status.update') }}" method="post">
                @csrf
                <input type="hidden" name="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translate('Status Update') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ostatus" class="form-label">{{ translate('Status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="ostatus">
                             
                                @foreach (App\Models\Order::delevaryStatus() as $status => $value )

                                    <option value="{{$value}}">
                                        {{
                                        ucfirst( $status)
                                        }}
                                    </option>
                                    
                                @endforeach
                            </select>
                        </div>
                        <div class="status_html"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="deliveryOffcanvas" aria-labelledby="deliveryOffcanvasLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="deliveryOffcanvasLabel">{{ translate('Delivery Man Assign') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">

            @php
                 $deliveryData = $order->deliveryManOrder;
            @endphp

            <div>
                <form action="{{ route('admin.order.assign') }}" method="POST">
                    @csrf

                    <input type="hidden" name="id" value="{{ $order->id }}">

                    <div id="deliveryman-assign">
                        <div class="mb-3">
                            <label for="delivery_man_id"
                                class="form-label">{{ translate('Delivery Man') }}
                                <span class="text-danger">*</span></label>
                            <select @if($order->status  == App\Models\Order::DELIVERED) disabled @endif class="form-select" name="delivery_man_id" id="delivery_man_id"
                                required>

                                <option disabled selected value="">
                                    {{ translate('Select a deliveryman') }}</option>

                                @foreach ($deliverymen as $deliveryman)
                                    <option
                                        {{ $deliveryman->id == @$deliveryData->deliveryman_id ? 'selected' : '' }}
                                        value={{ $deliveryman->id }}>
                                        {{ $deliveryman->first_name }}</option>
                                @endforeach

                            </select>
                        </div>

    
                        <div class="mb-3">
                            <label for="pickup_address"
                                class="form-label">{{ translate('Pickup address') }} <span class="text-danger">*</span>
                            </label>


                            <textarea @if($order->status  == App\Models\Order::DELIVERED) readonly @endif name="pickup_address" class="form-control"  required  placeholder="{{translate('Enter pickup address')}}  "  id="note" cols="4" rows="4">{{@$deliveryData->pickup_location}}</textarea>

                        </div>

              
                        <div class="mb-3"
                            id="deliveryManCharge">
                            <label for="delivery_man_charge"
                                class="form-label">{{ translate('Delivery Fee') }}
                            <i  data-bs-toggle="tooltip" data-bs-placement="top" title="{{translate('Deliveryman charge (this will goes into admin expenses)')}}" class="ri-information-line link-danger "></i>
              
                            </label>

                            <input @if($order->status  == App\Models\Order::DELIVERED) readonly @endif   value="{{round(@$deliveryData->amount,site_settings("digit_after_decimal",2))}}" type="number"
                                class="form-control" id="delivery_man_charge" name="delivery_man_charge"
                                placeholder="{{ translate('Enter an Amount') }}">
                        </div>

                        <div class="mb-3">
                            <label for="note"
                                class="form-label">{{ translate('Note') }}
                            </label>


                            <textarea    @if($order->status  == App\Models\Order::DELIVERED) readonly @endif name="note" class="form-control"  placeholder="{{translate('Enter note')}}"  id="note" cols="4" rows="4">{{@$deliveryData->note}}</textarea>


                        </div>

                        

                    </div>

                    @if($order->status  != App\Models\Order::DELIVERED)
  
                       <button type="submit"
                        class="btn btn-md btn-primary">{{ translate('Assign') }}</button>
                    @endif
                </form>
            </div>
     



            @if(@$deliveryData->time_line)
              <hr>

              <div class="mt-4">

                 <h5>
                     {{translate("Timeline")}}
                 </h5>
                <div data-simplebar class="timeline-log">

                  
                    <div class="acitivity-timeline acitivity-main">

                        @foreach (@$deliveryData->time_line as $key =>  $timeLine)

                            <div class="acitivity-item d-flex mb-1">
                                <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                    <div class="avatar-title bg-success-subtle text-success rounded-circle material-shadow">
                                        <i class="ri-shopping-cart-2-line"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 lh-base">
                                        {{k2t($key) }}
                                    </h6>
                                    <p class="text-muted mb-1">
                                        {{ $timeLine->details }}
                                    </p>
                                    <small class="mb-0 text-muted">
                                        {{diff_for_humans($timeLine->time)}}
                                    </small>
                                </div>
                            </div>
                            
                        @endforeach
              
             
                    </div>
    
                </div>

              </div>

            @endif
       

         
        </div>
    </div>

@endsection

@push('script-push')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ site_settings('gmap_client_key') }}&callback=loadGmap&libraries=places&v=3.49"
        defer></script>

    <script>

        if("{{@$order->billingAddress->address->latitude && @$order->billingAddress->address->longitude}}"){
            loadGmap()
        }

        function loadGmap() {


            try {

                var latitude = isNaN(parseFloat("{{ @$order->billingAddress->address->latitude}}")) ? 33.14751827254395 : parseFloat(
                        "{{ @$order->billingAddress->address->latitude}}");

            var longitude = isNaN(parseFloat("{{ @$order->billingAddress->address->longitude}}")) ? 73.7561387589157 : parseFloat("{{ @$order->billingAddress->address->longitude}}");



            var mapConfig = {
                lat: latitude,
                lng: longitude
            };

            const map = new google.maps.Map(document.getElementById("gmap-site-address"), {
                center: {
                    lat: latitude,
                    lng: longitude
                },
                zoom: 13,
                mapTypeId: "roadmap",
            });

            var marker = new google.maps.Marker({
                position: mapConfig,
                map: map,
            });

            marker.setMap(map);
            var geocoder = geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function(mapsMouseEvent) {

                var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                var coordinates = JSON.parse(coordinates);
                var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                marker.setPosition(latlng);
                map.panTo(latlng);

                document.getElementById('latitude').value = coordinates['lat'];
                document.getElementById('longitude').value = coordinates['lng'];

                geocoder.geocode({
                    'latLng': latlng
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            document.getElementById('address').value = results[1].formatted_address;
                        }
                    }

                });
            });

            const input = document.getElementById("map-input");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });
            let markers = [];
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }
                markers.forEach((marker) => {
                    marker.setMap(null);
                });
                markers = [];
                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) {
                        return;
                    }
                    var mrkr = new google.maps.Marker({
                        map,
                        title: place.name,
                        position: place.geometry.location,
                    });

                    google.maps.event.addListener(mrkr, "click", function(event) {


                        document.getElementById('latitude').value = this.position.lat();
                        document.getElementById('longitude').value = this.position.lng();

                    });

                    markers.push(mrkr);

                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
                
            } catch (error) {
                
            }

            
        };


        (function($) {
            "use strict";
            $('.orderstatus').on('click', function() {
                var modal = $('#updateorderstatus');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('select[name=status]').val($(this).data('status'));
                modal.modal('show');
            });

          
        })(jQuery);
    </script>
@endpush
