@extends('admin.layouts.app')

@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{translate($title)}}
                </h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                            {{translate('Home')}}
                        </a></li>
                        <li class="breadcrumb-item active">
                            {{translate('Dos Security Settings')}}
                        </li>
                    </ol>
                </div>
            </div>


            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <div class="row g-4 align-items-center">
                                <div class="col-sm">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            {{translate('Security Settings')}}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                         @php
                           $security = site_settings("security_settings",null) ? json_decode(site_settings("security_settings"))  : null;
                         @endphp

                        <div class="card-body">

                            <form action="{{route('admin.security.dos.update')}}" method="post">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between gap-3 form-control p-2">
                                            <p class="fw-bold mb-0">{{translate('Prevent Dos Attack')}}</p>

                                            <div class="form-group">
                                                <div class="form-check form-switch form-switch-md" dir="ltr">
                                                    <input
                                                        {{ @$security->dos_prevent == App\Enums\StatusEnum::true->status() ? 'checked' : '' }}
                                                        type="checkbox" class="form-check-input" name="site_settings[dos_prevent]"
                                                        value = '{{ App\Enums\StatusEnum::true->status()}}'
                                                        id="dos_prevent">
                                                    <label class="form-check-label" for="dos_prevent"></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-center flex-wrap gap-3 d-dos-input">
                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label mb-0" >
                                                    {{translate("If there are more than")}}
                                                </label>
                                                <input class="form-control" value='{{@$security->dos_attempts ?? 0}}'  required type="number" name="site_settings[dos_attempts]" >
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <label class="form-label mb-0" >
                                                    {{translate("attempts in")}}
                                                </label>

                                                <input class="form-control" value='{{@$security->dos_attempts_in_second ?? 0}}'  required type="number" name="site_settings[dos_attempts_in_second]" >

                                                <label class="w-nowrap mb-0">
                                                    {{translate("second")}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex align-items-center  gap-3 form-control p-2 d-flex mb-2">
                                            <div>
                                                <input class="form-check-input" {{@$security->dos_security == "captcha" ? "checked" :"" }} type="radio" name="site_settings[dos_security]" id="captcha" value="captcha">
                                                <label class="form-check-label" for="captcha">
                                                    {{translate('Show Captcha')}}
                                                </label>
                                            </div>

                                            <div>
                                                <input class="form-check-input" type="radio" {{@$security->dos_security == "block_ip" ?  "checked" :"" }} name="site_settings[dos_security]" id="blokedIp" value="block_ip">
                                                <label class="form-check-label" for="blokedIp">
                                                    {{translate('Block Ip')}}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="text-start">
                                            <button type="submit" class="btn btn-success">
                                                {{translate('Update')}}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection





