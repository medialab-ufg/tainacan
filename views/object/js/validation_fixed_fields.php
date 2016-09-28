<script>
    $(function () {
        set_title_valid();
    });
    //validando o titulo
    function set_title_valid(){
        var val =  $('#object_name').val();
        if($('#core_validation_title').length>0){
            if(val!==''){
                $('#core_validation_title').val('true');
                validate_all_fields();
                set_field_valid('title', 'core_validation_title');
            }
            
            $('#object_name').keyup(function(){
                if($(this).val()==''){
                    $('#core_validation_title').val('false');
                    set_field_valid('title', 'core_validation_title');
                    validate_all_fields();
                }else{
                    $('#core_validation_title').val('true');
                    set_field_valid('title', 'core_validation_title');
                    validate_all_fields(); 
                }
            });
        }
    }
    //conteudo
    function set_content_valid(){
        var editor  = CKEDITOR.instances.object_editor;
        var val = editor.getData();
        if($('#core_validation_content').length>0){
            if(val!==''){
                $('#core_validation_content').val('true');
                validate_all_fields();
                set_field_valid('content', 'core_validation_content');
            }
            editor.on('key', function() {
               var result = editor.getData();
               if(result==''){
                    $('#core_validation_content').val('false');
                    set_field_valid('content', 'core_validation_content');
                    validate_all_fields();
                }else{
                    $('#core_validation_content').val('true');
                    set_field_valid('content', 'core_validation_content');
                    validate_all_fields(); 
                }
            });
        }
    }
    
    function set_attachments_valid(){
         
         myDropzone.options.filedrop = {
            init: function () {
              this.on("complete", function (file) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                  console.log(myDropzone.getAcceptedFiles().length);
                }
              });
            }
          };
        //if($('#core_validation_attachments').length>0){
          //  console.log(myDropzone.getAcceptedFiles().length);
        //}
    }
</script>
