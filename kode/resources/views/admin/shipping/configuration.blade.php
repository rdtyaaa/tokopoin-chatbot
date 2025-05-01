@extends('admin.layouts.app')

@push('style-push')

   <style>


            .info-msg {
                color: #059;
                background-color: #BEF;
            }
   </style>


@endpush


@section('main_content')
    @php
        $notes = [
            'PRODUCT_CENTRIC'   => translate("The shipping cost is determined by summing the individual shipping costs of each product."),
            'FLAT'              => translate("The shipping cost remains fixed regardless of the number of products a customer purchases."),
            'LOCATION_BASED'    => translate("Shipping cost is a fixed rate based on the customer's area. For multiple products from one seller, the cost is determined by the shipping area. Configure area-wise costs in Shipping Cities "),
            'CARRIER_SPECIFIC'  => translate("Shipping cost is determined by the chosen carrier. Carriers can offer free shipping or set costs based on weight or price ranges. Configure these in Shipping Carriers ")
        ];


        $shippingConfiguration =  json_decode(site_settings('shipping_configuration'));





    @endphp
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
                            {{ translate('Shipping Configuration') }}
                        </li>
                    </ol>
                </div>
            </div>


            <div class="row">
                <div class="col-xl-6">
                    <div class="card h-100">
                        <div class="card-header border-bottom-dashed">
                            <div class="row g-4 align-items-center">
                                <div class="col-sm">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            {{ translate('Shipping Configuration') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.shipping.configuration.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="row g-3 mb-4">


                                    <div class="col">
                                        <label for="shipping_option"
                                            class="form-label">{{ translate('Select Shipping Option') }}
                                            <span class="text-danger">*</span> </label>

                                        <select class="form-select" id="shipping_option" name="shipping_option" required>

                                            <option disabled value="" selected>{{ translate('Select One') }}</option>

                                            @foreach ($shippingOptions as $key => $option)
                                                <option {{@$shippingConfiguration->shipping_option == $key ? "selected" :''}} value="{{ $key }}">{{ $option }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col">
                                        <label for="standard_shipping_fee"
                                            class="form-label">{{ translate('Flat Shipping Fee') }} <span
                                                class="text-danger">*</span> </label>
                                        <input type="number" name="standard_shipping_fee" id="standard_shipping_fee"
                                            value="{{ @$shippingConfiguration->standard_shipping_fee }}"
                                            class="form-control" placeholder="Set Flat Shipping Fee" required="">
                                    </div>

                                </div>


                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-success btn-xl fs-6 text-light">{{ translate('Save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6">
                    <div class="card h-100">
                        <div class="card-header border-bottom-dashed">
                            <div class="row g-4 align-items-center">
                                <div class="col-sm">
                                    <div>
                                        <h5 class="card-title mb-0">
                                            {{ translate('Notes') }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="notes-content" class=" d-flex align-items-start justify-content-start h-100">
                                <div class="info-msg p-3 w-100">
                                    <i class="fa fa-info-circle"></i>
                                    {{translate('Select a shipping option to see detailed information.')}}
                                 </div>
                               
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-include')
    <script>
        "use strict";

        var notes = @json($notes);
        
        var selectedOption = $('#shipping_option').val();
        var selectedNotes = notes[selectedOption];

        shippingNote(selectedNotes)


         $('#shipping_option').change(function() {
                var selectedOption = $(this).val();
                var selectedNotes = notes[selectedOption];

                shippingNote(selectedNotes)
        });


        function shippingNote(selectedNotes){

            $('.info-msg').html(selectedNotes);
        }




    </script>
@endpush
