@extends('admin.layouts.app')
@push('style-include')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/spectrum.css') }}">
    <link href="{{ asset('assets/backend/css/summnernote.css') }}" rel="stylesheet" type="text/css" />
@endpush
@push('style-push')
    <style>
        .order-variable ul {
            padding-left: 0 !important;
            display: grid;
            gap: 4px
        }

        .order-variable ul>li {
            list-style: none;
            color: var(--ig-gray-700);
        }

        .order-variable ul>li>span {
            color: var(--ig-primary)
        }

        .wp-config-btn {
            border: none;
            background: transparent;
            padding: 0;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;

        }

        .wp-config-btn>i {
            font-size: 16px;
            vertical-align: middle;
            animation: spin 3s infinite linear;
        }

        @keyframes(spin) {
            0% {
                -webkit-transform: rotate(0);
                -ms-transform: rotate(0);
                transform: rotate(0);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        .preview-message {
            position: relative;
            padding: 15px;
            z-index: 1;
        }

        .preview-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            inset: 0;
            z-index: -1;
        }

        .preview-bg>img {
            width: 100%;
            height: 100%;
        }

        .whatsapp-loader {
            min-height: 300px
        }


        .table--wrapper{
            overflow-x: auto;
            max-width: 100%;
        }

        table{
            width: 100%;
        }

        td{
            min-width: 160px;
            padding: 8px;
        }

    </style>
@endpush


@section('main_content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">
                    {{ translate($title) }}
                </h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                {{ translate('Home') }}
                            </a></li>
                        <li class="breadcrumb-item active">
                            {{ translate('System Setting') }}
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="d-flex align-items-center">
                        <h5 class="card-title mb-0 flex-grow-1">
                            {{ translate('System Setting') }}
                        </h5>
                    </div>
                </div>

                <div class="card-body">

                    @php
                        $tabs = [
                            'general_information' => translate('General settings'),
                            'business_information' => translate('Business settings'),
                            'theme_settings' => translate('Theme settings'),
                            'payment_setting' => translate('Payment settings'),
                            'customer_setting' => translate('Customer settings'),
                            'order_settings' => translate('Order settings'),
                            'order_notification' => translate('Order notification'),
                            'whatsapp_config' => translate('WhatsApp config'),
                            'firebase_configuration' => translate('Firebase configuration'),
                            'vendor_settings' => translate('Vendor settings'),
                            'system_preferences' => translate('Feature preferences'),
                            'reward_configuration' => translate('Reward point configuration'),
                            'otp_configuration' => translate('OTP configuration'),
                            'social_login' => translate('Social login'),
                            'captcha_settings' => translate('Recaptcha settings'),
                            'google_map' => translate('Google map setup'),
                            'logo_settings' => translate('Logo settings'),
                            'cron_settings' => translate('Cron settings'),
                            'analytical_tool' => translate('Analytical tools'),
                            'plugin_configuration' => translate('Chat Plugin Configuration'),
                        ];
                    @endphp


                    <div class="row gy-4">
                        <div class="col-lg-4 col-xl-3">
                            <div class="bg-light p-2">
                                <div class="d-flex align-items-center gap-2">
                                    <input placeholder="{{translate('Search here')}}" class="form-control" id='search' type="search">

                                    <button type="submit"
                                        class="btn btn-success waves ripple-light section-list-btn d-lg-none">
                                        <i class="ri-equalizer-fill align-bottom"></i>
                                    </button>
                                </div>

                                <div class="section-list-wrapper is-open mt-2">
                                    <div class="nav flex-column nav-pills section-list" id="v-pills-tab" role="tablist"
                                        aria-orientation="vertical">

                                        @foreach ($tabs as $key => $tab)
                                            <a class="setting-tab-list nav-link mb-2  {{ $loop->index == 0 ? 'active' : '' }}"
                                                id="v-pills-{{ $key }}-tab" data-bs-toggle="pill"
                                                href="#v-pills-{{ $key }}" role="tab"
                                                aria-controls="v-pills-{{ $key }}" aria-selected="true">
                                                {{ $tab }}
                                            </a>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-xl-9">
                            <div class="tab-content text-muted mt-md-0" id="v-pills-tabContent">

                                @foreach ($tabs as $key => $tab)
                                    <div class="tab-pane fade  {{ $loop->index == 0 ? 'active show' : '' }}"
                                        id="v-pills-{{ $key }}" role="tabpanel"
                                        aria-labelledby="v-pills-{{ $key }}-tab">
                                        @include('admin.setting.partials.' . $key)
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="cloud-api-config" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="cloud-api-config" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="mb-0 fs-14">
                        {{ translate('Cloud API Configuration') }}

                        <a href="https://developers.facebook.com/docs/whatsapp/cloud-api" target="_blank"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="{{ translate('Cloud API Configuration') }}" class="wp-config-btn text-danger ms-1">
                            <i class="ri-settings-3-line"></i>
                        </a>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>


                <div class="modal-body">
                    @include('admin.setting.partials.wp_cloud_api')
                </div>

            </div>
        </div>
    </div>


    <span id="map-default-latitude" data-latitude="{{ site_settings('latitude', '-33.8688') }}"></span>
    <span id="map-default-longitude" data-longitude="{{ site_settings('longitude', '151.2195') }}"></span>
@endsection

@push('script-include')
    <script src="{{ asset('assets/backend/js/summnernote.js') }}"></script>
    <script src="{{ asset('assets/backend/js/editor.init.js') }}"></script>
    <script src="{{ asset('assets/backend/js/spectrum.js') }}"></script>
    @include('admin.setting.partials.script')
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ site_settings('gmap_client_key') }}&callback=loadGmap&libraries=places&v=3.49"
        defer></script>

    <script>




     // Function to validate and add new row for price wise shipping
         $(document).on('click','.add-price-btn',function() {
            var isFilled = true;
            $('.add-price-row input[type="number"]').each(function(man,$v) {


                if ($(this).val().trim() === '') {
                    isFilled = false;
                }
            });

            if (!isFilled) {
                alert('Please fill all the fields before adding a new row.');
                return;
            }

            // Get the value of the last 'less than or equal' input and set the new 'greater than' input value accordingly
            var lastLessThanEqValue = parseInt($('input[name="order_wise[less_than_eq][]"]').last().val());
            var newGreaterThanValue = isNaN(lastLessThanEqValue) ? 0 : lastLessThanEqValue + 1;

            var newRow = `
                <tr>
                    <td>
                        <p class="mb-0">{{translate('Applicable if order amount is greater than')}}</p>
                        <div class="input-group">
                            <span class="input-group-text">{{default_currency()->symbol}}</span>
                            <input placeholder="{{translate('Enter order amount')}}" type="number" name="order_wise[greater_than][]" class="form-control" value="${newGreaterThanValue}">
                        </div>
                    </td>
                    <td>
                        <p class="mb-0">{{translate('Applicable if  order amount is less than or equal')}}</p>
                        <div class="input-group">
                            <span class="input-group-text">{{default_currency()->symbol}}</span>
                            <input placeholder="{{translate('Enter order amount')}}" type="number" name="order_wise[less_than_eq][]" class="form-control">
                        </div>
                    </td>

                     <td>
                        <p class="mb-0">
                            {{translate('Point')}}
                        </p>

                        <input step="1" min="0" value='0' placeholder="{{translate('Enter Point')}}"  name="order_wise[point][]"  type="number" class="form-control">
                    </td>

       
                    <td>
                        <button type="button" class="btn btn-sm btn-danger delete-row-btn">Delete</button>
                    </td>
                </tr>
            `;

            $('.add-price-row').append(newRow);
        });

        $(document).on('click', '.delete-row-btn', function() {
            $(this).closest('tr').remove();
        });





        $(document).on("click", '#configWp', function(e) {

            $('#cloud-api-config').modal('show');

            e.preventDefault();
        })




        function copyUrlFacebook() {
            var copyText = document.getElementById("callback_facebook_url");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            toaster('Copied the text : ' + copyText.value, 'success');
        }

        function copyGoogleUrl() {
            var copyText = document.getElementById("callback_google_url");
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");
            toaster('Copied the text : ' + copyText.value, 'success');
        }


        $(document).on('ready', function() {
            loadGmap()
        })

        function loadGmap() {

            var latitude = parseFloat($("#map-default-latitude").attr('data-latitude'));
            var longitude = parseFloat($("#map-default-longitude").attr('data-longitude'));
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
