@extends('install.layouts.master')
@section('content')

    <div class="installer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    @include('install.partials.progress_bar')
                    <div class="installer-wrapper bg--light">
                        <div class="i-card-md">
                            <div class="row justify-content-center">
                                    <div class="col-lg-8">
                                        <div class="text-center mb-5">
                                            <h5 class="title">
                                                {{trans("default.finished_note")}}
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-4">
                                    <div class="col-12 text-center mb-4">
                                        <div class="svg-container">    
                                            <svg class="ft-green-tick" xmlns="http://www.w3.org/2000/svg" height="100" width="100" viewBox="0 0 48 48" aria-hidden="true">
                                                <circle class="circle" fill="#5bb543" cx="24" cy="24" r="22"/>
                                                <path class="tick" fill="none" stroke="#FFF" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" d="M14 27l5.917 4.917L34 17"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="icon">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <div class="content">
                                                <p>Username : admin</p>
                                                <i class="bi bi-shield-check text--success"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="icon">
                                                <i class="bi bi-shield-lock"></i>
                                            </div>
                                            <div class="content">
                                                <p>password : 123123</p>
                                                <i class="bi bi-shield-check text--success"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-4">
                        <a href="{{route('admin.dashboard')}}"  class="i-btn btn--md btn--primary">
                            <i class="bi bi-house-door me-2"></i> {{trans("default.browser_home")}} 
                        </a>
                    </div>
            </div>
        </div>
    </div>

@endsection

@push('script-push')
<script>
  "use strict"

    var path = document.querySelector(".tick");
    var length = path.getTotalLength();

  </script>
@endpush
