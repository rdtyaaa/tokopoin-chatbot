
  function handleFileUpload(files) {
    var preview = $(".file-list");
    $(files).each(function (i, file) {
        var reader = new FileReader();
        uploadedFiles.push(file);

        reader.onload = function (e) {
          preview.append(
            `<li>
            <span class="remove-list" data-name="${file.name}">
              <i class="bi bi-x-circle"></i>
            </span>
            <img src="${e.target.result}" alt="${file.name}" />
          </li>`
          );
        };
        reader.readAsDataURL(file);
      });
  }

  var uploadedFiles = [];
  var fileInput ;
  $(document).on('change','.upload-filed input',function(e){


    fileInput = this;
    uploadedFiles = Array.from(uploadedFiles);
    handleFileUpload(fileInput.files);

    uploadedFiles = createFileList(uploadedFiles);
    fileInput.files = uploadedFiles;
  });

  $(document).on('click','.remove-list',function(e){

      var fileName = $(this).data("name");
      $(this).parent().remove();

      var selectedFiles = Array.from(uploadedFiles);

      selectedFiles = selectedFiles.filter(function (file) {
        return file.name != fileName;
      });

      var newFileList = new DataTransfer();
      selectedFiles.forEach(function (file) {
        newFileList.items.add(file);
      });

      uploadedFiles = newFileList.files;
      fileInput.files = newFileList.files;
    
  })