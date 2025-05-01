@if($flashDeal)
    <section class="bg--primary pt-80 pb-80">
        <div class="Container">
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-5">
                    <div class="deal-title-area">
                        <div class="title-left-content mb-5">
                            <h3>
                                {{$flashDeal->name}}
                                <span>{{@frontend_section_data($flash_deal->value,'heading')}}</span> </h3>
                            <p>{{@frontend_section_data($flash_deal->value,'sub_heading')}}</p>
                        </div>
                        <p class="time-remaining">
                            {{translate('Time Remaining')}}
                        </p>


                        <div class="date mb-5" >
                            <div class="time-clock">
                                <div class="d-flex flex-column">
                                    <div class="day_flash_deal"></div>
                                    <p>
                                         {{translate('Days')}}
                                    </p>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class='hr_flash_deal'></div>
                                    <p>{{translate('Hours')}}
                                    </p>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="mnts_flash_deal"></div>
                                    <p>
                                        {{translate('Minutes')}}
                                    </p>
                                </div>
                                <div class="d-flex flex-column">
                                <div class="s_flash_deal"></div>
                                    <p>
                                        {{translate('Seconds')}}
                                    </p>
                                </div>

                            </div>
                        </div>

                        <a href="{{route('flash.deal',$flashDeal->slug)}}" class="view-more-btn d-inline-flex">{{translate('View More Products')}} </a>
                    </div>
                </div>
                <div class="col-xl-9 col-lg-8 col-md-7">
                    <div>
                        <div class="arrow-style-one style-small d-flex justify-content-end align-items-center gap-3 mb-4">
                            <div class="arrow flash-prev" tabindex="0" role="button" aria-label="Previous slide">
                                <svg  version="1.1"  x="0" y="0" viewBox="0 0 493.356 493.356"   xml:space="preserve" class="prev"><g><path d="m490.498 239.278-109.632-99.929c-3.046-2.474-6.376-2.95-9.993-1.427-3.613 1.525-5.427 4.283-5.427 8.282v63.954H9.136c-2.666 0-4.856.855-6.567 2.568C.859 214.438 0 216.628 0 219.292v54.816c0 2.663.855 4.853 2.568 6.563 1.715 1.712 3.905 2.567 6.567 2.567h356.313v63.953c0 3.812 1.817 6.57 5.428 8.278 3.62 1.529 6.95.951 9.996-1.708l109.632-101.077c1.903-1.902 2.852-4.182 2.852-6.849 0-2.468-.955-4.654-2.858-6.557z" opacity="1" data-original="#000000" ></path></g></svg>
                            </div>
                            <div class="arrow flash-next" tabindex="0" role="button" aria-label="Next slide">
                                <svg  version="1.1"  x="0" y="0" viewBox="0 0 493.356 493.356"   xml:space="preserve" ><g><path d="m490.498 239.278-109.632-99.929c-3.046-2.474-6.376-2.95-9.993-1.427-3.613 1.525-5.427 4.283-5.427 8.282v63.954H9.136c-2.666 0-4.856.855-6.567 2.568C.859 214.438 0 216.628 0 219.292v54.816c0 2.663.855 4.853 2.568 6.563 1.715 1.712 3.905 2.567 6.567 2.567h356.313v63.953c0 3.812 1.817 6.57 5.428 8.278 3.62 1.529 6.95.951 9.996-1.708l109.632-101.077c1.903-1.902 2.852-4.182 2.852-6.849 0-2.468-.955-4.654-2.858-6.557z" opacity="1" data-original="#000000" ></path></g></svg>
                            </div>
                        </div>
                        @php
                            $products = App\Models\Product::with(['review','order','stock','stock.attribute'])
                                            ->whereIn('id', $flashDeal->products)
                                            ->inhouseProduct()
                                            ->physical()
                                            ->take(8)
                                            ->get()
                        @endphp

                        <div class="swiper flash-slider">
                            <div class="swiper-wrapper">
                                @forelse($products as $product)
                                    <div class="swiper-slide">
                                        <div class="product-item">
                                            <div class="product-img">
                                                <div class="todays-deal-cart">
                                                    @php
                                                        $randNum = rand(144,10000000);
                                                    @endphp
                                                    <div class="product-action">
                                                        <a href="javascript:void(0)" data-product_id = '{{$randNum }}' class="buy-now addtocartbtn">
                                                            <span class="buy-now-icon icon-white"><svg  version="1.1"  x="0" y="0" viewBox="0 0 511.997 511.997"   xml:space="preserve" ><g><path d="M405.387 362.612c-35.202 0-63.84 28.639-63.84 63.84s28.639 63.84 63.84 63.84 63.84-28.639 63.84-63.84-28.639-63.84-63.84-63.84zm0 89.376c-14.083 0-25.536-11.453-25.536-25.536s11.453-25.536 25.536-25.536c14.083 0 25.536 11.453 25.536 25.536s-11.453 25.536-25.536 25.536zM507.927 115.875a19.128 19.128 0 0 0-15.079-7.348H118.22l-17.237-72.12a19.16 19.16 0 0 0-18.629-14.702H19.152C8.574 21.704 0 30.278 0 40.856s8.574 19.152 19.152 19.152h48.085l62.244 260.443a19.153 19.153 0 0 0 18.629 14.702h298.135c8.804 0 16.477-6.001 18.59-14.543l46.604-188.329a19.185 19.185 0 0 0-3.512-16.406zM431.261 296.85H163.227l-35.853-150.019h341.003L431.261 296.85zM173.646 362.612c-35.202 0-63.84 28.639-63.84 63.84s28.639 63.84 63.84 63.84 63.84-28.639 63.84-63.84-28.639-63.84-63.84-63.84zm0 89.376c-14.083 0-25.536-11.453-25.536-25.536s11.453-25.536 25.536-25.536 25.536 11.453 25.536 25.536-11.453 25.536-25.536 25.536z" opacity="1" data-original="#000000" ></path></g></svg></span>
                                                            {{translate('Add to cart')}}
                                                        </a>
                                                    </div>
                                                    <form class="attribute-options-form-{{$randNum }}">
                                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                                    </form>
                                                </div>
                                                <a href="{{route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])}}">
                                                    <img src="{{show_image(file_path()['product']['featured']['path'].'/'.$product->featured_image,file_path()['product']['featured']['size'])}}" alt="{{$product->name}}">
                                                </a>
                                                <ul class="hover-action">
                                                    <li><a class="comparelist compare-btn wave-btn" data-product_id="{{$product->id}}" href="javascript:void(0)"><i class="fa-solid fa-code-compare"></i></a></li>
                                                </ul>
                                                <div class="quick-view">
                                                    <button class="quick-view-btn quick--view--product"  data-product_id="{{$product->id}}">
                                                        <svg  version="1.1" width="18" height="18" x="0" y="0" viewBox="0 0 519.644 519.644"   xml:space="preserve" ><g><path d="M259.822 427.776c-97.26 0-190.384-71.556-251.854-145.843-10.623-12.839-10.623-31.476 0-44.314 15.455-18.678 47.843-54.713 91.108-86.206 108.972-79.319 212.309-79.472 321.492 0 50.828 36.997 91.108 85.512 91.108 86.206 10.623 12.838 10.623 31.475 0 44.313-61.461 74.278-154.572 145.844-251.854 145.844zm0-304c-107.744 0-201.142 102.751-227.2 134.243a2.76 2.76 0 0 0 0 3.514c26.059 31.492 119.456 134.243 227.2 134.243s201.142-102.751 227.2-134.243c1.519-1.837-.1-3.514 0-3.514-26.059-31.492-119.456-134.243-227.2-134.243z"  data-original="#000000" ></path><path d="M259.822 371.776c-61.757 0-112-50.243-112-112s50.243-112 112-112 112 50.243 112 112-50.243 112-112 112zm0-192c-44.112 0-80 35.888-80 80s35.888 80 80 80 80-35.888 80-80-35.888-80-80-80z"  data-original="#000000" ></path></g></svg>
                                                        {{translate("Quick View")}}
                                                    </button>
                                                </div>
                                                @if($product->discount_percentage > 0)
                                                    <span class="offer-tag">{{translate('off')}}  {{round($product->discount_percentage)}} %</span>
                                                @endif
                                            </div>
                                            <div class="product-info todays-deal-product-info">
                                                <h4 class="product-title">
                                                    <a href="{{route('product.details',[$product->slug ? $product->slug : make_slug($product->name),$product->id])}}">{{$product->name}}</a>
                                                </h4>
                                                <div class="product-price todays-deal-price">
                                                    @php
                                                        $price      =  (@$product->stock->first()?->price ?? $product->price);
                                
                
                                                    @endphp

                                                    @if(($product->discount_percentage) > 0)

                                                        <span>
                                                            {{short_amount(cal_discount($product->discount_percentage,$price) )}}
                                                        </span>

                                                        <del>
                                                            {{short_amount($price)}}</del>
                                                    @else
                                                        <span>
                                                            {{short_amount($price)}}
                                                        </span>

                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty

                                    @include("frontend.partials.empty",['message' => 'No product found'])

                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
               $date = date("Y-m-d H:i:s",strtotime($flashDeal->end_date));
        @endphp
    </section>


    @push('scriptpush')
    <script>
        (function () {
            'use strict';
            var dateTime = "{{$date}}";
            const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;
            var days = document.querySelector('.day_flash_deal')
            var hours = document.querySelector('.hr_flash_deal')
            var minutes = document.querySelector('.mnts_flash_deal')
            var seconds = document.querySelector('.s_flash_deal')


            var time = dateTime;
            const countDown = new Date(time).getTime(),
                deal = setInterval(function() {
                const now = new Date().getTime(),
                distance = countDown - now;
                days.innerText =  Math.floor(distance / (day));
                hours.innerText =  Math.floor((distance % (day)) / (hour));
                minutes.innerText =  Math.floor((distance % (hour)) / (minute));
                seconds.innerText =   Math.floor((distance % (minute)) / second);

            }, 0)

        })();

    </script>
    @endpush
@endif


