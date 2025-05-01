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

                        <div class="card-header d-flex justify-content-between">
                            <h4 class="card-title">
                                {{
                                    translate("Wihdraw Request")
                                }}
                            </h4>

                            <a href="{{route('user.withdraw.list')}}" class="btn btn-lg btn-success ">
                                {{translate('Wihdraw list')}}
                            </a>

                        </div>

                        <div class="card-body">

    
                            <div class="row  g-4">
                                <div class="col-xl-4">
                                    <ul class="list-group list-group-flush">
        
                                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">

                                                <div class="ms-2 me-auto">{{translate('Method')}}</div>
                                                <span>{{optional($withdraw->method)->name}}</span>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                                <div class="ms-2 me-auto">{{translate('Amount')}}</div>
                                                <span>
                                                    {{show_amount($withdraw->amount,default_currency()->symbol) }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                                <div class="ms-2 me-auto">{{translate('Withdraw Charge')}}</div>
                                                <span>
                                                
                                                    {{show_amount($withdraw->charge,default_currency()->symbol) }}
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                                <div class="ms-2 me-auto">{{translate('Conversion Rate')}}</div>
                                                <span>1 {{default_currency()->name}} = {{round(($withdraw->rate))}} {{($withdraw->currency->name)}}</span>
                                            </li>
                                            <li class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                                <div class="ms-2 me-auto">{{translate('Final Amount')}}</div>
                                                <span>
                                                        {{
                                                        show_amount($withdraw->final_amount,$withdraw->currency->symbol)
                                                        }}
                                                
                                                </span>
                                            </li>
                                        </ul>
                                </div>
        
                                <div class="col-xl-8">
                                    <form action="{{route('user.withdraw.preview.store', $withdraw->id)}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="shadow-lg p-3 mb-5 bg-body rounded">
                                            <h3>{{translate('Withdraw Information')}}</h3><hr>
                                            <div class="row">
                                                @if($withdraw->method->user_information)
                                                    @foreach($withdraw->method->user_information as $key => $value)
                                                        @if($value->type == "text")
                                                            <div class="mb-3">
                                                                <label for="{{$key}}" class="form-label">{{($value->data_label)}} <span class="text-danger">*</span></label>
                                                                <input type="text" name="{{$key}}" id="{{$key}}" class="form-control" value="{{old($key)}}" placeholder="{{($value->data_label)}}" required>
                                                            </div>
                                                        @elseif($value->type == "file")
                                                            <div class="mb-3">
                                                                <label for="{{$key}}" class="form-label">{{($value->data_label)}} <span class="text-danger">*</span></label>
                                                                <input type="file" name="{{$key}}" id="{{$key}}" class="form-control" value="{{old($key)}}" placeholder="{{($value->data_label)}}" required>
                                                            </div>
                                                        @elseif($value->type == "textarea")
                                                            <div class="mb-3">
                                                                <label for="{{$key}}" class="form-label">{{($value->data_label)}} <span class="text-danger">*</span></label>
                                                                <textarea name="{{$key}}" id="{{$key}}" class="form-control" placeholder="{{($value->data_label)}}" required></textarea>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                            <div class="text-start">
                                                <button type="submit" class="btn btn-sm btn-success ">{{translate('Submit')}}</button>
                                            </div>
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        

                                {{-- @foreach ($methods as $method )
                                    <div class="col-xl-6 ">
                                        <div class="method-card rounded">
                                            <div class="card-header border-bottom-dashed p-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h5 class="mb-0 fs-14">
                                                        {{$method->name}}
                                                    </h5>
                                                </div>
                                            </div>

                                            <div class="row g-0">
                                                <div class="col-lg-5">
                                                    <div class="card-body h-100">
                                                        <div class="cardImageContainer">
                                                            <img src="{{show_image(file_path()['withdraw']['path'].'/'.$method->image,file_path()['withdraw']['size'])}}" class="card-img-top img-fluid img-thumbnail p-2" alt="{{ $method->image }}">
                                                        </div>

                                                        <div class="text-center plan-btn mt-4">
                                                            <button class="btn btn-md btn-success withdrawmethod" data-bs-toggle="modal"  data-id="{{$method->id}}" data-bs-target="#methodModal">
                                                                 {{translate('Withdraw Now')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-7">
                                                    <div class="p-4">
                                                        <div class="p-3 bg-light">
                                                            <h5 class="fs-15 mb-0"> {{translate('Method Details')}} :</h5>
                                                        </div>

                                                        <div class="pt-3">
                                                            <ul class="list-unstyled vstack gap-3 mb-0">
                                                                <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2 px-2 fs-14">
                                                                    <div class="me-auto">{{translate('Withdraw Limit')}}</div>
                                                                    <span>{{round(($method->min_limit))}} - {{round(($method->max_limit))}} {{(default_currency()->name)}}</span>
                                                                </li>

                                                                <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2 px-2 fs-14">
                                                                    <div class="me-auto">{{translate('Charge')}}</div>
                                                                    <span>{{round(($method->fixed_charge))}} {{(default_currency()->name)}} + {{round(($method->percent_charge))}} %</span>
                                                                </li>

                                                                <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2 px-2 fs-14">
                                                                    <div class="me-auto">{{translate('Currency')}}</div>
                                                                    <span>{{($method->currency->name)}} </span>
                                                                </li>

                                                                <li class="list-group-item d-flex justify-content-between align-items-start flex-wrap gap-2 px-2 fs-14">
                                                                    <div class="me-auto">{{translate('Processing Time')}}</div>
                                                                    <span>{{$method->duration}} {{translate('Hour')}}</span>
                                                                </li>                                                    
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach --}}
                          

                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





</section>



@endsection






