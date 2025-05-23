@php
    $seller = auth()->guard('seller')->user();
    $sellerOrderCount = App\Models\Order::sellerOrder()
        ->placed()
        ->physicalOrder()
        ->whereHas('orderDetails', function ($q) use ($seller) {
            $q->whereHas('product', function ($query) use ($seller) {
                $query->where('seller_id', $seller->id);
            });
        })
        ->count();
@endphp

<div class="app-menu navbar-menu">
    <div class="brand-logo">
        <a href="{{ route('seller.dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ show_image(file_path()['seller_site_logo']['path'] . '/' . @$seller->sellerShop->logoicon, file_path()['loder_logo']['size']) }}"
                    alt="logo.png" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ show_image(file_path()['seller_site_logo']['path'] . '/' . @$seller->sellerShop->seller_site_logo, file_path()['seller_site_logo']['size']) }}"
                    alt="logo.png" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm fs-20 header-item btn-vertical-sm-hover float-end p-0"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" class="scroll-bar" data-simplebar>
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title">
                    <span>
                        {{ translate('Menu') }}
                    </span>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('seller.dashboard') ? 'active' : '' }}"
                        href="{{ route('seller.dashboard') }}">
                        <i class="bx bxs-dashboard"></i> <span>
                            {{ translate('Dashboard') }}
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link chat-link {{ request()->routeIs('seller.customer.chat.*') ? 'active' : '' }}"
                        href="{{ route('seller.customer.chat.list') }}">
                        <i class="bx bxs-chat"></i> <span>
                            {{ translate('Chat') }}
                        </span>
                    </a>
                </li>

                <li class="menu-title">
                    <span>
                        {{ translate('Product & Orders') }}
                    </span>
                </li>

                <li class="nav-item">
                    <a class='nav-link {{ !request()->routeIs('seller.product.*') || !request()->routeIs('seller.digital.product.*') ? 'collapsed' : '' }} menu-link'
                        href="#inhouseProduct" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="inhouseProduct">
                        <i class='bx bx-home-smile'></i> <span>
                            {{ translate('Manage Product') }}
                        </span>
                    </a>

                    <div class='{{ request()->routeIs('seller.product.*') || request()->routeIs('seller.digital.product.*') ? 'show' : '' }} menu-dropdown mega-dropdown-menu collapse pt-1'
                        id="inhouseProduct">
                        <ul class="nav nav-sm flex-column gap-1">

                            <li class="nav-item">
                                <a href="{{ route('seller.product.index') }}"
                                    class='{{ request()->routeIs('seller.product.*') ? 'active' : '' }} nav-link'>
                                    {{ translate('Inhouse Product') }}
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('seller.digital.product.index') }}"
                                    class='{{ request()->routeIs('seller.digital.product.*') ? 'active' : '' }} nav-link'>
                                    {{ translate('digital Products') }}
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item mt-1">
                    <a class='nav-link menu-link {{ !request()->routeIs('seller.order.*') || !request()->routeIs('seller.digital.order.*') ? 'collapsed' : '' }}'
                        href="#manageOrder" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="manageOrder">
                        <i class='bx bxs-shopping-bags'></i>
                        <span>
                            {{ translate('Manage order') }}
                        </span>
                    </a>

                    <div class='{{ request()->routeIs('seller.order.*') || request()->routeIs('seller.digital.order.*') ? 'show' : '' }} menu-dropdown mega-dropdown-menu collapse pt-1'
                        id="manageOrder">
                        <ul class="nav nav-sm flex-column gap-1">
                            <li class="nav-item">
                                <a href="{{ route('seller.order.index') }}"
                                    class='{{ request()->routeIs('seller.order.*') ? 'active' : '' }} nav-link'>
                                    {{ translate('Orders') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('seller.digital.order.index') }}"
                                    class='{{ request()->routeIs('seller.digital.order.*') ? 'active' : '' }} nav-link'>
                                    {{ translate('Digital Orders') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title">
                    <span>
                        {{ translate('Settings & Report') }}
                    </span>
                </li>

                <li class="nav-item">
                    <a class='nav-link menu-link {{ request()->routeIs('seller.withdraw.*') ? 'active' : '' }}'
                        href="{{ route('seller.withdraw.history') }}">
                        <i class='bx bx-money'></i> <span>
                            {{ translate('Withdrawal History') }}
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class='nav-link menu-link {{ request()->routeIs('seller.deposit.*') ? 'active' : '' }}'
                        href="{{ route('seller.deposit.list') }}">
                        <i class='bx bxs-wallet'></i> <span>
                            {{ translate('Deposit Logs') }}
                        </span>
                    </a>
                </li>


                <li class="nav-item mt-1">
                    <a class="nav-link menu-link {{ request()->routeIs('seller.kyc.log.list') ? 'active' : '' }}"
                        href="{{ route('seller.kyc.log.list') }}">
                        <i class='bx bx-list-ul'></i> <span>
                            {{ translate('KYC
                                                                                                                                            Logs') }}
                        </span>
                    </a>
                </li>


                <li class="nav-item mt-1">
                    <a class="nav-link menu-link {{ request()->routeIs('seller.transaction.history') ? 'active' : '' }}"
                        href="{{ route('seller.transaction.history') }}">
                        <i class='bx bx-transfer-alt'></i> <span>
                            {{ translate('Transaction
                                                                                                                                            Logs') }}
                        </span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <a class="nav-link menu-link {{ request()->routeIs('seller.plan.*') ? 'active' : '' }}"
                        href="{{ route('seller.plan.index') }}">
                        <i class='bx bx-purchase-tag'></i> <span>
                            {{ translate('Subscription
                                                                                                                                            Plan') }}
                        </span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <a class="nav-link menu-link {{ request()->routeIs('seller.ticket.*') ? 'active' : '' }}"
                        href="{{ route('seller.ticket.index') }}">
                        <i class='bx bx-message'></i> <span>
                            {{ translate('Support
                                                                                                                                            Ticket') }}
                        </span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <a class="nav-link menu-link {{ request()->routeIs('seller.chatbot.setting.edit') ? 'active' : '' }}"
                        href="{{ route('seller.chatbot.setting.edit') }}">
                        <i class='bx bx-chat'></i> <span>
                            {{ translate('Chatbot Setting') }}
                        </span>
                    </a>
                </li>

                <li class="nav-item mt-1">
                    <a class="nav-link menu-link {{ request()->routeIs('seller.shop.setting') ? 'active' : '' }}"
                        href="{{ route('seller.shop.setting') }}">
                        <i class='bx bx-store'></i> <span>
                            {{ translate('Shop & Site Setting') }}
                        </span>
                    </a>
                </li>

                @if (@$seller->sellerShop->status == 1)
                    <li class="nav-item mt-1">
                        <a class="nav-link menu-link {{ request()->routeIs('seller.store.visit') ? 'active' : '' }}"
                            href="{{ route('seller.store.visit', [make_slug(@$seller->sellerShop->name ? @$seller->sellerShop->name : 'none'), @$seller->id]) }}">
                            <i class='bx bxs-store-alt'></i> <span>
                                {{ translate('View Store') }}
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

    </div>

    <div class="sidebar-background"></div>
</div>


<div class="vertical-overlay"></div>
