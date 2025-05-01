<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     @include('frontend.partials.seo')
    <title>{{site_settings('site_name')}} - {{translate($title)}}</title>
    <link rel="shortcut icon" href="{{show_image('assets/images/backend/logoIcon/'.site_settings("site_favicon"),file_path()['favicon']['size'])}}" type="image/x-icon">


    <link href="{{asset('assets/frontend/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/global/css/select2.min.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/frontend/css/select2.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/frontend/css/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/frontend/css/all.min.css')}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="{{asset('assets/frontend/css/line-awesome.min.css')}}" />

    <link href="{{asset('assets/frontend/css/nouislider.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/frontend/css/global.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/frontend/css/custom.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/frontend/css/view-ticket.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/frontend/css/bootstrap-custom.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/frontend/css/style.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/global/css/toastr.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/frontend/css/media.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/backend/css/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />


    @if (site_settings("google_analytics") == App\Enums\StatusEnum::true->status() )
       <script async src="https://www.googletagmanager.com/gtag/js?id={{site_settings('google_tracking_id')}}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{{site_settings("google_tracking_id")}}');
        </script>
    @endif


    @if (site_settings('facebook_pixel') == App\Enums\StatusEnum::true->status())
   
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ site_settings("facebook_pixel_id") }}');
            fbq('track', 'PageView');
        </script>
        <noscript>
            <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ site_settings("facebook_pixel_id") }}&ev=PageView&noscript=1"/>
        </noscript>

   @endif


    @include('frontend.partials.color')
    @stack('style-include')
    @stack('stylepush')

    <style>

        .newsletter {
            position: relative;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.6)),
                url("{{asset('assets/images/news_latter.jpg')}}");
            z-index: 1;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }

        .login-bg{
            background-size: cover !important;
            background-repeat: no-repeat !important;
            background-position: center center !important;
            z-index: 2 !important;
           background: url("{{asset('assets/images/login_bg.jpg')}}");
        }

        .contact-bg{
            background-size: cover !important;
            background-repeat: no-repeat !important;
            background-position: center center !important;
            z-index: 2 !important;
            background: url("{{asset('assets/images/contact.jpg')}}");
        }


        .footer {
                background: linear-gradient(
                    90deg,
                    rgba(255, 255, 255, 0.5),
                    rgba(255, 255, 255, 0.5)
                    ),
                    url("{{asset('assets/images/footer_bg.jpg')}}");
                position: relative;
                z-index: 1;
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
        }

        .testimonial-section {

            background: linear-gradient(90deg, var(--primary-light), var(--primary-light)),
                  url("{{asset('assets/images/testimonial.jpg')}}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }


        .title-left-content > h3::after {
            content: url("{{asset('assets/images/title-vector.png')}}");
            position: absolute;
            right: 0px;
            display: inline-block;
            top: 12px;
            width: 50px !important;
            height: 30px;
            z-index: -1;
        }

        .wp-floating-btn{
            position: fixed;
            bottom:130px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000 !important;
            
        }
        .wp-icon{
            width: 45px;
            height: 45px;
        }
        .wp-icon >svg{
            width: 100%;
            height: 100%;
        }


    </style>
</head>

<body>


    
    @if(site_settings('preloader') ==  App\Enums\StatusEnum::true->status())

        <div class="preloader-wrapper">
            <canvas id="bg"></canvas>
            <div class="loader-content">
                <div class="loader-img scale-up-center">
                    <img src="{{ show_image('assets/images/backend/logoIcon/'.site_settings('site_logo'), file_path()['site_logo']['size']) }}" alt="site-logo.jpg" class="img-fluid">
                </div>
            </div>
        </div>

    @endif

    @php
            $support = frontend_section('support');
            $contact = frontend_section('contact');
            $login = frontend_section('login');
            $apps_section =  frontend_section('app-section');
            $footer_text =  frontend_section('footer-text');
            $social_icon =  frontend_section('social-icon');
            $paymnet_image =  frontend_section('payment-image');
            $cookie = frontend_section('cookie');
            $news_latter = frontend_section('news-latter');
            $tawkTo = site_settings('tawk_to',null) 
                           ? json_decode(site_settings('tawk_to'),true) :
                                    [
                                                'property_id' => '@@',
                                                'widget_id'   => '@@',
                                                'status'      => '1',
                                    ];  
            


                                   


            $search_min = request()->input('search_min') 
                                ? request()->input('search_min') 
                                : ((short_amount(site_settings('search_min',0),false,false)));

            $search_max = request()->input('search_max') 
                                    ?  request()->input('search_max') 
                                    :  ((short_amount(site_settings('search_max',0),false,false)));





    @endphp
    @include('frontend.partials.header')

    @if(!session()->has('dont_show'))
        @includeWhen($news_latter->status == '1','frontend.partials.newsLatter')
    @endif

    <main>
        @yield('content')

        @includeWhen($news_latter->status == '1', 'frontend.section.news_later', ['news_latter' => $news_latter])

    </main>



        @include('frontend.partials.footer')
        @include('frontend.partials.sidebar')


        @if(site_settings("whats_app_plugin")  ==  App\Enums\StatusEnum::true->status())
            <div class="wp-floating-btn">
                <a href="https://wa.me/{{ site_settings("whats_app_number") }}?text={{site_settings('whats_app_number_int_message')}}" target="_blank">
                    <span class="wp-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"  clip-rule="evenodd"><path fill="#fff" d="M4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98c-0.001,0,0,0,0,0h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303z"/><path fill="#fff" d="M4.868,43.803c-0.132,0-0.26-0.052-0.355-0.148c-0.125-0.127-0.174-0.312-0.127-0.483l2.639-9.636c-1.636-2.906-2.499-6.206-2.497-9.556C4.532,13.238,13.273,4.5,24.014,4.5c5.21,0.002,10.105,2.031,13.784,5.713c3.679,3.683,5.704,8.577,5.702,13.781c-0.004,10.741-8.746,19.48-19.486,19.48c-3.189-0.001-6.344-0.788-9.144-2.277l-9.875,2.589C4.953,43.798,4.911,43.803,4.868,43.803z"/><path fill="#cfd8dc" d="M24.014,5c5.079,0.002,9.845,1.979,13.43,5.566c3.584,3.588,5.558,8.356,5.556,13.428c-0.004,10.465-8.522,18.98-18.986,18.98h-0.008c-3.177-0.001-6.3-0.798-9.073-2.311L4.868,43.303l2.694-9.835C5.9,30.59,5.026,27.324,5.027,23.979C5.032,13.514,13.548,5,24.014,5 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974C24.014,42.974,24.014,42.974,24.014,42.974 M24.014,4C24.014,4,24.014,4,24.014,4C12.998,4,4.032,12.962,4.027,23.979c-0.001,3.367,0.849,6.685,2.461,9.622l-2.585,9.439c-0.094,0.345,0.002,0.713,0.254,0.967c0.19,0.192,0.447,0.297,0.711,0.297c0.085,0,0.17-0.011,0.254-0.033l9.687-2.54c2.828,1.468,5.998,2.243,9.197,2.244c11.024,0,19.99-8.963,19.995-19.98c0.002-5.339-2.075-10.359-5.848-14.135C34.378,6.083,29.357,4.002,24.014,4L24.014,4z"/><path fill="#40c351" d="M35.176,12.832c-2.98-2.982-6.941-4.625-11.157-4.626c-8.704,0-15.783,7.076-15.787,15.774c-0.001,2.981,0.833,5.883,2.413,8.396l0.376,0.597l-1.595,5.821l5.973-1.566l0.577,0.342c2.422,1.438,5.2,2.198,8.032,2.199h0.006c8.698,0,15.777-7.077,15.78-15.776C39.795,19.778,38.156,15.814,35.176,12.832z"/><path fill="#fff" fill-rule="evenodd" d="M19.268,16.045c-0.355-0.79-0.729-0.806-1.068-0.82c-0.277-0.012-0.593-0.011-0.909-0.011c-0.316,0-0.83,0.119-1.265,0.594c-0.435,0.475-1.661,1.622-1.661,3.956c0,2.334,1.7,4.59,1.937,4.906c0.237,0.316,3.282,5.259,8.104,7.161c4.007,1.58,4.823,1.266,5.693,1.187c0.87-0.079,2.807-1.147,3.202-2.255c0.395-1.108,0.395-2.057,0.277-2.255c-0.119-0.198-0.435-0.316-0.909-0.554s-2.807-1.385-3.242-1.543c-0.435-0.158-0.751-0.237-1.068,0.238c-0.316,0.474-1.225,1.543-1.502,1.859c-0.277,0.317-0.554,0.357-1.028,0.119c-0.474-0.238-2.002-0.738-3.815-2.354c-1.41-1.257-2.362-2.81-2.639-3.285c-0.277-0.474-0.03-0.731,0.208-0.968c0.213-0.213,0.474-0.554,0.712-0.831c0.237-0.277,0.316-0.475,0.474-0.791c0.158-0.317,0.079-0.594-0.04-0.831C20.612,19.329,19.69,16.983,19.268,16.045z" clip-rule="evenodd"/></svg>
                    </span>
                </a>
            </div>
        @endif




        @if( $cookie->status == '1'  && !session()->has('cookie_consent') )
            @include('frontend.partials.cookie')
        @endif

     <script src="{{asset('assets/global/js/jquery.min.js')}}"></script>
     <script src="{{asset('assets/global/js/select2.min.js')}}"></script>

     <script src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
     <script src="{{asset('assets/frontend/js/swiper-bundle.min.js')}}"></script>
     <script src="{{asset('assets/frontend/js/nouislider.min.js')}}"></script>
     <script src="{{asset('assets/global/js/toastify-js.js')}}"></script>
     <script src="{{asset('assets/global/js/helper.js')}}"></script>
     <script src="{{asset('assets/frontend/js/script.js')}}"></script>
     <script  src="{{asset('assets/backend/js/flatpickr.js')}}"></script>


     @stack('script-include')
     @include('partials.notify')
     @include('frontend.partials.script')
     @stack('scriptpush')
     <script>
        "use strict";


        function social_share(title, w, h) {


            var variantName = $(".attribute-select:checked").map(function() {
                                    return $(this).val();
                                }).get().join("-");


            var message = $('.wp-message').val();

            var price = $(".price-section span").text();
            if(!price){
                price  = "{{show_currency()}}"+$("#total").text();
            }
       
            message = message.replace("[price]", price.trim());
            message = message.replace("[variant_name]", variantName);

         
            var phone  = $('.wp-number').val()
       
            var url    = "https://wa.me/" + phone + "?text=" + encodeURIComponent(message);

 

          var dualScreenLeft =
            window.screenLeft != undefined ? window.screenLeft : screen.left;
          var dualScreenTop =
            window.screenTop != undefined ? window.screenTop : screen.top;

            var width = window.innerWidth
              ? window.innerWidth
              : document.documentElement.clientWidth
              ? document.documentElement.clientWidth
              : screen.width;
            var height = window.innerHeight
              ? window.innerHeight
              : document.documentElement.clientHeight
              ? document.documentElement.clientHeight
              : screen.height;

            var left = width / 2 - w / 2 + dualScreenLeft;
            var top = height / 2 - h / 2 + dualScreenTop;
            var newWindow = window.open(
              url,
              title,
              "scrollbars=yes, width=" +
                w +
                ", height=" +
                h +
                ", top=" +
                top +
                ", left=" +
                left
            );

            if (window.focus) {
              newWindow.focus();
            }
      }


        $(document).on('click','.product-gallery-small-img',function(e){

            var src = $(this).find('img').attr('src')
            $('.qv-lg-image').attr('src',src)
            $('.magnifier').css("background-image", "url(" + src + ")");
        })
        $(document).on('click','.quick-view-img',function(e){

            var src = $(this).find('img').attr('src')
            $('.qv-lg-image').attr('src',src)

        })

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        @if(isset($tawkTo['widget_id']))
                var status = "{{ $tawkTo['status']}}"
                var widget_id = "{{ $tawkTo['widget_id']}}"
                var property_id = "{{ $tawkTo['property_id']}}"

                if(status == '1')
                {
                    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
                    (function(){

                        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                        s1.async=true;
                        s1.src=`https://embed.tawk.to/${property_id}/${widget_id}`;
                        s1.charset='UTF-8';
                        s1.setAttribute('crossorigin','*');
                        s0.parentNode.insertBefore(s1,s0);
                    })();
                }

        @endif

        var newsLetterModal = document.getElementById('newsletterModal');
        var currentRoute  = "{{Route::currentRouteName()}}"
        if (newsLetterModal != null && currentRoute == 'home') {
            window.addEventListener("load", () => {
                var newModal = new bootstrap.Modal(document.getElementById('newsletterModal'), {});
                setTimeout(() => {
                    newModal.show();
                }, 3000)
            })
        }

         $(function(){
             $(document).on('click', '.modal-closer',function(e){
                 e.preventDefault();
                 var data = $('#dont_show').val();
                 var check = $('#dont_show').is(':checked');
                 if(check){
                     $.ajax({
                         headers: {"X-CSRF-TOKEN": "{{csrf_token()}}"},
                         type: "POST",
                         url: "{{route('newslatter.close')}}",
                         dataType: "json",
                         data: {data,  "_token" :"{{csrf_token()}}"},
                   
                     });
                 }
             })
             $(document).on('click', '.update_qty',function(e) {
                    var cartItemQuantity = $('#quantity').val();
                    if ($(this).hasClass('increment')) {
                        cartItemQuantity++;
                    } else {
                        if (cartItemQuantity > 1) {
                            cartItemQuantity--;
                        } else {
                            cartItemQuantity = 1;
                        }
                    }

                    $('#quantity').val(cartItemQuantity);
                });
             })

            $(document).on('click','.oder-btn',function(e){
                $(this).html(`<i class="fa-solid fa-cart-shopping label-icon align-middle fs-14 ">
                             </i>
                                <div class="spinner-border  order-spinner me-1 " role="status">
                                    <span class="visually-hidden"></span>
                                </div>`);

            });


            $(document).on('click','.attribute-select',function(e){
                    var form = $('.quick-view-form')[0]
                    var data = new FormData(form);
                    $.ajax({
                            headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                            url: "{{route('product.stock.price')}}",
                            method: "post",
                            data: data,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                var response_data = JSON.parse(response);
                                const currencySymbol = '{{show_currency()}}';
                                const price = response_data.discount_price === 0 ? response_data.price : response_data.discount_price;
                                const priceHtml = `<span>${currencySymbol}${price}</span>`;
                                const discountHtml = response_data.discount_price !== 0 ? ` <del>${currencySymbol}${response_data.price}</del>` : '';
                                const html = priceHtml + discountHtml;

                                var stockHtml  = `<div class="${response_data.stock ? "instock" :"outstock"}">
                                                           <i class="${response_data.stock ? "fa-solid fa-circle-check"  :"fas fa-times-circle"}"></i>
                                                        <p>
                                                            ${response_data.stock ? 'In Stock': 'Stock out' }
                                                        </p>
                                                    </div>`;


                                $('#quick-view-stock').html(stockHtml)

                                $('.price-section').html(html)
                            }
                        });


            })


               // cookie configuration
      $(document).on('click','.cookie-control',function(e){

            $('.js-cookie-consent').hide();
            $.ajax({
                method:'get',
                url: "{{route('accept.cookie')}}",
                dataType: 'json',

                success: function(response){

                    toaster(response.message,'success')

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
                                toaster(error.responseJSON.message,'danger')
                            }
                            else{
                                toaster( error.responseJSON.error,'danger')
                            }
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }
                }
            })
        })




        </script>


</body>

</html>
