
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="menu-sidebar" aria-labelledby="menu-sidebar">
    <div class="sidebar-top">
        <div class="sidebar-logo">
            <a href="{{route('home')}}">
                <img src="{{ show_image('assets/images/backend/logoIcon/'.site_settings('site_logo'), file_path()['site_logo']['size']) }}" alt="site_logo.png">
            </a>
        </div>
        <button type="button" class="sidebar-closer" data-bs-dismiss="offcanvas" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sidebar-middel-container">
            <div class="mt-4 mb-4 pb-3 d-flex align-items-center justify-content-between gap-3">
                <div class="lang-dropdown">
                    <div class="Dropdown">
                        @php
                            $lang = $languages->where('code',strtolower(session()->get('locale')));
                            $code = count($lang)!=0 ? $lang->first()->code:"en";
                            $languages = $languages->where('code','!=',$code);
                        @endphp

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
                <div class="currency-dropdown">
                    @php
                        $site_currency  = session()->get('web_currency');
                    @endphp
                    <div class="Dropdown">
                        <button class="dropdown__button" type="button"> {{  $site_currency->name}}
                            @if(count($languages) != 0)
                              <span class="dropdown_button_icon"><i class="fa-solid fa-chevron-down"></i></span>
                            @endif
                        </button>
                        <ul class="dropdown__list">
                            @foreach($currencys as $currency)
                                <li class="chanage_currency dropdown__list-item {{$site_currency->id == $currency->id ? 'dropdown__list-item_active' :'' }}  " data-value="{{$currency->id}}">{{$currency->name}}</li>
                            @endforeach
                        </ul>
                        <input class="dropdown__input_hidden" type="text" name="select-category" value=""/>
                    </div>
                </div>
            </div>
            <div class="sidebar-middel">
                <div class="mobile-category-container">
                    <button class="w-100 mobile-categoryBtn" type="button" data-bs-toggle="collapse" data-bs-target="#mobileCategoryBtn" aria-expanded="false" aria-controls="mobileCategoryBtn">
                        <span class="d-flex align-items-center gap-3">
                            <i class="fa-solid fa-border-all fs-14"></i>

                            {{translate("All Categories")}}
                        </span>
                        <i class="fa-solid fa-chevron-down fs-14 chevron-rotate"></i>
                    </button>
                    <div class="collapse" id="mobileCategoryBtn">
                        <ul class="browse-categories-items">
                            @php
                                    $physicalCategories = $categories->filter(function ($category) {
                                       return $category->physicalProduct->isNotEmpty();
                                    });
                      
                           @endphp
                            @foreach($physicalCategories->take(9) as $category)
                                <li class="browse-categories-item flex-column">
                                    <a href="{{route('category.product', [ $category->slug ? $category->slug :  make_slug(@get_translation($category->name)), $category->id])}}" @if($category->parent_count > 0) data-bs-toggle="collapse" data-bs-target="#mobileCategory-{{$loop->index}}" aria-expanded="false" aria-controls="mobileCategory-{{$loop->index}}" @endif >
                                        <div >
                                            <span>
                                                <img src="{{show_image(file_path()['category']['path'].'/'.$category->image_icon,file_path()['category']['size'])}}" alt="{{$category->image_icon}}">
                                            </span>
                                            {{@get_translation($category->name)}}
                                        </div>
                                        @if($category->parent_count !=0)
                                            <i class="fa-solid fa-chevron-down"></i>
                                        @endif
                                    </a>
                                    @if($category->parent_count !=0)
                                        <div class="collapse mobilecategories-dropdown" id="mobileCategory-{{$loop->index}}">
                                            <ul class="categories-dropdown-items">
                                                @foreach($category->parent as $childCat)
                                                    <li class="categories-dropdown-item"><a href="{{route('category.product', [$category->slug ? $category->slug :  make_slug(@get_translation($category->name)), $category->id])}}">    {{@get_translation($childCat->name)}} <i
                                                            class="fa-solid fa-chevron-right"></i></a></li>
                                                @endforeach

                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                            <li class="browse-categories-item">
                                <a href="{{route('all.category')}}">
                                        {{translate("See All Categories")}}
                                    <span><i class="fa-solid fa-chevron-right"></i></span></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="sidebar-navbar mt-3">
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            @foreach ($menus as $menu)
                                <a href="{{url($menu->url)}}" class="sidebar-nav-link ">
                                    <span>
                                    <small class="mobile-nav-icon"><img class="rounded-circle " src="{{show_image(file_path()['menu']['path'].'/'.$menu->image,file_path()['menu']['size'])}}" alt="{{$menu->name}}"> </small>
                                    {{$menu->name}}</span> <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            @endforeach
                        </li>
                    </ul>
                </div>

                <div class="sidebar-navbar border-top mt-4 pt-2">
                    <ul class="sidebar-nav">
                        <li class="sidebar-nav-item">
                            <a href="{{'user.wishlist.item'}}" class="sidebar-nav-link">
                                <span>
                                <small class="mobile-nav-icon"><i class="fa-regular fa-heart"></i></small>
                                    {{translate("Wishlist")}}
                                </span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </li>
                        <li class="sidebar-nav-item">
                            <a href="{{route('compare')}}" class="sidebar-nav-link">
                                <span> <small class="mobile-nav-icon"><i class="fa-solid fa-code-compare"></i></small>
                                    {{translate("Compare")}}
                                </span> <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </li>
                        @if(auth_user('web'))
                            <li class="sidebar-nav-item">
                                <a href="{{'user.track.order'}}" class="sidebar-nav-link">
                                    <span>
                                        <small class="mobile-nav-icon"><i class="fa-solid fa-location-crosshairs"></i></small>
                                        {{translate("Track Order")}}
                                    </span>
                                <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </li>
                        @endif

       
                        @if(site_settings('multi_vendor',App\Enums\StatusEnum::true->status()) ==  App\Enums\StatusEnum::true->status())
                            <li class="sidebar-nav-item">
                                <a href="{{route('seller.register')}}" class="sidebar-nav-link">
                                    <span>
                                        <small class="mobile-nav-icon"><i class="fa-solid fa-store"></i></small>
                                        {{translate("Become A Seller")}}
                                    </span>
                                <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="filter-sidebar" aria-labelledby="filter-sidebar">
    <div class="sidebar-top">
        <div class="sidebar-logo">
            <a href="{{route('home')}}">
                <img src="{{ show_image('assets/images/backend/logoIcon/'.site_settings('site_logo'), file_path()['site_logo']['size']) }}" alt="{{site_settings('site_logo')}}">
            </a>
        </div>
        <button type="button" class="sidebar-closer" data-bs-dismiss="offcanvas" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="sidebar-middel-container">
            <div class="sidebar-middel">
                <div>
                    <div class="card-header px-0 py-4">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <h5 class="card-title">
                                    {{translate('Filter')}}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class=" filter-accordion">
                        <div class="py-4 border-bottom">
                            <p class="text-uppercase fs-13 fw-semibold filter-by">
                                {{translate('Category')}}
                            </p>
                            <ul class="list-unstyled mb-0 mt-3 filter-list">
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{route('category.product', [$category->slug ? $category->slug :   make_slug(@get_translation($category->name)), $category->id])}}" class="d-flex py-1 align-items-center position-relative">
                                            <div class="flex-grow-1">
                                                <h5 class="listname">{{@get_translation($category->name)}}</h5>
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <span class="flex-shrink-0 ms-2 badge bg-light text-muted fs-12">
                                                    {{
                                                        $category->houseProduct->count()
                                                    }}
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="py-4 border-bottom">
                            <p class="text-uppercase fs-13 fw-semibold filter-by">
                                {{translate("Filter By Price")}}
                            </p>
                            <form action="{{route(Route::currentRouteName(),Route::current()->parameters())}}" method="GET">
                                <div class="range-slider mb-4">
                                    @php
                          			$search_min = request()->input('search_min') 
						                    ? request()->input('search_min') 
											: ((short_amount(site_settings('search_min',0),false,false)));

                                $search_max = request()->input('search_max') 
                                                        ?  request()->input('search_max') 
                                                        :  ((short_amount(site_settings('search_max',0),false,false)));
                                    @endphp
                                    <div class="slider-area">
                                        <div id="responsive-range" class="slider">

                                        </div>
                                    </div>
                                    <div class="formCost d-flex gap-2 align-items-center">
                                        <input class="form-control form-control-sm" name="search_min" id="skip-value-lower-1" type="number"  value="{{$search_min}}" min="{{$search_min}}" max="{{$search_max}}" />
                                            <span class="text-muted fs-14">
                                                {{translate('to')}}
                                            </span>
                                        <input
                                            class="form-control form-control-sm" name="search_max" id="skip-value-upper-1" type="number" value="{{$search_max}}" min="{{$search_min}}" max="{{$search_max}}"/>
                                    </div>
                                </div>
                                <button type="submit" class="address-btn wave-btn w-100">
                                    {{translate('filter')}}
                                </button>
                           </form>
                        </div>
                        <div class="py-4">
                            <span class="text-uppercase fs-13 fw-semibold filter-by">
                                {{translate('Brands')}}
                            </span>
                            <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                <ul class="list-unstyled mb-0 filter-list">
                                    @foreach($brands as $brand)
                                        <li>
                                            <a href="{{route('brand.product',[ $brand->slug ? $brand->slug :  make_slug(@get_translation($brand->name)), $brand->id])}}" class="d-flex align-items-center position-relative">
                                                <div class="flex-grow-1">
                                                    <h5 class="listname @if(request()->routeIs('brand.product'))
                                                        {{request()->route('brand_id') == $brand->id ? 'cate-menu-active' :'' }}
                                                        @endif ">{{(@get_translation($brand->name))}}</h5>
                                                </div>

                                                <span class="flex-shrink-0 ms-2 badge bg-light text-muted fs-12">{{($brand->houseProduct->count())}}</span>

                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scriptpush')
<script>
    'use strict';

	var rangeSearch = document.getElementById("responsive-range");
	if (rangeSearch != null) {
		var y = [
			document.getElementById("skip-value-lower-1"),
			document.getElementById("skip-value-upper-1")
		];

		noUiSlider.create(rangeSearch, {
			start: [{{$search_min }},{{$search_max}}],
			connect: true,
			behaviour: "drag",
			step: 1,
            range: {
				min: {{round((double)short_amount(site_settings('search_min',0),false,false))}},
				max: {{round((double)short_amount( site_settings('search_max',0),false,false))}}
			},
			format: {
				from: function (value) {
					return parseInt(value);
				},
				to: function (value) {
					return parseInt(value);
				}
			}
		});

		rangeSearch.noUiSlider.on("update", function (values, handle) {
			y[handle].value = values[handle];
		});
	}

</script>
@endpush

