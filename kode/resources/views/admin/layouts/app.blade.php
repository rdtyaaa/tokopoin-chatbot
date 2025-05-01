<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" data-layout-default="vertical" data-sidebar-size="lg" data-topbar="light" data-sidebar="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{csrf_token()}}" />
    <title>{{site_settings('site_name')}} - {{translate($title)}}</title>
    <link rel="shortcut icon" href="{{show_image('assets/images/backend/logoIcon/'.site_settings("site_favicon"),file_path()['favicon']['size'])}}" type="image/x-icon">
    <link href="{{asset('assets/global/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/global/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/global/css/simplebar.min.css')}}" rel="stylesheet" type="text/css" />
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
        @include('admin.partials.topbar')
        @include('admin.partials.sidebar')
        <div class="main-container">
            @yield('main_content')

            @if(!request()->routeIs('admin.inhouse.order.print'))
                @include('admin.partials.footer')
            @endif

            @include('admin.partials.ai_modal')
        </div>
    </div>
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>

    <div class="loader-wrapper update-loader d-none">

        <div class="loader">

          <div class="mask"></div>
          <div class="mask2"></div>

        </div>
        <div class="warning-text">
            {{translate("Do not close window while proecessing")}}
             <span class="dots-container  ms-2">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
             </span>
        </div>
    </div>

    <script src="{{asset('assets/global/js/jquery.min.js')}}"></script>
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





        var aiTexarea = '';
        var textEditor = '';

        $(".ai-lang").select2({
			placeholder:"{{translate('Select Country')}}",
			dropdownParent: $("#aiModal"),
		})

        flatpickr("#datePicker", {
            dateFormat: "Y-m-d",
            mode: "range",
        });


        $(".chanage_currency").on("click", function() {
            var currency = $(this).attr('data-value')
            window.location.href = "{{route('home')}}/currency/change/"+currency;
        });



        $(document).on('click','.option-btn',function(e){


            var key = $(this).attr('name');
            var value = $(this).attr('value');
            var modal =  $('#aiModal');
            modal.find('.ai-content-option').attr('name',key)
            modal.find('.ai-content-option').val(value)


        });

        $(document).on('click','.insert-result',function(e){

            var modal  =  $('#aiModal');
            var result = modal.find('.ai-result').val()
            if(textEditor != ''){
                textEditor.summernote('code',result);
            }else{
                aiTexarea.val(result)
            }



        });


        $(document).on('change','.ai-lang',function(e){

            if(!$(this).val() =='' || !$(this).val() ==' '){
                $('#AiForm').submit()

            }
            e.preventDefault();
        });



        $(document).on('submit','#AiForm',function(e){

            var formData = $(this).serialize();
            var modal =  $('#aiModal');
            $.ajax({
                url: "{{route('ai.content')}}",
                type: "post",
                data:formData,
                dataType:'json',
                beforeSend: function() {

                    modal.find('.ai-content-generate').addClass("d-none")
                    modal.find('.insert-result').addClass("d-none")
                    modal.find('.ai-modal-footer').addClass("d-none")
                    modal.find('.ai-content-loader').removeClass("d-none")

                },
                success:(function (response) {

                    if(response.status){
                        modal.find('.ai-result').val(response.message)
                        modal.find('.result-section').removeClass("d-none")
                        modal.find('.ai-modal-footer').removeClass("d-none")
                        modal.find('.insert-result').removeClass("d-none")
                    }
                    else{
                        modal.find('.ai-content-generate').removeClass("d-none")
                        toaster(response.message,'danger')
                    }

                }),
                error:(function (error) {
                    modal.find('.ai-content-generate').removeClass("d-none")
                    if(error && error.responseJSON){
                        if(error.responseJSON.message){
                            toaster(error.responseJSON.message,'danger')
                        }
                        else{
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }

                    }
                    else{
                        toaster("{{translate('This Function is Not Avaialbe For Website Demo Mode')}}",'danger')
                    }
                }),
                complete: function() {

                    modal.find('.ai-content-loader').addClass("d-none")

                },
            })

            e.preventDefault()
        })



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


        // Summer Note modal
        $(document).on("click", ".close", function (e) {
            $(this).closest(".modal").modal("hide");
        });



        // update status event start
        $(document).on('click', '.status-update', function (e) {
            const id = $(this).attr('data-id')
            var column = ($(this).attr('data-column'))
            var route = ($(this).attr('data-route'))
            var modelName = ($(this).attr('data-model'))
            var status = ($(this).attr('data-status'))
            const data = {
                'id': id,
                'model': modelName,
                'column': column,
                'status': status,

            }
            updateStatus(route, data)
        })

        // update status method
        function updateStatus(route, data) {
            var responseStatus;
            $.ajax({
                method: 'POST',

                url: route,
                data: {
                    data,
                    "_token" :"{{csrf_token()}}",
                },
                dataType: 'json',
                success: function (response) {

                    response = response[0];
                    if(response){
                        responseStatus = response.status? "success" :"danger"
                        toaster(response.message,responseStatus)
                            location.reload();
                    }

                    location.reload();
                },
                error: function (error) {

                    if(error && error.responseJSON){
                        if(error.responseJSON.errors){
                            for (let i in error.responseJSON.errors) {
                                toaster(error.responseJSON.errors[i][0],'danger')
                            }
                        }
                        else{
                            toaster( error.responseJSON.error,'danger')
                        }
                    }
                    else{
                        toaster(error.message,'danger')
                    }

                }
            })
        }

        @if( $openAi->status == 1)

            $('textarea.form-control').on('input', function() {

                if(!$(this).hasClass("ai-prompt-input")){
                    var words = $(this).val().trim().split(/\s+/).length;

                    if ($(this).next('.ai-generator-btn').length === 0) {
                        if (words >= 2) {
                            $(this).after(`
                                        <button type="button" class="ai-generator-btn mt-3 ai-modal-btn" >
                                            <span class="ai-icon btn-success waves ripple-light">
                                                    <span class="spinner-border d-none" aria-hidden="true"></span>

                                                    <i class="ri-robot-line"></i>
                                            </span>

                                            <span class="ai-text">
                                                {{translate('Generate With AI')}}
                                            </span>
                                        </button>
                        `);
                        }
                    } else {
                        if (words < 2) {
                            $(this).next('.ai-generator-btn').remove();
                        }
                    }
                }
            });

        @endif


        function removeTags(str) {
            if ((str === null) || (str === ''))
                return false;
            else
                str = str.toString();
                str = str.replace(/^[\s\n]+/, '');
                str = str.replace(/(<([^>]+)>|&nbsp;|)/ig, '');

                return str.trim();
        }


        $(document).on("input", '.custom-prompt-option', function (e) {
            var modal = $('#aiModal');
            var oldPrompt = modal.find('.custom-prompt').attr('data-value');
            var prompt = modal.find('.custom-prompt').val();
            var inputText = $(this).val().trim();

            if (prompt && inputText) {
                var lines = prompt.split('\n');
                var basePrompt = lines[0];
                var updatedPrompt = basePrompt + '\n' + inputText;
                modal.find('.custom-prompt').val(updatedPrompt);
            } else if (inputText) {
                modal.find('.custom-prompt').val(inputText);
            }
        });



        $('.search-menu-input').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase(); 
            if (searchTerm) {
                $('#navbar-nav li.nav-item').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1);
                });
            } else {
                $('#navbar-nav li.nav-item').show();
            }

            $('#navbar-nav .menu-title').each(function() {
                var $menuTitle = $(this);
                var hasVisibleItems = $menuTitle.nextUntil('.menu-title', 'li.nav-item:visible').length > 0;
                
                $menuTitle.toggle(hasVisibleItems);
            });
        });
   




        $(document).on("click",'.ai-modal-btn',function(e){

            var modal =  $('#aiModal');
            modal.find('.custom-prompt-option').val('')
            modal.find('.custom-prompt').val('')

            modal.find(".translate-section").addClass('d-none');
            modal.find(".ai-options").addClass('d-none');
            modal.find(".default-section").removeClass('d-none');

            aiTexarea  =   $(this).prev('textarea');
            textEditor = '';

            var textareaValue =  $(this).prev('textarea').val()

            if($(this).closest('.text-editor-area').length > 0){
                textEditor =  $(this).closest('.text-editor-area').find('.text-editor');
                var textareaValue = $(this).closest('.text-editor-area').find('.text-editor').summernote('code');
                textareaValue =  removeTags(textareaValue);
            }

            if(textareaValue && textareaValue != "" &&  textareaValue != " "){
                modal.find('.custom-prompt').val((textareaValue))
                modal.find('.custom-prompt').attr('data-value',(textareaValue))
            }

            modal.find('.insert-result').addClass("d-none")
            modal.find('.ai-content-generate').removeClass("d-none")
            modal.find('.ai-modal-footer').removeClass("d-none")
            modal.find('.ai-content-loader').addClass("d-none")
            modal.find('.result-section').addClass("d-none")
            modal.find(".ai-lang").select2({
                placeholder:"{{translate('Select Language')}}",
                dropdownParent: $("#aiModal"),
	     	})

             $('#language').val('').trigger('change');

            modal.modal('show');


        })


        const aiMdoal =document.querySelector("#aiModal")
        if (aiMdoal) {
            const moreOption        = aiMdoal.querySelector("#more-option");
            const translateOption   = aiMdoal.querySelector("#translate-option");
            const aiOptions         = aiMdoal.querySelector(".ai-options");
            const translateSection  = aiMdoal.querySelector(".translate-section");
            const btnClose          = aiMdoal.querySelector(".btn-close");

            moreOption.addEventListener("click",()=>{
                moreOption.parentElement.classList.add("d-none");
                aiOptions.classList.remove("d-none")
            })
            translateOption.addEventListener("click",()=>{
                translateOption.parentElement.classList.add("d-none");
                translateSection.classList.remove("d-none")
            })

            btnClose.addEventListener("click",()=>{
                moreOption.parentElement.classList.remove("d-none");
                aiOptions.classList.add("d-none")
                translateOption.parentElement.classList.remove("d-none");
                translateSection.classList.add("d-none")
            })

            const optionCloser = document.querySelectorAll(".ai-option-closer");
            optionCloser.forEach((e) => {
                e.addEventListener("click",()=>{
                        moreOption.parentElement.classList.remove("d-none");
                        aiOptions.classList.add("d-none")
                        translateOption.parentElement.classList.remove("d-none");
                        translateSection.classList.add("d-none")
               })
            })
        }




        $(document).on('click','.copy-content',function(e){

            var modal =  $('#aiModal');

            var textarea =  modal.find('.ai-result');
            textarea.select();
            document.execCommand('copy');
            window.getSelection().removeAllRanges();
            toaster("{{translate('Text copied to clipboard!')}}", 'success');

        });

        $(document).on('click','.download-text',function(e){
            var modal =  $('#aiModal');

            var content =  modal.find('.ai-result').val();

            var blob = new Blob([content], { type: 'text/html' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'downloaded_content.html';

            document.body.appendChild(link);
            link.click();

            document.body.removeChild(link);

        });


        //file upload preview
    $(document).on('change', '.img-preview', function (e) {
        var file = e.target.files[0];
        var size = ($(this).attr('data-size')).split("x");
        $(this).closest('div').find('.image-preview-section').html(
            `<img alt='${file.type}' class="mt-2 rounded  d-block"
                style="width:${size[0]}px;height:${size[1]}px;"
                src='${URL.createObjectURL(file)}'>`
        );
        e.preventDefault();
    })

    </script>

</body>
</html>
