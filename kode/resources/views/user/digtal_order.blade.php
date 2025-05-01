@extends('frontend.layouts.app')
@section('content')
   @php
     $promo_banner = frontend_section('promotional-offer');
   @endphp

<div class="breadcrumb-banner">
    <div class="breadcrumb-banner-img">
        <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($breadcrumb->value,'image'),@frontend_section_data($breadcrumb->value,'image','size'))}}" alt="breadcrumb.jpg">
    </div>
    <div class="page-Breadcrumb">
        <div class="Container">
            <div class="breadcrumb-container">
                <h1 class="breadcrumb-title">{{($title)}}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">
                            {{translate('home')}}
                        </a></li>

                        <li class="breadcrumb-item active" aria-current="page">
                            {{translate($title)}}
                        </li>

                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="pb-80">
    <div class="Container">
        <div class="row g-4">
            @include('user.partials.dashboard_sidebar')

            <div class="col-xl-9 col-lg-8">
                <div class="profile-user-right">
                    <a href="{{@frontend_section_data($promo_banner->value,'image','url')}}" class="d-block">
                        <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($promo_banner->value,'image'),@frontend_section_data($promo_banner->value,'image','size'))}}" alt="banner.jpg">
                    </a>

                    <div class="card mt-5">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                                {{translate('Digital Orders')}}
                                        </h4>
                                    </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-nowrap align-middle">
                                    <thead class="table-light">
                                        <tr class="text-muted fs-14">
                                            <th scope="col" class="text-start">{{translate('Order Number')}}</th>
                                            <th scope="col" class="text-start">{{translate('Place On')}}</th>
                                            <th scope="col" class="text-center">{{translate('Payment Status')}}</th>
                                            <th scope="col" class="text-center">{{translate('Total Price')}}</th>
                                            <th scope="col" class="text-end">{{translate('Action')}}</th>
                                            </tr>
                                        </tr>
                                    </thead>

                                    <tbody class="border-bottom-0">
                                        @forelse($digtal_orders as $order)
                                            <tr class="fs-14 tr-item">
                                                <td class="text-start">
                                                    <a href="{{route('user.digital.order.details', $order->order_id)}}"    class="badge-soft-primary py-1 px-2">{{$order->order_id}}
                                                    </a>
                                                </td>

                                                <td class="text-start">
                                                    {{get_date_time($order->created_at)}}
                                                </td>

                                                <td class="text-center">
                                                    @if($order->payment_status == 2)
                                                        <span  class="badge badge-soft-success">{{translate('Paid')}}</span>
                                                    @elseif($order->payment_status == 1)
                                                        <span class="badge badge-soft-danger" >{{translate('Unpaid')}}</span>
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    {{short_amount($order->amount)}}
                                                </td>

                                                <td class="text-end">
                                                    <div class="d-flex align-items-center gap-3 justify-content-end">
                                                        @if($order->payment_status == '1' && $order->status != '6')
                                                            <a href="javascript:void(0)" class="order--cancel badge badge-soft-danger fs-12 pointer" data-bs-toggle="modal" data-bs-target="#deleteOrder" data-id="{{$order->id}}">
                                                            <i class="fa-solid fa-trash"></i></a>
                                                        @endif

                                                        <a href="{{route('user.digital.order.details', $order->order_id)}}"    class="badge badge-soft-info fs-12 pointer"><i class="fa-regular fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr >
                                                <td class="text-center text-muted py-5" colspan="5"><p>{{translate('No Data Found')}}</p></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 d-flex align-items-center justify-content-end">
                                    {{$digtal_orders->links()}}

                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</section>



@endsection



