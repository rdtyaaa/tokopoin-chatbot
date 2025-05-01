
@if(count($campaigns)>0)
<section class="upcomming-Campaign pb-80">
    <div class="Container">
        <div class="campaign-container">
            <div class="section-title">
                <div class="section-title-left">
                    <div class="title-left-content">
                        <h3 class="mb-0">
                            {{@frontend_section_data($campaign_section->value,'heading')}}
                        </h3>
                    </div>
                </div>
                <div class="section-title-right">
                    <a href="{{route('campaign')}}" class="view-more-btn">
                        {{translate("View More")}}

                    </a>
                </div>
            </div>

            @php
               $dates = [];
            @endphp


            <div class="row g-4">
                @forelse ($campaigns as $campaign)
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <a href="{{route('campaign.details',$campaign->slug)}}" class="campaign">
                            <div class="campaign-ban">
                                <img src="{{show_image(file_path()['campaign_banner']['path'].'/'.$campaign->banner_image,file_path()['campaign_banner']['size'])}}" alt="{{$campaign->banner_image}}">
                            </div>
                            <div class="campaign-info text-center">
                                <h5 class="fs-16 text-muted mb-4">
                                    {{$campaign->name}}
                                </h5>
                                <div class="campaign-time mt-3">
                                        <div class="campaign-time-item">
                                            <span class="days"></span>
                                            <small>
                                                {{translate("Days")}}
                                            </small>
                                        </div>
                                        <div class="campaign-time-item">
                                            <span class='hours'></span>
                                            <small >
                                               {{translate("Hours")}}
                                            </small>
                                        </div>
                                        <div class="campaign-time-item">
                                            <span class="minutes"></span>
                                            <small >
                                                {{translate("Minute")}}
                                            </small>
                                        </div>
                                        <div class="campaign-time-item">
                                            <span class="seconds"></span>
                                            <small >
                                                {{translate('Second')}}
                                            </small>
                                        </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    @php
                       array_push($dates ,date("Y-m-d H:i:s",strtotime($campaign->end_time)))
                    @endphp
                @empty
                   <div class="col-12">
                        @include("frontend.partials.empty",['message' => 'No Data Found'])
                    </div>
                @endforelse

            </div>

        </div>
    </div>
</section>

@if( @frontend_section_data($promo_banner->value,'position') == 'campaign')
  @includeWhen($promo_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_banner])
@endif

@if( @frontend_section_data($promo_second_banner->value,'position') == 'campaign')
    @includeWhen($promo_second_banner->status == '1', 'frontend.section.promotinal_banner', ['promo_banner' => $promo_second_banner])
@endif

@push('scriptpush')
<script>
    'use strict';
     var dateTime = <?php echo json_encode($dates); ?>;
     const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;
        var days = document.getElementsByClassName('days')
        var hours = document.getElementsByClassName('hours')
        var minutes = document.getElementsByClassName('minutes')
        var seconds = document.getElementsByClassName('seconds')
     for(let i =0 ;i<dateTime.length; i++){
        var time = dateTime[i];
        const countDown = new Date(time).getTime(),
            x = setInterval(function() {
            const now = new Date().getTime(),
            distance = countDown - now;
            days[i].innerText =  Math.floor(distance / (day));
            hours[i].innerText =  Math.floor((distance % (day)) / (hour));
            minutes[i].innerText =  Math.floor((distance % (hour)) / (minute));
            seconds[i].innerText =   Math.floor((distance % (minute)) / second);

        }, 0)
     }
</script>
@endpush

@endif
