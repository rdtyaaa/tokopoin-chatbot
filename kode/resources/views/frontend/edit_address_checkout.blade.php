<input type="hidden" name="id" value="{{@$userAddress->id}}">

<div class="row g-4">

    <div class="col-md-12">
        <div>
            <label for="edit-address_name" class="form-label">
                {{ translate('Address Name') }} <span class="text-danger">*</span>
            </label>

            <input type="text" class="form-control" id="edit-address_name" name="address_name"
                placeholder="{{ translate('Enter name') }}" value="{{ @$userAddress->name}}">
        </div>
    </div>

    <div class="col-md-6">
        <div>
            <label for="edit-billinginfo-firstName" class="form-label">
                {{ translate('First Name') }}
                <span class="text-danger">*</span>
            </label>

            <input type="text" class="form-control " id="edit-billinginfo-firstName"
                name="first_name" placeholder="{{ translate('Enter first name') }}"
                value="{{ old('first_name') ? old('first_name') : @$userAddress->first_name }}">
        </div>
    </div>

    <div class="col-md-6">
        <div>
            <label for="edit-billinginfo-lastName" class="form-label">
                {{ translate('Last Name') }}
                <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control " id="edit-billinginfo-lastName"
                name="last_name" placeholder="{{ translate('Enter last name') }}"
                value="{{ old('last_name') ? old('last_name') : @$userAddress->last_name }}">
        </div>
    </div>

    <div class="col-md-6">
        <div>
            <label for="edit-billinginfo-email" class="form-label">
                {{ translate('Email') }}
                <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" id="edit-billinginfo-email"
                value="{{ @$userAddress->email ? $userAddress->email : old('email') }}"
                placeholder="Enter email">
        </div>
    </div>

    <div class="col-md-6">
        <div>
            <label for="edit-billinginfo-phone" class="form-label">
                {{ translate('Phone') }}
                <span class="text-danger">*</span>
            </label>
            <input type="text" name="phone" class="form-control "
                id="edit-billinginfo-phone"
                value="{{ old('phone') ? old('phone') : @$userAddress->phone }}"
                placeholder="Enter phone no.">
        </div>
    </div>

    <div class="col-12">
        <div>
            <label for="edit-billinginfo-address" class="form-label">
                {{ translate('Address') }}

                <span class="text-danger"> *</span>

            </label>
            <textarea name="address" class="form-control address" id="edit-billinginfo-address" placeholder="Enter address"
                rows="3">{{ old('address') ? old('address') : @$userAddress->address->address }}</textarea>
        </div>
    </div>

    <div class="col-md-12">

        <div>
            <label for="edit-billinginfo-country_id" class="form-label">
                {{ translate('Country') }} <span class="text-danger">*</span>
            </label>

            <select class="form-control edit-location country" name="country_id"
                id="edit-billinginfo-country_id">

                @foreach ($countries as $country)
                    <option {{@$userAddress->country_id == $country->id ? 'selected' : ''}} value="{{ $country->id }}">
                        {{ $country->name }}
                    </option>
                @endforeach

            </select>
        </div>

    </div>

    <div class="col-md-4">
        <div>
            <label for="edit-billinginfo-state_id" class="form-label">
                {{ translate('State') }} <span class="text-danger">*</span>
            </label>

            <select class="form-control edit-location state" name="state_id"
                id="edit-billinginfo-state_id">

                @foreach(@$userAddress->country->states as $state)
                <option {{@$userAddress->state_id == $state->id ? 'selected' : ''}} value="{{$state->id}}">{{ $state->name }}
                </option>
                @endforeach

            </select>

        </div>
    </div>

    <div class="col-md-4">
        <div>
            <label for="edit-billinginfo-city_id" class="form-label">
                {{ translate('City') }} <span class="text-danger">*</span>
            </label>

            <select class="form-control edit-location city" name="city_id"
                id="edit-billinginfo-city_id">
                @foreach(@$userAddress->state->cities as $city)
                <option {{@$userAddress->city_id == $city->id ? 'selected' : ''}} value="{{$city->id}}">{{ $city->name }}
                </option>
                @endforeach

            </select>

        </div>
    </div>

    <div class="col-md-4">
        <div>
            <label for="edit-billinginfo-zip" class="form-label">
                {{ translate('Zip Code') }} <span class="text-danger">*</span>
            </label>
            <input class="form-control " type="text" id="edit-billinginfo-zip" name="zip"
                value="{{ old('zip') ? old('zip') : @$userAddress->zip }}"
                placeholder="{{ translate('1205') }}" required>
        </div>
    </div>
</div>

<div class="map-container" id="map-container-user-edit">
    <div class="row g-4">
        <div class="col-xl-6">
            <div>
                <label for="edit-billinginfo-latitude" class="form-label">
                    {{ translate('Latitude') }} <span class="text-danger">*</span>
                </label>
                <input required type="text" name="latitude" id="edit-billinginfo-latitude"
                    class="form-control latitude" value="{{@$userAddress->address->latitude}}">
            </div>
        </div>

        <div class="col-xl-6">
            <div>
                <label for="edit-billinginfo-longitude" class="form-label">
                    {{ translate('Longitude') }} <span class="text-danger">*</span>
                </label>
                <input required type="text" name="longitude"
                    id="edit-billinginfo-longitude" class="form-control longitude"
                    value="{{@$userAddress->address->longitude}}">
            </div>
        </div>

        <div class="col-12">
            <input id="map-search-user-edit" class="form-control mt-1 map-search-input" type="text" placeholder="{{ translate('Search your location here') }}">

            <div id="gmap-user-edit" class="rounded w-100 mb-5 h-400 gmap-site-address"></div>
        </div>
    </div>
</div>
