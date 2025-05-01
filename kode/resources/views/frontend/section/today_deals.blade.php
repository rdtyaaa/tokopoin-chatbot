
<section class="pt-80">
    <div class="Container">
        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>{{@frontend_section_data($todays_deal->value,'heading')}} </h3>
                    <p>{{@frontend_section_data($todays_deal->value,'sub_heading')}}</p>
                </div>
            </div>
            <div class="section-title-right">
                <a href="{{route('todays.deal')}}" class="view-more-btn">{{translate('View More')}} </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-3 col-lg-4 col-md-5">
                <div class="todays-deal-banner sticky-side-div">
                    <div class="offer-wrapper">
                        <div class="title">
                            <span>
                                {{translate("Upto")}}
                            </span>
                            <h2>{{@frontend_section_data($todays_deal->value,'banner_title')}}</h2>
                        </div>
                        <p>{{@frontend_section_data($todays_deal->value,'banner_description')}}</p>
                        <div class="date style-two mb-50" id="timerOne">
                            <div class="time-clock">
                                <div class='hr_deal'></div>
                                <div class="mnts_deal"></div>
                                <div class="st_deal"></div>
                            </div>
                        </div>
                        <div class="section-title-right">
                            <a href="{{route('todays.deal')}}" class="all-campaign-btn wave-btn">{{translate('View Products')}} </a>
                        </div>
                    </div>

                    <img class="w-100" src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($todays_deal->value,'image'),@frontend_section_data($todays_deal->value,'image','size'))}}" alt="today-deal.png">

                </div>
            </div>

            <div class="col-xl-9 col-lg-8 col-md-7">
                <div class="row g-2 g-md-4">
                    @include('frontend.partials.product',['products'=>$todays_deals_products])
                </div>
            </div>
        </div>
    </div>
</section>


@if( @frontend_section_data($promo_banner->value,'position') == 'todays-deals')
  @includeWhen($promo_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_banner])
@endif

@if( @frontend_section_data($promo_second_banner->value,'position') == 'todays-deals')
    @includeWhen($promo_second_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_second_banner])
@endif

@push('scriptpush')
<script>
    (function () {
        'use strict';
        function startCountdown(targetTime,timerCountDown){
            const nowx = new Date();
            const target = new Date(`${nowx.toDateString()} ${targetTime}`);
            const diff = target - nowx;
            var st = Math.floor(diff / 1000);

            function updateCountdown() {
                const nowx = new Date();

                const diff = target - nowx;
                st = Math.floor(diff / 1000);
                if (st <= 0) {
                    clearInterval(timer);
                    return;
                }
                const hr = Math.floor(st / 3600);
                st -= hr * 3600;
                const mnts = Math.floor(st / 60);
                st -= mnts * 60;

                const timerCounter =document.querySelector(timerCountDown)
                let spanSingleHr = timerCounter.querySelector(".hr_deal");
                let spanSingleMin = timerCounter.querySelector(".mnts_deal");
                let spanSingleSec = timerCounter.querySelector(".st_deal");

                let formattedHours =  spanSingleHr.textContent = hr.toString().padStart(2, "0");
                let formattedMins = spanSingleMin.textContent = mnts.toString().padStart(2, "0");
                let formattedSecs = spanSingleSec.textContent = st.toString().padStart(2, "0");

                spanSingleHr.textContent = formattedHours;
                spanSingleMin.textContent = formattedMins;
                spanSingleSec.textContent = formattedSecs;


                let creatPHour = document.createElement("p");
                let creatPMin = document.createElement("p");
                let creatPSec = document.createElement("p");

                creatPHour.textContent = "Hours";
                spanSingleHr.appendChild(creatPHour);

                creatPMin.textContent = "Minutes";
                spanSingleMin.appendChild(creatPMin);

                creatPSec.textContent = "Seconds";
                spanSingleSec.appendChild(creatPSec);

            }

            updateCountdown();
            const timer = setInterval(updateCountdown, 1000);
        }

        startCountdown("23:59:59", "#timerOne");

    })();

</script>
@endpush
