@extends('admin.layouts.app')

@push('style-push')

  <style>
    .select-input-wrapper{
        width: 100% !important;
    }
    .select-input-wrapper select.country-code{
        border-radius: 0.25rem 0 0 0.25rem;
    }
    .select-input-wrapper input{
        border-radius:  0 0.25rem 0.25rem 0;
    }

  </style>

@endpush


@section('main_content')

<div class="page-content">
    <div class="container-fluid">

        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">
                {{translate($title)}}
            </h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">
                        {{translate('Home')}}
                    </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.delivery-man.list')}}">
                        {{translate('Delivery man')}}
                    </a></li>
                    <li class="breadcrumb-item active">
                        {{translate('Create')}}
                    </li>
                </ol>
            </div>
        </div>

        <form action="{{route('admin.delivery-man.store')}}" method="post"  enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header border-bottom-dashed">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm">
                            <div>
                                <h5 class="card-title mb-0">
                                    {{translate('Basic information')}}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-12">
                            <div class="row g-3">

                                <div class="col-lg-6">
                                    <div>
                                        <label for="first_name" class="form-label">{{translate('First name')}}  <span class="text-danger">*</span></label>
                                        <input type="text" required class="form-control" id="first_name" value="{{old('first_name')}}" name="first_name" placeholder="{{translate('Enter first name')}}" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div>
                                        <label for="last_name" class="form-label">{{translate('Last name')}}</label>
                                        <input type="text"  class="form-control" id="last_name" value="{{old('last_name')}}" name="last_name" placeholder="{{translate('Enter last name')}}" required>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div>
                                        <label for="username" class="form-label">{{translate('Username')}}  <span class="text-danger">*</span></label>
                                        <input type="text" required class="form-control" id="username" value="{{old('username')}}" name="username" placeholder="{{translate('Enter username')}}" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div>
                                        <label for="email" class="form-label">{{translate('Email')}}  <span class="text-danger">*</span></label>
                                        <input type="email" required class="form-control" id="email" value="{{old('email')}}" name="email" placeholder="{{translate('Enter email')}}" required>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label for="country_id" class="form-label">{{translate('Country')}}  <span class="text-danger">*</span></label>


                                    <select name="country_id" id="country_id" class="select2 form-control country">
                                        @foreach ($countries as $country )

                                           <option value="{{  $country->id }}">
                                             {{ $country->name }}
                                           </option>

                                        @endforeach
                                    </select>


                                </div>


                                <div class="col-lg-6">
                                    <label for="phone" class="form-label">{{translate('Phone')}}  <span class="text-danger">*</span></label>

                                    <div class="select-input-wrapper">
                                        <div class="row g-0">
                                            <div class="col-lg-2 col-sm-3 col-4">
                                                <select name="phone_code" class="select2 form-control country-code">
                                                    @foreach (App\Enums\Settings\GlobalConfig::TELEPHONE_CODES as $code )

                                                       <option value="{{ $code['code'] }}">
                                                         {{ $code['name']}}
                                                       </option>

                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-10 col-sm-9 col-8">
                                                <input required class="flex-grow-1 form-control" id="phone" type="text" placeholder="0XXXXXX"  name="phone" value="{{old('phone')}}">
                                            </div>

                                        </div>
                                    </div>

                                </div>


                                <div class="col-lg-6">
                                    <div>
                                        <label for="password" class="form-label">{{translate('Password')}}  <span class="text-danger">*</span></label>
                                        <input type="text" required class="form-control" id="password" value="{{old('password')}}" name="password" placeholder="{{translate('Enter password')}}" required>
                                    </div>
                                </div>


                                <div class="col-xl-6 col-lg-6">
                                    <div>
                                        <label for="image" class="form-label">
                                            {{translate('Image')}}  <span class="text-danger">
                                                ({{file_path()['profile']['delivery_man']['size'] }})
                                            </span>
                                        </label>
                                        <input data-size ="{{file_path()['profile']['delivery_man']['size']}}" type="file" class="preview form-control w-100"
                                            name="image" id="image">
                                    </div>
                                    <div id="image-preview-section">

                                    </div>
                                </div>


                                <div class="col-12">
                                    <div>
                                        <label for="address" class="form-label">
                                            {{translate('Address')}} <span class="text-danger" >*</span>
                                        </label>
                                        <input required id="address"   type="text" name="address" class="form-control" value="{{old('address')}}">
                                    </div>
                                </div>


                                <div class="col-xl-6">
                                    <div>
                                        <label for="latitude" class="form-label">
                                            {{translate('Latitude')}} <span class="text-danger" >*</span>
                                        </label>
                                        <input required id="latitude"   type="text" name="latitude" class="form-control" value="">
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <div>
                                        <label for="longitude" class="form-label">
                                            {{translate('Longitude')}} <span class="text-danger" >*</span>
                                        </label>
                                        <input required  id="longitude" type="text" name="longitude" class="form-control" value="">
                                    </div>
                                </div>


                                <div class="col-12">


                                      <input id="map-input" class="form-control mt-1 map-search-input"
                                        type="text"
                                        placeholder="{{translate('Search your loaction here')}}"/>



                                        <div class="rounded w-100  mb-5 h-400"
                                             id="gmap-site-address"></div>

                                </div>



                            


                            </div>

                            <div class="text-start mt-4">
                                <button type="submit" class="btn btn-success">
                                    {{translate('Add')}}
                                </button>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


@endsection

@push('script-push')
<script src="https://maps.googleapis.com/maps/api/js?key={{site_settings('gmap_client_key')}}&callback=loadGmap&libraries=places&v=3.49"
defer></script>


<script>


           $('.country-code').select2({

           });
           $('.country').select2({

           });


           $(document).on('ready',function (){
                loadGmap()
            })

            function loadGmap() {

                var latitude = 33.14751827254395;
                var longitude = 73.7561387589157;
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
                google.maps.event.addListener(map, 'click', function (mapsMouseEvent) {

                    var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2);
                    var coordinates = JSON.parse(coordinates);
                    var latlng = new google.maps.LatLng(coordinates['lat'], coordinates['lng']);
                    marker.setPosition(latlng);
                    map.panTo(latlng);

                    document.getElementById('latitude').value = coordinates['lat'];
                    document.getElementById('longitude').value = coordinates['lng'];

                    geocoder.geocode({'latLng': latlng}, function (results, status) {

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

                        google.maps.event.addListener(mrkr, "click", function (event) {


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


            $('.newdata').on('click', function () {
	        var html = `
		        <div class="row g-3 border-bottom pb-3 mb-3 newuserdata">
		    		<div class="col-lg-3">
						<input name="key_name[]" class="form-control" type="text" required placeholder="{{translate('Ex:passport number')}}">
					</div>



                    <div class="col-lg-3 input-value-section">
							<input name="value[]" class="form-control input-value" type="text" required placeholder="{{translate('Ex:1234')}}">
					</div>



                   <div class="col-lg-3 input-value-section">
							<input accept="image/*" name="file[]" class="form-control input-value" type="file"  placeholder="{{translate('Ex:1234')}}">
					</div>



		    		<div class="col-lg-2 col-12 text-right">
		                <span class="input-group-btn">
		                    <button class="btn btn-danger btn-md removeBtn" type="button">
								<i class="ri-delete-bin-line"></i>
		                    </button>
		                </span>
		            </div>
		        </div>`;
	        $('.newdataadd').append(html);
	       });
            $(document).on('click', '.removeBtn', function () {
                $(this).closest('.newuserdata').remove();
            });





</script>
@endpush
