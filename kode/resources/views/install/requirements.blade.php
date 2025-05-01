@extends('install.layouts.master')
@section('content')

<div class="installer-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-11">

                    @php
                        $flag = true;
                    @endphp

                    @include('install.partials.progress_bar')
                  
                    <div class="installer-wrapper bg--light">
                        <div class="i-card-md">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="list-header">
                                        <h6>
                                            Server Requirements
                                        </h6>
                                    </div>
                                    <ul class="permission-list">
                                        @foreach(Arr::get($requirements,'requirements',[]) as $type => $requirement)
                                            <li class="list {{ @$phpSupportInfo['supported'] ? 'list-success' : 'list-error' }}">
                                                <div>
                                                    <strong>{{ ucfirst($type) }}</strong>
                                                    @if($type == 'php')
                                                    <span class="mx-2">(minimum version {{ $phpSupportInfo['minimum'] }} required)</span>
                                                    <span> {{ $phpSupportInfo['current'] }}</span>
                                                </div>

                                                    @if(@$phpSupportInfo['supported'])
                                                      <i class="bi bi-check-circle-fill i-success"></i>
                                                    @else
                                                            @php
                                                               $flag = false;
                                                            @endphp
                                                       <i class="bi bi-exclamation-circle-fill i-danger"></i>
                                                    @endif
                                                @endif
                                            </li>
                                            @foreach($requirements['requirements'][$type] as $extention => $enabled)
                                                <li class="list {{ $enabled ? 'list-success' : 'list-error' }}">
                                                    {{ $extention }}

                                                    @if($enabled)
                                                       <i class="bi bi-check-circle-fill i-success"></i>
                                                    @else
                                                            @php
                                                                $flag = false;
                                                            @endphp
                                                       <i class="bi bi-exclamation-circle-fill i-danger"></i>
                                                    @endif
                                                </li>
                                            @endforeach
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <div class="list-header">
                                        <h6>
                                            File Permissions
                                        </h6>
                                    </div>
                                    <ul class="permission-list">
                                        @foreach($permissions as $permission)
                         
                                            <li class="list {{Arr::get($permission ,'isSet' ,false) ? 'list-success' : 'list-error' }}">
                                                {{ Arr::get($permission ,'folder' ,null) }} ({{Arr::get($permission ,'permission' ,null)}})
                                                @if(@$permission['isSet'])
                                                   <i class="bi bi-check-circle-fill i-success"></i>
                                                @else
                                                        @php
                                                            $flag = false;
                                                        @endphp
                                                   <i class="bi bi-exclamation-circle-fill i-danger"></i>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                              @php
                                 session()->put('system_requirments',$flag)
                              @endphp
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <div class="d-flex gap-2 justify-content-between">

                            <a href="{{route('install.init')}}"  class="ai--btn i-btn btn--md btn--primary"> 
                                <i class="bi bi-arrow-left me-2"></i>{{trans("default.btn_previous")}}
                            </a>

             
                            @php
                               $btnUrl  = route('install.envato.verification',['verify_token' => bcrypt(base64_decode('ZW52YXRvX3ZlcmlmaWNhdGlvbg=='))]);
                            @endphp
                            
                            <a href="{{$btnUrl}}"  class="ai--btn i-btn btn--md btn--primary"> 
                                @if( session()->get('system_requirments'))
                                  {{trans("default.btn_next")}} <i class="bi bi-arrow-right ms-2"></i>
                                @else
                                    {{trans("default.btn_refresh")}}  <i class="bi bi-arrow-repeat ms-2"></i> 
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection