
<section class="service-featured d-md-block d-none pt-40">
    <div class="Container">
        <div class="row g-0">
            @foreach(json_decode($service_sections->value,true) as $service)
                <div class="col-xl-3 col-sm-6">
                    <div class="service-featured-item">
                        <span class="service-featured-icon">
                            @php echo @$service['icon'] @endphp
                        </span>
                        <div class="service-featured-content">
                            <span> {{@$service['heading']}}</span>
                            <small> {{@$service['sub_heading']}}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@if( @frontend_section_data($promo_banner->value,'position') == 'service-section')
  @includeWhen($promo_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_banner])
@endif

@if( @frontend_section_data($promo_second_banner->value,'position') == 'service-section')
    @includeWhen($promo_second_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_second_banner])
@endif
