<section class="promotional-banner pt-80 pb-80">
    <div class="Container">
        <div class="add-banners">
            <div class="add-banner">
                <a href="{{@frontend_section_data($promo_banner->value,'image','url')}}">
                     <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($promo_banner->value,'image'),@frontend_section_data($promo_banner->value,'image','size'))}}" alt="promo_banner.jpg" >
                </a>
            </div>

            <div class="add-banner">
                <a href="{{@frontend_section_data($promo_banner->value,'image_2','url')}}">
                     <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($promo_banner->value,'image_2'),@frontend_section_data($promo_banner->value,'image_2','size'))}}" alt="promo_banner.jpg">
                </a>
            </div>
        </div>
    </div>
</section>
