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
                           onclick="select_<?php echo $type ?>()" value='#'> 
                    &nbsp;<?php echo $title ?>
                </h4>                   
                <?php
                foreach ($items[$type] as $file) { 
                    ?>
                    <div    id="wrapper_<?php echo $file['ID'] ?>"  
                            class="col-md-3 item-default" 
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
                                      <img src="<?php echo get_item_thumbnail_default($file['ID']); ?>" class="img-responsive">
                                <?php }  ?> 
                            </div>     
                            <input required="required" 
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
            'pdf' => __('PDF Files','tainacan'),
            'audio' => __('Audio Files','tainacan'),
            'others' => __('Others Files','tainacan')
        ];
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
                    if ($("#parent_" + $(this).val()).val() === '') {
                        set_item_selected_colour($(this).val());
                        $(this).attr('checked', 'checked');
                    }
                });
                selectedItems();
            }
            /***************************** DESmarca todos os items *********************************************/
            function unselectAll() {
                $.each($("input:checkbox[name='selected_items']"), function () {
                    $(this).removeAttr('checked');
                    if ($("#parent_" + $(this).val()).val() === '') { // se nao for anexo
                        clean_item_selected_colour($(this).val());
                    }
                    selectedItems();
                });
            }
            /******************************** Esconde os itens selecionados *******************************************/
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
                        var allItems = $("#items_id").val().split(',').filter(Boolean);
                        var images = $("#items_images").val().split(',').filter(Boolean);
                        var videos = $("#items_video").val().split(',').filter(Boolean);
                        var audios = $("#items_audio").val().split(',').filter(Boolean);
                        var pdfs = $("#items_pdf").val().split(',').filter(Boolean);
                        var others = $("#items_other").val().split(',').filter(Boolean);
                        $.each($("input:checkbox[name='selected_items']:checked"), function () {
                            //retiro dos checados
                            $(this).removeAttr('checked');
                            //escondo seu wrapper
                            $("#wrapper_" + $(this).val()).hide();
                            //retiro o item do array de items geral
                            if (allItems.length > 0 && allItems.indexOf($(this).val()) >= 0) {
                                allItems.splice(allItems.indexOf($(this).val()), 1);
                                $("#items_id").val(allItems.join(','));
                                if (allItems.length == 0) {
                                    $("#selectOptions").attr('disabled', 'disabled');// retiro a opcao de retirar todos pois nao ha itens
                                    $("#no_item_uploaded").show();
                                }
                                selectedItems();
                            }
                            //retiro do array e de seu container de tipo
                            if (images.length > 0 && images.indexOf($(this).val()) >= 0) {
                                images.splice(images.indexOf($(this).val()), 1);
                                $("#items_images").val(images.join(','));
                                if (images.length == 0) {
                                    $("#container_images").hide();
                                }
                            } else if (videos.length > 0 && videos.indexOf($(this).val()) >= 0) {
                                videos.splice(videos.indexOf($(this).val()), 1);
                                $("#items_video").val(videos.join(','));
                                if (videos.length == 0) {
                                    $("#container_videos").hide();
                                }
                            } else if (audios.length > 0 && audios.indexOf($(this).val()) >= 0) {
                                audios.splice(audios.indexOf($(this).val()), 1);
                                $("#items_audio").val(audios.join(','));
                                if (audios.length == 0) {
                                    $("#container_audios").hide();
                                }
                            } else if (pdfs.length > 0 && pdfs.indexOf($(this).val()) >= 0) {
                                pdfs.splice(pdfs.indexOf($(this).val()), 1);
                                $("#items_pdf").val(pdfs.join(','));
                                if (pdfs.length == 0) {
                                    $("#container_pdfs").hide();
                                }
                            } else if (others.length > 0 && others.indexOf($(this).val()) >= 0) {
                                others.splice(others.indexOf($(this).val()), 1);
                                $("#items_other").val(others.join(','));
                                if (others.length == 0) {
                                    $("#container_others").hide();
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
                            selectedItems();
                        } else if (array.indexOf($(this).val()) >= 0 && !$(id).is(':checked')) {
                            $(this).removeAttr('checked');
                            clean_item_selected_colour($(this).val());
                            selectedItems();
                        }
                    });
                }
            }

            function select_image() {
                var images = $("#items_images").val().split(',').filter(Boolean);
                clear_selected(images, '#selectAllImages');
            }
            function selectVideo() {
                var videos = $("#items_video").val().split(',').filter(Boolean);
                clear_selected(videos, '#selectAllVideo');
            }
            function selectAudio() {
                var audios = $("#items_audio").val().split(',').filter(Boolean);
                clear_selected(audios, '#selectAllAudio');
            }
            function selectPdf() {
                var pdfs = $("#items_pdf").val().split(',').filter(Boolean);
                clear_selected(pdfs, '#selectAllPdf');
            }
            function selectOther() {
                var others = $("#items_other").val().split(',').filter(Boolean);
                clear_selected(others, '#selectAllOther');
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
                } else if (selected_items.length > 1) {
                    $("#buttonSelectedAttachments").hide();// mostra o botao de anexos
                    
                    if($("input[name='object_license']")&&$("input[name='object_license']:checked")){
                        $("input[name='object_license']:checked").removeAttr('checked');
                    }

                    $("#form_properties_items").show(); // mostra o formulario para edicao
                    $("#no_properties_items").hide(); // mostra a mensagem que solicita a selecao de itens
                    $("#labels_items_selected").show(); // mostra a div q tem as quantidades de itens selecionados
                    $("#number_of_items_selected").text(selected_items.length);// total de items selecionados
                    //limpo o formulario
                    $("#multiple_object_name").val(''); //limpo o campo do tiulo
                    $("#multiple_object_name").attr("placeholder", "<?php _e('Replace ', 'tainacan') ?>" + selected_items.length + " <?php _e(' titles', 'tainacan') ?>");
                    $("#multiple_object_description").attr("value", "");//limpo o campo de descricao
                    clear_fields_multiple();
                } else {
                    $("#buttonSelectedAttachments").hide();// mostra o botao de anexos
                    $('#list_ranking_items').hide();
                    $("#no_properties_items").show(); // mostra a mensagem que solicita a selecao de itens
                    $("#form_properties_items").hide(); // esconde o formulario para edicao
                    //limpo o formulario
                    $("#multiple_object_name").val('');
                    $("#multiple_object_description").attr("value", "");
                    $("#multiple_object_tags").val('');
                    //limpo VALORES das propriedades de objeto
                    var objectProperties = $("#multiple_properties_object_id").val().split(',').filter(Boolean);
                    for (var i = 0; i < objectProperties.length; i++) {
                        $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add option:selected").each(function () {
                            $(this).remove(); //or whatever else
                        });

                    }
                    //esconde anexos
                    clear_fields_multiple();
                }
            }
            // FIM: AO SELECIONAR ITEMS
            /**
            *  funcao que limpa todos os campos do menu esquerdo
            */
            function clear_fields_multiple(){
                 //pego as tags comuns aos itens selecionados
                 $("#multiple_object_tags").val('');
        //            var all_tags = [];
        //            var tags = [];
        //            $.each($("input:checkbox[name='selected_items']:checked"), function () {
        //                tags = $("#tags_" + $(this).val()).val().split(',');
        //                tags = tags.filter(function (v) {
        //                    return v !== ''
        //                });
        //                for (var i = 0; i < tags.length; i++) {
        //                    all_tags.push(tags[i]);
        //                }
        //            });
        //            if (all_tags.length > 0) {// se existir tags em qualquer um dos itens
        //                all_tags = remove_duplicates_safe(all_tags);
        //                $("#multiple_object_tags").val(all_tags.join(','));
        //            }
                    //BUSCANDO VALORES das **propriedades de objeto** para cada item selecionado
                    var objectProperties = $("#multiple_properties_object_id").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < objectProperties.length; i++) {
                        var allValues = [];
                        $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add option:selected").each(function () {
                            setPropertyObject($(this).val(), objectProperties[i], true);
                            $(this).remove(); //or whatever else
                        }); // limpo os campos primeiramente
                        $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
                            var value_property = $("#socialdb_property_" + objectProperties[i] + "_" + $(this).val()).val().split('||').filter(Boolean); // TORNA O VALOR DO hidden desta propriedade em array
                            for (var j = 0; j < value_property.length; j++) {//loop que varre todos os valores de cada objeto para uma unica propriedade
                                allValues.push(value_property[j]);//coloco os valores para esta propriedade em array unico
                            }
                        });
                        if (allValues.length > 0) {// se existir objects
                            allValues = remove_duplicates_safe(allValues);// retiro os duplicados
                            for (var j = 0; j < allValues.length; j++) {
                                $("#multiple_property_value_" + objectProperties[i] + "_<?php echo $object_id ?>_add").append("<option class='selected' value='" + allValues[j] + "' selected='selected' >" + allValues[j].split('_')[1] + "</option>");
                            }
                        }
                    }
                    //BUSCANDO VALORES das **propriedades de data** para cada item selecionado
                    var dataProperties = $("#multiple_properties_data_id").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < dataProperties.length; i++) {
                        $('.multiple_socialdb_property_' + dataProperties[i]).each(function(index,value){
                             $(this).val('');
                        });
                    }
                    //BUSCANDO VALORES das **propriedades de termos** para o item selecionado
                    //checkbox
                    var checkboxes = $("#multiple_properties_terms_checkbox").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < checkboxes.length; i++) {
                        $.each($("input:checkbox[name='multiple_socialdb_propertyterm_" + checkboxes[i] + "']:checked"), function () {
                            $(this).removeAttr('checked');
                        });
        //                $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
        //                    var categories = $("#socialdb_property_" + checkboxes[i] + "_" + $(this).val()).val().split(',').filter(function (v) {
        //                        return v !== ''
        //                    });
        //                    $.each($("input:checkbox[name='multiple_socialdb_propertyterm_" + checkboxes[i] + "']"), function () {
        //                        if (categories.length > 0 && categories.indexOf($(this).val()) >= 0) {
        //                            $(this).attr('checked', 'checked');
        //                        }
        //                    });
        //                });
        //                setCategoriesCheckbox(checkboxes[i], 'not');
                    }
                    //multipleSelect
                    var multipleSelects = $("#multiple_properties_terms_multipleselect").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < multipleSelects.length; i++) {
                        $("select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "']").val([]);
        //                $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
        //                    var categories = $("#socialdb_property_" + multipleSelects[i] + "_" + $(this).val()).val().split(',').filter(function (v) {
        //                        return v !== ''
        //                    });
        //                    $.each($("select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "'] option"), function () {
        //                        if (categories.length > 0 && categories.indexOf($(this).val()) >= 0) {
        //                            $(this).attr('selected', 'selected');
        //                        }
        //                    });
        //                });
        //                setCategoriesSelectMultiple(multipleSelects[i], "select[name='multiple_socialdb_propertyterm_" + multipleSelects[i] + "']");
                    }
                    //treecheckbox
                    var multiple_properties_terms_treecheckbox = $("#multiple_properties_terms_treecheckbox").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < multiple_properties_terms_treecheckbox.length; i++) {
                        $("#multiple_field_property_term_" + multiple_properties_terms_treecheckbox[i]).dynatree("getTree").reload();
                        //$("#multiple_field_property_term_"+multiple_properties_terms_treecheckbox[i]).dynatree("getRoot").visit(function(node){
                        //   node.select(false);
                        // });
        //                $.each($("input:checkbox[name='selected_items']:checked"), function () {  // varro todos os objetos     
        //                    var categories = $("#socialdb_property_" + multiple_properties_terms_treecheckbox[i] + "_" + $(this).val()).val().split(',').filter(function (v) {
        //                        return v !== ''
        //                    });
        //                    $("#multiple_field_property_term_" + multiple_properties_terms_treecheckbox[i]).dynatree("getRoot").visit(function (node) {
        //                        if (categories.length > 0 && categories.indexOf(node.data.key) >= 0) {
        //                            node.select(true);
        //                        }
        //                    });
        //                });
                    }
                    //radio
                    var radios = $("#multiple_properties_terms_radio").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < radios.length; i++) {
                        $.each($("input[name='multiple_socialdb_propertyterm_" + radios[i] + "']"), function () {
                            $(this).removeAttr('checked');
                        });
                    }
                    //selectbox
                    var select = $("#multiple_properties_terms_selectbox").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < select.length; i++) {
                        $.each($("select[name='multiple_socialdb_propertyterm_" + select[i] + "'] option"), function () {
                            $(this).removeAttr('selected');
                        });
                    }
                    //dynatree radio
                    var multiple_properties_terms_tree = $("#multiple_properties_terms_tree").val().split(',').filter(function (v) {
                        return v !== ''
                    });
                    for (var i = 0; i < multiple_properties_terms_tree.length; i++) {
                         $("#multiple_field_property_term_" + multiple_properties_terms_tree[i]).dynatree("getTree").reload();
        //                var categories = $("#socialdb_property_" + multiple_properties_terms_tree[i] + "_" + item_id).val().split(',').filter(function (v) {
        //                    return v !== ''
        //                });
        //                $("#multiple_field_property_term_" + multiple_properties_terms_tree[i]).dynatree("getRoot").visit(function (node) {
        //                    node.select(false);
        //                });
                    }
                    //esconde anexos
                    destroy_dropzone();
            }

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
        </script> 
        <?php
    }
}
