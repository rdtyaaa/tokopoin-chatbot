@extends('frontend.layouts.app')
@push('stylepush')
    <style>
        .h-400 {
            height: 400px;
        }
        .map-search-input {
            width: 100%;
            max-width: 250px;
            position: relative!important;
            top: 6px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            background-color: white!important;

        }

        .form-check-label{
            padding-bottom: 1.5rem !important;
        }

        .custom-payment{
            width:60px;
            height:60px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
    </style>
@endpush
@section('content')

@php
   $shippingConfiguration      = json_decode(site_settings('shipping_configuration'));

@endphp

    <div class="breadcrumb-banner">
        <div class="breadcrumb-banner-img">
            <img src="{{ show_image(file_path()['frontend']['path'] . '/' . @frontend_section_data($breadcrumb->value, 'image'), @frontend_section_data($breadcrumb->value, 'image', 'size')) }}"
                alt="breadcrumb.jpg">
        </div>
        <div class="page-Breadcrumb">
            <div class="Container">
                <div class="breadcrumb-container">
                    <h1 class="breadcrumb-title">{{ $title }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('/') }}">
                                    {{ translate('home') }}
                                </a></li>

                            <li class="breadcrumb-item active" aria-current="page">
                                {{ translate($title) }}
                            </li>

                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="pb-80">
        <div class="Container"> 
            <form action="{{ route('user.order') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-xxl-8 col-xl-7 col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    {{ translate('Checkout') }}
                                </h5>
                            </div>

                            <div class="card-body checkout-tab">
                                <div class="step-arrow-nav">
                                    <ul class="nav nav-pills nav-justified custom-nav" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link p-3 active wave-btn" id="pills-bill-info-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-bill-info" type="button"
                                                role="tab" aria-controls="pills-bill-info" aria-selected="true">
                                                <svg version="1.1" x="0" y="0" viewBox="0 0 32 32" xml:space="preserve"
                                                    fill-rule="evenodd">
                                                    <g>
                                                        <path
                                                            d="m25.961 28.749.039.001.032-.003c.144-.018.718-.128.718-.747A9.75 9.75 0 0 0 17 18.25h-2a9.75 9.75 0 0 0-9.748 9.796.664.664 0 0 0 .242.507.747.747 0 0 0 .506.197s.75-.043.75-.75A8.25 8.25 0 0 1 15 19.75h2a8.25 8.25 0 0 1 8.252 8.296.664.664 0 0 0 .242.507.746.746 0 0 0 .467.196z"
                                                            opacity="1" data-original="#000000"></path>
                                                        <path
                                                            d="M16 3.25c-4.553 0-8.25 3.697-8.25 8.25s3.697 8.25 8.25 8.25 8.25-3.697 8.25-8.25S20.553 3.25 16 3.25zm0 1.5c3.725 0 6.75 3.025 6.75 6.75s-3.025 6.75-6.75 6.75-6.75-3.025-6.75-6.75S12.275 4.75 16 4.75z"
                                                            opacity="1" data-original="#000000"></path>
                                                    </g>
                                                </svg>
                                                {{ translate('Personal Info') }}
                                            </button>
                                        </li>

                                        @if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC")
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link p-3 wave-btn shipping-tab" id="pills-bill-address-tab"
                                                    data-bs-toggle="pill" data-bs-target="#pills-bill-address" type="button"
                                                    role="tab" aria-controls="pills-bill-address" aria-selected="false">
                                                    <svg version="1.1" x="0" y="0" viewBox="0 0 512 512" xml:space="preserve">
                                                        <g>
                                                            <path
                                                                d="M386.689 304.403c-35.587 0-64.538 28.951-64.538 64.538s28.951 64.538 64.538 64.538c35.593 0 64.538-28.951 64.538-64.538s-28.951-64.538-64.538-64.538zm0 96.807c-17.796 0-32.269-14.473-32.269-32.269s14.473-32.269 32.269-32.269 32.269 14.473 32.269 32.269c0 17.797-14.473 32.269-32.269 32.269zM166.185 304.403c-35.587 0-64.538 28.951-64.538 64.538s28.951 64.538 64.538 64.538 64.538-28.951 64.538-64.538-28.951-64.538-64.538-64.538zm0 96.807c-17.796 0-32.269-14.473-32.269-32.269s14.473-32.269 32.269-32.269c17.791 0 32.269 14.473 32.269 32.269 0 17.797-14.473 32.269-32.269 32.269zM430.15 119.675a16.143 16.143 0 0 0-14.419-8.885h-84.975v32.269h75.025l43.934 87.384 28.838-14.5-48.403-96.268z"
                                                                opacity="1" data-original="#000000"></path>
                                                            <path
                                                                d="M216.202 353.345h122.084v32.269H216.202zM117.781 353.345H61.849c-8.912 0-16.134 7.223-16.134 16.134 0 8.912 7.223 16.134 16.134 16.134h55.933c8.912 0 16.134-7.223 16.134-16.134 0-8.912-7.223-16.134-16.135-16.134zM508.612 254.709l-31.736-40.874a16.112 16.112 0 0 0-12.741-6.239H346.891V94.655c0-8.912-7.223-16.134-16.134-16.134H61.849c-8.912 0-16.134 7.223-16.134 16.134s7.223 16.134 16.134 16.134h252.773V223.73c0 8.912 7.223 16.134 16.134 16.134h125.478l23.497 30.268v83.211h-44.639c-8.912 0-16.134 7.223-16.134 16.134 0 8.912 7.223 16.134 16.134 16.134h60.773c8.912 0 16.134-7.223 16.135-16.134V264.605c0-3.582-1.194-7.067-3.388-9.896zM116.706 271.597H42.487c-8.912 0-16.134 7.223-16.134 16.134 0 8.912 7.223 16.134 16.134 16.134h74.218c8.912 0 16.134-7.223 16.134-16.134.001-8.911-7.222-16.134-16.133-16.134zM153.815 208.134H16.134C7.223 208.134 0 215.357 0 224.269s7.223 16.134 16.134 16.134h137.681c8.912 0 16.134-7.223 16.134-16.134s-7.222-16.135-16.134-16.135z"
                                                                opacity="1" data-original="#000000"></path>
                                                            <path
                                                                d="M180.168 144.672H42.487c-8.912 0-16.134 7.223-16.134 16.134 0 8.912 7.223 16.134 16.134 16.134h137.681c8.912 0 16.134-7.223 16.134-16.134.001-8.911-7.222-16.134-16.134-16.134z"
                                                                opacity="1" data-original="#000000"></path>
                                                        </g>
                                                    </svg>
                                                    {{ translate('Shipping Info') }}
                                                </button>
                                            </li>
                                        @endif

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link p-3 wave-btn @if(@$shippingConfiguration->shipping_option == "LOCATION_BASED" && auth()->user())

                                                        fetch-city-shipping

                                                    @endif" id="pills-payment-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-payment" type="button"
                                                role="tab" aria-controls="pills-payment" aria-selected="false">
                                                <svg version="1.1" x="0" y="0" viewBox="0 0 100 100" xml:space="preserve">
                                                    <g>
                                                        <path
                                                            d="M86 70.5v-39c0-3.58-2.92-6.5-6.5-6.5h-59c-3.58 0-6.5 2.92-6.5 6.5v39c0 3.58 2.92 6.5 6.5 6.5h59c3.58 0 6.5-2.92 6.5-6.5zm-4 0a2.5 2.5 0 0 1-2.5 2.5h-59a2.5 2.5 0 0 1-2.5-2.5V40h64zM82 36H18v-4.5a2.5 2.5 0 0 1 2.5-2.5h59a2.5 2.5 0 0 1 2.5 2.5z"
                                                            opacity="1" data-original="#000000"></path>
                                                        <path
                                                            d="M70.5 66c3.58 0 6.5-2.92 6.5-6.5S74.08 53 70.5 53 64 55.92 64 59.5s2.92 6.5 6.5 6.5zm0-9a2.5 2.5 0 0 1 0 5 2.5 2.5 0 0 1 0-5zM24 53h30c1.1 0 2-.9 2-2s-.9-2-2-2H24c-1.1 0-2 .9-2 2s.9 2 2 2zM24 63h23c1.1 0 2-.9 2-2s-.9-2-2-2H24c-1.1 0-2 .9-2 2s.9 2 2 2z"
                                                            opacity="1" data-original="#000000"></path>
                                                    </g>
                                                </svg>
                                                {{ translate('Payment Info') }}
                                            </button>
                                        </li>


                                    </ul>
                                </div>

                                <div class="tab-content checkout-form-content">

                                    <div class="tab-pane fade show active" id="pills-bill-info" role="tabpanel"
                                        aria-labelledby="pills-bill-info-tab">
                                        <div class="tab-header">
                                            <h5>
                                                {{ translate('Shipping Information') }}
                                            </h5>

                                            <p class="text-muted">
                                                {{ translate('Please fill all information below') }}
                                            </p>

                                        </div>

                                        <div>

                                            @if (auth()->user())

                                                <div class="shipping-address">
                                                    <div class="row g-4">

                                                        @if (auth()->user()->billingAddress)
                                                            @foreach (auth()->user()->billingAddress as $address)
                                                                <div class="col-xl-6">
                                                                    <div class="address-card">
                                                                        <div class="form-check card-radio">
                                                                            <input id="{{ $loop->index }}-address"
                                                                                type="radio"
                                                                                class="form-check-input checkout-radio-btn user-address-input"
                                                                                value="{{ $address->id }}"
                                                                                name="address_id"
                                                                                {{ $loop->index == 0 ? 'checked' : '' }}>

                                                                            <label class="form-check-label pointer"
                                                                                for="{{ $loop->index }}-address">

                                                                                <span
                                                                                    class="text-wrap d-flex flex-column gap-1">
                                                                                    <span class="address-title">
                                                                                        {{ $address->name }}
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('First Name') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->first_name }}
                                                                                        </small>

                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('Last Name') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->last_name }}
                                                                                        </small>
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('Email') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->email }}
                                                                                        </small>
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('Phone') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->phone }}
                                                                                        </small>
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('Zip') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->zip }}
                                                                                        </small>
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('Address') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->address->address }}
                                                                                        </small>
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('Country') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->country->name }}
                                                                                        </small>
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('State') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->state->name }}
                                                                                        </small>
                                                                                    </span>

                                                                                    <span class="address-item">
                                                                                        <span>
                                                                                            {{ translate('City') }}
                                                                                        </span>
                                                                                        <small>
                                                                                            {{ $address->city->name }}
                                                                                        </small>
                                                                                    </span>
                                                                                </span>

                                                                            </label>
                                                                        </div>

                                                                        <div class="address-actions">
                                                                            <a href="{{ route('user.address.delete', $address->id) }}"
                                                                                class="delete-address address-action-btn danger"
                                                                                type="button">
                                                                                <i class="fas fa-trash-alt"></i>
                                                                            </a>

                                                                            <a href="javascript:void(0)"
                                                                                address-id="{{ $address->id }}"
                                                                                class="edit-address address-action-btn "
                                                                                type="button">
                                                                                <i class="fa-regular fa-pen-to-square"></i>
                                                                            </a>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif

                                                        <div class="col-12">
                                                            <button type="button" class="create-address"
                                                                data-bs-toggle="modal" data-bs-target="#createAddress">
                                                                <span class="address-icon">
                                                                    <svg id="fi_2312340" enable-background="new 0 0 24 24"
                                                                        viewBox="0 0 24 24"
                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="m14.25 0h-11.5c-1.52 0-2.75 1.23-2.75 2.75v15.5c0 1.52 1.23 2.75 2.75 2.75h6.59c-.54-1.14-.84-2.41-.84-3.75 0-1.15.22-2.25.64-3.26.2-.51.45-1 .74-1.45.65-1.01 1.49-1.87 2.48-2.54.51-.35 1.05-.64 1.63-.86.93-.39 1.95-.61 3.01-.63v-5.76c0-1.52-1.23-2.75-2.75-2.75z"
                                                                            fill="#eceff1"></path>
                                                                        <g fill="#90a4ae">
                                                                            <path
                                                                                d="m14 9c0 .05 0 .1-.01.14-.58.22-1.12.51-1.63.86h-8.36c-.55 0-1-.45-1-1s.45-1 1-1h9c.55 0 1 .45 1 1z">
                                                                            </path>
                                                                            <path
                                                                                d="m9.88 12.54c-.29.45-.54.94-.74 1.45-.04.01-.09.01-.14.01h-5c-.55 0-1-.45-1-1s.45-1 1-1h5c.38 0 .72.22.88.54z">
                                                                            </path>
                                                                            <path
                                                                                d="m8 6h-4c-.552 0-1-.448-1-1s.448-1 1-1h4c.552 0 1 .448 1 1s-.448 1-1 1z">
                                                                            </path>
                                                                        </g>
                                                                        <path
                                                                            d="m17.25 24c-3.722 0-6.75-3.028-6.75-6.75s3.028-6.75 6.75-6.75 6.75 3.028 6.75 6.75-3.028 6.75-6.75 6.75z"
                                                                            class="added"></path>
                                                                        <path
                                                                            d="m17.25 21c-.552 0-1-.448-1-1v-5.5c0-.552.448-1 1-1s1 .448 1 1v5.5c0 .552-.448 1-1 1z"
                                                                            fill="#fff"></path>
                                                                        <path
                                                                            d="m20 18.25h-5.5c-.552 0-1-.448-1-1s.448-1 1-1h5.5c.552 0 1 .448 1 1s-.448 1-1 1z"
                                                                            fill="#fff"></path>
                                                                    </svg>
                                                                </span>
                                                                {{ translate('Add New Address') }}
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            @else
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div>
                                                            <label for="billinginfo-firstName" class="form-label">
                                                                {{ translate('First Name') }}
                                                                <span class="text-danger">*</span>
                                                            </label>

                                                            <input type="text" class="form-control user-info"
                                                                id="billinginfo-firstName" name="first_name"
                                                                placeholder="{{ translate('Enter first name') }}"
                                                                value="{{ old('first_name') ? old('first_name') : @$user->name }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div>
                                                            <label for="billinginfo-lastName" class="form-label">
                                                                {{ translate('Last Name') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" class="form-control user-info"
                                                                id="billinginfo-lastName" name="last_name"
                                                                placeholder="{{ translate('Enter last name') }}"
                                                                value="{{ old('phone') ? old('phone') : @$user->last_name }}">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div>
                                                            <label for="billinginfo-email" class="form-label">
                                                                {{ translate('Email') }}
                                                                <span class="text-danger">*</span></label>
                                                            <input type="email" name="email"
                                                                class="form-control user-info" id="billinginfo-email"
                                                                value="{{ @$user->email ? $user->email : old('email') }}"
                                                                placeholder="Enter email">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div>
                                                            <label for="billinginfo-phone" class="form-label">
                                                                {{ translate('Phone') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="text" name="phone"
                                                                class="form-control user-info" id="billinginfo-phone"
                                                                value="{{ old('phone') ? old('phone') : @$user->phone }}"
                                                                placeholder="Enter phone no.">
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div>
                                                            <label for="billinginfo-address" class="form-label">
                                                                {{ translate('Address') }}

                                                                <span class="text-danger"> *</span>

                                                            </label>
                                                            <textarea name="address" class="form-control user-info address" id="billinginfo-address" placeholder="Enter address"
                                                                rows="3">{{ old('address') ? old('address') : @$user->address->address }}</textarea>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">

                                                        <div>
                                                            <label for="billinginfo-country_id" class="form-label">
                                                                {{ translate('Country') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>

                                                            <select class="form-control guest-location country guest-country"
                                                                name="country_id" id="billinginfo-country_id">

                                                                <option value="">
                                                                    {{translate('Select country')}}
                                                                </option>
                                                                @foreach ($countries as $country)
                                                                    <option value="{{ $country->id }}">
                                                                        {{ $country->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div>
                                                            <label for="billinginfo-state_id" class="form-label">
                                                                {{ translate('State') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>

                                                            <select class="form-control guest-location state"
                                                                name="state_id" id="billinginfo-state_id">

                                                                <option value="">{{ translate('Select State') }}
                                                                </option>

                                                            </select>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div>
                                                            <label for="billinginfo-city_id" class="form-label">
                                                                {{ translate('City') }} <span class="text-danger">*</span>
                                                            </label>

                                                            <select class="form-control guest-location city city-base-shipping"
                                                                name="city_id" id="billinginfo-city_id">

                                                                <option value="">{{ translate('Select City') }}

                                                                </option>

                                                            </select>

                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div>
                                                            <label for="billinginfo-zip" class="form-label">
                                                                {{ translate('Zip Code') }} <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <input class="form-control user-info" type="text"
                                                                id="billinginfo-zip" name="zip"
                                                                value="{{ old('zip') ? old('zip') : @$user->address->zip }}"
                                                                placeholder="{{ translate('1205') }}" required>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="map-container" id="map-container-guest">
                                                    <div class="row g-4">
                                                        <div class="col-xl-6">
                                                            <div>
                                                                <label for="billinginfo-latitude" class="form-label">
                                                                    {{ translate('Latitude') }} <span
                                                                        class="text-danger">*</span>
                                                                </label>
                                                                <input required type="text" name="latitude"
                                                                    id="billinginfo-latitude"
                                                                    class="form-control latitude" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-xl-6">
                                                            <div>
                                                                <label for="billinginfo-longitude" class="form-label">
                                                                    {{ translate('Longitude') }} <span
                                                                        class="text-danger">*</span>
                                                                </label>
                                                                <input required type="text" name="longitude"
                                                                    id="billinginfo-longitude"
                                                                    class="form-control longitude" value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <input id="mar-search-guest"
                                                                class="form-control mt-1 map-search-input" type="text"
                                                                placeholder="{{ translate('Search your location here') }}">
                                                            <div id="gmap-guest"
                                                                class="rounded w-100 mb-5 h-400 gmap-site-address"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div
                                                class="d-flex align-items-center @if (!auth()->user()) justify-content-sm-between justify-content-start @else justify-content-end @endif flex-wrap gap-4 mt-5">

                                                @if (!auth()->user())
                                                    <div class="d-flex align-items-center gap-2">

                                                        <input type="checkbox" name="create_account"
                                                            value="{{ App\Enums\StatusEnum::true->status() }}"
                                                            class="custom-checkbox" id="create_account">
                                                        <label for="create_account">
                                                            {{ translate('Register with the provided information') }}
                                                        </label>

                                                    </div>
                                                @endif


                                                @if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC")
                                                    <button type="button" class="nexttab check-input btn-label shipping-tab wave-btn"
                                                        data-nexttab="pills-bill-address-tab">
                                                        <i
                                                            class="fa-solid fa-truck-arrow-right label-icon align-middle fs-14"></i>
                                                        {{ translate('Proceed to Shipping') }}
                                                    </button>

                                                @else
                                                    <button type="button" class="btn-label nexttab shiping-input @if(@$shippingConfiguration->shipping_option == "LOCATION_BASED" && auth()->user())

                                                        fetch-city-shipping

                                                    @endif
                                                    "
                                                        data-nexttab="pills-payment-tab"><i
                                                            class="fa-solid fa-credit-card label-icon align-middle fs-14"></i>
                                                        {{ translate('Continue to Payment') }}
                                                    </button>
                                                @endif


                                            </div>

                                        </div>

                                    </div>

                                    @if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC")
                                        {{--  shipping carrier --}}
                                        <div class="tab-pane fade" id="pills-bill-address" role="tabpanel"
                                            aria-labelledby="pills-bill-address-tab">

                                            <div class="tab-header">
                                                <h5>
                                                    {{ translate('Shipping Method') }}
                                                </h5>
                                                <p class="text-muted">
                                                    {{ translate('Please fill all information below') }}
                                                </p>
                                            </div>

                                            <div class="mt-4">
                                                <div class="row g-4 shipping-configuration-section">
                                                    @include('frontend.partials.shipping_configuration')
                                                </div>
                                            </div>

                                            <div
                                                class="d-flex align-items-start justify-content-sm-between justify-content-center flex-wrap gap-4 mt-5">
                                                <button type="button" class="btn-label previestab"
                                                    data-previous="pills-bill-info-tab"><i
                                                        class="fa-solid fa-arrow-left label-icon align-middle fs-14"></i>
                                                    {{ translate('Back to Personal Info') }}

                                                </button>
                                                <button type="button" class="btn-label nexttab shiping-input"
                                                    data-nexttab="pills-payment-tab"><i
                                                        class="fa-solid fa-credit-card label-icon align-middle fs-14"></i>
                                                    {{ translate('Continue to Payment') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    {{--  payment carrier --}}
                                    <div class="tab-pane fade" id="pills-payment" role="tabpanel"
                                        aria-labelledby="pills-payment-tab">

                                        @if(auth_user('web') && 
                                           site_settings('customer_wallet') == App\Enums\StatusEnum::true->status() )
                                        
                                            <div class="tab-header">
                                                <h5>
                                                    {{ translate('Payment Type') }}
                                                </h5>

                                                <p class="text-muted">
                                                    {{ translate('Please select A Payment Type') }}
                                                </p>
                                            </div>

                                            <div class="row mb-4">
                                                <div class=" col-md-6">
                                                    <div class="form-check card-radio">
                                                        <input type="radio" id="Traditional"
                                                            name="wallet_payment" checked  value="{{  App\Enums\StatusEnum::false->status() }}"
                                                            class="form-check-input payment-radio-btn payment-type">
                                                        <label class="form-check-label pointer"
                                                            for="Traditional">
                                                            <span class="d-flex align-items-center gap-4">
                                                                <span class="payment_icon custom-payment">
                                                                    <i class="fa-solid fa-money-bill-transfer"></i>
                                                                </span>

                                                                <span class="fs-14 text-wrap">
                                                                    {{
                                                                        translate('Traditional')
                                                                    }}
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check card-radio">
                                                        <input type="radio" id="wallet_payment"
                                                            name="wallet_payment" value="{{  App\Enums\StatusEnum::true->status() }}"
                                                            class="form-check-input payment-radio-btn payment-type">
                                                        <label class="form-check-label pointer"
                                                            for="wallet_payment">
                                                            <span class="d-flex align-items-center gap-4">
                                                                <span class="payment_icon custom-payment">
                                                                    <i class="fa-solid fa-wallet"></i>
                                                                </span>

                                                                <span class="fs-14 text-wrap">
                                                                    {{
                                                                        translate('Wallet')
                                                                    }}
                                                                   <p class="fs-12 mt-2">
                                                                    {{ 
                                                                        short_amount(auth_user('web')->balance)
                                                                    }}
                                                                  </p>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div id="paymentSelection"  @if(auth_user('web') &&  site_settings('customer_wallet') == App\Enums\StatusEnum::true->status()) class="d-none mt-4"   @endif  >
                                            <div class="tab-header">
                                                <h5>
                                                    {{ translate('Payment Selection') }}
                                                </h5>
    
                                                <p class="text-muted">
                                                    {{ translate('Please select A Payment Method') }}
                                                </p>
                                            </div>
    
    
                                            @if (site_settings('cash_on_delivery', App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status())
                                                <div class="mb-5">
    
                                                    <div class="row">
    
                                                        <div class="col-lg-12">
                                                            <div class="form-check card-radio">
                                                                <input id="cod" type="radio" name="payment_id"
                                                                    value="{{ App\Enums\StatusEnum::false->status() }}"
                                                                    class="form-check-input">
                                                                <label class="form-check-label" for="cod">
                                                                    <span class="d-flex align-items-center gap-4">
                                                                        <span class="payment_icon">
                                                                            <img src="{{ asset('assets/images/frontend/payment/cod.jpg') }}"
                                                                                alt="cod.jpg">
                                                                        </span>
    
                                                                        <span class="fs-14 text-wrap">
                                                                            {{ translate('Cash on Delivery') }}
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
    
                                                    </div>
                                                </div>
                                            @endif
    
    
    
                                            @if (site_settings('digital_payment', App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status())
                                                <div class="tab-header">
                                                    <h5>
                                                        {{ translate('Digital Payment') }}
                                                    </h5>
    
                                                </div>
                                                <div class="row g-4">
                                                    @foreach ($paymentMethods as $paymentMethod)
                                                        <div class="col-xl-4 col-md-6">
                                                            <div class="form-check card-radio">
                                                                <input type="radio" id="payment-{{ $paymentMethod->id }}"
                                                                    name="payment_id" value="{{ $paymentMethod->id }}"
                                                                    class="form-check-input payment-radio-btn">
                                                                <label class="form-check-label pointer"
                                                                    for="payment-{{ $paymentMethod->id }}">
                                                                    <span class="d-flex align-items-center gap-4">
                                                                        <span class="payment_icon">
                                                                            <img src="{{ show_image(file_path()['payment_method']['path'] . '/' . $paymentMethod->image, file_path()['payment_method']['size']) }}"
                                                                                alt="{{ $paymentMethod->image }}">
                                                                        </span>
    
                                                                        <span class="fs-14 text-wrap">
                                                                            {{ $paymentMethod->name }}
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
    
                                                </div>
                                            @endif
    
    
    
                                            @if (site_settings('offline_payment', App\Enums\StatusEnum::false->status()) == App\Enums\StatusEnum::true->status())
                                                <div class="tab-header mt-5">
                                                    <h5>
                                                        {{ translate('Manual Payment') }}
                                                    </h5>
                                                </div>
                                                <div class="row g-4">
                                                    @foreach ($manualPaymentMethods as $manualPaymentMethod)
                                                        <div class="col-xl-4 col-md-6">
                                                            <div class="form-check card-radio">
                                                                <input type="radio"
                                                                    id="payment-{{ $manualPaymentMethod->id }}"
                                                                    name="payment_id" value="{{ $manualPaymentMethod->id }}"
                                                                    class="form-check-input payment-radio-btn">
                                                                <label class="form-check-label pointer"
                                                                    for="payment-{{ $manualPaymentMethod->id }}">
                                                                    <span class="d-flex align-items-center gap-4">
                                                                        <span class="payment_icon">
                                                                            <img src="{{ show_image(file_path()['payment_method']['path'] . '/' . $manualPaymentMethod->image, file_path()['payment_method']['size']) }}"
                                                                                alt="{{ $manualPaymentMethod->image }}">
                                                                        </span>
    
                                                                        <span class="fs-14 text-wrap">
                                                                            {{ $manualPaymentMethod->name }}
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
    
                                                </div>
                                            @endif
                                        </div>




                                        <div
                                            class="d-flex align-items-start justify-content-sm-between justify-content-center flex-wrap gap-4 mt-5">
                                            <button type="button" class="btn-label previestab"
                                                 @php
                                                     $dataPreviousAttribute = @$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC" ? "pills-bill-address-tab" : "pills-bill-info-tab";
                                                 @endphp
                                                data-previous="{{ $dataPreviousAttribute}}"  ><i
                                                    class="fa-solid fa-arrow-left label-icon align-middle fs-14"></i>
                                                    @if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC")
                                                              {{ translate('Back to Shipping') }}
                                                    @else
                                                            {{ translate('Back to Personal Info') }}
                                                    @endif
                                            </button>
                                            <button type="submit"
                                                class="nexttab check-input btn-label  wave-btn oder-btn"><i
                                                    class="fa-solid fa-cart-shopping label-icon align-middle fs-14 ">

                                                </i>

                                                {{ translate('Order') }}
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-4 col-xl-5 col-lg-4">
                        <div class="card checkout-product">
                            <div class="card-header">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title fs-18 mb-0">
                                            {{ translate('Order Summary') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body order-summary">

                                 @include('frontend.partials.order_summary')

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </section>


        <div class="modal fade" id="createAddress" tabindex="-1" data-bs-backdrop="static" aria-labelledby="createAddress"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ translate('Create  Address') }}
                        </h5>

                        <button type="button" class="btn btn-danger fs-14 modal-closer rounded-circle"
                            data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form action="{{ route('user.address.store') }}" method="post">
                        @csrf

                        <div class="modal-body">
                            <div class="row g-4">

                                <div class="col-md-12">
                                    <div>
                                        <label for="add-address_name" class="form-label">
                                            {{ translate('Address Name') }} <span class="text-danger">*</span>
                                        </label>

                                        <input required type="text" class="form-control" id="add-address_name" name="address_name"
                                            placeholder="{{ translate('Enter name') }}" value="{{ old('address_name') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label for="add-billinginfo-firstName" class="form-label">
                                            {{ translate('First Name') }}
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input required type="text" class="form-control " id="add-billinginfo-firstName"
                                            name="first_name" placeholder="{{ translate('Enter first name') }}"
                                            value="{{ old('first_name') ? old('first_name') : @$user->name }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label for="add-billinginfo-lastName" class="form-label">
                                            {{ translate('Last Name') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input required type="text" class="form-control " id="add-billinginfo-lastName"
                                            name="last_name" placeholder="{{ translate('Enter last name') }}"
                                            value="{{ old('last_name') ? old('last_name') : @$user->last_name }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label for="add-billinginfo-email" class="form-label">
                                            {{ translate('Email') }}
                                            <span class="text-danger">*</span></label>
                                        <input  required type="email" name="email" class="form-control" id="add-billinginfo-email"
                                            value="{{ @$user->email ? $user->email : old('email') }}"
                                            placeholder="Enter email">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div>
                                        <label for="add-billinginfo-phone" class="form-label">
                                            {{ translate('Phone') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input required type="text" name="phone" class="form-control "
                                            id="add-billinginfo-phone"
                                            value="{{ old('phone') ? old('phone') : @$user->phone }}"
                                            placeholder="Enter phone no.">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div>
                                        <label for="add-billinginfo-address" class="form-label">
                                            {{ translate('Address') }}

                                            <span class="text-danger"> *</span>

                                        </label>
                                        <textarea required name="address" class="form-control address" id="add-billinginfo-address" placeholder="Enter address"
                                            rows="3">{{ old('address') ? old('address') : @$user->address->address }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">

                                    <div>
                                        <label for="add-billinginfo-country_id" class="form-label">
                                            {{ translate('Country') }} <span class="text-danger">*</span>
                                        </label>

                                        <select required class="form-control user-location country" name="country_id"
                                            id="add-billinginfo-country_id">

                                            <option disabled selected value="">{{translate('Select a country')}}</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label for="add-billinginfo-state_id" class="form-label">
                                            {{ translate('State') }} <span class="text-danger">*</span>
                                        </label>

                                        <select required class="form-control user-location state" name="state_id"
                                            id="add-billinginfo-state_id">

                                            <option value="">{{ translate('Select State') }}
                                            </option>

                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label for="add-billinginfo-city_id" class="form-label">
                                            {{ translate('City') }} <span class="text-danger">*</span>
                                        </label>

                                        <select required class="form-control user-location city" name="city_id"
                                            id="add-billinginfo-city_id">

                                            <option value="">{{ translate('Select City') }}
                                            </option>

                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div>
                                        <label for="add-billinginfo-zip" class="form-label">
                                            {{ translate('Zip Code') }} <span class="text-danger">*</span>
                                        </label>
                                        <input required class="form-control " type="text" id="add-billinginfo-zip" name="zip"
                                            value="{{ old('zip') ? old('zip') : @$user->address->zip }}"
                                            placeholder="{{ translate('1205') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="map-container" id="map-container-user">
                                <div class="row g-4">
                                    <div class="col-xl-6">
                                        <div>
                                            <label for="add-billinginfo-latitude" class="form-label">
                                                {{ translate('Latitude') }} <span class="text-danger">*</span>
                                            </label>
                                            <input required type="text" name="latitude" id="add-billinginfo-latitude"
                                                class="form-control latitude" value="">
                                        </div>
                                    </div>

                                    <div class="col-xl-6">
                                        <div>
                                            <label for="add-billinginfo-longitude" class="form-label">
                                                {{ translate('Longitude') }} <span class="text-danger">*</span>
                                            </label>
                                            <input required type="text" name="longitude" id="add-billinginfo-longitude"
                                                class="form-control longitude" value="">
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <input id="map-search-user" class="form-control mt-1 map-search-input" type="text"
                                            placeholder="{{ translate('Search your location here') }}">

                                        <div id="gmap-user" class="rounded w-100 mb-5 h-400 gmap-site-address"></div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="modal-footer">
                            <div class="d-flex align-items-center gap-4">
                                <button type="button" class="btn btn-danger fs-12 px-3" data-bs-dismiss="modal">
                                    {{ translate('Cancel') }}
                                </button>
                                <button type="submit" class="btn btn-success fs-12 px-3">
                                    {{ translate('Submit') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="updateAddress" tabindex="-1" data-bs-backdrop="static" aria-labelledby="updateAddress"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ translate('Update Billing Address') }}
                        </h5>

                        <button type="button" class="btn btn-danger fs-14 modal-closer rounded-circle"
                            data-bs-dismiss="modal">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form action="{{ route('user.address.update') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row g-4 address-section">


                            </div>
                        </div>

                        <div class="modal-footer">
                            <div class="d-flex align-items-center gap-4">
                                <button type="button" class="btn btn-danger fs-12 px-3" data-bs-dismiss="modal">
                                    {{ translate('Cancel') }}
                                </button>
                                <button type="submit" class="btn btn-success fs-12 px-3">
                                    {{ translate('Submit') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

@endsection


@push('scriptpush')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ site_settings('gmap_client_key') }}&libraries=places&v=3.49"
        defer></script>

    <script>
        "use strict";

        
        @if(auth_user('web') && site_settings('customer_wallet') == App\Enums\StatusEnum::true->status() )

              
             if($('.payment-type').val() == 1){
                $('#paymentSelection').addClass('d-none')
             }else{
                $('#paymentSelection').removeClass('d-none')
             }

            $(document).on('change','.payment-type',function(){
                var value = $(this).val()
                if(value == 1){
                    $('#paymentSelection').addClass('d-none')
                }else{
                    $('#paymentSelection').removeClass('d-none')
                }
            })



        @endif

        var countries = @json($countries);


        function loadGmap(container) {
            if(container){

                    var latitude = isNaN(parseFloat(container.querySelector(".latitude").value)) ? 33.14751827254395 : parseFloat(
                        container.querySelector(".latitude").value);

                    var longitude = isNaN(parseFloat(container.querySelector(".longitude").value)) ? 73.7561387589157 : parseFloat(
                        container.querySelector(".longitude").value);

                    var mapConfig = {
                        lat: latitude,
                        lng: longitude
                    };

                    const mapElement = container.querySelector(".gmap-site-address");
                    const map = new google.maps.Map(mapElement, {
                        center: {
                            lat: latitude,
                            lng: longitude
                        },
                        zoom: 13,
                        mapTypeId: "roadmap",
                    });

                    var marker = new google.maps.Marker({
                        position: mapConfig,
                        map: map,
                    });

                    marker.setMap(map);
                    var geocoder = new google.maps.Geocoder();
                    google.maps.event.addListener(map, 'click', function(mapsMouseEvent) {
                        var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                        var coordinates = JSON.parse(coordinates);
                        var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                        marker.setPosition(latlng);
                        map.panTo(latlng);

                        var latitudeInput = container.querySelector('.latitude');
                        var longitudeInput = container.querySelector('.longitude');


                        latitudeInput.value = coordinates['lat'];
                        longitudeInput.value = coordinates['lng'];

                        geocoder.geocode({
                            'latLng': latlng
                        }, function(results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results[1]) {
                                    container.querySelector('.address').value = results[1].formatted_address;
                                }
                            }
                        });
                    });

                    const input = container.querySelector('.map-search-input');
                    const searchBox = new google.maps.places.SearchBox(input);
                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                    map.addListener("bounds_changed", () => {
                        searchBox.setBounds(map.getBounds());
                    });
                    let markers = [];
                    searchBox.addListener("places_changed", () => {
                        const places = searchBox.getPlaces();
                        if (places.length == 0) {
                            return;
                        }
                        markers.forEach((marker) => {
                            marker.setMap(null);
                        });
                        markers = [];
                        const bounds = new google.maps.LatLngBounds();
                        places.forEach((place) => {
                            if (!place.geometry || !place.geometry.location) {
                                return;
                            }
                            var mrkr = new google.maps.Marker({
                                map,
                                title: place.name,
                                position: place.geometry.location,
                            });

                            google.maps.event.addListener(mrkr, "click", function(event) {
                                var latitudeInput = container.querySelector('.latitude');
                                var longitudeInput = container.querySelector('.longitude');

                                latitudeInput.value = this.position.lat();
                                longitudeInput.value = this.position.lng();
                            });

                            markers.push(mrkr);

                            if (place.geometry.viewport) {
                                bounds.union(place.geometry.viewport);
                            } else {
                                bounds.extend(place.geometry.location);
                            }
                        });
                        map.fitBounds(bounds);
                    });
            }
        };

        document.addEventListener("DOMContentLoaded", function() {
            loadGmap(document.querySelector('#map-container-guest'));
        });

        document.getElementById('createAddress').addEventListener('shown.bs.modal', function() {
            loadGmap(document.querySelector('#map-container-user'));
        });


        $(".guest-location").select2({

        });

        $(".user-location").each(function() {
            $(this).select2({
                dropdownParent: $('#createAddress .modal-content')
            });
        });

        function handleCountryChange() {

            $('.country').change(function() {
                var countryId = $(this).val();
                var states = countries.find(country => country.id == countryId).states;

                var stateDropdown = $(this).closest('.row').find('.state');
                var cityDropdown = $(this).closest('.row').find('.city');

                stateDropdown.empty().append('<option value="">{{ translate('Select State') }}</option>');
                cityDropdown.empty().append('<option value="">{{ translate('Select City') }}</option>');

                if (states) {
                    $.each(states, function(key, state) {
                        stateDropdown.append('<option value="' + state.id + '">' + state.name +
                            '</option>');
                    });
                }
            });
        }

        function handleStateChange() {
            $('.state').change(function() {

                var stateId = $(this).val();
                var countryId = $(this).closest('.row').find('.country').val();

                var states = countries.find(country => country.id == countryId).states;
                var cities = states.find(state => state.id == stateId).cities;

                var cityDropdown = $(this).closest('.row').find('.city');

                cityDropdown.empty().append('<option value="">{{ translate("Select City") }}</option>');

                if (cities) {
                    $.each(cities, function(key, city) {
                        cityDropdown.append(`<option  value="${city.id}"> ${city.name} </option>`);
                    });
                }
            });
        }

        handleCountryChange();
        handleStateChange();


        @if(@$shippingConfiguration->shipping_option == "CARRIER_SPECIFIC")
            $(document).on('click', ".shipping-tab", function(e) {

                    var countryId = null;
                    var address_id = null;

                    if($('.guest-country').val()){
                        countryId = $('.guest-country').val()
                    }

                    if($('.user-address-input:checked').val()){
                        address_id = $('.user-address-input:checked').val()
                    }

                    $.ajax({
                                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                                url: "{{route('fetch.shipping.carrier')}}",
                                method: "POST",
                                data: { country_id: countryId ?? countryId ,address_id: address_id ?? address_id  },
                                success: function (response) {
                                    if(response.status){
                                        $('.shipping-configuration-section').html(response.shipping_carrier)

                                    }else{
                                        toaster(response.message,'danger')
                                    }
                                },

                                error: function (error){
                                        if(error && error.responseJSON){
                                            if(error.responseJSON.errors){
                                                for (let i in error.responseJSON.errors) {
                                                    toaster(error.responseJSON.errors[i][0],'danger')
                                                }
                                            }
                                            else{
                                                if((error.responseJSON.message)){
                                                    toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                                                }
                                                else{
                                                    toaster(error.responseJSON.error,'danger')
                                                }
                                            }
                                        }
                                        else{
                                            toaster(error.message,'danger')
                                        }
                                    },
                                complete: function() {


                                },
                    });



            })


            $(document).on('click', ".shipping-method", function(e) {

                var id = $(this).val();
                var address_id = null;
                var countryId = null;

                if($('.guest-country').val()){
                    countryId = $('.guest-country').val()
                }

                if($('.user-address-input:checked').val()){
                    address_id = $('.user-address-input:checked').val()
                 }


                if(id){
                        var $element = $(this);
                        var html  = $element.html();
                        $.ajax({
                            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                            url: "{{route('get.shipping.method')}}",

                            beforeSend: function() {
                                $('.order-summary-loader').removeClass('d-none');
                            },
                            method: "POST",
                            data: { method_id: id ,country_id: countryId ?? countryId ,address_id: address_id ?? address_id },
                            success: function (response) {
                                if(response.status){
                                    $('.order-summary').html(response.order_summary)
                                }else{
                                    toaster(response.message,'danger')
                                }
                            },

                            error: function (error){
                                    if(error && error.responseJSON){
                                        if(error.responseJSON.errors){
                                            for (let i in error.responseJSON.errors) {
                                                toaster(error.responseJSON.errors[i][0],'danger')
                                            }
                                        }
                                        else{
                                            if((error.responseJSON.message)){
                                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                                            }
                                            else{
                                                toaster(error.responseJSON.error,'danger')
                                            }
                                        }
                                    }
                                    else{
                                        toaster(error.message,'danger')
                                    }
                                },
                            complete: function() {




                            },
                        });
                }




            });



        @endif



        @if(@$shippingConfiguration->shipping_option == "LOCATION_BASED")

            $(document).on('change', ".city-base-shipping", function(e) {
                var id = $(this).val();

                var address_id = null;
                if($('.user-address-input:checked').val()){
                    address_id = $('.user-address-input:checked').val()
                }


                fetchShippingViaCity(id ,address_id )

            });


            $(document).on('click', ".fetch-city-shipping", function(e) {
                var id = null;
                var address_id = null;
                if($('.user-address-input:checked').val()){
                    address_id = $('.user-address-input:checked').val()

                }
                fetchShippingViaCity(id ,address_id )

            });



            function fetchShippingViaCity(id ,address_id){


                if(id ||address_id ){
                        var $element = $(this);
                        var html  = $element.html();
                        $.ajax({
                            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                            url: "{{route('fetch.shipping.city')}}",

                            beforeSend: function() {
                                $('.order-summary-loader').removeClass('d-none');
                            },
                            method: "POST",
                            data: { city_id: id ,address_id: address_id ?? address_id},
                            success: function (response) {
                                if(response.status){
                                    $('.order-summary').html(response.order_summary)
                                }else{
                                    toaster(response.message,'danger')
                                }

                            },

                            error: function (error){
                                    if(error && error.responseJSON){
                                        if(error.responseJSON.errors){
                                            for (let i in error.responseJSON.errors) {
                                                toaster(error.responseJSON.errors[i][0],'danger')
                                            }
                                        }
                                        else{
                                            if((error.responseJSON.message)){
                                                toaster(error.responseJSON.message == 'Unauthenticated.' ? "You need to login first" :error.responseJSON.message ,'danger')
                                            }
                                            else{
                                                toaster(error.responseJSON.error,'danger')
                                            }
                                        }
                                    }
                                    else{
                                        toaster(error.message,'danger')
                                    }
                                },
                            complete: function() {




                            },
                        });
                }

            }
        @endif




        $(document).on('click', ".edit-address", function(e) {

            var addressID = $(this).attr('address-id');
            var $btnHtml = '  <i class="fa-regular fa-pen-to-square"></i>';
            $.ajax({
                url: '{{ route('user.address.edit') }}',
                type: 'POST',
                beforeSend: function() {
                    $('.edit-address').html(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
                    <span class="visually-hidden"></span>
                </div>`);
                },
                data: {
                    id: addressID,
                    "_token": "{{ csrf_token() }}",
                },
                dataType: 'json',
                success: function(response) {

                    $('#updateAddress .address-section').html(response.html);
                    $("#updateAddress").modal('show');

                    $('#updateAddress .edit-location').each(function() {
                        $(this).select2({
                            dropdownParent: $('#updateAddress .modal-content')
                        });
                    });

                    handleCountryChange();
                    handleStateChange();
                    loadGmap(document.querySelector('#map-container-user-edit'));
                },
                error: function(xhr, status, error) {},

                complete: function() {

                    $('.edit-address').html($btnHtml);
                },

            });

            e.preventDefault();
        });
    </script>
@endpush
