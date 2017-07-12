<?php

class ItemsClass extends FormItemMultiple{
     public function listItems($items) { 
        ?>
        <?php 
        foreach ($this->setTypes() as $type => $title):
            if(isset($items[$type])):
            ?>
            <div  id="container_<?php echo $type ?>"  class='col-md-12' style="background-color: #c1d0dd; padding-right: 0px; padding-left: 15px;margin-top: 15px;padding-top: 5px;">
                <h4>
                    <input class="class_selected_items" 
                           type='checkbox' id='select-all-<?php echo $type ?>' 
                           onclick="select_type('<?php echo $type ?>','#select-all-<?php echo $type ?>')" value='#'> 
                    &nbsp;<?php echo $title ?>
                </h4>                   
                <?php
                foreach ($items[$type] as $file) { 
                    $this->addDraft($file['ID']);
                    ?>
                    <div    id="wrapper_<?php echo $file['ID'] ?>"  
                            class="col-md-3 item-default item-<?php echo $type ?>" 
                            item="<?php echo $file['ID'] ?>"
                            style="padding-top: 20px;cursor: pointer;">
                        <center>
                            <div class="item" 
                                 style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" 
                                 id="panel_<?php echo $file['ID'] ?>"  
                                 onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->   
                                <input style="display:none" 
                                       class="class_selected_items" 
                                       id="item_option_<?php echo $file['ID'] ?>" 
                                       onchange="selectedItems()" 
                                       type="checkbox" 
                                       name="selected_items"  
                                       value="<?php echo $file['ID'] ?>">
                                <input 
                                    id="attachment_option_<?php echo $file['ID'] ?>"  
                                    onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" 
                                    class="class_checkboxAttachments" 
                                    style="display:none"
                                    type="checkbox" 
                                    name="checkboxAttachments"  
                                    value="<?php echo $file['ID'] ?>">
                                <?php 
                                if(get_the_post_thumbnail($file['ID'], 'thumbnail')){
                                   echo get_the_post_thumbnail($file['ID'], 'thumbnail');
                                }else{ ?>
                                <img width="150" src="<?php echo get_item_thumbnail_default($file['ID']); ?>" class="img-responsive">
                                <?php }  ?> 
                            </div>     
                            <input 
                                   style="margin-top: 10px;" 
                                   placeholder="<?php _e('Add a title','tainacan') ?>" 
                                   type="text" 
                                   class='input_title'
                                   id='title_<?php echo $file['ID'] ?>' 
                                   name='title_<?php echo $file['ID'] ?>' 
                                   value='<?php echo $file['name'] ?>'>
                            <!-- Hidden para as categorias, tags e attachments  -->
                        </center>          
                    </div>    
                  <?php         
                }
                ?>
            </div>
            <?php
            endif;
        endforeach;
        ?>
        <!--------------- FIM: container todos os itens  ----------------------------->
        <div style="display: none" class="col-md-12" id='attachments_item_upload'>
             <h3><?php _e('Attachments','tainacan'); ?></h3>
             <div  id="dropzone_new" class="dropzone" style="min-height: 150px;">
             </div>
         </div>
        <?php
        $this->initScriptsLocal();
    }
    
    /**
     * 
     * @return type
     */
    public function setTypes() {
        return [
            'text' => __('Text/Url','tainacan'),
            'image' => __('Image Files','tainacan'),
            'video' => __('Videos Files','tainacan'),
            'videos' => __('Videos Files','tainacan'),
            'pdf' => __('PDF Files','tainacan'),
            'audio' => __('Audio Files','tainacan'),
            'others' => __('Others Files','tainacan')
        ];
    }
    
    /**
     * 
     */
    public function addDraft($id){
        $items = get_user_meta(get_current_user_id(), 'socialdb_collection_' . $this->collection_id . '_betafile');
        if(!$items || !in_array($id, $items)){
            add_user_meta(get_current_user_id(), 'socialdb_collection_' . $this->collection_id . '_betafile',$id);
        }
    }
    
