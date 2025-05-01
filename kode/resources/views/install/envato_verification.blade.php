@extends('install.layouts.master')
@section('content')

    <div class="installer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    @include('install.partials.progress_bar')
                    <form action="{{route('install.purchase.code.verification')}}" method="post">
                     @csrf
                        <div class="installer-wrapper bg--light">
                            <div class="i-card-md">
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="username">
                                                Envato Username
                                            </label>
                                            <input name="username" value="{{old('username')}}" type="text" id="username" placeholder="Username">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="purchase_code">Purchase Code</label>
                                            <input type="text" name="purchase_code" id="purchase_code" value="{{old('purchase_code')}}" placeholder="Purchase code">
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </div>
                        <div class="text-center mt-4">
                            <div class="d-flex gap-2 justify-content-between">

                                <a href="{{route('install.requirement.verification',['verify_token' => bcrypt(base64_decode('cmVxdWlyZW1lbnRz'))])}}"  class="ai--btn i-btn btn--md btn--primary"> 
                                    <i class="bi bi-arrow-left me-2"></i>{{trans("default.btn_previous")}}
                                </a>
                                
                                <button  class="ai--btn i-btn btn--md btn--primary"> 
                                    {{trans("default.btn_next")}} <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>    
            </div>    
        </div>    
    </div>    

@endsection