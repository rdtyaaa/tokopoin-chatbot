<div class="col-xl-3 col-lg-4">
    <div class="profile-user-left sticky-side-div">
        <div class="profile-user-info">
            <div class="profile-user-top align-items-canter flex-column">
                <div class="profile-user-img flex-shrink-0">
                    <img src="{{show_image(file_path()['profile']['user']['path'].'/'.$user->image,file_path()['profile']['user']['size'])}}" alt="{{auth_user('web')->name}}" />
                </div>

                <div class="profile-user-name text-center w-100">
                    <h5>
                        {{auth_user('web')->name}}
                    </h5>
                    <a href="javascript:void(0)"> {{auth_user('web')->email}} </a>

                    @if(site_settings('customer_wallet') == App\Enums\StatusEnum::true->status())

              

                        <div class="mt-4 bg-light px-4 py-5 text-center">
                            <h6 class="fs-18 mb-3">
                                 {{translate('Wallet Balance')}}
                            </h6>
                            <p class="fs-14">
                                {{ 
                                    short_amount(auth_user('web')->balance)
                                }}
                            </p>

                            <div class="d-flex align-items-center justify-content-center gap-4 mt-5">
                                <a href="{{route('user.deposit.create')}}" class="btn btn-lg btn-success ">
                                    {{translate('Deposit')}}
                                </a>
                                <a href="{{route('user.withdraw.method')}}" class="btn btn-lg btn-danger ">
                                    {{translate('Withdraw')}}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="nav flex-column nav-pills gap-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">


                    <a href="{{route('user.dashboard')}}"  class="nav-link account-tab {{request()->routeIs('user.dashboard') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Dashboard")}}
                        </span>
                    </a>

                    <a href="{{route('user.digital.order.list')}}"  class="nav-link account-tab {{request()->routeIs('user.digital.order.list') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Digital Orders")}}
                        </span>
                    </a>


                    <a href="{{route('user.transactions')}}"  class="nav-link account-tab {{request()->routeIs('user.transactions') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Transactions")}}
                        </span>
                    </a>



                    <a href="{{route('user.wishlist.item')}}"  class="nav-link account-tab {{request()->routeIs('user.wishlist.item') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Wishlist")}}
                        </span>
                    </a>


                    <a href="{{route('cart.view')}}"  class="nav-link account-tab {{request()->routeIs('cart.view') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Cart")}}
                        </span>
                    </a>


                    @if(site_settings('club_point_system' ,0) ==  App\Enums\StatusEnum::true->status())
                        <a href="{{route('user.reward.points')}}"  class="nav-link account-tab {{request()->routeIs('user.reward.*') ? 'active' : ''}}  ">
                            <span>
                                {{translate("Reward points")}}
                            </span>
                        </a>
                    @endif


              
                    <a href="{{route('user.seller.chat.list')}}"  class="nav-link account-tab {{request()->routeIs('user.seller.chat.*') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Seller Chat")}}
                        </span>
                    </a>


                    @if (site_settings('delivery_man_module') == \App\Enums\StatusEnum::true->status() &&
                           site_settings('chat_with_deliveryman') == \App\Enums\StatusEnum::true->status()
                        )
                        <a href="{{route('user.deliveryman.chat.list')}}"  class="nav-link account-tab {{request()->routeIs('user.deliveryman.chat.list.*') ? 'active' : ''}}  ">
                            <span>
                                {{translate("Deliveryman Chat")}}
                            </span>
                        </a>
                    @endif


                    
                    <a href="{{route('user.reviews')}}"  class="nav-link account-tab {{request()->routeIs('user.reviews') ? 'active' : ''}}  ">
                        <span>
                            {{translate("My Reviews")}}
                        </span>
                    </a>


                    <a href="{{route('user.support.ticket.index')}}"  class="nav-link account-tab {{request()->routeIs('user.support.ticket.index') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Support Ticket")}}
                        </span>
                    </a>


                    <a href="{{route('user.profile')}}"  class="nav-link account-tab {{request()->routeIs('user.profile') ? 'active' : ''}}  ">
                        <span>
                            {{translate("Manage Profile")}}
                        </span>
                    </a>


                    <a href="{{route("logout")}}" class="nav-link account-tab mt-2">
                        {{translate("Log Out")}}
                    </a>
            </div>

        </div>
    </div>
</div>
