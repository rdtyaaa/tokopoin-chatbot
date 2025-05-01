@extends('frontend.layouts.app')
@section('content')
@php
    $support = frontend_section('support');
@endphp
<div class="breadcrumb-banner">
    <div class="breadcrumb-banner-img">
        <img src="{{show_image(file_path()['frontend']['path'].'/'.@frontend_section_data($breadcrumb->value,'image'),@frontend_section_data($breadcrumb->value,'image','size'))}}" alt="breadcrumb.jpg">
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


<section class="faq-section pb-80">

        <div class="Container">
            <div class="sidebar-support-container">
                <div class="row g-4 justify-content-start align-items-center mb-40">
                    <div class="col-lg-9">
                        <div class="title-left-content">
                            <h3>{{@frontend_section_data($support->value,'heading')}}</h3>
                            <p>{{@frontend_section_data($support->value,'sub_heading')}}</p>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <div class="support-form">
                            <input type="text" name="search" id="searchFaq" placeholder="Start typing your search..">
                            <i class="las la-search"></i>
                        </div>
                    </div>
                </div>
                <h4>{{translate('Frequently asked questions')}}</h4>
                <div class="faqs-tab">
                    <div class="nav faq-options" id="nav-tab" role="tablist">
                        <div class="nav-link active" id="information-tab" data-bs-toggle="tab" data-bs-target="#info-tab" role="tab" aria-controls="info-tab" aria-selected="false">
                            <div class="faq-option">
                                <div class="faq-option-icon">
                                    <i class="las la-info"></i>
                                </div>
                                <span>{{ translate('Information') }}</span>
                            </div>
                        </div>
                        <div class="nav-link" id="pricingPlan-tab" data-bs-toggle="tab" data-bs-target="#pricingPlan" role="tab" aria-controls="pricingPlan" aria-selected="false">
                            <div class="faq-option">
                                <div class="faq-option-icon">
                                <i class="las la-tags"></i>
                                </div>
                                <span> {{translate("Pricing & Plans")}}</span>
                            </div>
                        </div>
                        <div class="nav-link" id="subscription-tab" data-bs-toggle="tab" data-bs-target="#subscription" role="tab" aria-controls="subscription" aria-selected="false">
                            <div class="faq-option">
                                <div class="faq-option-icon">
                                <i class="las la-question"></i>
                                </div>
                                <span>
                                    {{translate("Sales Question")}}
                                </span>
                            </div>
                        </div>
                        <div class="nav-link" id="helps-tab" data-bs-toggle="tab" data-bs-target="#helps" role="tab" aria-controls="helps" aria-selected="false">
                            <div class="faq-option">
                                <div class="faq-option-icon">
                                <i class="las la-book"></i>
                                </div>
                                <span>
                                    {{translate("Usage Guides")}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="faqs-tabs-content">

                    <div class="tab-content support-faqs-container " id="nav-tabContent">

                        <div class="search-faq-section tab-pane fade show active" id="info-tab" role="tabpanel" aria-labelledby="information-tab">
                                <div class="accordion accordion-flush" id="information">

                                    @php
                                        $supportFaqs = $faqs->where('support_category','Information Center')
                                    @endphp
                                    @foreach($supportFaqs as $faq)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header faq-accordion-header" id="information-heading-{{$faq->id}}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#information-collapse{{$faq->id}}" aria-expanded="false" aria-controls="information-collapse{{$faq->id}}">
                                                    {{$faq->question }} <span class="accordion_button_icon"><i class="fa-solid fa-chevron-down"></i></span>
                                                </button>
                                            </h2>
                                            <div id="information-collapse{{$faq->id}}" class="accordion-collapse collapse" aria-labelledby="information-heading-{{$faq->id}}" data-bs-parent="#informationFaqs">
                                                <div class="accordion-body">  {{ $faq->answer }}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                </div>
                        </div>

                        <div class="search-faq-section tab-pane fade" id="pricingPlan" role="tabpanel" aria-labelledby="pricingPlan-tab">
                            <div class="accordion accordion-flush" id="plan-section">

                                @php
                                    $supportFaqs = $faqs->where('support_category','Pricing & Plans')
                                @endphp

                                @foreach($supportFaqs as $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header faq-accordion-header" id="information-heading-plans-{{$faq->id}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#information-collapse-plans{{$faq->id}}" aria-expanded="false" aria-controls="information-collapse-plans{{$faq->id}}">
                                            {{$faq->question }} <span class="accordion_button_icon"><i class="fa-solid fa-chevron-down"></i></span>
                                        </button>
                                        </h2>
                                        <div id="information-collapse-plans{{$faq->id}}" class="accordion-collapse collapse" aria-labelledby="information-heading-plans-{{$faq->id}}" data-bs-parent="#pricingPlan">
                                            <div class="accordion-body">  {{ $faq->answer }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="search-faq-section tab-pane fade" id="subscription" role="tabpanel" aria-labelledby="subscription-tab">
                                <div class="accordion accordion-flush" id="subscription-section">

                                @php
                                    $supportFaqs = $faqs->where('support_category','Sales And Question')
                                @endphp
                                @foreach($supportFaqs as $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header faq-accordion-header" id="information-heading-question-{{$faq->id}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#information-collapse-question{{$faq->id}}" aria-expanded="false" aria-controls="information-collapse-question{{$faq->id}}">
                                            {{$faq->question }} <span class="accordion_button_icon"><i class="fa-solid fa-chevron-down"></i></span>
                                        </button>
                                        </h2>
                                        <div id="information-collapse-question{{$faq->id}}" class="accordion-collapse collapse" aria-labelledby="information-heading-question-{{$faq->id}}" data-bs-parent="#subscription">
                                        <div class="accordion-body">  {{ $faq->answer }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class=" search-faq-section  tab-pane fade" id="helps" role="tabpanel" aria-labelledby="helps-tab">
                            <div class="accordion accordion-flush" id="helps-section">

                                @php
                                    $supportFaqs = $faqs->where('support_category','Usage Guide')
                                @endphp
                                @foreach($supportFaqs as $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header faq-accordion-header" id="information-heading-guide-{{$faq->id}}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#information-collapse-guide{{$faq->id}}" aria-expanded="false" aria-controls="information-collapse-guide{{$faq->id}}">
                                            {{$faq->question }} <span class="accordion_button_icon"><i class="fa-solid fa-chevron-down"></i></span>
                                        </button>
                                        </h2>
                                        <div id="information-collapse-guide{{$faq->id}}" class="accordion-collapse collapse" aria-labelledby="information-heading-guide-{{$faq->id}}" data-bs-parent="#helps">
                                        <div class="accordion-body">  {{ $faq->answer }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
