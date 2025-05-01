@extends('frontend.layouts.app')
@section('content')

<div class="breadcrumb-banner">
     <div class="breadcrumb-banner-img">
         <img src="{{show_image(file_path()['frontend']['path'].'/'.frontend_section_data($breadcrumb->value,'image'),frontend_section_data($breadcrumb->value,'image','size'))}}" alt="breadcrumb.jpg">
     </div>
    <div class="page-Breadcrumb">
        <div class="Container">
            <div class="breadcrumb-container">
                <h1 class="breadcrumb-title">{{($title)}}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{url('/')}}">
                            {{translate('home')}}
                        </a></li>

                        <li class="breadcrumb-item active" aria-current="page">
                            {{translate($title)}}
                        </li>

                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>


<section class="pb-80">
    <div class="Container">
        <div class="section-title">
            <div class="section-title-left">
                <div class="title-left-content">
                    <h3>
                        {{translate("All Campaigns")}}
                    </h3>
                    <p>
                        {{translate("All of our Campaigns")}}
                    </p>
                </div>
            </div>
        </div>
        @php
           $dates = [];

        @endphp
        <div class="allCampaigns">
            <div class="row g-4">
                @forelse($campaigns as $campaign)
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

@endsection


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
