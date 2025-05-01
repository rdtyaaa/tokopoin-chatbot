
@if($items->count() != 0)
   @php
     $subTotal = 0;
   @endphp
    <div class="cart-products">
        @foreach($items as $data)
            <div class="cart-product">
                    <div class="cart-product-img">
                        <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$data->product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$data->product->name}}">
                    </div>
                <div class="cart-product-info">
                    <h4 class="cart-product-title"><a href="{{route('product.details',[$data->product->slug ? $data->product->slug :  make_slug($data->product->name),$data->product->id])}}">{{$data->product->name}}</a></h4>
                    <span class="cart-product-price">
                    
                        {{$data->quantity}}  X {{short_amount($data->price - $data->total_taxes)}} </span>
                </div>
                <span data-id="{{$data->id}}" class="remove-product addtocart-remove-btn remove-cart-data"><i class="fa-solid fa-xmark"></i></span>
            </div>
             @php
                $subTotal+= (($data->price - $data->total_taxes)*$data->quantity);
             @endphp
        @endforeach

    </div>

    <div class="cart-product-total-price">
        <span>{{translate("Total")}}: </span>
        <small> {{short_amount($subTotal)}} </small>
    </div>

    <div class="cart-product-action">
        <a href="{{route('cart.view')}}" class="btn--fill">
            {{translate("View Cart")}}
        </a>
        <a href="{{route('user.checkout')}}">
             {{translate("Checkout")}}
        </a>
    </div>

    <div class="cart-loader loader-spinner d-none">
        <div class="spinner-border" role="status">
            <span class="visually-hidden"></span>
        </div>
   </div>

 @else

        <div class="empty-cart">
            <p class="fs-14 text-muted ">{{translate('No product available in your Cart')}}</p>
        </div>
 @endif
