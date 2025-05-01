
<section class="pb-80">
    <div class="Container">
        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>{{@frontend_section_data($top_brands_sections->value,'heading')}} </h3>
                    <p>{{@frontend_section_data($top_brands_sections->value,'sub_heading')}}</p>
                </div>
            </div>
            <div class="section-title-right">
              <a href="{{route('top.brand')}}" class="view-more-btn">
                   {{translate("View More")}}
                </a>
            </div>
        </div>

        <div class="row g-2 g-sm-4">
            @forelse($brands->take(12) as $brand)
                <div class="col-xxl-2 col-xl-3 col-md-4 col-sm-6">
                    <a href="{{route('brand.product', [ $brand->slug ? $brand->slug :  make_slug(get_translation($brand->name)), $brand->id])}}" class="brand-item">
                        <div class="top-brand-logo">
                                <img src="{{show_image(file_path()['brand']['path'].'/'.$brand->logo ,file_path()['brand']['size'])}}" alt="{{$brand->logo}}">
                        </div>
                        <div class="top-brand-info">
                            <h5 class="text-break">{{@(get_translation($brand->name))}}</h5>
                                <p class="fs-12">
                                        ({{($brand->houseProduct->count())}})
                                    {{translate('Products')}}
                                </p>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12">
                    @include("frontend.partials.empty",['message' => 'No product found'])
                </div>
            @endforelse
        </div>
    </div>
</section>

@if( @frontend_section_data($promo_banner->value,'position') == 'top-brands')
    @includeWhen($promo_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_banner])
@endif

@if( @frontend_section_data($promo_second_banner->value,'position') == 'top-brands')
    @includeWhen($promo_second_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_second_banner])
@endif