    /**
     * 
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsLocal() {
        ?>
        <script>
            var myDropzone;
                $('.input_title').blur(function(){
                    $.ajax({
                        url: $('#src').val() + '/controllers/object/form_item_controller.php',
                        type: 'POST',
                        data: {
                            operation: 'saveTitle',
                            value: $(this).val().trim(),
                            item_id: $(this).parent().parent().attr('item'),
                            collection_id:$('#collection_id').val()
                        }
                    }).done(function (result) {
                    });
                });
            /******************************** Manipulação das cores ao selecionar itens setando se eh anexo o u nao******************************************/
            function focusItem(id) {
                if ($('#buttonBackItems').is(':visible')) { // eh pq esta selecionando anexos
                    var selected_id = getOnlyItemID();
                    if (selected_id != id && $('#attachment_option_' + id).is(':checked')) {
                        $('#attachment_option_' + id).removeAttr('checked');
                        $('#attachment_option_' + id).trigger('change');
                        clean_item_selected_colour(id);
                    } else if (selected_id != id) {
                        $('#attachment_option_' + id).attr('checked', 'checked');
                        $('#attachment_option_' + id).trigger('change');
                        set_attachment_selected_colour(id);
                    }
                }
                else {//se estiver selecionando itens
                    if ($('#item_option_' + id).is(':checked')) {
                        $('#item_option_' + id).removeAttr('checked');
                        $('#item_option_' + id).trigger('change');
                        clean_item_selected_colour(id);
                    } else {
                        $('#item_option_' + id).attr('checked', 'checked');
                        $('#item_option_' + id).trigger('change');
                        set_item_selected_colour(id);
                    }

                }
            }
            
            
            
