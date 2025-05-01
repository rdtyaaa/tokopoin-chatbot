

$(document).ready(function() {
    'use strict'

    $('.text-editor').summernote({
        height: 300,
        placeholder: 'Start typing...',
        dialogsInBody: true,
        toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough' ]],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['fullscreen'],
        ['insert', ['picture', 'link', 'video']],
        ],
        callbacks: {
            onInit: function() {
                
            },

        }
    });

    $(".note-image-input").removeAttr('name');
  });