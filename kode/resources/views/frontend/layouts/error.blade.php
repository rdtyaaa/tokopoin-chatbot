<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{asset('assets/frontend/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/line-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/frontend/css/global.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/global/css/toastr.css')}}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{show_image('assets/images/backend/logoIcon/'.site_settings("site_favicon"),file_path()['favicon']['size'])}}" type="image/x-icon">

    <title>
            {{@$title  ?? translate('Not Found')}}
    </title>
     @stack('stylepush')
  </head>
  <body>

    @yield('content')

    <script src="{{asset('assets/global/js/jquery.min.js')}}"></script>
    <script src="{{asset('assets/global/js/toastify-js.js')}}"></script>
    <script src="{{asset('assets/global/js/helper.js')}}"></script>
    @include('partials.notify')
    @stack('scriptpush')
  </body>
</html>

