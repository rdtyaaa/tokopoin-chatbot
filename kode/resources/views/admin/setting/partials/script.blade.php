<script>
    "use strict";

    
       
          $('.time-zone').select2({
                 placeholder: "{{translate('Select timezone')}}"     
          });
          $('.country').select2({
                      
          });
  
          function copyUrl(){
              var copyText = document.getElementById("cron_url");
              copyText.select();
              copyText.setSelectionRange(0, 99999)
              document.execCommand("copy");
              toaster('Copied the text : ' + copyText.value,'success')
          }
          function copyQueueUrl(){
              var copyText = document.getElementById("queue_url");
              copyText.select();
              copyText.setSelectionRange(0, 99999)
              document.execCommand("copy");
              toaster('Copied the text : ' + copyText.value,'success')
          }

            
        (function ($) {
          "use strict";
  
     
         
          $('.color_picker_show').spectrum({
              color: $(this).data('color'),
              change: function (color) {
                  $(this).parent().siblings('.color_code').val(color.toHexString().replace(/^#?/, ''));
              }
          });
  
          $('.color_code').on('input', function () {
              var clr = $(this).val();
              $(this).parents('.input-group').find('.color_picker_show').spectrum({
                  color: clr,
              });
          });
  
  
    
          $(document).on('change','#minimum_order_amount_check',function(e){
             
              var status =  $(this).val();
  
              if(status == "{{App\Enums\StatusEnum::true->status()}}"){
                  $('.minimum-order-amount-section').removeClass('d-none');
  
              }else{
                  $('.minimum-order-amount-section').addClass('d-none');
              }
          
          })
    
          $(document).on('change','#free_delivery',function(e){
             
              var status =  $(this).val();
  
              if(status == "{{App\Enums\StatusEnum::true->status()}}"){
                  $('.free-delivery-amount-section').removeClass('d-none');
  
              }else{
                  $('.free-delivery-amount-section').addClass('d-none');
              }
          
          })
  
          //reset primary color
          $(document).on('click','#reset-primary-color',function(e){
              var color = '{{site_settings("primary_color")}}'
              $('#primary_color').val(color);
              $(this).parents('.input-group').find('.color_picker_show').spectrum({
                  color: '{{site_settings("primary_color")}}',
              });
              e.preventDefault()
          })
  
          //reset secondary color
          $(document).on('click','#reset-secondary-color',function(e){
              var color = '{{site_settings("secondary_color")}}'
             $('#secondary_color').val(color);
              $(this).parents('.input-group').find('.color_picker_show').spectrum({
                  color: '{{site_settings("secondary_color")}}',
              });
              e.preventDefault()
          })
          //reset font color
          $(document).on('click','#reset-font-color',function(e){
              var color = '{{site_settings("font_color")}}'
             $('#font_color').val(color);
              $(this).parents('.input-group').find('.color_picker_show').spectrum({
                  color: '{{site_settings("font_color")}}',
              });
              e.preventDefault()
          })
  
          //seller mode status update
           $(document).on('click','#seller-mode',function(e){
              updateSellerMode()
           })
        
        
            //seller mode status update
           $(document).on('click','#debug-mode',function(e){
                  $.ajax({
                      method:'get',
                      data :$(this).attr('data-value'),
                      url:"{{ route('admin.debug.mode') }}",
                      dataType:'json',
                      success:function(response){
  
                          toaster(`Status Updated`,'success')
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
           })
  
           //update seller mode function
            function updateSellerMode()
            {
              $.ajax({
                  method:'get',
                  url:"{{ route('admin.seller.mode') }}",
                  dataType:'json',
                  success:function(response){
                      $('#seller-status').html('')
                      $('#seller-status').html(`${response.status}`)
                      toaster(`Seller Mode ${response.status}`,'success')
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




        // load wp templates
        $(document).on('click','.load-wp-template',function(e){

            e.preventDefault();

    
            var submitButton = $(this);
            $.ajax({
                method:'get',
                url: "{{route('admin.load.templates')}}",
                dataType: 'json',

                beforeSend: function() {
                        submitButton.find(".note-btn-spinner").remove();

                        submitButton.html(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
                                <span class="visually-hidden"></span>
                            </div>  {{translate("Load template")}}`);
                    },
                success: function(response){
                
                    if(response.status){

                        var options = `<option value=""> Select template  </option>`;
            
                        response.templates.map(function(template , index){
                            options     += `<option value="${template.name}"> ${template.name}  </option>`;
                        
                        })

                        $("#wp_template").html(options)

        
                    }else{
                        toaster( response.message,'danger')
                    }
        
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
                },
                complete: function() {
                    submitButton.html(`<i class="ri-refresh-line fs-16"></i>
                                            {{translate("Load template")}}`);

                    $('.preview-content-section').html(`<div class="col-12 text-center justify-content-center">
                        {{ translate("Select a template first")}}
                    </div>`);
                    $('.whatsapp-loader ').addClass("d-none");
                },
            })

        });



        // load wp templates
        $(document).on('change','#wp_template',function(e){

            e.preventDefault();
            var templateName = $(this).val();
    
            if(templateName){

                $.ajax({
                method:'post',
                url: "{{route('admin.get.template')}}",
                data :{
                    "_token": '{{ csrf_token() }}',
                    'template_name' : templateName
                },
                dataType: 'json',

                beforeSend: function() {
                    $('.preview-content-section').addClass("d-none");
                    $('.whatsapp-loader ').removeClass("d-none");
                },
                success: function(response){
                
                    if(response.status){

                       $('.wp-preview-section').html(response.preview_html)
        
                    }else{
                        toaster( response.message,'danger')
                    }
        
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
                },
                complete: function() {
                    $('.preview-content-section').removeClass("d-none");
                    $('.whatsapp-loader ').addClass("d-none");
                },
            })

            }


        });




          $(document).on('submit','.settingsForm',function(e){
  
              e.preventDefault();
              var data =   new FormData(this)
              var route = $(this).attr('data-route')
              var submitButton = $(e.originalEvent.submitter);
              $.ajax({
              method:'post',
              url: route,
              dataType: 'json',
              cache: false,
              processData: false,
              contentType: false,
              data: data,
              beforeSend: function() {
                      submitButton.find(".note-btn-spinner").remove();
  
                      submitButton.append(`<div class="ms-1 spinner-border spinner-border-sm text-white note-btn-spinner " role="status">
                              <span class="visually-hidden"></span>
                          </div>`);
                  },
              success: function(response){
                  var className = 'success';
                  if(!response.status){
                      className = 'danger';
                  }
                  toaster( response.message,className)

                  if(response.status && response.preview_html){
                    $('.wp-preview-section').html(response.preview_html)
                  }
                
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
              },
              complete: function() {
                  submitButton.find(".note-btn-spinner").remove();
              },
              })
  
          });
  
  
          $('#search').keyup(function(){
  
              var found = false;
  
              var value = $(this).val().toLowerCase();
              $('.setting-tab-list').each(function(){
                  var lcval = $(this).text().toLowerCase();
                  if(lcval != 'Promotional Offer'){
                      if(lcval.indexOf(value)>-1){
                          $('.section-list-wrapper').removeClass('is-open')
                          $(this).show();
                          found = true;
                      } else {
                          $(this).hide();
                      }
                  }
              });
  
              if (!found) {
                  $('.no-data-found').remove();
                  $('.section-list-wrapper').append(`<div class="mb-2 no-data-found py-4 text-center bg-white " id="v-pills-messages-tab" data-bs-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false" tabindex="-1">
                          <b class="fw-bold">
                               {{translate("Noting found")}}
                          </b>
                  </div>`);
              } else {
                  $('.no-data-found').remove();
              }
          });
  

   
   
  
        
  
      })(jQuery);
  </script>