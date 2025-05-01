<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" class="sr" data-sidebar="open">

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{csrf_token()}}" />
   <title>
      {{@config('installer.app_name')}}-{{@$title}}
   </title>

   <link href="{{asset('assets/frontend/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
   <link href="{{asset('assets/frontend/css/bootstrap-icons.min.css')}}" rel="stylesheet" type="text/css" />
   <link href="{{asset('assets/global/css/toastr.css')}}" rel="stylesheet" type="text/css" />

   <style>
      .main{
            min-height: 100vh;
            padding: 30px 0;
            background: var(--color-white);
            background-size: cover;
            background-repeat: no-repeat;
            position: relative;
            z-index: 1;
        }
        .main::before{
          content: '';
          position: absolute;
          left: 10px;
          top: 10px;
          width: 300px;
          height: 300px;
          border-radius: 50%;
          background: var(--color-primary-light-2);
          filter: blur(170px);
          z-index: -1;
        }
        .main::after{
          content: '';
          position: absolute;
          right: 10px;
          bottom: 10px;
          width: 300px;
          height: 300px;
          border-radius: 50%;
          background: var(--color-primary-light-2);
          filter: blur(170px);
          z-index: -1;
        }
   </style>
   <link href="{{asset('assets/install/css/style.css')}}" rel="stylesheet" type="text/css" />

    @stack('styles')
    @stack('style-include')
  </head>
  <body>

    <main class="main d-flex flex-column justify-content-center align-items-center" id="main">
      <div class="text-center mb-5">
        <h4 class="text-dark">
            {{@config('installer.app_name')}} - {{@$title}}
        </h4>
      </div>
       @yield('content')
             
    </main>


    <script src="{{asset('assets/global/js/jquery.min.js')}}"></script>
    <script src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/global/js/toastify-js.js')}}"></script>
    <script src="{{asset('assets/global/js/helper.js')}}"></script>

    @include('partials.notify')
    @stack('script-include')
    @stack('script-push')

    <script>
       'use strict'

      $('.ai--btn').click(function(){
        var $html = '<span></span><span></span><span></span>';
        $(this).html($html);
      });

      $(document).on('click','.toggle-password',function(e){

            var parentAuthInput = $(this).closest('.auth-input');
            var passwordField = parentAuthInput.find('.toggle-input');
            var fieldType = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', fieldType);
            var toggleIcon = parentAuthInput.find('.toggle-icon');
            toggleIcon.toggleClass('bi-eye bi-eye-slash');
      });

 

       var activeItem = document.querySelector('li.active');
        if (activeItem) {
          var listItems = document.querySelectorAll('ul li');
          listItems.forEach(function(item, index) {
            if (item === activeItem) {
              for (var i = 0; i < index; i++) {
                listItems[i].classList.add('active');
              }
            }
          });
        }

    </script>
  </body>
