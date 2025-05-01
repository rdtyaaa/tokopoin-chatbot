<header id="header">
    <div class="header-layout">
        <div class="header-navbar">
            <div class="d-flex">
                <div class="brand-logo horizontal-logo">

                    <a href="{{route('seller.dashboard')}}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{show_image(file_path()['seller_site_logo']['path'].'/'.@$seller->sellerShop->logoicon,file_path()['loder_logo']['size'])}}" alt="seller_site_logo_sm.png" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{show_image(file_path()['seller_site_logo']['path'].'/'.@$seller->sellerShop->seller_site_logo)}}" alt="{{@$seller->sellerShop->seller_site_logo}}" height="17">
                        </span>
                    </a>
                </div>

                <div class="header-action-btn d-flex align-items-center me-md-2 me-xl-3">
                    <button type="button"
                        class="btn btn-sm px-3 fs-22 vertical-menu-btn hamburger-btn btn-ghost-secondary topbar-btn btn-icon rounded-circle waves ripple-dark"
                        id="hamburger-btn">
                        <i class='bx bx-chevrons-left'></i>
                    </button>
                </div>
            </div>

            <div class="d-flex align-items-center">
                @php
                        $currency  = session()->get('web_currency');
               
                @endphp
                <div class="dropdown card-header-dropdown header-item">
                    <button  @if(count($currencys) != 0) data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @endif
                    class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle currency-btn waves ripple-dark dropdown-toggle">
                        <span class="fw-bold fs-12">
                            {{  $currency->name}}
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">

                        @foreach($currencys as $currency)
                            <a class="dropdown-item chanage_currency" data-value="{{$currency->id}}" href="javascript:void(0)">
                                {{$currency->name}}
                            </a>

                        @endforeach

                    </div>
                </div>
                <div class="dropdown topbar-head-dropdown header-item ms-1">

                    @php
                        $lang = $languages->where('code',session()->get('locale'));

                        $code = count($lang)!=0 ? $lang->first()->code:"en";
                        $languages = $languages->where('code','!=',$code);
                    @endphp
                    <button type="button" class="dropdown-toggle btn btn-icon topbar-btn btn-ghost-secondary rounded-circle lang-btn waves ripple-dark"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                         <span class="fw-bold fs-12">
                            {{ $code}}
                        </span>

                    </button>

                    @if(count($languages) != 0)
                        <div class="dropdown-menu dropdown-menu-end">
                            @foreach($languages as $language)
                                <a href="{{route('language.change',$language->code)}}" class="dropdown-item notify-item language py-2" data-lang="{{$language->code}}"
                                    title="{{$language->name}}">
                                    <span class="align-middle lang-code">
                                        {{$language->code}}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button"
                        class="btn btn-icon topbar-btn btn-ghost-secondary rounded-circle waves ripple-dark"
                        data-toggle="fullscreen">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>

                <div class="dropdown ms-md-3 ms-2 header-item topbar-user">
                    <button type="button" class="btn waves ripple-dark" id="page-header-user-dropdown"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{show_image(file_path()['profile']['seller']['path'].'/'.auth_user('seller')->image,file_path()['profile']['seller']['size'])}}"
                                alt="{{auth_user('seller')->image}}">

                            <span class="text-start ms-xl-2 d-none d-lg-block lh-1">
                                <span class="mb-0 fw-bold user-name-text text-light">
                                    {{auth_user('seller')->name}}
                                </span>
                            </span>
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end">

                        <h6 class="dropdown-header">
                        {{translate('Welcome')}}
                        {{auth_user('seller')->name}}</h6>
                            <a class="dropdown-item" href="{{route('seller.profile')}}"><i
                            class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                            class="align-middle">
                            {{translate('Settings')}}
                        </span></a>
                        <a class="dropdown-item" href="{{route('seller.logout')}}"><i
                                class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">
                                {{translate('Logout')}}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
