
@extends('frontend.layouts.error')
@push('stylepush')

    <style>
            .captcha-default {
                max-width: 170px;
                width: 100%;
            }
            .refresher{
                width: 60px;
                height: 60px;
                font-size: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                background: #dec714;
                color: #111;
                border-radius: 8px;
            }
            .submit {
                --btn-bg:red;
                z-index: 1;
                padding: 15px 30px;
                font-size: 1.6rem;
                font-weight: 600;
                text-transform: capitalize;
                color: #fff;
                background-color: var(--btn-bg);
                line-height: 1;
                border: 0.1rem solid  var(--btn-bg);
                overflow: hidden;
                white-space: nowrap;
                display: flex;
                -webkit-box-align: center;
                -ms-flex-align: center;
                align-items: center;
                border-radius: 4px;
            }

            .form-control {
                padding: 12px;
                font-size: 16px;
            }
                    
            .form-control:focus {
                border-color: var(--bs-border-color);
                outline: 0;
                box-shadow: unset;
            }
    </style>

@endpush
@section('content')
<section class="search-ticket pt-100 pb-100 d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card search-ticket-card card-effect">
                    <div class="card-body p-lg-5 p-4">
                        <div>
                            <h3 class="mb-5 fs-1 text-center">
                                {{translate("Verify Yourself")}}
                            </h3>
                            <form action="{{route('dos.security.verify')}}" method="POST">
                                @csrf
                                <div class="mb-5">                
                                    <a id='genarate-captcha' class="d-flex align-items-center justify-content-center gap-3">
                                        <img class="captcha-default pe-2 pointer" src="{{ route('captcha.genarate',1) }}" id="default-captcha">

                                        <i class="las la-redo-alt  refresher lh-1"></i>
                                    </a>
                                </div>

                                <div class="mb-5">
                                    <label for="captcha" class="form-label">
                                        {{translate("Captcha code")}} <span class="text-danger" >*</span>
                                    </label>
                                    <input required type="text" name="captcha" id="captcha" required   class="form-control" id="captcha" placeholder="{{translate('Enter captcha code')}}">
                                </div>

                                <button class="submit" type="submit">
                                    <span>{{translate("Verify")}}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scriptpush')

<script>
    'use strict'

    $(document).on('click','#genarate-captcha',function(e){
        var url = "{{ route('captcha.genarate',[":randId"]) }}"
        url = (url.replace(':randId',Math.random()))
        document.getElementById('default-captcha').src = url;
        e.preventDefault()
    })
</script>

@endpush