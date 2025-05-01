@include('frontend.partials.topbar')

<div class="header-middle">
    <div class="container-fluid">
        <div class="header-middle-container">
            <div class="middle-left">
                @php
                    $lang = $languages->where('code',strtolower(session()->get('locale')));
                    $code = count($lang)!=0 ? $lang->first()->code:"en";
                    $languages = $languages->where('code','!=',$code);
                @endphp

                <div class="lang-dropdown middle-left-item">
                    <div class="Dropdown">
                         <button class="dropdown__button" type="button">{{$lang->first()->name}}
                            @if(count($languages) != 0)
                              <span class="dropdown_button_icon"><i class="fa-solid fa-chevron-down"></i></span>
                            @endif
                        </button>
                        @if(count($languages) != 0)
                            <ul class="dropdown__list">
                                @foreach($languages as $language)
                                    <li class="dropdown__list-item" data-value="{{$language->code}}">
                                        <a href="{{route('language.change',$language->code)}}" class="notify-item language" data-lang="{{$language->code}}">

                                            {{$language->name}}

                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <input class="dropdown__input_hidden" type="text" name="select-category" value=""/>
                    </div>
                </div>

                <div class="currency-dropdown middle-left-item">
                    @php
                         $currency  = session()->get('web_currency');
                    @endphp
                    <div class="Dropdown">
                        <button class="dropdown__button" type="button">
                            {{  $currency->name }}
                             @if(count($currencys) != 0)
                              <span class="dropdown_button_icon"><i class="fa-solid fa-chevron-down"></i></span>
                            @endif
                        </button>
                        <ul class="dropdown__list">
                            @foreach($currencys as $currency)
                                <li class="chanage_currency dropdown__list-item" data-value="{{$currency->id}}">{{$currency->name}}</li>
                            @endforeach

                        </ul>
                        <input class="dropdown__input_hidden" type="text" name="select-category" value=""/>
                    </div>
                </div>

                <a href="tel:{{site_settings('phone')}}" class="header-contact middle-left-item">
                    <div class="header-contact-number">
                        <small>
                            {{translate("Need Help?")}}
                        </small>
                        <span>
                            {{site_settings('phone')}}
                        </span>
                    </div>
                </a>
            </div>
            <div class="middle-right mobile-menu">
                <nav class="navBar">
                    <a class="d-lg-none" data-bs-toggle="offcanvas" href="#menu-sidebar" role="button" >
                        <svg width="20" height="14" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 7H13M1 1H19M1 13H19"  stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>
                            {{translate('Menu')}}
                        </span>
                    </a>

                    <a href="{{route('cart.view')}}" class="d-lg-none">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1H2.30616C2.55218 1 2.67519 1 2.77418 1.0452C2.86142 1.0851 2.93535 1.1492 2.98715 1.2299C3.04593 1.3215 3.06333 1.4433 3.09812 1.6869L3.57143 5M3.57143 5L4.62332 12.7314C4.75681 13.7125 4.82355 14.2031 5.0581 14.5723C5.26478 14.8977 5.56108 15.1564 5.91135 15.3174C6.3089 15.5 6.8039 15.5 7.7941 15.5H16.352C17.2945 15.5 17.7658 15.5 18.151 15.3304C18.4905 15.1809 18.7818 14.9398 18.9923 14.6342C19.2309 14.2876 19.3191 13.8247 19.4955 12.8988L20.8191 5.9497C20.8812 5.6238 20.9122 5.4609 20.8672 5.3335C20.8278 5.2218 20.7499 5.1277 20.6475 5.068C20.5308 5 20.365 5 20.0332 5H3.57143ZM9 20C9 20.5523 8.5523 21 8 21C7.4477 21 7 20.5523 7 20C7 19.4477 7.4477 19 8 19C8.5523 19 9 19.4477 9 20ZM17 20C17 20.5523 16.5523 21 16 21C15.4477 21 15 20.5523 15 20C15 19.4477 15.4477 19 16 19C16.5523 19 17 19.4477 17 20Z"  stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>
                            {{translate("Cart")}}
                        </span>
                    </a>

                    @if($support->status =='1')
                        <a href="{{route('support')}}">
                        <svg viewBox="-2.4 -2.4 28.80 28.80" role="img" xmlns="http://www.w3.org/2000/svg" aria-labelledby="supportIconTitle"  stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none" color="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"  stroke-width="0.192"></g><g id="SVGRepo_iconCarrier"> <title id="supportIconTitle">Support</title> <path d="M18,9 L16,9 C14.8954305,9 14,9.8954305 14,11 L14,13 C14,14.1045695 14.8954305,15 16,15 L16,15 C17.1045695,15 18,14.1045695 18,13 L18,9 C18,4.02943725 13.9705627,0 9,0 C4.02943725,0 0,4.02943725 0,9 L0,13 C1.3527075e-16,14.1045695 0.8954305,15 2,15 L2,15 C3.1045695,15 4,14.1045695 4,13 L4,11 C4,9.8954305 3.1045695,9 2,9 L0,9" transform="translate(3 3)"></path> <path d="M21,14 L21,18 C21,20 20.3333333,21 19,21 C17.6666667,21 16,21 14,21"></path> </g></svg>
                            <span>{{translate("Support")}}</span>
                        </a>
                    @endif

                    @if($contact->status =='1')
                        <a  href="{{route('contact')}}" role="button">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.3639 10.1924V9.6267C17.3639 9.5421 17.3639 9.4997 17.3634 9.4639C17.3247 6.7585 15.1409 4.57479 12.4355 4.53605C12.3997 4.53553 12.3574 4.53553 12.2728 4.53553H11.7071M20.8996 10.8995V9.9095C20.8996 9.0649 20.8996 8.6426 20.8677 8.2866C20.5226 4.43191 17.4677 1.37699 13.613 1.03187C13.257 1 12.8347 1 11.99 1H11.0001M7.77779 8.2234C7.32459 9.2632 7.55398 10.4744 8.35598 11.2765L10.7529 13.6733C11.5549 14.4753 12.7661 14.7047 13.8059 14.2515C14.8457 13.7982 16.057 14.0276 16.859 14.8297L18.3172 16.2879C18.3878 16.3585 18.4232 16.3938 18.4518 16.425C19.1548 17.1901 19.1548 18.3662 18.4518 19.1314C18.4232 19.1625 18.3878 19.1978 18.3172 19.2685L17.4309 20.1548C16.7091 20.8766 15.6685 21.1796 14.6721 20.9582C7.88248 19.4494 2.57988 14.1468 1.07118 7.3572C0.849684 6.3608 1.15268 5.32026 1.87448 4.59846L2.76088 3.71212C2.83148 3.64147 2.86678 3.60615 2.89788 3.57756C3.66308 2.87448 4.83919 2.87448 5.60439 3.57756C5.63549 3.60615 5.67079 3.64147 5.74149 3.71212L7.19958 5.17032C8.00168 5.97237 8.23109 7.1836 7.77779 8.2234Z"  stroke-width="1.3" stroke-linecap="round"/>
                            </svg>
                            <span>
                                {{translate("Contact")}}
                            </span>
                        </a>
                    @endif

                    @if(site_settings('multi_vendor',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::true->status())

                        <a href="{{route('seller.register')}}"  class="d-lg-flex d-none">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 512 512"   xml:space="preserve" ><g><path d="M475.635 154.341h-.005l-33.8-90.833a32.14 32.14 0 0 0-29.99-20.839H100.156a32.139 32.139 0 0 0-29.99 20.841l-33.8 90.833c-4.2 11.313-4.6 23.32-1.125 33.81a63.847 63.847 0 0 0 39.427 40.168V416A53.4 53.4 0 0 0 128 469.333h256A53.4 53.4 0 0 0 437.333 416V228.319a63.847 63.847 0 0 0 39.427-40.168c3.48-10.49 3.079-22.497-1.125-33.81Zm-124.771-10.51A74.573 74.573 0 0 1 352 156.8V168a42.667 42.667 0 1 1-85.333 0V64h70.109ZM160 156.8a74.573 74.573 0 0 1 1.135-12.974L175.225 64h70.109v104A42.667 42.667 0 1 1 160 168ZM55.49 181.438c-1.958-5.9-1.646-12.883.87-19.661l33.8-90.828a10.717 10.717 0 0 1 10-6.948h53.4l-13.435 76.122a96.139 96.139 0 0 0-1.458 16.682V168a42.675 42.675 0 0 1-83.177 13.438ZM416 416a32.035 32.035 0 0 1-32 32H128a32.035 32.035 0 0 1-32-32V232a64 64 0 0 0 53.333-28.656 63.959 63.959 0 0 0 106.667 0 63.959 63.959 0 0 0 106.667 0A64 64 0 0 0 416 232Zm40.51-234.562A42.675 42.675 0 0 1 373.333 168v-11.2a96.139 96.139 0 0 0-1.458-16.682L358.44 64h53.4a10.718 10.718 0 0 1 10 6.945l33.792 90.828c2.524 6.782 2.837 13.763.878 19.665Z"  opacity="1" data-original="#000000" ></path><path d="M345.177 303.391 292.4 291.964 265.219 245.3a10.668 10.668 0 0 0-18.437 0L219.6 291.964l-52.781 11.427a10.668 10.668 0 0 0-5.7 17.536l35.99 40.273-5.443 53.729a10.667 10.667 0 0 0 14.917 10.833L256 403.984l49.417 21.776a10.667 10.667 0 0 0 14.917-10.833l-5.443-53.727 35.984-40.271a10.668 10.668 0 0 0-5.7-17.536Zm-49.323 47.094a10.693 10.693 0 0 0-2.661 8.182l4.068 40.188-36.961-16.287a10.664 10.664 0 0 0-8.6 0l-36.96 16.286 4.068-40.187a10.693 10.693 0 0 0-2.661-8.182l-26.913-30.115 39.474-8.547a10.685 10.685 0 0 0 6.964-5.057L256 271.859l20.328 34.906a10.685 10.685 0 0 0 6.964 5.057l39.474 8.547Z"  opacity="1" data-original="#000000" ></path></g></svg>
                            <span>
                                {{translate('Become A Seller')}}
                            </span>
                        </a>
                    @endif

                    <a href="{{route('user.track.order')}}" class="d-lg-flex d-none">
                        <span>
                            {{translate('Track Order')}}
                        </span>
                    </a>

                    @if(!auth_user('web'))
                        <a href="{{route("login")}}" class="login-sidebar">
                            <svg  viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 15L19 10M19 10L14 5M19 10H7M10 15C10 15.93 10 16.395 9.89778 16.7765C9.62038 17.8117 8.81173 18.6204 7.77646 18.8978C7.39496 19 6.92997 19 6 19H5.5C4.10218 19 3.40326 19 2.85195 18.7716C2.11687 18.4672 1.53284 17.8831 1.22836 17.1481C1 16.5967 1 15.8978 1 14.5V5.5C1 4.10217 1 3.40326 1.22836 2.85195C1.53284 2.11687 2.11687 1.53284 2.85195 1.22836C3.40326 1 4.10218 1 5.5 1H6C6.92997 1 7.39496 1 7.77646 1.10222C8.81173 1.37962 9.62038 2.18827 9.89778 3.22354C10 3.60504 10 4.07003 10 5"  stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>
                                {{translate('Login')}}
                            </span>
                        </a>
                    @endif

                    @if(auth_user('web'))
                        <a href="{{route('user.dashboard')}}"> <svg  version="1.1"    x="0" y="0" viewBox="0 0 512 512.001"   xml:space="preserve" ><g><path class="profile-icon" d="M210.352 246.633c33.882 0 63.218-12.153 87.195-36.13 23.969-23.972 36.125-53.304 36.125-87.19 0-33.876-12.152-63.211-36.129-87.192C273.566 12.152 244.23 0 210.352 0c-33.887 0-63.22 12.152-87.192 36.125s-36.129 53.309-36.129 87.188c0 33.886 12.156 63.222 36.13 87.195 23.98 23.969 53.316 36.125 87.19 36.125zM144.379 57.34c18.394-18.395 39.973-27.336 65.973-27.336 25.996 0 47.578 8.941 65.976 27.336 18.395 18.398 27.34 39.98 27.34 65.972 0 26-8.945 47.579-27.34 65.977-18.398 18.399-39.98 27.34-65.976 27.34-25.993 0-47.57-8.945-65.973-27.34-18.399-18.394-27.344-39.976-27.344-65.976 0-25.993 8.945-47.575 27.344-65.973zM426.129 393.703c-.692-9.976-2.09-20.86-4.149-32.351-2.078-11.579-4.753-22.524-7.957-32.528-3.312-10.34-7.808-20.55-13.375-30.336-5.77-10.156-12.55-19-20.16-26.277-7.957-7.613-17.699-13.734-28.965-18.2-11.226-4.44-23.668-6.69-36.976-6.69-5.227 0-10.281 2.144-20.043 8.5a2711.03 2711.03 0 0 1-20.879 13.46c-6.707 4.274-15.793 8.278-27.016 11.903-10.949 3.543-22.066 5.34-33.043 5.34-10.968 0-22.086-1.797-33.043-5.34-11.21-3.622-20.3-7.625-26.996-11.899-7.77-4.965-14.8-9.496-20.898-13.469-9.754-6.355-14.809-8.5-20.035-8.5-13.313 0-25.75 2.254-36.973 6.7-11.258 4.457-21.004 10.578-28.969 18.199-7.609 7.281-14.39 16.12-20.156 26.273-5.558 9.785-10.058 19.992-13.371 30.34-3.2 10.004-5.875 20.945-7.953 32.524-2.063 11.476-3.457 22.363-4.149 32.363C.343 403.492 0 413.668 0 423.949c0 26.727 8.496 48.363 25.25 64.32C41.797 504.017 63.688 512 90.316 512h246.532c26.62 0 48.511-7.984 65.062-23.73 16.758-15.946 25.254-37.59 25.254-64.325-.004-10.316-.351-20.492-1.035-30.242zm-44.906 72.828c-10.934 10.406-25.45 15.465-44.38 15.465H90.317c-18.933 0-33.449-5.059-44.379-15.46-10.722-10.208-15.933-24.141-15.933-42.587 0-9.594.316-19.066.95-28.16.616-8.922 1.878-18.723 3.75-29.137 1.847-10.285 4.198-19.937 6.995-28.675 2.684-8.38 6.344-16.676 10.883-24.668 4.332-7.618 9.316-14.153 14.816-19.418 5.145-4.926 11.63-8.957 19.27-11.98 7.066-2.798 15.008-4.329 23.629-4.56 1.05.56 2.922 1.626 5.953 3.602 6.168 4.02 13.277 8.606 21.137 13.625 8.86 5.649 20.273 10.75 33.91 15.152 13.941 4.508 28.16 6.797 42.273 6.797 14.114 0 28.336-2.289 42.27-6.793 13.648-4.41 25.058-9.507 33.93-15.164 8.043-5.14 14.953-9.593 21.12-13.617 3.032-1.973 4.903-3.043 5.954-3.601 8.625.23 16.566 1.761 23.636 4.558 7.637 3.024 14.122 7.059 19.266 11.98 5.5 5.262 10.484 11.798 14.816 19.423 4.543 7.988 8.208 16.289 10.887 24.66 2.801 8.75 5.156 18.398 7 28.675 1.867 10.434 3.133 20.239 3.75 29.145v.008c.637 9.058.957 18.527.961 28.148-.004 18.45-5.215 32.38-15.937 42.582zm0 0"  data-original="#000000" ></path></g></svg>
                            <span>
                                {{translate('Dashboard')}}
                            </span>
                        </a>
                    @endif
                </nav>
            </div>
        </div>
    </div>
</div>

<header class="sticky">
    <div class="header-bottom">
        <div class="container-fluid">
            <div class="header-bottom-container">
                <div class="header-logo">
                    <a href="{{route('home')}}">
                        <img src="{{ show_image('assets/images/backend/logoIcon/'.site_settings('site_logo'), file_path()['site_logo']['size']) }}" alt="site_logo.png">
                    </a>
                </div>

                <div class="mobile-search">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <form   method="GET" class="search-bar w-100">
                            <div class="live-search-input">
                                <div class="search">
                                  <i class="fa fa-search"></i>
                                  <input id="search-text" type="search" placeholder="{{translate('What are you looking for?')}}" name="search" value=""  class="form-control">
                                </div>
                                <div class="live-search d-none search-result-box">
                                    <div id="search-result">
                                    </div>
                                    <div class="live-search-loader">
                                        <div class="d-flex align-items-center py-5">
                                            <div class="flex-grow-1 fs-14 me-3">
                                                {{translate('Loading')}}
                                            </div>
                                            <div>
                                                <span class="spinner-grow flex-shrink-0" role="status"></span>
                                                <span class="spinner-grow flex-shrink-0" role="status"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <span class="mobile-search-close d-md-none"><button class="badge-soft-danger fs-14"><i class="fa-solid fa-xmark"></i></button></span>
                    </div>
                </div>
                <div class="mobile-search-tigger d-md-none">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div class="header-action d-none d-md-none d-lg-flex">
                    <div class="header-action-item">
                        <div class="header-action-container">
                            <a href="{{route('user.wishlist.item')}}" class="each-action-item d-lg-flex d-none">
                                <div class="position-relative">
                                    <div class="header-action-icon">
                                        <svg  version="1.1"    x="0" y="0" viewBox="0 0 512.001 512"   xml:space="preserve" ><g><path d="M256 455.516c-7.29 0-14.316-2.641-19.793-7.438-20.684-18.086-40.625-35.082-58.219-50.074l-.09-.078c-51.582-43.957-96.125-81.918-127.117-119.313C16.137 236.81 0 197.172 0 153.871c0-42.07 14.426-80.883 40.617-109.293C67.121 15.832 103.488 0 143.031 0c29.555 0 56.621 9.344 80.446 27.77C235.5 37.07 246.398 48.453 256 61.73c9.605-13.277 20.5-24.66 32.527-33.96C312.352 9.344 339.418 0 368.973 0c39.539 0 75.91 15.832 102.414 44.578C497.578 72.988 512 111.801 512 153.871c0 43.3-16.133 82.938-50.777 124.738-30.993 37.399-75.532 75.356-127.106 119.309-17.625 15.016-37.597 32.039-58.328 50.168a30.046 30.046 0 0 1-19.789 7.43zM143.031 29.992c-31.066 0-59.605 12.399-80.367 34.914-21.07 22.856-32.676 54.45-32.676 88.965 0 36.418 13.535 68.988 43.883 105.606 29.332 35.394 72.961 72.574 123.477 115.625l.093.078c17.66 15.05 37.68 32.113 58.516 50.332 20.961-18.254 41.012-35.344 58.707-50.418 50.512-43.051 94.137-80.223 123.469-115.617 30.344-36.618 43.879-69.188 43.879-105.606 0-34.516-11.606-66.11-32.676-88.965-20.758-22.515-49.3-34.914-80.363-34.914-22.758 0-43.653 7.235-62.102 21.5-16.441 12.719-27.894 28.797-34.61 40.047-3.452 5.785-9.53 9.238-16.261 9.238s-12.809-3.453-16.262-9.238c-6.71-11.25-18.164-27.328-34.61-40.047-18.448-14.265-39.343-21.5-62.097-21.5zm0 0" data-original="#000000" ></path></g></svg>
                                     </div>
                                     <span class="header-action-badge wishlist--itemcount d-none"></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="each-action-item-sub">{{translate('Favorite')}}</span>
                                    {{translate('My Wishlist')}}
                                </div>
                            </a>
                        </div>
                        <div class="header-action-container">
                            <a href="{{route('compare')}}" class="each-action-item d-lg-flex d-none">
                                <div class="position-relative">
                                    <div class="header-action-icon">
                                        <svg  version="1.1"   x="0" y="0" viewBox="0 0 24 24"   xml:space="preserve" ><g><path d="M22 18a3 3 0 1 1-3.75-2.894V8A1.251 1.251 0 0 0 17 6.75h-2.19l1.72 1.72a.75.75 0 1 1-1.06 1.06l-3-3a.75.75 0 0 1 0-1.06l3-3a.75.75 0 0 1 1.06 1.06l-1.72 1.72H17A2.753 2.753 0 0 1 19.75 8v7.106A2.993 2.993 0 0 1 22 18zM8.53 14.47a.75.75 0 0 0-1.06 1.06l1.72 1.72H7A1.251 1.251 0 0 1 5.75 16V8.894a3 3 0 1 0-1.5 0V16A2.753 2.753 0 0 0 7 18.75h2.19l-1.72 1.72a.75.75 0 1 0 1.06 1.06l3-3a.75.75 0 0 0 0-1.06z" opacity="1" data-original="#000000" ></path></g></svg>
                                    </div>
                                    <span class="header-action-badge compare--total--item d-none"></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="each-action-item-sub">{{translate('Compare')}}</span>
                                    {{translate('Compare list')}}
                                </div>
                            </a>
                        </div>
                        <div class="header-action-container">
                            <div class="each-action-item d-lg-flex d-none">
                                <div class="position-relative">
                                    <div class="header-action-icon">
                                       <svg  version="1.1"    x="0" y="0" viewBox="0 0 511.728 511.728"   xml:space="preserve" ><g><path d="M147.925 379.116c-22.357-1.142-21.936-32.588-.001-33.68 62.135.216 226.021.058 290.132.103 17.535 0 32.537-11.933 36.481-29.017l36.404-157.641c2.085-9.026-.019-18.368-5.771-25.629s-14.363-11.484-23.626-11.484c-25.791 0-244.716-.991-356.849-1.438L106.92 54.377c-4.267-15.761-18.65-26.768-34.978-26.768H15c-8.284 0-15 6.716-15 15s6.716 15 15 15h56.942a6.246 6.246 0 0 1 6.017 4.592l68.265 253.276c-12.003.436-23.183 5.318-31.661 13.92-8.908 9.04-13.692 21.006-13.471 33.695.442 25.377 21.451 46.023 46.833 46.023h21.872a52.18 52.18 0 0 0-5.076 22.501c0 28.95 23.552 52.502 52.502 52.502s52.502-23.552 52.502-52.502a52.177 52.177 0 0 0-5.077-22.501h94.716a52.185 52.185 0 0 0-5.073 22.493c0 28.95 23.553 52.502 52.502 52.502 28.95 0 52.503-23.553 52.503-52.502a52.174 52.174 0 0 0-5.464-23.285c5.936-1.999 10.216-7.598 10.216-14.207 0-8.284-6.716-15-15-15zm91.799 52.501c0 12.408-10.094 22.502-22.502 22.502s-22.502-10.094-22.502-22.502c0-12.401 10.084-22.491 22.483-22.501h.038c12.399.01 22.483 10.1 22.483 22.501zm167.07 22.494c-12.407 0-22.502-10.095-22.502-22.502 0-12.285 9.898-22.296 22.137-22.493h.731c12.24.197 22.138 10.208 22.138 22.493-.001 12.407-10.096 22.502-22.504 22.502zm74.86-302.233c.089.112.076.165.057.251l-15.339 66.425H414.43l8.845-67.023 58.149.234c.089.002.142.002.23.113zm-154.645 163.66v-66.984h53.202l-8.84 66.984zm-74.382 0-8.912-66.984h53.294v66.984zm-69.053 0h-.047c-3.656-.001-6.877-2.467-7.828-5.98l-16.442-61.004h54.193l8.912 66.984zm56.149-96.983-9.021-67.799 66.306.267v67.532zm87.286 0v-67.411l66.022.266-8.861 67.145zm-126.588-67.922 9.037 67.921h-58.287l-18.38-68.194zm237.635 164.905H401.63l8.84-66.984h48.973l-14.137 61.217a7.406 7.406 0 0 1-7.25 5.767z" data-original="#000000" ></path></g></svg>
                                     </div>
                                     <span class="header-action-badge cart-items-count d-none"></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="each-action-item-sub">{{translate('Your Cart:')}}</span>
                                    <p id="total-cart-amount"></p>
                                </div>
                                <div class="cart-dropdown addtocart-dropdown cart--itemlist">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('frontend.partials.md_header')
</header>





