  @if(count($banners) > 0)
    <div class="banner">
      <div class="Container">
        <div class="banner-container">
          <div class="browse-by-categorie d-xl-block d-none">
              <ul class="browse-categories-items">
                   @php
                      $physicalCategories = $categories->filter(function ($category) {
                        return $category->physicalProduct->isNotEmpty();
                      });
                    
                   @endphp
                  @foreach($physicalCategories->take(9) as $category)
                      <li class="browse-categories-item">
                          <a href="{{route('category.product', [$category->slug ? $category->slug :  make_slug(@get_translation($category->name)), $category->id])}}">
                              <div>
                                  <span>
                                    <img class="w-100" src="{{show_image(file_path()['category']['path'].'/'.$category->image_icon,file_path()['category']['size'])}}" alt="{{$category->image_icon}}">
                                  </span>
                                  {{@get_translation($category->name)}}
                              </div>
                                @if($category->parent_count !=0)
                                    <i class="fa-solid fa-chevron-right"></i>
                                @endif
                          </a>
                          @if($category->parent_count !=0)
                              <div class="categories-dropdown">
                                <h6 class="categories-dropdown-title"> {{@get_translation($category->name)}}</h6>
                                  <ul class="categories-dropdown-items">
                                      @foreach($category->parent as $childCat)
                                          <li class="categories-dropdown-item"><a href="{{route('category.sub.product', [ $category->slug ? $category->slug : make_slug(@get_translation($category->name)), $childCat->id])}}">{{@get_translation($childCat->name)}} </a></li>
                                      @endforeach
                                  </ul>
                              </div>
                          @endif
                      </li>
                  @endforeach
              </ul>

                <a href="{{route('all.category')}}" class="browse-categories-btn">
                        {{translate('View All')}}
                        <span><i class="fa-solid fa-chevron-right"></i></span>
                </a>
          </div>
          <div class="banner-wrapper">
            <div class="swiper banner-slider">
                <div class="swiper-wrapper">
                      @foreach($banners as $banner)
                        <div class="swiper-slide">
                            <a href="{{$banner->btn_url}}" class=" banner-slide-img" >
                                <img class="w-100" src="{{show_image(file_path()['banner_image']['path'].'/'.$banner->bg_image,file_path()['banner_image']['size'])}}" alt="{{$banner->bg_image}}">
                            </a>
                        </div>
                      @endforeach
                  </div>
                <div class="banner-pagination pagination-one"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif



