<div class="header-menu d-lg-block d-none">
    <div class="Container">
        <div class="header-menu-container">
            <div class="header-menuLeft">
                <div class="menu-category">
                    <div class="menu-category-btn collapsed menu-category-tigger


                    " data-bs-toggle="collapse" data-bs-target="#categoryCollapse" role="button">
                        <div class="d-flex align-items-center gap-3">
                            <svg  version="1.1"   x="0" y="0" viewBox="0 0 24 24"   xml:space="preserve" ><g><g  fill-rule="evenodd" clip-rule="evenodd"><path d="M2.25 4c0-.967.783-1.75 1.75-1.75h5c.967 0 1.75.784 1.75 1.75v5A1.75 1.75 0 0 1 9 10.75H4A1.75 1.75 0 0 1 2.25 9zM4 3.75a.25.25 0 0 0-.25.25v5c0 .138.112.25.25.25h5A.25.25 0 0 0 9.25 9V4A.25.25 0 0 0 9 3.75zM2.25 15c0-.967.784-1.75 1.75-1.75h5c.967 0 1.75.783 1.75 1.75v5A1.75 1.75 0 0 1 9 21.75H4A1.75 1.75 0 0 1 2.25 20zM4 14.75a.25.25 0 0 0-.25.25v5c0 .138.112.25.25.25h5a.25.25 0 0 0 .25-.25v-5a.25.25 0 0 0-.25-.25zM13.25 4c0-.966.783-1.75 1.75-1.75h5c.967 0 1.75.784 1.75 1.75v5A1.75 1.75 0 0 1 20 10.75h-5A1.75 1.75 0 0 1 13.25 9zM15 3.75a.25.25 0 0 0-.25.25v5c0 .138.112.25.25.25h5a.25.25 0 0 0 .25-.25V4a.25.25 0 0 0-.25-.25zM13.25 15c0-.967.783-1.75 1.75-1.75h5c.967 0 1.75.783 1.75 1.75v5A1.75 1.75 0 0 1 20 21.75h-5A1.75 1.75 0 0 1 13.25 20zm1.75-.25a.25.25 0 0 0-.25.25v5c0 .138.112.25.25.25h5a.25.25 0 0 0 .25-.25v-5a.25.25 0 0 0-.25-.25z"  opacity="1" data-original="#000000" ></path></g></g></svg>
                            <p class="d-flex align-items-center gap-3">
                                <span>
                                    {{translate('Categories')}}
                                </span>
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-down category-dropdown-icon"></i>
                    </div>
                </div>

                @php
                    $physicalCategories = $categories->filter(function ($category) {
                       return $category->physicalProduct->isNotEmpty();
                    });
              
               @endphp
                <div class="floting-categories">
                    <div class="collapse" id="categoryCollapse">
                        <div class="browse-categories d-lg-block d-none">
                            <ul class="browse-categories-items">
                                @forelse($physicalCategories->take(9) as $category)
                                    <li class="browse-categories-item">
                                        <a href="{{route('category.product', [$category->slug ? $category->slug :   make_slug(@get_translation($category->name)), $category->id])}}">
                                            <div >
                                                <span>
                                                  <img src="{{show_image(file_path()['category']['path'].'/'.$category->image_icon,file_path()['category']['size'])}}" alt="{{$category->image_icon}}">
                                                </span>
                                                {{@get_translation($category->name)}}
                                            </div>
                                            @if($category->parent_count !=0)
                                                <i class="fa-solid fa-chevron-right"></i>
                                            @endif
                                        </a>
                                        @if($category->parent_count !=0)
                                            <div class="categories-dropdown">
                                                <ul class="categories-dropdown-items">
                                                    @foreach($category->parent as $childCat)
                                                        <li class="categories-dropdown-item"><a href="{{route('category.sub.product', [ $category->slug ? $category->slug : make_slug(@get_translation($category->name)), $childCat->id])}}">{{@get_translation($childCat->name)}} </a></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </li>
                                @empty

                                    <li class="browse-categories-item">
                                        @include("frontend.partials.empty",['message' => 'No Categories Found'])
                                    </li>

                                @endforelse
                                @if($categories->count() > 0)
                                    <li class="browse-categories-item">
                                        <a href="{{route('all.category')}}">
                                            {{translate('View All')}}
                                            <span><i class="fa-solid fa-chevron-right"></i></span></a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-menuRight">
                   <ul class="menus">
                        @foreach ($menus as $menu)
                            <li class="menu">
                                <a class="navLink @php echo menu_active($menu->url) @endphp" href="{{url($menu->url)}}">
                                    {{$menu->name}}
                                </a>
                            </li>
                        @endforeach
                  </ul>
            </div>
        </div>
    </div>
</div>
