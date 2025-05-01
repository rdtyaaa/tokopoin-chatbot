@extends('install.layouts.master')
@section('content')
    <div class="installer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">

                    @include('install.partials.progress_bar')
            
                    <div class="installer-wrapper bg--light">
                        <div class="i-card-md">
                            <div class="row g-md-4 g-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="icon">
                                            <i class="bi bi-database"></i>
                                        </div>
                                        <div class="content">
                                            <p>{{trans("default.init_dbname")}}</p>
                                            <i class="bi bi-shield-fill-check text--success"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="icon">
                                            <i class="bi bi-database-lock"></i>
                                        </div>
                                        <div class="content">
                                            <p>{{trans("default.init_dbpassword")}}</p>
                                            <i class="bi bi-shield-fill-check text--success"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="icon">
                                            <i class="bi bi-database-check"></i>
                                        </div>
                                        <div class="content">
                                            <p>{{trans("default.init_dbusername")}}</p>
                                            <i class="bi bi-shield-fill-check text--success"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="icon">
                                            <i class="bi bi-database-gear"></i>
                                        </div>
                                        <div class="content">
                                            <p>{{trans("default.init_dbhost")}}</p>
                                            <i class="bi bi-shield-fill-check text--success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>        
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <div class="text-end">
                            <a href="{{route('install.requirement.verification',['verify_token' => bcrypt(base64_decode('cmVxdWlyZW1lbnRz'))])}}"  class="ai--btn i-btn btn--md btn--primary"> 
                                {{trans("default.btn_next")}} <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
@endsection