            /***************************** Seleciona todos os items *********************************************/
            function selectAll() {
                $.each($("input:checkbox[name='selected_items']"), function () {
                    //if ($("#parent_" + $(this).val()).val() === '') {
                        set_item_selected_colour($(this).val());
                        $(this).attr('checked', 'checked');
                    //}
                });
                selectedItems();
            }
            /***************************** DESmarca todos os items *********************************************/
            function unselectAll() {
                $.each($("input:checkbox[name='selected_items']"), function () {
                    $(this).removeAttr('checked');
                    //if ($("#parent_" + $(this).val()).val() === '') { // se nao for anexo
                        clean_item_selected_colour($(this).val());
                    //}
                    selectedItems();
                });
            }
            /******************************** Esconde os itens selecionados *******************************************/
            function unpublish_item(id){
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {operation: 'unpublish_item', id: id}
                }).done(function (result) { 
                });
            }
            
            function removeSelected() {
                var size = $("input:checkbox[name='selected_items']:checked").length;
                swal({
                    title: '<?php _e('Attention', 'tainacan') ?>',
                    text: '<?php _e('Are you sure to remove ', 'tainacan') ?>' + size + ' <?php _e('item(s) selected?', 'tainacan') ?>',
                    type: "info",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-primary',
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function (isConfirm) {
                    if (isConfirm) {
                        var counter = 0;
                        var allItems = getAllIds();
                        var images = getAllvaluesByType('image');
                        var videos = getAllvaluesByType('video');
                        var audios = getAllvaluesByType('audio');
                        var pdfs = getAllvaluesByType('pdf');
                        var others = getAllvaluesByType('other');
                        var texts = getAllvaluesByType('text');
                        $.each($("input:checkbox[name='selected_items']:checked"), function () {
                            //retiro dos checados
                            $(this).removeAttr('checked');
                            unpublish_item($(this).val());
                            //escondo seu wrapper
                            $("#wrapper_" + $(this).val()).remove();
                            //retiro o item do array de items geral
                            if (allItems.length > 0 && allItems.indexOf($(this).val()) >= 0) {
                                if (allItems.length == 0) {
                                    $("#selectOptions").attr('disabled', 'disabled');// retiro a opcao de retirar todos pois nao ha itens
                                    $("#no_item_uploaded").show();
                                }
                                selectedItems();
                            }
                            //retiro do array e de seu container de tipo
                            if (images.length > 0 && images.indexOf($(this).val()) >= 0) {
                                images.splice(images.indexOf($(this).val()), 1);
                                if (images.length == 0) {
                                    $("#container_image").hide();
                                }
                            } else if (videos.length > 0 && videos.indexOf($(this).val()) >= 0) {
                                videos.splice(videos.indexOf($(this).val()), 1);
                                $("#items_video").val(videos.join(','));
                                if (videos.length == 0) {
                                    $("#container_video").hide();
                                }
                            } else if (audios.length > 0 && audios.indexOf($(this).val()) >= 0) {
                                audios.splice(audios.indexOf($(this).val()), 1);
                                $("#items_audio").val(audios.join(','));
                                if (audios.length == 0) {
                                    $("#container_audio").hide();
                                }
                            } else if (pdfs.length > 0 && pdfs.indexOf($(this).val()) >= 0) {
                                pdfs.splice(pdfs.indexOf($(this).val()), 1);
                                $("#items_pdf").val(pdfs.join(','));
                                if (pdfs.length == 0) {
                                    $("#container_pdf").hide();
                                }
                            } else if (others.length > 0 && others.indexOf($(this).val()) >= 0) {
                                others.splice(others.indexOf($(this).val()), 1);
                                $("#items_other").val(others.join(','));
                                if (others.length == 0) {
                                    $("#container_other").hide();
                                }
                            }else if (texts.length > 0 && texts.indexOf($(this).val()) >= 0) {
                                texts.splice(texts.indexOf($(this).val()), 1);
                                if (others.length == 0) {
                                    $("#container_text").hide();
                                }
                            }
                            counter++;
                        });
                        if (counter > 0) {
                            toastr.success(counter + '<?php _e(' items/item removed successfully!', 'tainacan') ?>', '<?php _e('Success', 'tainacan') ?>', set_toastr_class());
                        }
                    }
                });
            }
            /***************************** MARCA/DESMARCA items de um determinado tipo *********************************************/
            function clear_selected(array, id) {
                if (array.length > 0) {
                    $.each($("input:checkbox[name='selected_items']"), function () {
                        if (array.indexOf($(this).val()) >= 0 && $(id).is(':checked')) {
                            $(this).attr('checked', 'checked');
                            set_item_selected_colour($(this).val());
                        } else if (array.indexOf($(this).val()) >= 0 && !$(id).is(':checked')) {
                            $(this).removeAttr('checked');
                            clean_item_selected_colour($(this).val());
                        }
                    });
                    selectedItems();
                }
            }

            function select_type(type,seletor) {
                var array = [];
                $.each($('.item-'+type),function(index,value){
                    array.push($(value).attr('item'))
                });
                clear_selected(array, seletor);
            }
            /****/
            function getAllvaluesByType(type){
                var array = [];
                $.each($('.item-'+type),function(index,value){
                    array.push($(value).attr('item'))
                });
                return array;
            }
            //
            function getAllIds(){
                $("input:checkbox[name='selected_items']:checked")
                var array = [];
                $.each($("input:checkbox[name='selected_items']"),function(index,value){
                    array.push($(value).val())
                });
                return array;
            }
            
            /******************************* AO SELECIONAR OS ITEMS VIA CHECKBOX *********************/
            function selectedItems() {
                var selected_items = [];
                $.each($("input:checkbox[name='selected_items']:checked"), function () {
                    selected_items.push($(this).val());
                });
                if (selected_items.length == 1) {// quando selecionado um item seus valores vao para o form a esquerda
                    item_id = selected_items[0];
                    //$("#buttonSelectedAttachments").show();// mostra o botao de anexos
                    $("#form_properties_items").show(); // mostra o formulario para edicao
                    $("#no_properties_items").hide(); // mostra a mensagem que solicita a selecao de itens
                    $("#labels_items_selected").show(); // mostra a div q tem as quantidades de itens selecionados
                    $("#number_of_items_selected").text(selected_items.length);// total de items selecionados
                    //verifico as acoes que devem ser executadas
                    //hook para acionar as acoes de cada widget
                    $('#item-multiple-selected').val(item_id);
                    if(Hook.is_register( 'get_single_item_value')){
                        Hook.callMultiple( 'get_single_item_value', [ item_id ] );
                    }
                    //mostra os anexos
                    init_dropzone_attachments(item_id);
                } else if (selected_items.length > 1) {
                    $("#buttonSelectedAttachments").hide();// mostra o botao de anexos
                    $("#form_properties_items").show(); // mostra o formulario para edicao
                    $("#no_properties_items").hide(); // mostra a mensagem que solicita a selecao de itens
                    $("#labels_items_selected").show(); // mostra a div q tem as quantidades de itens selecionados
                    $("#number_of_items_selected").text(selected_items.length);// total de items selecionados
                    //limpo o formulario
                    $("#multiple_object_name").val(''); //limpo o campo do tiulo
                    $("#multiple_object_name").attr("placeholder", "<?php _e('Replace ', 'tainacan') ?>" + selected_items.length + " <?php _e(' titles', 'tainacan') ?>");
                    $("#multiple_object_description").attr("value", "");//limpo o campo de descricao
                    //hook para acionar as acoes de cada widget
                    $('#item-multiple-selected').val(selected_items.join(','));
                    if(Hook.is_register( 'get_multiple_item_value')){
                        Hook.callMultiple( 'get_multiple_item_value', selected_items );
                    }
                    //esconde anexos
                    destroy_dropzone();
                } else {
                    $("#buttonSelectedAttachments").hide();// mostra o botao de anexos
                    $('#list_ranking_items').hide();
                    $("#no_properties_items").show(); // mostra a mensagem que solicita a selecao de itens
                    $("#form_properties_items").hide(); // esconde o formulario para edicao
                    if(Hook.is_register( 'get_multiple_item_value')){
                        Hook.callMultiple( 'get_multiple_item_value', [] );
                    }
                    //esconde anexos
                    destroy_dropzone();
                }
            }
            // FIM: AO SELECIONAR ITEMS

            /*********************** FUNCOES DE SUPORTE ********************************/
            // funcao q retorna o id do item selecionado para edicao
            function getOnlyItemID() {
                var item_id;
                $.each($("input:checkbox[name='selected_items']:checked"), function () {
                    item_id = $(this).val();// pego o item aonde sera inserido os attachaments
                });
                return item_id;
            }
            // get value of the property
            function multiple_get_val(value) {
                if (value === '') {
                    return false;
                } else if (value.split(',')[0] === '' && value !== '') {
                    return [value];
                } else {
                    return value.split(',');
                }
            }
            // get value of the property
            function get_val(value) {
                if (value === ''||value===undefined) {
                    return false;
                } else if (value.split(',')[0] === '' && value !== '') {
                    return [value];
                } else {
                    return value.split(',');
                }
            }
            //removendo arrays com itens duplicados
            function remove_duplicates_safe(arr) {
                var obj = {};
                var arr2 = [];
                for (var i = 0; i < arr.length; i++) {
                    if (!(arr[i] in obj)) {
                        arr2.push(arr[i]);
                        obj[arr[i]] = true;
                    }
                }
                return arr2;

            }
            //seta cor selecionado item
            function set_item_selected_colour(id) {
               // $('#panel_' + id).css('background-color', '#c0c0c0');
                $('#wrapper_' + id).addClass('selected-border');
            }
            //seta cor selecionado item
            function set_attachment_selected_colour(id) {
                //$('#panel_' + id).css('background-color', '#c7cadd');
            }
            //limpa cor selecionado
            function clean_item_selected_colour(id) {
               // $('#panel_' + id).css('background-color', '#e8e8e8');
                $('#wrapper_' + id).removeClass('selected-border');
            }
            //esconde checkboxes dos grupos por tipo
            function hide_group_checkboxes() {
                $('#selectAllImages').hide();
                $('#selectAllVideo').hide();
                $('#selectAllPdf').hide();
                $('#selectAllAudio').hide();
                $('#selectAllOther').hide();
            }
            //mostra checkboxes dos grupos por tipo
            function show_group_checkboxes() {
                $('#selectAllImages').show();
                $('#selectAllVideo').show();
                $('#selectAllPdf').show();
                $('#selectAllAudio').show();
                $('#selectAllOther').show();
            }
            //setar classe do toaster
            function set_toastr_class() {
                return {positionClass: 'toast-bottom-right', preventDuplicates: true};
            }
            
            //anexos do item
            function init_dropzone_attachments(id){
                //dropzone para adicionar anexos
                destroy_dropzone();
                $('#dropzone_new').html('');
                $('#dropzone_new')
                        .append('<div class="dz-message" data-dz-message><span style="text-align: center;vertical-align: middle;line-height: 90px;"><h2><span class="glyphicon glyphicon-upload"></span><b><?php _e('Drop Files','tainacan')  ?></b> <?php _e('to upload','tainacan')  ?></h2><h4>(<?php _e('or click','tainacan')  ?>)</h4>')
                        ;
                $('#attachments_item_upload').show();
                 myDropzone = new Dropzone("div#dropzone_new", {
                        accept: function(file, done) {
                              if (file.type === ".exe") {
                                  done("Error! Files of this type are not accepted");
                              }
                              else { done(); }
                        },
                        init: function () {
                            thisDropzone = this;
                            this.on("removedfile", function (file) {
                                if(!noCleaning){
                                    //    if (!file.serverId) { return; } // The file hasn't been uploaded
                                    $.get($('#src').val() + '/controllers/object/object_controller.php?operation=delete_file&object_id=' + id + '&file_name=' + file.name, function (data) {
                                        if (data.trim() === 'false') {
                                            showAlertGeneral('<?php _e("Atention!", 'tainacan') ?>', '<?php _e("An error ocurred, File already removed or corrupted!", 'tainacan') ?>', 'error');
                                        } else {
                                            showAlertGeneral('<?php _e("Success", 'tainacan') ?>', '<?php _e("File removed!", 'tainacan') ?>', 'success');
                                        }
                                    }); // Send the file id along
                                }
                            });
                            $.get($('#src').val() + '/controllers/object/object_controller.php?operation=list_files&object_id=' + id, function (data) {
                                try {
                                    //var jsonObject = JSON.parse(data);
                                    $.each(data, function (key, value) {
                                        if (value.name !== undefined && value.name !== 0) {
                                            var mockFile = {name: value.name, size: value.size};
                                            thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                        }
                                    });
                                }
                                catch (e)
                                {
                                    // handle error 
                                }
                            });
                        },                
                        url: $('#src').val() + '/controllers/object/object_controller.php?operation=save_file&object_id=' + id,
                        addRemoveLinks: true

                    });

            }
            
            //destroy dropzone
            function destroy_dropzone(){
                $('#attachments_item_upload').hide();
                if(myDropzone){
                   noCleaning = true;
                   myDropzone.destroy();
                   noCleaning = false;
                }
            }
        </script> 
        <?php
    }
}
