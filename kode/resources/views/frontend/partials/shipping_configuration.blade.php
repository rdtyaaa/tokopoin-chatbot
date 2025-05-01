
@foreach ($shippingDeliverys as $shippingDelivery)

    <div class="col-md-6">
        <div class="form-check card-radio">
            <input
               data-shipping_price="{{ short_amount($shippingDelivery->price, false, false) }}"
                id="{{ $shippingDelivery->id }}" name="shipping_method"
                type="radio"
                class="form-check-input shiping-info checkout-radio-btn shipping-method "
                value="{{ $shippingDelivery->id }}">
            <label class="form-check-label pointer"
                for="{{ $shippingDelivery->id }}">

                    <span class="fs-16 float-end mt-3 text-wrap d-block">

                        {{ short_amount($shippingDelivery->price) }}

                    </span>
        

                <span class="fs-14 mb-1 text-wrap d-block">
                    {{ @$shippingDelivery->name }}
                </span>

                <span class="text-muted fs-12 fw-normal text-wrap d-block">
                    {{ translate('Delivery In') }}
                    {{ $shippingDelivery->duration }}
                    {{ translate('Days') }}
                </span>
            </label>
        </div>
    </div>
@endforeach