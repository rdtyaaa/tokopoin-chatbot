@extends('install.layouts.master')
@section('content')
    <div class="installer-section">
        <div class="container ">
            <div class="row justify-content-center">
                <div class="col-lg-11">
                    @include('install.partials.progress_bar')
                    <form action="{{route('install.db.store')}}" method="post">
                        @csrf
                        <div class="installer-wrapper bg--light">
                            <div class="each-slide">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="db_host">
                                                Database Host
                                            </label>
                                            <input name="db_host" value="localhost" type="text" id="db_host" placeholder="Ex:localhost">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="db_port">
                                                Database Port
                                            </label>
                                            <input name="db_port"  value="3306" type="number" id="db_port" placeholder="Database port">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-inner">
                                            <label for="db_port">
                                                Database name
                                            </label>
                                            <input name="db_database" value="{{old('db_database')}}"  type="text" id="db_database" placeholder="Database Name">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-inner">
                                            <label for="db_username">
                                                Database Username
                                            </label>
                                            <input name="db_username" value="{{old('db_username')}}" type="text" id="db_username" placeholder="Database Username">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-inner auth-input">
                                        <span class="auth-input-icon toggle-password">
                                            <i class="bi bi-eye toggle-icon "></i>
                                        </span>

                                            <label for="db_password">
                                                Database Password
                                            </label>
                                            <input name="db_password" class="toggle-input" value="{{old('db_password')}}" type="password" id="db_password" placeholder="Database Password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <div class="d-flex gap-2 justify-content-end">
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