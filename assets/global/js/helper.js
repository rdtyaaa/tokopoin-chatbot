
    'use strict'
   //toaster functions
    function toaster(text,className){
        Toastify({
            newWindow: !0,
            text: text,
            gravity: 'top',
            position: 'right',
            className: "bg-" + className,
            stopOnFocus: !0,
            offset: { x:  0, y: 0 },
            duration:3000,
            close: "close" == "close",

        }).showToast();
    }

     //EMPTY INPUT FIELD 
     function emptyInputFiled(id, selector = 'id', html = true) {
         var identifier = selector === 'id' ? `#${id}` : `.${id}`;
        $(identifier)[html ? 'html' : 'val']('');
      }


    //delete event start
    $(document).on('click',".delete-item",function(e){
        e.preventDefault();
        var href = $(this).attr('data-href');
        $("#delete-modal").modal("show");
        $("#delete-href").attr("href", href);
    })

    //file upload preview
    $(document).on('change', '.preview', function (e) {
        emptyInputFiled('image-preview-section')
        var file = e.target.files[0];
        var size =  ($(this).attr('data-size')).split("x");

        imagePreview(file, 'image-preview-section',size);
        e.preventDefault();
    })


    //SINGLE IMAGE PREVIEW METHOD
    function  imagePreview (file, id,size) {
        $(`#${id}`).append(
            `<img alt='${file.type}' class="mt-2 rounded  d-block"
             style="width:${size[0]}px;height:${size[1]}px;"
            src='${URL.createObjectURL(file)}'>`
        );
    }





    



