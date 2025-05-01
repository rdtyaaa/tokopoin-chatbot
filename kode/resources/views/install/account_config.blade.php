@extends('install.layouts.master')
@section('content')

    <div class="installer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    <div class="installer-wrapper bg--light">
                        <div class="i-card-md">
                        <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="text-center mb-5">
                                        <h5 class="title">
                                            {{trans("default.account_setup_title")}}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        <form action="{{route('install.account.setup')}}" method="post">
                          @csrf
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="username">
                                            Username
                                        </label>
                                        <input name="username" value="{{old('username')}}" type="text" id="username" placeholder="Enter your username">
                                    </div>
                                </div>
    
                                <div class="col-lg-6">
                                    <div class="form-inner">
                                        <label for="email">
                                            Email
                                        </label>
                                        <input name="email"   value="{{old('email')}}" type="email" id="email" placeholder="Enter your email">
                                    </div>
                                </div>
    
                                <div class="col-lg-12">
                                    <div class="form-inner">
                                        <label for="password">
                                            Password <span class="text-danger">Min :5</span>
                                        </label>
                                        <input name="password" value="{{old('password')}}"  type="text" id="password" placeholder="Enter your password">
                                    </div>
                                </div>
                            </div>
                    
                            <div class="text-center mt-4">
                                <button  class="i-btn btn--lg btn--primary"> 
                                    {{trans("default.btn_next")}} <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection