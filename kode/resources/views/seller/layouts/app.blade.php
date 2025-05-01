
<!DOCTYPE html>
<html lang="en" data-layout-default="vertical" data-sidebar-size="lg" data-topbar="light" data-sidebar="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{csrf_token()}}" />
    <title>{{(site_settings('site_name'))}} - {{translate($title)}}</title>
    <link rel="shortcut icon" href="{{show_image('assets/images/backend/logoIcon/'.site_settings("site_favicon"),file_path()['favicon']['size'])}}" type="image/x-icon">
    <link href="{{asset('assets/global/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/global/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/backend/css/root.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/backend/css/style.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/backend/css/custom.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/global/css/toastr.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/global/css/select2.min.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/global/css/select2.css')}}" rel="stylesheet" />
    <link href="{{asset('assets/backend/css/flatpickr.min.css')}}" rel="stylesheet" type="text/css" />

    @stack('style-include')
    @stack('style-push')
</head>
<body>

    <div id="layout-container">
        @include('seller.partials.topbar')
        @include('seller.partials.sidebar')
        <div class="main-container">
            @yield('main_content')
            @if(request()->routeIs('admin.order.print'))
                @include('admin.partials.footer')
            @endif
        </div>
    </div>

    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    <script src="{{asset('assets/global/js/jquery.min.js')}}" ></script>
    <script src="{{asset('assets/global/js/select2.min.js')}}"></script>

    <script src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/backend/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    <script src="{{asset('assets/global/js/toastify-js.js')}}"></script>
    <script src="{{asset('assets/global/js/simplebar.min.js')}}"></script>
    <script src="{{asset('assets/global/js/helper.js')}}"></script>
    <script  src="{{asset('assets/backend/js/app.js')}}"></script>
    <script  src="{{asset('assets/backend/js/flatpickr.js')}}"></script>
    @include('partials.notify')
    @stack('script-include')
    @stack('script-push')

    <script>
        'use strict'

        flatpickr("#datePicker", {
            dateFormat: "Y-m-d",
            mode: "range",
        });
        $(".chanage_currency").on("click", function() {
            var currency = $(this).attr('data-value')
            window.location.href = "{{route('home')}}/currency/change/"+currency;
        });
      
        $(document).on('click', '.note-btn.dropdown-toggle', function (e) {
                    var $clickedDropdown = $(this).next();
            $('.note-dropdown-menu.show').not($clickedDropdown).removeClass('show');
            $clickedDropdown.toggleClass('show');
            e.stopPropagation();
        });

        $(document).on('click', function(e) {

            if (!$(e.target).closest('.note-btn.dropdown-toggle').length) {
                $(".note-dropdown-menu").removeClass("show");
            }
        });
      
      
         
    </script>
</body>
</html>
