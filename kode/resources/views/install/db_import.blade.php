@extends('install.layouts.master')
@section('content')
    <div class="installer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">

                    @include('install.partials.progress_bar')
            
                    <div class="installer-wrapper bg--light">
                        <div class="i-card-md">
                            <div class="p-4 bg--danger-light mb-4">
                                <p class="text--dark"><span class="bg--danger text-white py-0 px-2 d-inline-block me-2">Warning  :</span>  
                                        @php echo trans('default.dbimport_warning')  @endphp
                                </p>
                            </div>
                            <div class="row g-md-4 g-3  justify-content-center">
                                <div class="col-md-6 text-center  ">
                                     <div class="d-flex gap-2  justify-content-center">
                                        <a href="{{route('install.db.import.store',['force'=> false,'verify_token' => bcrypt(base64_decode('ZGJfaW1wb3J0'))])}}"  class="i-btn btn--lg  btn--success btn--primary"> 
                                            {{trans('default.btn_import')}}
                                             <i class="ms-2 bi bi-database"></i>
                                         </a>
                                         <a href="{{route('install.db.import.store',['force'=> true,'verify_token' => bcrypt(base64_decode('ZGJfaW1wb3J0'))])}}"  class="i-btn btn--lg btn--danger btn--primary"> 
                                            {{trans('default.btn_force_import')}}  <i class="ms-2 bi bi-database-down"></i>
                                         </a>
                                     </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection