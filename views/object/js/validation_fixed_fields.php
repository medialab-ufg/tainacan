<script>
    $(function () {
        set_title_valid();
        set_description_valid();
        set_source_valid();
        set_thumbnail_valid();
        set_tags_valid();
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
    //validacao de anexos
    function set_attachments_valid(count){
        if($('#core_validation_attachments').length>0){
            if(count>0){
                $('#core_validation_attachments').val('true');
                validate_all_fields();
                set_field_valid('attachments', 'core_validation_attachments');
            }
            
            if(count==0){
                 $('#core_validation_attachments').val('false');
                 set_field_valid('attachments', 'core_validation_attachments');
                 validate_all_fields();
             }else{
                 $('#core_validation_attachments').val('true');
                 set_field_valid('attachments', 'core_validation_attachments');
                 validate_all_fields(); 
             }
        }
    }
    //descricao
    function set_description_valid(){
        var val =  $('#object_description_example').val();
        if($('#core_validation_description').length>0){
            if(val!==''){
                $('#core_validation_description').val('true');
                validate_all_fields();
                set_field_valid('description', 'core_validation_description');
            }
            
            $('#object_description_example').keyup(function(){
                if($(this).val()==''){
                    $('#core_validation_description').val('false');
                    set_field_valid('description', 'core_validation_description');
                    validate_all_fields();
                }else{
                    $('#core_validation_description').val('true');
                    set_field_valid('description', 'core_validation_description');
                    validate_all_fields(); 
                }
            });
        }
    }
    //fonte
    function set_source_valid(){
        var val =  $('#object_source').val();
        if($('#core_validation_source').length>0){
            if(val!==''){
                $('#core_validation_source').val('true');
                validate_all_fields();
                set_field_valid('source', 'core_validation_source');
            }
            
            $('#object_source').keyup(function(){
                if($(this).val()==''){
                    $('#core_validation_source').val('false');
                    set_field_valid('source', 'core_validation_source');
                    validate_all_fields();
                }else{
                    $('#core_validation_source').val('true');
                    set_field_valid('source', 'core_validation_source');
                    validate_all_fields(); 
                }
            });
        }
    }
    //tags
    function set_tags_valid(){
        var val =  $('#object_tags').val();
        if($('#core_validation_tags').length>0){
            if(val!==''){
                $('#core_validation_tags').val('true');
                validate_all_fields();
                set_field_valid('tags', 'core_validation_tags');
            }
            
            $('#object_tags').keyup(function(){
                if($(this).val()==''){
                    $('#core_validation_tags').val('false');
                    set_field_valid('tags', 'core_validation_tags');
                    validate_all_fields();
                }else{
                    $('#core_validation_tags').val('true');
                    set_field_valid('tags', 'core_validation_tags');
                    validate_all_fields(); 
                }
            });
        }
    }
    //miniatura
    function set_thumbnail_valid(){
        var val =  $('#object_thumbnail').val();
        if($('#core_validation_thumbnail').length>0){
            if(val!==''){
                $('#core_validation_thumbnail').val('true');
                validate_all_fields();
                set_field_valid('thumbnail', 'core_validation_thumbnail');
            }
            $('#object_thumbnail').change(function(){
                if($(this).val()==''){
                    $('#core_validation_thumbnail').val('false');
                    set_field_valid('thumbnail', 'core_validation_thumbnail');
                    validate_all_fields();
                }else{
                    $('#core_validation_thumbnail').val('true');
                    set_field_valid('thumbnail', 'core_validation_thumbnail');
                    validate_all_fields(); 
                }
            });
        }
    }
</script>
