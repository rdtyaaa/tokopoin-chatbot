
<div class="app-menu navbar-menu">


    <div class="brand-logo">
        <a href="{{route('admin.dashboard')}}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{show_image(file_path()['site_logo']['path']."/".site_settings('admin_logo_sm'),file_path()['loder_logo']['size'])}}" alt="{{@site_settings('admin_logo_lg')}}">
            </span>

            <span class="logo-lg">
                <img src="{{show_image(file_path()['site_logo']['path']."/".site_settings('admin_logo_lg') ,file_path()['admin_site_logo']['size'])}}" alt="{{site_settings('admin_logo_lg')}}">


            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" class="scroll-bar" data-simplebar>

        {{-- <div class="pt-3">
            <input type="search" name="search" class="form-control bg-transparent text-light search-menu-input" placeholder="{{translate('Search here')}}">
        </div> --}}

        <div class="container-fluid">
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title">
                        <span>
                        {{translate('Menu')}}
                        </span>
                </li>

                @if(permission_check('view_dashboard') || permission_check('view_seller') || permission_check('view_admin') || permission_check('view_roles') )
                    <li class="nav-item">
                        <a class="nav-link menu-link  {{request()->routeIs('admin.dashboard') ? 'active' :''  }}  " href="{{route('admin.dashboard')}}">
                            <i class="bx bxs-dashboard"></i> <span>
                                {{translate('Dashboard')}}
                            </span>
                        </a>
                    </li>

                    @if(permission_check('view_admin') || permission_check('view_roles') )
                        <li class="nav-item">
                            <a class="nav-link menu-link {{!request()->routeIs('admin.role.*') || !request()->routeIs('admin.index') || !request()->routeIs('admin.edit') || !request()->routeIs('admin.create') ?'collapsed' :''    }}" href="#manage-staff" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="manage-staff">
                                <i class='bx bxs-user-detail'></i> <span>
                                    {{translate('Access Control')}}
                                </span>
                            </a>
                            <div class="pt-1 collapse  {{request()->routeIs('admin.role.*') || request()->routeIs('admin.index') ||  request()->routeIs('admin.edit')  || request()->routeIs('admin.create') ?'show' :''    }}   menu-dropdown" id="manage-staff">
                                <ul class="nav nav-sm flex-column gap-1">

                                    @if(permission_check('view_admin'))
                                        <li class="nav-item">
                                            <a href="{{route('admin.index')}}" class="nav-link  {{request()->routeIs('admin.index')
                                            || request()->routeIs('admin.edit') || request()->routeIs('admin.create')
                                            ?'active' :''}}  ">
                                            {{translate('Staffs')}}
                                            </a>
                                        </li>
                                    @endif

                                    @if(permission_check('view_roles'))
                                        <li class="nav-item">
                                            <a href="{{route('admin.role.index')}}" class="nav-link {{request()->routeIs('admin.role.*')?'active' :''}}">
                                            {{translate('Roles')}}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif



                    @if(permission_check('view_seller') && site_settings('multi_vendor',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::true->status())
                        <li class="nav-item">
                            <a class="nav-link menu-link
                            {{ !request()->routeIs('admin.seller.shop') || !request()->routeIs('admin.seller.info.*') || !request()->routeIs('admin.plan.*') ? 'collapsed' :''}}
                            " href="#sellermanage" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sellermanage">
                                <i class='bx bxs-store'></i>  <span>
                                    {{translate("Sellers")}}

                                </span>
                            </a>
                            <div class="pt-1 collapse
                            {{request()->routeIs('admin.seller.shop') || request()->routeIs('admin.seller.info.*') || request()->routeIs('admin.plan.*') ? 'show' :''}}
                            menu-dropdown mega-dropdown-menu" id="sellermanage">
                                <ul class="nav nav-sm flex-column gap-1">
                                    <li class="nav-item">
                                        <a href="{{route('admin.seller.shop')}}" class="
                                        {{request()->routeIs('admin.seller.shop')?'active' :''}}
                                        nav-link">
                                            {{translate('Shop Analytics')}}
                                        </a>
                                    </li>

                                    @if(permission_check('view_seller'))

                                        <li class="nav-item">
                                            <a class="nav-link {{request()->routeIs('admin.seller.info.*')?'active' :'' }}" href="{{route('admin.seller.info.index')}}">
                                                {{translate("Manage Seller")}}
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link {{request()->routeIs('admin.plan.subscription')?'active' :'' }}  " href="{{route('admin.plan.subscription')}}">
                                            {{translate("Seller Subscription")}}
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link {{request()->routeIs('admin.plan.index')?'active' :'' }} " href="{{route('admin.plan.index')}}">
                                               {{translate("Pricing Plan")}}
                                            </a>
                                        </li>
                                    @endif


                                </ul>
                            </div>
                        </li>
                    @endif

                @endif


                @if(permission_check('view_brand') || permission_check('view_categroy') || permission_check('view_product') || permission_check('view_order') )
                    <li class="menu-title">
                        <span>
                            {{translate('Product & Order')}}
                        </span>
                    </li>
                @endif

                @if(permission_check('view_brand') || permission_check('view_categroy') || permission_check('view_product') )
                    <li class="nav-item">
                        <a class="nav-link   {{ !request()->routeIs('admin.item.*')?'collapsed' :'' }} menu-link" href="#inhouseProduct" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="inhouseProduct">
                            <i class='bx bxl-product-hunt'></i> <span>
                                {{translate("Manage Product")}}
                                @if($seller_new_digital_product_count > 0 || $seller_new_physical_product_count > 0 )
                                  <i class=" text-danger las la-exclamation "></i>
                                @endif
                            </span>
                        </a>
                        <div class="pt-1 collapse  {{request()->routeIs('admin.item.*') || request()->routeIs('admin.product.seller.*') || request()->routeIs('admin.digital.product.*') || request()->routeIs('admin.product.reviews') ?'show' :'' }} menu-dropdown mega-dropdown-menu" id="inhouseProduct">
                            <ul class="nav nav-sm flex-column gap-1">

                                @if(permission_check('view_product'))
                                    <li class="nav-item">
                                        <a href="{{route('admin.item.product.inhouse.index')}}" class=" {{request()->routeIs('admin.item.product.inhouse.*') || request()->routeIs('admin.product.reviews')?'active' :'' }}  nav-link">
                                            {{translate("Inhouse Product")}}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{request()->routeIs('admin.product.seller.*')?'active' :'' }}  " href="{{route('admin.product.seller.index')}}">

                                            {{translate("Seller Product")}}
                                            @if($seller_new_physical_product_count > 0)
                                                <span title="{{translate('Seller New Product')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger ">
                                                   {{ $seller_new_physical_product_count}}
                                                </span>
                                            @endif
                                        </a>
                                    </li>


                                    <li class="nav-item">
                                        <a class="nav-link" href="#digitalProduct" data-bs-toggle="collapse" role="button"
                                            aria-expanded="false" aria-controls="digitalProduct">

                                                {{translate("Digital Product")}}

                                                @if($seller_new_digital_product_count > 0 )
                                                   <i class=" text-danger las la-exclamation "></i>
                                                @endif

                                        </a>
                                        <div class="pt-1 collapse {{request()->routeIs('admin.digital.product.*')?'show' :'' }} menu-dropdown" id="digitalProduct">
                                            <ul class="nav nav-sm flex-column gap-1">
                                                <li class="nav-item">
                                                    <a href="{{route('admin.digital.product.index')}}" class="nav-link {{request()->routeIs('admin.digital.product.index') || request()->routeIs('admin.digital.product.create') || request()->routeIs('admin.digital.product.edit') || request()->routeIs('admin.digital.product.attribute') ||   request()->routeIs('admin.digital.product.attribute.*')  ?'active' :''}}">
                                                    {{translate("Inhouse Product")}}
                                                    </a>
                                                </li>

                                                <li class="nav-item">
                                                    <a href="{{ route('admin.digital.product.seller') }}" class="nav-link {{request()->routeIs('admin.digital.product.seller') ||  request()->routeIs('admin.digital.product.seller.*')  ?'active' :'' }}">
                                                        {{translate("Seller Product")}}
                                                        @if($seller_new_digital_product_count > 0)
                                                            <span title="{{translate('Seller New Product')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger">
                                                              {{ $seller_new_digital_product_count}}
                                                            </span>
                                                        @endif
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>


                                @endif


                                @if(permission_check('view_brand'))
                                    <li class="nav-item">
                                        <a href="{{route('admin.item.brand.index')}}" class="
                                        {{request()->routeIs('admin.item.brand.*')? 'active' :'' }}
                                        nav-link">
                                            {{translate('Brand')}}
                                        </a>
                                    </li>
                                @endif

                                @if(permission_check('view_category'))
                                    <li class="nav-item">
                                        <a href="{{route('admin.item.category.index')}}" class="{{request()->routeIs('admin.item.category.*')?'active' :'' }}  nav-link">
                                            {{translate("Category")}}
                                        </a>
                                    </li>
                                @endif
                                @if(permission_check('view_product'))
                                    <li class="nav-item">
                                        <a href="{{route('admin.item.attribute.index')}}" class="{{request()->routeIs('admin.item.attribute.*')?'active' :'' }}  nav-link">
                                            {{translate("Attribute")}}
                                        </a>
                                    </li>
                                @endif
                                @if(permission_check('manage_taxes'))
                                    <li class="nav-item">
                                        <a href="{{route('admin.tax.list')}}" class="{{request()->routeIs('admin.tax.*')?'active' :'' }}  nav-link">
                                            {{translate("Tax")}}
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </li>
                @endif

                @if(permission_check('view_order'))
                    <li class="nav-item">
                        <a class="nav-link menu-link
                        {{ !request()->routeIs('admin.inhouse.order.*') ||  !request()->routeIs('admin.digital.order.*') || !request()->routeIs('admin.seller.order.*') ? 'collapsed' :''}}
                        " href="#manageOrder" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="manageOrder">
                            <i class='bx bxs-shopping-bags'></i> <span>
                                {{translate('Manage Order')}}
                                @if($physical_product_order_count > 0 || $physical_product_seller_order_count > 0)
                                    <i class=" text-danger las la-exclamation "></i>
                                @endif
                            </span>
                        </a>
                        <div class="pt-1 collapse
                        {{request()->routeIs('admin.inhouse.order.*') ||  request()->routeIs('admin.digital.order.*') || request()->routeIs('admin.seller.order.*') ? 'show' :''}}
                        menu-dropdown mega-dropdown-menu" id="manageOrder">
                            <ul class="nav nav-sm flex-column gap-1">
                                <li class="nav-item">
                                    <a href="{{route('admin.inhouse.order.index')}}" class="
                                    {{request()->routeIs('admin.inhouse.order.*')?'active' :''}}
                                    nav-link">
                                        <span>
                                            {{translate('Inhouse Order')}}

                                            @if($physical_product_order_count > 0 )
                                               <small title="{{translate('Placed Order')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger ms-2">
                                               {{ $physical_product_order_count}}
                                               </small>
                                            @endif
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.seller.order.index')}}" class="nav-link   {{request()->routeIs('admin.seller.order.*')?'active' :''}}">
                                        <span>
                                             {{translate('Seller Order')}}
                                             @if($physical_product_seller_order_count > 0 )
                                                <small title="{{translate('Seller Placed Order')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger ms-2">
                                                {{ $physical_product_seller_order_count}}
                                                </small>
                                             @endif
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ !request()->routeIs('admin.digital.order.*') ? 'collapsed' :''}}" href="#digitalOrder" data-bs-toggle="collapse"
                                        role="button" aria-expanded="false" aria-controls="digitalOrder">
                                        <span>
                                            {{translate('Digital Order')}}
                                        </span>
                                    </a>
                                    <div class="pt-1 collapse {{ request()->routeIs('admin.digital.order.*') ? 'show' :''}} menu-dropdown" id="digitalOrder">
                                        <ul class="nav nav-sm flex-column gap-1">
                                            <li class="nav-item">
                                                <a href="{{route('admin.digital.order.product.inhouse')}}" class="nav-link  {{request()->routeIs('admin.digital.order.product.inhouse') ? 'active' : '' }}  ">

                                                    {{translate('Inhouse
                                                    Order')}}
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('admin.digital.order.product.seller')}}" class=" {{request()->routeIs('admin.digital.order.product.seller') ? 'active' : '' }} nav-link">
                                                    {{translate('Seller
                                                    Order')}}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(permission_check('manage_customer'))
                    <li class="menu-title">
                        <span>
                            {{translate("USER,REPORTS & SUPPORT
                            ")}}
                       </span>
                    </li>

                  

                    <li class="nav-item">
                        <a class="nav-link menu-link {{!request()->routeIs('admin.customer.*') ?  'collapsed' :
                        ''    }}" href="#customer" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="customer">
                            <i class='bx bxs-user-detail'></i>  <span>
                                {{translate("Customers")}}
                            
                            </span>
                        </a>
                        <div class="pt-1 collapse  {{request()->routeIs('admin.customer.*') ?'show' :''   }}   menu-dropdown" id="customer">
                            <ul class="nav nav-sm flex-column gap-1">

                                    <li class="nav-item">
                                        <a href="{{route('admin.customer.index')}}" class="nav-link 
                                        {{ request()->routeIs('admin.customer.*') && 
                                         !request()->routeIs('admin.customer.rewards')  ? 'active' :'' }}  ">
                                            {{translate("List")}}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{route('admin.customer.rewards')}}" class="nav-link  {{request()->routeIs('admin.customer.rewards')? 'active' :'' }}  ">
                                            {{translate("Reward point")}}
                                        </a>
                                    </li>

                            </ul>
                        </div>
                    </li>

                @endif
                @if(permission_check('view_support'))

                    <li class="nav-item">
                        <a class="nav-link menu-link {{request()->routeIs('admin.support.*') ? 'active' :'' }}  " href="{{route('admin.support.ticket.index')}}">
                            <i class='bx bx-support' ></i>
                            <span class="w-100">
                                {{translate('Support Ticket')}}

                                @if($running_ticket > 0 )
                                    <small title="{{translate('Running Ticket')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger ms-2">
                                    {{ $running_ticket}}
                                    </small>
                                @endif
                            </span>

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{!request()->routeIs('admin.report.user.*')  ?'collapsed' :
                        ''    }}" href="#userReport" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="userReport">
                            <i class='bx bxs-report' ></i> <span>
                                {{translate('Reports')}}
                                @if($withdraw_pending_log_count > 0 || $deposit_pending_log_count > 0 )
                                   <i class=" text-danger las la-exclamation "></i>
                                @endif
                            </span>
                        </a>
                        <div class="pt-1 collapse  {{request()->routeIs('admin.report.*') || request()->routeIs('admin.payment.index') ||  request()->routeIs('admin.payment.*') ||  request()->routeIs('admin.deposit.*') || request()->routeIs('admin.withdraw.log.*')  ?'show' :''   }}   menu-dropdown" id="userReport">
                            <ul class="nav nav-sm flex-column gap-1">

                                    <li class="nav-item">
                                        <a href="{{route('admin.report.user.transaction')}}" class="nav-link  {{request()->routeIs('admin.report.*.transaction') ? 'active' :'' }}  ">
                                            {{translate("Transactions")}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{route('admin.payment.index')}}" class="nav-link  {{request()->routeIs('admin.payment.*') ? 'active':'' }}  ">
                                            {{translate("Payment")}}
                                        </a>
                                    </li>


                                    <li class="nav-item">
                                        <a href="{{route('admin.deposit.index')}}" class="nav-link  {{request()->routeIs('admin.deposit.*') ? 'active':'' }}  ">
                                            {{translate("Deposit")}}

                                            @if($deposit_pending_log_count > 0 )
                                                <span title="{{translate('Pending Deposit')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger ">
                                                {{ $deposit_pending_log_count}}
                                                </span>
                                             @endif
                                        </a>
                                    </li>
                                    
                                    
                                    <li class="nav-item">
                                        <a href="{{route('admin.withdraw.log.index')}}" class="nav-link  {{request()->routeIs('admin.withdraw.log.*') ? 'active' :'' }}  ">
                                            {{translate("Widthdraw")}}

                                            @if($withdraw_pending_log_count > 0 )
                                                <span title="{{translate('Pending Withdraw')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger ">
                                                {{ $withdraw_pending_log_count}}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                    
                                    <li class="nav-item">
                                        <a href="{{route('admin.report.kyc.log')}}" class="nav-link  {{request()->routeIs('admin.report.kyc.*') ? 'active' :'' }}  ">
                                            {{translate("KYC Log")}}

                                            @if($requested_kyc_log > 0 )
                                                <span title="{{translate('Requested KYC log')}}" data-bs-toggle="tooltip" data-bs-placement="top" class="badge bg-danger ">
                                                {{ $requested_kyc_log}}
                                                </span>
                                            @endif
                                        </a>
                                    </li>

                            </ul>
                        </div>
                    </li>

                @endif

                @if(permission_check('manage_delivery_man'))

                    <li class="menu-title">
                        <span>
                            {{translate("Delivery Man
                            ")}}
                        </span>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link menu-link {{!request()->routeIs('admin.delivery-man.*') || !request()->routeIs('admin.general.deliveryman.setting')  ?'collapsed' :
                        ''    }}" href="#deliveryMan" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="deliveryMan">
                            <i class='bx bxs-user-detail'></i>  <span>
                                {{translate("Delivery Man
                                ")}}
                            
                            </span>
                        </a>
                        <div class="pt-1 collapse  {{request()->routeIs('admin.delivery-man.*') || request()->routeIs('admin.general.deliveryman.setting')  ?'show' :''   }}   menu-dropdown" id="deliveryMan">
                            <ul class="nav nav-sm flex-column gap-1">

                                    <li class="nav-item">
                                        <a href="{{route('admin.delivery-man.list')}}" class="nav-link {{
                                            request()->routeIs('admin.delivery-man.*') &&  
                                            !request()->routeIs('admin.delivery-man.configuration') &&  
                                            !request()->routeIs('admin.delivery-man.kyc.configuration') && 
                                            !request()->routeIs('admin.delivery-man.rewards') &&
                                            !request()->routeIs('admin.delivery-man.referral') 
                                            ? 'active' :'' }}  ">
                                            {{translate("List")}}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{route('admin.delivery-man.rewards')}}" class="nav-link  {{request()->routeIs('admin.delivery-man.rewards')? 'active' :'' }}  ">
                                            {{translate("Reward log")}}
                                        </a>
                                    </li>


                                    <li class="nav-item">
                                        <a href="{{route('admin.delivery-man.referral')}}" class="nav-link  {{request()->routeIs('admin.delivery-man.referral')? 'active' :'' }}  ">
                                            {{translate("Referral log")}}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{route('admin.delivery-man.configuration')}}" class="nav-link  {{request()->routeIs('admin.delivery-man.configuration')? 'active' :'' }}  ">
                                            {{translate("Configuration")}}
                                        </a>
                                    </li>

                                    
                                    <li class="nav-item">
                                        <a href="{{route('admin.delivery-man.kyc.configuration')}}" class="nav-link  {{request()->routeIs('admin.delivery-man.kyc.configuration')? 'active' :'' }}  ">
                                            {{translate("KYC configuration")}}
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="{{route('admin.general.deliveryman.setting')}}" class="nav-link  {{request()->routeIs('admin.general.deliveryman.setting')? 'active' :'' }}  ">
                                            {{translate("APP Setting")}}
                                        </a>
                                    </li>

                            </ul>
                        </div>
                    </li>

                @endif


                @if(permission_check('manage_frontend') || permission_check('manage_blog') || permission_check('manage_deal') ||  permission_check('manage_offer') || permission_check('manage_cuppon') ||   permission_check('manage_campaign') )
                    <li class="menu-title">
                        <span>
                            {{translate("Website Setup")}}
                        </span>
                    </li>

                    <li class="nav-item mt-1">
                        <a class="nav-link {{ !request()->routeIs('admin.frontend.promotional.*') || !request()->routeIs('admin.frontend.section.*') ?'collapsed' :''   }} menu-link" href="#forntendSection" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="forntendSection">
                            <i class='bx bx-world'></i> <span>
                                {{translate("Appearances")}}
                            </span>
                        </a>
                        <div class="pt-1 collapse {{request()->routeIs('admin.frontend.section') || request()->routeIs('admin.menu.*') || request()->routeIs('admin.page.*') || request()->routeIs('admin.faq.*') ||  request()->routeIs('admin.frontend.testimonial.*') ||request()->routeIs('admin.blog.*') ||request()->routeIs('admin.home.category') ?'show' :''   }} menu-dropdown" id="forntendSection">
                            <ul class="nav nav-sm flex-column gap-1">
                                <li class="nav-item">
                                    <a href="{{route('admin.frontend.section')}}" class="nav-link {{request()->routeIs('admin.frontend.section')?'active' :'' }}  ">
                                    {{translate("Frontend Section")}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.menu.index')}}" class="nav-link {{request()->routeIs('admin.menu.*')?'active':'' }}  ">
                                      {{translate("Menus")}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.frontend.testimonial.index')}}" class="nav-link {{request()->routeIs('admin.frontend.testimonial.*')?'active' :'' }}  ">
                                      {{translate("Testimonials")}}
                                    </a>
                                </li>

                                @if(permission_check('manage_blog'))
                                    <li class="nav-item">
                                        <a href="{{route('admin.blog.index')}}" class="nav-link {{request()->routeIs('admin.blog.*')?'active' :'' }}  ">
                                        {{translate("Blogs")}}
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a href="{{route('admin.page.index')}}" class="nav-link {{request()->routeIs('admin.page.*')?'active' :'' }}  ">
                                    {{translate("Pages")}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.faq.index')}}" class="nav-link {{request()->routeIs('admin.faq.*')?'active' :'' }}  ">
                                        {{translate('Support FAQ')}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.home.category')}}" class="nav-link {{request()->routeIs('admin.home.category')?'active' :'' }}  ">
                                      {{translate("Home Category")}}
                                    </a>
                                </li>


                            </ul>
                        </div>
                    </li>


                    <li class="nav-item mt-1">
                        <a class="nav-link {{ !request()->routeIs('admin.frontend.promotional.*') || !request()->routeIs('admin.frontend.section.*') ?'collapsed' :''    }} menu-link" href="#manageBanner" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="manageBanner">
                            <i class='bx bx-images'></i> <span>
                                {{translate("Banner")}}
                            </span>
                        </a>
                        <div class="pt-1 collapse {{request()->routeIs('admin.frontend.promotional.*') || request()->routeIs('admin.frontend.section.*') ?'show' :''   }} menu-dropdown" id="manageBanner">
                            <ul class="nav nav-sm flex-column gap-1">
                                <li class="nav-item">
                                    <a href="{{route('admin.frontend.promotional.banner')}}" class="nav-link {{request()->routeIs('admin.frontend.promotional.*')?'active' :'' }}  ">
                                    {{translate("Promotional Banner")}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.frontend.section.banner')}}" class="nav-link {{request()->routeIs('admin.frontend.section.*')?'active' :'' }}  ">
                                    {{translate("Banner")}}</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    @if(permission_check('manage_deal') ||  permission_check('manage_offer') || permission_check('manage_cuppon') ||   permission_check('manage_campaign') || permission_check('manage_frontend') )
                        <li class="nav-item">
                            <a class="nav-link {{ !request()->routeIs('admin.promote.*') || !request()->routeIs('admin.campaign.*') ?'collapsed' :''    }} menu-link" href="#managePromotion" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="managePromotion">
                                <i class='bx bx-volume-low'></i> <span>
                                    {{translate("Marketing")}}
                                </span>
                            </a>
                            <div class="pt-1 collapse {{request()->routeIs('admin.promote.*') || request()->routeIs('admin.campaign.*') || request()->routeIs('admin.subscriber.*') || request()->routeIs('admin.contact.*') ?'show' :''    }} menu-dropdown" id="managePromotion">
                                <ul class="nav nav-sm flex-column gap-1">


                                    @if(permission_check('manage_cuppon'))
                                        <li class="nav-item">
                                            <a href="{{route('admin.promote.coupon.index')}}" class="nav-link {{request()->routeIs('admin.promote.coupon.*')?'active' :'' }}  ">
                                            {{translate("Coupon")}}
                                            </a>
                                        </li>
                                    @endif

                                    @if(permission_check('manage_campaign'))
                                        <li class="nav-item">
                                            <a href="{{route('admin.campaign.index')}}" class="nav-link {{request()->routeIs('admin.campaign.*')?'active' :'' }}  ">
                                            {{translate("Campaign")}}</a>
                                        </li>
                                    @endif

                                    @if(permission_check('manage_offer'))
                                        <li class="nav-item">
                                            <a href="{{route('admin.promote.flash.deals.index')}}" class="nav-link {{request()->routeIs('admin.promote.flash.deals.*')?'active' :'' }}  ">
                                            {{translate("Flash Deals")}}</a>
                                        </li>
                                    @endif

                                    @if(permission_check('manage_frontend'))
                                        <li class="nav-item">
                                            <a class="nav-link {{request()->routeIs('admin.subscriber.*')?'active' :'' }}  " href="{{route('admin.subscriber.index')}}">

                                                    {{translate("Subscribers")}}

                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link {{request()->routeIs('admin.contact.index') ? 'active' :'' }}  " href="{{route('admin.contact.index')}}">

                                                {{translate('Contacts')}}

                                            </a>
                                        </li>

                                    @endif

                                </ul>
                            </div>
                        </li>
                    @endif
                @endif



                @if(permission_check('view_settings') || permission_check('view_languages') || permission_check('view_method'))
                    <li class="menu-title">
                        <span>
                            {{translate('System Setting')}}
                        </span>
                    </li>
                    <li class="nav-item mt-1">
                        <a class="nav-link {{ !request()->routeIs('admin.general.setting.*')? 'collapsed' :''}} menu-link"  href="#generalSetting" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="generalSetting">
                            <i class='bx bx-cog'></i> <span>
                                {{translate('Setup & Configuration')}}
                            </span>
                        </a>
                        <div class="pt-1 collapse {{request()->routeIs('admin.general.setting.*') || request()->routeIs('admin.seo.index') ||  request()->routeIs('admin.mail.*')|| request()->routeIs('admin.sms.*') || request()->routeIs('admin.general.app.setting') || request()->routeIs('admin.shipping.*') ||request()->routeIs('admin.withdraw.method.*') || request()->routeIs('admin.notification.templates.*')  || request()->routeIs('admin.gateway.payment.*') || request()->routeIs('admin.language.*')    ? 'show' :''}} menu-dropdown" id="generalSetting">
                            <ul class="nav nav-sm flex-column gap-1">
                                <li class="nav-item">
                                    <a href="{{route('admin.general.setting.index')}}" class="{{request()->routeIs('admin.general.setting.index')? 'active' :''}}   nav-link">
                                        {{translate('System Setting')}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.general.setting.kyc')}}" class="{{request()->routeIs('admin.general.setting.kyc')? 'active' :''}}   nav-link">
                                        {{translate('Vendor KYC Settings')}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('admin.general.app.setting')}}" class=" {{request()->routeIs('admin.general.app.setting')? 'active' :''}} nav-link">
                                        {{translate('Customer APP Settings')}}
                                    </a>
                                </li>
                            
                                <li class="nav-item">
                                    <a href="{{route('admin.seo.index')}}" class="{{request()->routeIs('admin.seo.index')? 'active' :''}}   nav-link">
                                        {{translate('SEO')}}
                                    </a>
                                </li>

                                @if(permission_check('view_languages'))
                                    <li class="nav-item">
                                        <a class="nav-link
                                            {{request()->routeIs('admin.language.*')?'active' :''}}
                                        " href="{{route('admin.language.index')}}">
                                                {{translate('Languages')}}
                                        </a>
                                    </li>
                                @endif

                                @if(permission_check('view_method'))
                                    <li class="nav-item">
                                        <a class="nav-link  {{request()->routeIs('admin.gateway.payment.*')?'active' :'' }}  " href="{{route('admin.gateway.payment.method')}}">
                                            <span>
                                                {{translate('Payment  Methods')}}
                                            </span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{request()->routeIs('admin.withdraw.method.*')? 'active' :'' }} " href="{{route('admin.withdraw.method.index')}}">
                                            <span>
                                                {{translate('Withdraw Methods')}}
                                            </span>
                                        </a>
                                    </li>
                                @endif


                                <li class="nav-item">
                                    <a href="{{route('admin.notification.templates.index')}}" class="{{request()->routeIs('admin.notification.templates.*')? 'active' :''}}   nav-link">
                                        {{translate('Notification Templates')}}
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a class="nav-link  {{!request()->routeIs('admin.mail.*') ? 'collapsed'  :''   }}  "  href="#emailConfiguration" data-bs-toggle="collapse" role="button"
                                        aria-expanded="false" aria-controls="emailConfiguration">
                                            {{translate('Email Configuration')}}

                                    </a>
                                    <div class="pt-1 collapse

                                    {{request()->routeIs('admin.mail.*') ? 'show'  :''   }}
                                    menu-dropdown" id="emailConfiguration">
                                        <ul class="nav nav-sm flex-column gap-1">
                                            <li class="nav-item">
                                                <a href="{{route('admin.mail.configuration')}}" class='{{request()->routeIs("admin.mail.configuration") || request()->routeIs("admin.mail.edit") ? "active"  :""   }}  nav-link'>
                                                    {{translate('Mail Gateway')}}
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{route('admin.mail.global.template')}}" class="

                                                {{request()->routeIs('admin.mail.global.template') ? 'active'  :''   }}
                                                nav-link">
                                                {{translate('Global template')}}
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link  {{!request()->routeIs('admin.sms.*') ? 'collapsed'  :''   }}  "  href="#smsConfiguration" data-bs-toggle="collapse" role="button"
                                        aria-expanded="false" aria-controls="smsConfiguration">
                                            {{translate('SMS Configuration')}}

                                    </a>
                                    <div class="pt-1 collapse

                                    {{request()->routeIs('admin.sms.*') ? 'show'  :''   }}
                                    menu-dropdown" id="smsConfiguration">
                                        <ul class="nav nav-sm flex-column gap-1">
                                            <li class="nav-item">
                                                <a href="{{route('admin.sms.gateway.index')}}" class='{{request()->routeIs("admin.sms.gateway.index") || request()->routeIs("admin.sms.gateway.edit") ? "active"  :""   }}  nav-link'>
                                                    {{translate('SMS Gateway')}}
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{route('admin.sms.global.template')}}" class="

                                                {{request()->routeIs('admin.sms.global.template') ? 'active'  :''   }}
                                                nav-link">
                                                {{translate('Global template')}}
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </li>

                                @if(permission_check('view_settings') ||  permission_check('manage_countries') || permission_check('manage_states') || permission_check('manage_cities')  || permission_check('manage_zones') )

                                    <li class="nav-item">
                                        <a class="nav-link   {{request()->routeIs('admin.shipping.*') ?'collapsed' :''  }}" href="#shippingMethod" data-bs-toggle="collapse" role="button"
                                            aria-expanded="false" aria-controls="shippingMethod">
                                            <span>

                                                {{translate('Shipping')}}
                                            </span>
                                        </a>
                                        <div class="pt-1 collapse {{request()->routeIs('admin.shipping.*') ?'show' :''  }} menu-dropdown" id="shippingMethod">
                                            <ul class="nav nav-sm flex-column gap-1">

                                                <li class="nav-item">
                                                    <a href="{{route('admin.shipping.configuration.index')}}" class=" {{request()->routeIs('admin.shipping.configuration.*') ?'active' :''  }}  nav-link">
                                                        {{translate('Configuration')}}
                                                    </a>
                                                </li>

                                                @if(permission_check('manage_countries'))
                                                    <li class="nav-item">
                                                        <a href="{{route('admin.shipping.country.index')}}" class=" {{request()->routeIs('admin.shipping.country.*') ?'active' :''  }}  nav-link">
                                                            {{translate('Shipping Country')}}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if(permission_check('manage_states'))
                                                    <li class="nav-item">
                                                        <a href="{{route('admin.shipping.state.index')}}" class=" {{request()->routeIs('admin.shipping.state.*') ?'active' :''  }}  nav-link">
                                                            {{translate('Shipping Sate')}}
                                                        </a>
                                                    </li>
                                                @endif


                                                @if(permission_check('manage_cities'))
                                                    <li class="nav-item">
                                                        <a href="{{route('admin.shipping.city.index')}}" class=" {{request()->routeIs('admin.shipping.city.*') ?'active' :''  }}  nav-link">
                                                            {{translate('Shipping Cities')}}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if(permission_check('manage_zones'))
                                                    <li class="nav-item">
                                                        <a href="{{route('admin.shipping.zone.index')}}" class=" {{request()->routeIs('admin.shipping.zone.*') ?'active' :''  }}  nav-link">
                                                            {{translate('Shipping Zone')}}
                                                        </a>
                                                    </li>
                                                @endif

             
                                                <li class="nav-item">
                                                    <a href="{{route('admin.shipping.delivery.index')}}" class="{{request()->routeIs('admin.shipping.delivery.*') ?'active':''  }}  nav-link">
                                                    {{translate('Shipping Delivery')}}

                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a href="{{route('admin.general.setting.currency.index')}}" class="nav-link {{request()->routeIs('admin.general.setting.currency.index')? 'active' :''}}   ">
                                        {{translate('Currencies')}}
                                    </a>
                                </li>




                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link
                            {{request()->routeIs('admin.general.ai.configuration')?'active' :''}}
                        " href="{{route('admin.general.ai.configuration')}}">

                        <i class='bx bx-bot'></i> <span>
                            {{translate('AI Configuration')}}
                        </span>

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link {{ !request()->routeIs('admin.security.*')   ?'collapsed' :''  }}  "   href="#managaeSecurity" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="managaeSecurity">
                            <i class="ri-shield-check-line"></i> <span>
                                {{translate('Security Settings')}}
                            </span>
                        </a>
                        <div class="pt-1 collapse {{ request()->routeIs('admin.security.*')     ?'show' :''  }}  menu-dropdown" id="managaeSecurity">
                            <ul class="nav nav-sm flex-column gap-1">
                                <li class="nav-item">
                                    <a class="nav-link
                                        {{request()->routeIs('admin.security.ip.list')?'active' :''}}
                                    " href="{{route('admin.security.ip.list')}}">
                                            {{translate('Visitors')}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link
                                        {{request()->routeIs('admin.security.dos')?'active' :''}}
                                    " href="{{route('admin.security.dos')}}">
                                            {{translate('Dos Security')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menu-link
                            {{request()->routeIs('admin.system.update.init')?'active' :''}}
                        " href="{{route('admin.system.update.init')}}">
                        <i class="ri-refresh-line"></i> <span>
                                {{translate('System Upgrade')}}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link
                            {{request()->routeIs('admin.addon.manager')?'active' :''}}
                        " href="{{route('admin.addon.manager')}}">

                        <i class="ri-sound-module-fill"></i>
                        <span>
                                {{translate('Addon manager')}}
                            </span>
                        </a>
                    </li>

                    <li class="nav-item mt-1">
                        <a class="nav-link menu-link {{request()->routeIs('admin.system.info')? 'active' :''}}  " href="{{route('admin.system.info')}}">
                            <i class='bx bx-info-circle'></i> <span>
                                {{translate('System Information')}}
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

