@extends('frontend.layouts.app')
@section('content')

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

<section>
    <div class="Container">
        <div class="row g-4">
            <div    @if($order->custom_information) class="col-xl-9 col-lg-8" @else class="col-12" @endif >
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                    <h4 class="card-title">
                                        {{translate("Ordar Details
                                        ")}}
                                    </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-nowrap align-middle">
                                <thead class="table-light">
                                    <tr class="text-muted fs-14">

                                        <th scope="col" class="text-start">
                                            {{translate("Product")}}
                                        </th>
                                        <th scope="col" class="text-center">
                                            {{translate("Qty")}}
                                        </th>

                                        <th scope="col" class="text-center">
                                            {{translate("Attribute")}}
                                        </th>

                                        <th scope="col" class="text-center">
                                            {{translate("Total Price")}}
                                        </th>
                                        <th scope="col" class="text-center">
                                            {{translate('Status')}}
                                        </th>

                                    </tr>
                                </thead>

                                <tbody class="border-bottom-0">
                                    <tr>
                                        <td>
                                            <div class="wishlist-product align-items-center">
                                                <div class="wishlist-product-img">
                                                    <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$orderDetail->product->featured_image)}}" alt="{{$orderDetail->product->name}}">
                                                </div>

                                                <div class="wishlist-product-info">
                                                    <h4 class="product-title">{{$orderDetail->product->name}}</h4>

                                                    <div class="ratting mb-0">
                                                        @php echo show_ratings($orderDetail->product->review->avg('ratings')) @endphp
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center">
                                            {{$orderDetail->quantity}}
                                        </td>

                                        <td class="text-center">
                                            {{@$digitalProductAttributes?$digitalProductAttributes->name : "N/A"}}
                                        </td>

                                        <td class=" text-center">
                                            {{short_amount($orderDetail->total_price)}}
                                        </td>
                                        <td class=" text-center">
                                            @php echo order_status_badge($orderDetail->status)  @endphp
                                        </td>

                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>

            @if($order->custom_information)
                <div class="col-xl-3 col-lg-4">
            
                
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                    <h4 class="card-title">
                                        {{translate("Custom information
                                        ")}}
                                    </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <ul>
                        
                        
                                @foreach($order->custom_information as $key => $value)
                                    <li>
                                        {{$key}} :
                                    
                                        <span class="text-break">
                                                @if(is_array($value))
                                                    {{ implode(",", $value)}}
                                                @else
                                                {{$value}}
                                                @endif
                                        </span>
                                    </li>
                                @endforeach
                        </ul>
                        
                    </div>

                </div>

                </div>
           @endif

        </div>
    </div>
</section>


@endsection

