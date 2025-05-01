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
                                    translate("Wihdraw Methods")
                                }}
                            </h4>

                            <a href="{{route('user.withdraw.list')}}" class="btn btn-lg btn-success ">
                                {{translate('Wihdraw list')}}
                            </a>

                        </div>

                        <div class="card-body">

                 
            
                            <div class="row g-4">

                                @foreach ($methods as $method )
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
                                @endforeach
                          

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="methodModal" tabindex="-1" aria-labelledby="methodModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" >{{translate('Withdraw Now')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close" id="close-modal"></button>
                </div>
                <form action="{{route('user.withdraw.request')}}" method="POST">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="modal-body">
                        <div>
                            <input type="text" class="form-control" value="{{old('amount')}}" name="amount" placeholder="{{translate('Enter amount')}}">
        
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-md btn-danger " data-bs-dismiss="modal">{{translate('Close')}}</button>
                        <button type="submit" class="btn btn-md btn-success ">{{translate('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


</section>



@endsection



@push('scriptpush')
<script>
	"use strict";
	$('.withdrawmethod').on('click', function(){
		var modal = $('#methodModal');
		modal.find('input[name=id]').val($(this).data('id'));
		modal.modal('show');
	});
</script>
@endpush




