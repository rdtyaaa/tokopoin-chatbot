@extends('frontend.layouts.app')
@push('stylepush')
    <style>
        .h-400 {
            height: 400px;
        }


        .map-search-input {
            width: 100%;
            max-width: 250px;
            position: absolute !important;
            top: 6px !important;
            left: 50% !important;
            transform: translateX(-50%) !important;

        }
    </style>
@endpush
@section('content')
    @php
        $promo_banner = frontend_section('promotional-offer');
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
            <div class="row g-4">
                @include('user.partials.dashboard_sidebar')

                <div class="col-xl-9 col-lg-8">
                    <div class="profile-user-right">
                        <a href="{{ @frontend_section_data($promo_banner->value, 'image', 'url') }}" class="d-block">
                            <img class="w-100"
                                src="{{ show_image(file_path()['frontend']['path'] . '/' . @frontend_section_data($promo_banner->value, 'image'), @frontend_section_data($promo_banner->value, 'image', 'size')) }}"
                                alt="banner.jpg">
                        </a>

                        <div class="card mt-5">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{ translate('Profile Info') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('user.profile.update') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="profileName" class="form-label">
                                                {{ translate('Name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="name" id="profileName"
                                                value="{{ $user->name }}" placeholder="{{ translate('Enter name') }}"
                                                required="">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="last_name" class="form-label">
                                                {{ translate('Last Name') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="last_name" id="last_name"
                                                value="{{ $user->last_name }}"
                                                placeholder="{{ translate('Enter last Name') }}" required="">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="userName" class="form-label">
                                                {{ translate('Username') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="username" id="userName"
                                                value="{{ $user->username }}"
                                                placeholder="{{ translate('Enter User Name') }}" required="">
                                        </div>

                                        <div class="col-md-6">

                                            <label for="phone" class="form-label">
                                                {{ translate('Phone') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="phone" id="phone"
                                                value="{{ $user->phone }}" placeholder="{{ translate('Enter Phone') }}"
                                                required="">

                                        </div>

                                        <div class="col-md-6">

                                            <label for="address" class="form-label">
                                                {{ translate('Address') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="address" id="address"
                                                value="{{ @$user->address->address }}"
                                                placeholder="{{ translate('Enter Address') }}" required="">

                                        </div>

                                        <div class="col-md-6">
                                            <label for="country" class="form-label">
                                                {{ translate('Country') }} <span class="text-danger">*</span>
                                            </label>

                                            <select class="form-control select2" name="country_id" id="country" required>
                                                @foreach ($countries as $country)
                                                    <option  {{ $country->id == @$user->country_id  ? 'selected' : ''}} value="{{ $country->id }}"> {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="city" class="form-label">
                                                {{ translate('City') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="city" id="city"
                                                value="{{ @$user->address->city }}"
                                                placeholder="{{ translate('Enter City') }}" required="">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="state" class="form-label">
                                                {{ translate('State') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="state" id="state"
                                                value="{{ @$user->address->state }}"
                                                placeholder="{{ translate('Enter State') }}" required="">
                                        </div>

                                        <div class="col-md-12">
                                            <label for="zip" class="form-label">
                                                {{ translate('Zip') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="zip" id="zip"
                                                value="{{ @$user->address->zip }}"
                                                placeholder="{{ translate('Enter Zip') }}" required="">
                                        </div>

                                        <div class="col-md-12">
                                            <label for="file" class="form-label">
                                                {{ translate('Upload Image') }}
                                            </label>
                                            <input class="form-control" type="file" name="image" id="file">
                                        </div>

                                        <div class="col-xl-6">
                                            <div>
                                                <label for="latitude" class="form-label">
                                                    {{ translate('Latitude') }} <span class="text-danger">*</span>
                                                </label>
                                                <input id="latitude" type="text" name="latitude"
                                                    class="form-control" value="{{ @$user->address->latitude }}">
                                            </div>
                                        </div>

                                        <div class="col-xl-6">
                                            <div>
                                                <label for="longitude" class="form-label">
                                                    {{ translate('Longitude') }} <span class="text-danger">*</span>
                                                </label>
                                                <input id="longitude" type="text" name="longitude"
                                                    class="form-control" value="{{ @$user->address->longitude }}">
                                            </div>
                                        </div>

                                        <div class="col-xl-12">

                                            <input id="map-input" class="form-control mt-1 map-search-input" type="text"
                                            placeholder="{{ translate('Search your loaction here') }}" />

                                            <div class="rounded w-100  mb-5 h-400" id="gmap-site-address"></div>

                                        </div>
                                    </div>

                                    <div class="mt-5">
                                        <button type="submit" class="all-campaign-btn">
                                            {{ translate('Save Change') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card mt-5">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <h4 class="card-title">
                                            {{ translate('Update Password') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <form action="{{ route('user.password.update') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="current_password" class="form-label">
                                                {{ translate('Current Password') }}
                                            </label>
                                            <input class="form-control" type="text" name="current_password"
                                                id="current_password"
                                                placeholder="{{ translate('Enter Current Password') }}">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password" class="form-label">
                                                {{ translate('New Password') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="password" id="password"
                                                placeholder="{{ translate('Enter New Password') }}" required="">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="password_confirmation" class="form-label">
                                                {{ translate('Confirm Password') }} <span class="text-danger">*</span>
                                            </label>
                                            <input class="form-control" type="text" name="password_confirmation"
                                                id="password_confirmation"
                                                placeholder="{{ translate('Enter Confirm Password') }}" required="">
                                        </div>
                                    </div>

                                    <div class=" mt-5">
                                        <button type="submit" class="all-campaign-btn">
                                            {{ translate('Update') }}
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection

@push('scriptpush')

    <script
    src="https://maps.googleapis.com/maps/api/js?key={{ site_settings('gmap_client_key') }}&callback=loadGmap&libraries=places&v=3.49"
    defer></script>
    <script>
        $(".select2").select2({
            placeholder: "{{ translate('Select Country') }}",
        })

        $(document).on('ready', function() {
            loadGmap()
        })

        function loadGmap() {

            var latitude  =  parseFloat("{{@$user->address->latitude ?? 33.14751827254395}}");
            var longitude =  parseFloat("{{@$user->address->longitude ?? 73.7561387589157}}");
            var mapConfig = {
                lat: latitude,
                lng: longitude
            };

            const map = new google.maps.Map(document.getElementById("gmap-site-address"), {
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
            var geocoder = geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function(mapsMouseEvent) {

                var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                var coordinates = JSON.parse(coordinates);
                var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                marker.setPosition(latlng);
                map.panTo(latlng);

                document.getElementById('latitude').value = coordinates['lat'];
                document.getElementById('longitude').value = coordinates['lng'];

                geocoder.geocode({
                    'latLng': latlng
                }, function(results, status) {

                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            document.getElementById('address').value = results[1].formatted_address;
                        }
                    }

                });
            });

            const input = document.getElementById("map-input");
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


                        document.getElementById('latitude').value = this.position.lat();
                        document.getElementById('longitude').value = this.position.lng();

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
        };
    </script>
@endpush
