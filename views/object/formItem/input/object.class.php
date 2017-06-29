<?php
include_once(dirname(__FILE__) . '/../../../../helpers/view_helper.php');
include_once(dirname(__FILE__) . '/../../../../helpers/advanced_search/advanced_search_helper.php');

class ObjectClass extends FormItem {

    public $compound_id;
    public $property_id;
    public $index_id;
    public $item_id;

    public function generate($compound, $property, $item_id, $index_id) {
        $this->compound_id = $compound['id'];
        $this->property_id = $property['id'];
        $this->index_id = $index_id;
        $this->item_id = $item_id;
        $compound_id = $compound['id'];
        $property_id = $property['id'];
        if ($property_id == 0) {
            $property = $compound;
        }
        $hasDefaultValue = (isset($property['metas']['socialdb_property_default_value']) && $property['metas']['socialdb_property_default_value']!='') ? $property['metas']['socialdb_property_default_value'] : false;
        $values = ($this->value && is_array($this->getValues($this->value[$index_id][$property_id]))) ? $this->getValues($this->value[$index_id][$property_id]) : false;
        $values = (!$values && $hasDefaultValue) ? [$hasDefaultValue] : $values;
        $autoValidate = ($values && !empty($values)) ? true : false;
        $this->isRequired = ($property['metas'] && $property['metas']['socialdb_property_required'] && $property['metas']['socialdb_property_required'] != 'false') ? true : false;
        $isMultiple = ($property['metas']['socialdb_property_object_cardinality'] == 'n') ? true : false;
        $isView = $this->viewValue($property,$values,'term');
        if($isView){
            return true;
        }
        $isReverse = ($property['metas'] && $property['metas']['socialdb_property_object_reverse'] && is_numeric($property['metas']['socialdb_property_object_reverse'])) ? $property['metas']['socialdb_property_object_reverse']: 'false';
        
        ?>
        <input type="hidden" id="cardinality_<?php echo $compound_id; ?>_<?php echo $property_id; ?>_<?php echo $index_id; ?>" value="<?php echo ($isMultiple) ? 'true' : 'false'  ?>">
        <input type="hidden" id="reverse_<?php echo $compound_id; ?>_<?php echo $property_id; ?>_<?php echo $index_id; ?>" value="<?php echo $isReverse  ?>">
        <input type="hidden" id="required_<?php echo $compound_id; ?>_<?php echo $property_id; ?>_<?php echo $index_id; ?>" value="<?php echo (string)$this->isRequired  ?>">
        <?php if($this->isRequired): ?>
        <div class="form-group" 
             id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
             style="border-bottom:none;">
                <span style="display: none;" class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
                <span style="display: none;" class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
                <span id="input2Status" class="sr-only">(status)</span>
                <input type="hidden" 
                       <?php if($property_id !== 0): ?>
                       compound="<?php echo $compound['id'] ?>"
                       <?php endif; ?>
                       property="<?php echo $property['id'] ?>"
                       class="validate-class validate-compound-<?php echo $compound['id'] ?>"
                       value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
        </div>        
        <?php elseif($property_id !== 0): ?> 
        <input  type="hidden" 
                compound="<?php echo $compound['id'] ?>"
                property="<?php echo $property['id'] ?>"
                id="validation-<?php echo $compound['id'] ?>-<?php echo $property_id ?>-<?php echo $index_id; ?>"
                class="compound-one-field-should-be-filled-<?php echo $compound['id'] ?>"
                value="<?php echo ($autoValidate) ? 'true' : 'false' ?>">
        <?php endif;   ?>
        <div class="metadata-related">
            <h6><b><?php _e('Related items', 'tainacan') ?></b></h6>
            <?php //$this->insert_button_add_other_collection($property, $object_id, $collection_id) ?>
            <span id="no_results_property_<?php echo $compound_id; ?>_<?php echo $property_id; ?>_<?php echo $index_id; ?>">
                <?php if (!$autoValidate): // verifico se ele esta na lista de objetos da colecao    ?>    
                <input type="text" 
                       disabled="disabled"
                       placeholder="<?php _e('No registers', 'tainacan') ?>"
                       class="form-control" >
                <?php endif;  ?>
            </span>
            <span id="results_property_<?php echo $compound_id; ?>_<?php echo $property_id; ?>_<?php echo $index_id; ?>">
                <ul>
                    <?php if ($values && !empty($values)): // verifico se ele esta na lista de objetos da colecao   ?>    
                        <?php
                        //$property['metas']['value'] = array_unique($property['metas']['value']);
                        //$id = $property['metas']['value'][$i];
                        foreach ($values as $id): 
                        ?>
                        <li id="inserted_property_object_<?php echo $compound_id ?>_<?php echo $property_id ?>_<?php echo $index_id ?>_<?php echo $id; ?>" 
                            onclick="original_remove_in_item_value_compound_<?php echo $compound_id ?>_<?php echo $property_id; ?>_<?php echo $index_id; ?>('<?php echo $id; ?>',this)"
                            item="<?php echo $id; ?>" class="selected-items-property-object property-<?php echo $property['id']; ?>">
                                <?php echo get_post($id)->post_title; ?>
                            <span style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span>
                        </li>       
                        <?php endforeach;  ?>    
                    <?php endif; ?>
                </ul>
            </span>
            <!--button class="btn  btn-lg btn-primary btn-primary pull-right js-show-metadata-search-<?php echo $compound_id; ?>-<?php echo $property_id; ?>-<?php echo $index_id; ?>"
                    type="button"><?php _e('Add', 'tainacan') ?></button-->
        </div>
        <div class="metadata-search"
             id="metadata-search-<?php echo $compound_id; ?>-<?php echo $property_id; ?>-<?php echo $index_id; ?>"
             >
                 <?php $this->search_related_properties_to_search($property); ?>     
        </div>
        <div class="metadata-matching"
             style="display:none"
             id="metadata-result-<?php echo $compound_id; ?>-<?php echo $property_id; ?>-<?php echo $index_id; ?>" >
        </div>   
        <?php
        $this->initScriptsObjectClass($compound_id, $property_id, $item_id, $index_id,$isMultiple);
        if($hasDefaultValue): ?>
            <script>
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type:'object',
                        <?php if($property_id!==0) echo 'indexCoumpound:0,' ?>
                        value: '<?php echo $hasDefaultValue ?>',
                        item_id:'<?php echo $item_id ?>',
                        compound_id:'<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $property_id ?>',
                        index: <?php echo $index_id ?>,
                        reverse: $('#reverse_<?php echo $compound_id ?>_<?php echo $property_id; ?>_<?php echo $index_id; ?>').val()
                    }
                });
            </script>
        <?php endif;
    }

    /**
     * scripts
     * @param type $property
     * @param type $item_id
     * @param type $index
     */
    public function initScriptsObjectClass($compound_id, $propert_id, $item_id, $index_id,$isMultiple) {
        ?>
        <script>
            //mostrar o campo de pesquisa
            $('.js-show-metadata-search-<?php echo $compound_id; ?>-<?php echo $propert_id; ?>-<?php echo $index_id; ?>').click(function () {
                $('#metadata-search-<?php echo $compound_id; ?>-<?php echo $propert_id; ?>-<?php echo $index_id; ?>').show();
                $('#metadata-result-<?php echo $compound_id; ?>-<?php echo $propert_id; ?>-<?php echo $index_id; ?>').hide();
                $(this).hide()
            });
            /******************************************************************************/
            $(function () {
                var src = $('#src').val();
                search_list_properties_term_insert_objects();
                var search_properties_autocomplete = search_get_val($("#search_properties_autocomplete").val());
                autocomplete_object_property_add(search_properties_autocomplete);
                
                //OLHAR ARQUIVO listItemFormItem.php pois faz a listagem com os itens encontrados
                $('#property_object_search_submit_<?php echo $compound_id ?>_<?php echo $propert_id ?>_<?php echo $index_id ?>').submit(function (e) {
                    e.preventDefault();
                    show_modal_main();
                    $.ajax({
                        url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
                        type: 'POST',
                        data: new FormData(this),
                        processData: false,
                        contentType: false
                    }).done(function (result) {
                        elem = jQuery.parseJSON(result);
                        hide_modal_main();
                        if (elem.not_found) {
                            swal({
                                title: '<?php _e("Attention!", 'tainacan') ?>',
                                text: '<?php _e("No results found!", 'tainacan') ?>',
                                type: "warning",
                                cancelButtonText: '<?php _e("Cancel", 'tainacan') ?>',
                                showCancelButton: true,
                                confirmButtonClass: 'btn-success',
                                closeOnConfirm: true,
                                closeOnCancel: true
                            });
                        } else {
                            $('#metadata-result-<?php echo $compound_id; ?>-<?php echo $propert_id; ?>-<?php echo $index_id; ?>').show();
                            $('#metadata-search-<?php echo $compound_id; ?>-<?php echo $propert_id; ?>-<?php echo $index_id; ?>').hide();
                            $('#metadata-result-<?php echo $compound_id; ?>-<?php echo $propert_id; ?>-<?php echo $index_id; ?>').html(elem.page);
                        }
                    });
                    e.preventDefault();
                });
                //# - inicializa os tooltips
                $(".advanced_search_title_<?php echo $compound_id ?>_<?php echo $propert_id ?>_<?php echo $index_id ?>").autocomplete({
                    source: $('#src').val() + '/controllers/object/object_controller.php?operation=get_objects_by_property_json&verify_selected=true&property_id=' + <?php echo $propert_id ?>,
                    messages: {
                        noResults: '',
                        results: function () {
                        }
                    },
                    minLength: 2,
                    select: function (event, ui) {
                        event.preventDefault();
                        $("#advanced_search_title_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>").val('');
                        if ($('#avoid_selected_items_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>').val() === 'true' && ui.item.is_selected && ui.item.is_selected === true) {
                            toastr.error(ui.item.label + ' <?php _e(' is already inserted!', 'tainacan') ?>', '<?php _e('Attention!', 'tainacan') ?>', {positionClass: 'toast-bottom-right'});
                            return false;
                        }
                        console.log('<?php echo $property['metas']['socialdb_property_avoid_items'] ?>', $('#inserted_property_object_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_' + ui.item.value).length);
                        if ($('#avoid_selected_items_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>').val() === 'false') {
                            console.log($('#inserted_property_object_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_' + ui.item.value));
                            if ($('#inserted_property_object_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_' + ui.item.value).length === 0) {
                                <?php if(!$isMultiple): ?>
                                    $('#results_property_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> ul').html('');
                                <?php endif; ?>
                                $('#no_results_property_<?php echo $compound_id ?>_<?php echo $propert_id ?>_<?php echo $index_id ?>').hide();
                                $('#results_property_<?php echo $compound_id ?>_<?php echo $propert_id ?>_<?php echo $index_id ?> ul')
                                        .append('<li id="inserted_property_object_<?php echo $compound_id ?>_<?php echo $propert_id ?>_<?php echo $index_id ?>_' + ui.item.value + '" item="' + ui.item.value + '" class="selected-items-property-object property-<?php echo $propert_id; ?>">' + ui.item.label
                                                + '<span  onclick="original_remove_in_item_value_compound_<?php echo $compound_id ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>('+ui.item.value+',this)" style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span></li>');
                                //validacao do campo
                                original_add_in_item_value_compound_<?php echo $compound_id ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>(ui.item.value);
                            }
                        } else {
                            $("#advanced_search_title_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>").val(ui.item.label);
                        }
                    }
                });

                $('[data-toggle="tooltip"]').tooltip();
            });

            function clear_all_field(form) {
                $(form + ' input[type=text]').val('');
                $(form + ' select option[value=""]').prop('checked', true);
            }

            function autocomplete_object_property_add(search_properties_autocomplete) {
                if (search_properties_autocomplete) {
                    $.each(search_properties_autocomplete, function (idx, property_id) {
                        $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #autocomplete_value_" + property_id).autocomplete({
                            source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                            messages: {
                                noResults: '',
                                results: function () {
                                }
                            },
                            minLength: 2,
                            select: function (event, ui) {
                                console.log(event);
                                $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #autocomplete_value_" + property_id).val('');
                                //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                                var temp = $("#property_value_" + property_id).val();
                                if (typeof temp == "undefined") {
                                    //$("#property_object_search_submit_<?php echo $propert_id ?> #autocomplete_value_" + property_id).val(ui.item.value);
                                }
                                if ($('#inserted_property_object_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_' + ui.item.value).length == 0) {
                                    var object_id = ($('#object_id_add').length > 0) ? $('#object_id_add').val() : $('#object_id_edit').val();
                                    if ($('#cardinality_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_' + object_id).val() == '1') {
                                        $('#results_property_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> ul').html('');
                                    }
                                    $('#results_property_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> ul')
                                            .append('<li id="inserted_property_object<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_' + ui.item.value + '" item="' + ui.item.value + '" class="selected-items-property-object property-<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>">' + ui.item.label
                                                    + '<span  onclick="remove_item_objet(this)" style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span></li>');
                                    //validacao do campo
                                    original_add_in_item_value_compound_<?php echo $compound_id ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>(ui.item.value);
                                }
                            }
                        });
                    });
                }
            }

            function search_autocomplete_object_property_add(property_id, object_id) {
                console.log($("#property_object_search_submit_" + property_id + " #autocomplete_value_" + property_id + "_" + object_id));
                $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #autocomplete_value_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_" + object_id).autocomplete({
                    source: $('#src').val() + '/controllers/collection/collection_controller.php?operation=list_items_search_autocomplete&property_id=' + property_id,
                    messages: {
                        noResults: '',
                        results: function () {
                        }
                    },
                    minLength: 2,
                    select: function (event, ui) {
                        console.log(event);
                        $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #autocomplete_value_" + property_id).val('');
                        //var temp = $("#chosen-selected2 [value='" + ui.item.value + "']").val();
                        var temp = $("#property_value_" + property_id).val();
                        if (typeof temp == "undefined") {
                            $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #autocomplete_value_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>").val(ui.item.value);
                        }
                    }
                });
            }

            function clear_select_object_property(e) {
                $('option:selected', e).remove();
                //$('.chosen-selected2 option').prop('selected', 'selected');
            }

            //remove no formulario de fato
            function original_remove_in_item_value_compound_<?php echo $compound_id ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>(id,seletor){
                $(seletor).parent().remove();
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'removeValue',
                        type:'object',
                        <?php if($property_id!==0) echo 'indexCoumpound:0,' ?>
                        value: id,
                        item_id:'<?php echo $item_id ?>',
                        compound_id:'<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $propert_id ?>',
                        index: <?php echo $index_id ?>,
                        reverse: $('#reverse_<?php echo $compound_id ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>').val()
                    }
                });
                if($('#results_property_<?php echo $compound_id; ?>_<?php echo $propert_id?>_<?php echo $index_id; ?> ul li').length==0){
                     validateFieldsMetadataText('','<?php echo $compound_id ?>','<?php echo $propert_id ?>','<?php echo $index_id ?>');
                     $('#no_results_property_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>').show();
                }
            }
            //adiciona no formulario de fato
            function original_add_in_item_value_compound_<?php echo $compound_id ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>(id){
                $.ajax({
                    url: $('#src').val() + '/controllers/object/form_item_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'saveValue',
                        type:'object',
                        <?php if($propert_id!==0) echo 'indexCoumpound:0,' ?>
                        value: id,
                        item_id:'<?php echo $item_id ?>',
                        compound_id:'<?php echo $compound_id ?>',
                        property_children_id: '<?php echo $propert_id ?>',
                        index: <?php echo $index_id ?>,
                        reverse: $('#reverse_<?php echo $compound_id ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>').val()
                    }
                });
                console.log(id,'<?php echo $compound_id ?>','<?php echo $propert_id ?>','<?php echo $index_id ?>');
                validateFieldsMetadataText(id,'<?php echo $compound_id ?>','<?php echo $propert_id ?>','<?php echo $index_id ?>')
            }

            //************************* properties terms ******************************************//
            function search_list_properties_term_insert_objects() {
                var radios = search_get_val($("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_properties_terms_radio").val());
                var selectboxes = search_get_val($("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_properties_terms_selectbox").val());
                var trees = search_get_val($("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_properties_terms_tree").val());
                var checkboxes = search_get_val($("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_properties_terms_checkbox").val());
                var multipleSelects = search_get_val($("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_properties_terms_multipleselect").val());
                var treecheckboxes = search_get_val($("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_properties_terms_treecheckbox").val());
                search_list_radios(radios);
                search_list_tree(trees);
                search_list_selectboxes(selectboxes);
                search_list_multipleselectboxes(multipleSelects);
                search_list_checkboxes(checkboxes);
                search_list_treecheckboxes(treecheckboxes);
            }
            // radios
            function search_list_radios(radios) {
                if (radios) {
                    $.each(radios, function (idx, radio) {
                        $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: radio}
                        }).done(function (result) {
                            elem = jQuery.parseJSON(result);
                            $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + radio).html('');
                            $.each(elem.children, function (idx, children) {
                                var required = '';
                                if (elem.metas.socialdb_property_required === 'true') {
                                    required = 'required="required"';
                                }
                                //  if (property.id == selected) {
                                //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                                //  } else {
                                var name = "'socialdb_propertyterm_" + radio + "'";
                                $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + radio)
                                        .append('<input ' + required + ' onchange="onRadioChecked(' + name + ',' + radio + ')" type="radio" name="socialdb_propertyterm_' + radio + '" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                                //  }
                            });
                        });
                    });
                }
            }
            // checkboxes
            function search_list_checkboxes(checkboxes) {
                if (checkboxes) {
                    $.each(checkboxes, function (idx, checkbox) {
                        $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: checkbox}
                        }).done(function (result) {
                            elem = jQuery.parseJSON(result);
                            $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + checkbox).html('');
                            var name = "'socialdb_propertyterm_" + checkbox + "[]'";
                            $.each(elem.children, function (idx, children) {
                                $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + checkbox).append('<input onchange="onCheckboxValue(' + name + ',' + checkbox + ')" type="checkbox" name="socialdb_propertyterm_' + checkbox + '[]" value="' + children.term_id + '">&nbsp;' + children.name + '<br>');
                            });
                            var required = '';
                            if (elem.metas.socialdb_property_required === 'true') {
                                required = 'required';
                            }
                            //$('#search_field_property_term_' + checkbox).append('<input type="hidden" name="checkbox_required_'+checkbox+'" value="'+required+'" >');
                        });
                    });
                }
            }

            // selectboxes
            function search_list_selectboxes(selectboxes) {
                if (selectboxes) {
                    $.each(selectboxes, function (idx, selectbox) {
                        $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: selectbox}
                        }).done(function (result) {
                            elem = jQuery.parseJSON(result);
                            $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + selectbox).html('');
                            $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + selectbox).append('<option value=""><?php _e('Select...', 'tainacan') ?></option>');
                            $.each(elem.children, function (idx, children) {
                                //  if (property.id == selected) {
                                //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                                //  } else {
                                $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + selectbox).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                                //  }
                            });
                        });
                    });
                }
            }

            // multiple
            function search_list_multipleselectboxes(multipleSelects) {
                if (multipleSelects) {
                    $.each(multipleSelects, function (idx, multipleSelect) {
                        $.ajax({
                            url: $('#src').val() + '/controllers/property/property_controller.php',
                            type: 'POST',
                            data: {collection_id: $("#collection_id").val(), operation: 'get_children_property_terms', property_id: multipleSelect}
                        }).done(function (result) {
                            elem = jQuery.parseJSON(result);
                            $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #field_property_term_' + multipleSelect).html('');
                            $.each(elem.children, function (idx, children) {
                                //  if (property.id == selected) {
                                //     $('#property_object_reverse').append('<option selected="selected" value="' + property.id + '">' + property.name + ' - (' + property.type + ')</option>');
                                //  } else {
                                $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_' + multipleSelect).append('<option value="' + children.term_id + '">' + children.name + '</option>');
                                //  }
                            });
                        });
                    });
                }
            }

            // treecheckboxes
            function search_list_treecheckboxes(treecheckboxes) {
                if (treecheckboxes) {
                    $.each(treecheckboxes, function (idx, treecheckbox) {
                        $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_" + treecheckbox).dynatree({
                            selectionVisible: true, // Make sure, selected nodes are visible (expanded).  
                            checkbox: true,
                            initAjax: {
                                url: $('#src').val() + '/controllers/category/category_controller.php',
                                data: {
                                    collection_id: $("#collection_id").val(),
                                    property_id: treecheckbox,
                                    operation: 'initDynatreeDynamic'
                                }
                                , addActiveKey: true
                            },
                            onLazyRead: function (node) {
                                node.appendAjax({
                                    url: $('#src').val() + '/controllers/collection/collection_controller.php',
                                    data: {
                                        collection: $("#collection_id").val(),
                                        key: node.data.key,
                                        classCss: node.data.addClass,
                                        //operation: 'findDynatreeChild'
                                        operation: 'expand_dynatree'
                                    }
                                });
                            },
                            onClick: function (node, event) {
                                // Close menu on click
                                $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #property_object_category_id").val(node.data.key);
                                $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #property_object_category_name").val(node.data.title);

                            },
                            onKeydown: function (node, event) {
                            },
                            onCreate: function (node, span) {
                            },
                            onPostInit: function (isReloading, isError) {
                            },
                            onActivate: function (node, event) {
                            },
                            onSelect: function (flag, node) {
                                var cont = 0;
                                var selKeys = $.map(node.tree.getSelectedNodes(), function (node) {
                                    return node;
                                });
                                var selKeysValue = $.map(node.tree.getSelectedNodes(), function (node) {
                                    return node.data.key;
                                });
                                $("#socialdb_propertyterm_" + treecheckbox).html('');
                                $.each(selKeys, function (index, key) {
                                    cont++;
                                    $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #socialdb_propertyterm_" + treecheckbox).append('<input type="hidden" name="socialdb_propertyterm_' + treecheckbox + '[]" value="' + key.data.key + '" >');
                                });
                                if (selKeysValue.length > 0) {
                                    append_category_properties_adv(selKeysValue.join(','), treecheckbox);
                                }
                            },
                            dnd: {
                            }
                        });
                    });
                }
            }

            // tree
            function search_list_tree(trees) {
                if (trees) {
                    console.log(trees);
                    $.each(trees, function (idx, tree) {
                        $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_field_property_term_" + tree).dynatree({
                            checkbox: true,
                            // Override class name for checkbox icon:
                            classNames: {checkbox: "dynatree-radio"},
                            selectMode: 1,
                            selectionVisible: true, // Make sure, selected nodes are visible (expanded). 
                            initAjax: {
                                url: $('#src').val() + '/controllers/category/category_controller.php',
                                data: {
                                    collection_id: $("#collection_id").val(),
                                    property_id: tree,
                                    // hide_checkbox: 'true',
                                    operation: 'initDynatreeDynamic'
                                }
                                , addActiveKey: true
                            },
                            onLazyRead: function (node) {
                                node.appendAjax({
                                    url: $('#src').val() + '/controllers/collection/collection_controller.php',
                                    data: {
                                        collection: $("#collection_id").val(),
                                        key: node.data.key,
                                        hide_checkbox: 'true',
                                        classCss: node.data.addClass,
                                        //operation: 'findDynatreeChild'
                                        operation: 'expand_dynatree'
                                    }
                                });
                            },
                            onClick: function (node, event) {
                                // Close menu on click
                                var key = node.data.key;
                                if (key.search('moreoptions') < 0 && key.search('alphabet') < 0) {
                                    $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_socialdb_propertyterm_" + tree).html('');
                                    $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #search_socialdb_propertyterm_" + tree).append('<option selected="selected" value="' + node.data.key + '" >' + node.data.title + '</option>')
                                }
                            },
                            onKeydown: function (node, event) {
                            },
                            onCreate: function (node, span) {
                            },
                            onPostInit: function (isReloading, isError) {
                            },
                            onActivate: function (node, event) {
                            },
                            onSelect: function (flag, node) {
                                if ($("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #socialdb_propertyterm_" + tree).val() === node.data.key) {
                                    $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #socialdb_propertyterm_" + tree).val("");
                                    $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #core_validation_' + tree).val('false');
                                } else {
                                    $("#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #socialdb_propertyterm_" + tree).val(node.data.key);
                                    append_category_properties_adv(node.data.key, tree);
                                    $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #core_validation_' + tree).val('true');
                                }
                            },
                            dnd: {
                            }
                        });
                    });
                }
            }

            // get value of the property
            function search_get_val(value) {
                if (!value || value === '') {
                    return false;
                } else if (value.split(',')[0] === '' && value !== '') {
                    return [value];
                } else {
                    return value.split(',');
                }
            }
            /******************************* Category Properties***************************/
            function append_category_properties_adv(id, property_id) {
                //busco os metadados da categoria selecionada    
                if (id !== '') {
                    //adicionando metadados
                    $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #append_properties_categories_' + property_id + '_adv')
                            .html('<center><img width="100" heigth="100" src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><?php _e('Loading metadata for this field', 'tainacan') ?></center>');
                    $.ajax({
                        url: $('#src').val() + '/controllers/advanced_search/advanced_search_controller.php',
                        type: 'POST',
                        data: {operation: 'get_categories_properties', properties_to_avoid: $('#properties_id_avoid').val(), categories: id, property_searched_id: property_id}
                    }).done(function (result) {
                        console.log('carregando metadados da propriedade', property_id);
                        hide_modal_main();
                        //list_all_objects(selKeys.join(", "), $("#collection_id").val());
                        $('#property_object_search_submit_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?> #append_properties_categories_' + property_id + '_adv').html(result);
                    });
                }
            }

            function onRadioChecked(name, property_id) {
                append_category_properties_adv($('input[name=' + name + ']:checked').val(), property_id);
            }

            function onSelectValue(seletor, property_id) {
                append_category_properties_adv($(seletor).val(), property_id);
            }

            function onCheckboxValue(name, property_id) {
                var values = [];
                $.each($('input[name=' + name + ']:checked'), function (index, value) {
                    values.push($(value).val());
                });
                append_category_properties_adv(values.join(','), property_id);
            }
        </script> 
        <?php
    }

    /**
     * metodo que retorna o html
     * 
     * @param type $property
     */
    public function search_related_properties_to_search($property) {
        $property_data = [];
        $property_object = [];
        $property_term = [];
        $property_compounds = [];
        $properties = $property['metas']["socialdb_property_to_search_in"];
        if (isset($properties) && $properties != '') {
            $properties = explode(',', $properties);
            foreach ($properties as $property_related) {
                $property_related = $this->get_all_property($property_related, true);
                if ($property_related['id'] == $this->terms_fixed['title']->term_id):
                    $has_title = true;
                elseif (isset($property_related['metas']['socialdb_property_data_widget'])):
                    $property_data[] = $property_related;
                elseif (isset($property_related['metas']['socialdb_property_object_category_id'])):
                    $property_object[] = $property_related;
                elseif (isset($property_related['metas']['socialdb_property_term_widget'])):
                    $property_term[] = $property_related;
                elseif (isset($property_related['metas']['socialdb_property_compounds_properties_id'])):
                    $all_values = [];
                    $values = explode(',', $property_related['metas']['socialdb_property_compounds_properties_id']);
                    foreach ($values as $value) {
                        $all_values[] = $this->get_all_property($value, true);
                    }
                    $property_related['metas']['socialdb_property_compounds_properties_id'] = $all_values;
                    $property_compounds[] = $property_related;
                endif;
            }
        }
        //include dirname(__FILE__) . '/../../views/advanced_search/search_property_object_metadata.php';
        $this->generateFormSearch($property, $has_title, $property_data, $property_object, $property_term, $property_compounds, $all_values);
    }

    /**
     * 
     * @param type $property
     * @param type $has_title
     * @param type $property_data
     * @param type $property_object
     * @param type $property_term
     * @param type $property_compounds
     * @param type $all_values
     */
    public function generateFormSearch($property, $has_title, $property_data, $property_object, $property_term, $property_compounds, $all_values) {
        ?>
        <form ></form>
        <form id="property_object_search_submit_<?php echo $this->compound_id ?>_<?php echo $this->property_id ?>_<?php echo $this->index_id ?>" >
            <input type="hidden" name="property_id" value="<?php echo $this->property_id ?>">
            <input type="hidden" name="collection_id" value="0">
            <input type="hidden" name="compound_id" value="<?php echo $this->compound_id ?>">
            <input type="hidden" name="contador" value="<?php echo $this->index_id ?>">
            <input type="hidden" name="item_id" value="<?php echo $this->item_id ?>">
            <!------------------------------------------------------------------------------>
            <input type="hidden" name="avoid_selected_items" id="avoid_selected_items_<?php echo $this->compound_id ?>_<?php echo $this->property_id ?>_<?php echo $this->index_id ?>" value="<?php echo (isset($property['metas']['socialdb_property_avoid_items']) && $property['metas']['socialdb_property_avoid_items'] == 'true') ? 'true' : 'false' ?>">
            <input type="hidden" name="categories" value="<?php echo (is_array($property['metas']['socialdb_property_object_category_id'])) ? implode(",", $property['metas']['socialdb_property_object_category_id']) : $property['metas']['socialdb_property_object_category_id'] ?>">
            <input type="hidden" name="properties_id" value="<?php echo (is_array($properties)) ? implode(',', $properties) : '' ?>">
            <?php
            $advanced_search_helper = new AdvancedSearchHelper();
            $properties_terms_radio = [];
            $properties_terms_tree = [];
            $properties_terms_selectbox = [];
            $properties_terms_checkbox = [];
            $properties_terms_multipleselect = [];
            $properties_terms_treecheckbox = [];
            $properties_autocomplete = [];
            ?>
            <?php if ((empty($property_data) && empty($property_term) && empty($property_object) && empty($property_compounds)) || $has_title):
                ?>    
                <div class="row col-md-12">
                    <label class="col-md-12 no-padding" for="advanced_search_title">
                        <?php echo $this->get_labels_search_obejcts($property['metas']['socialdb_property_object_category_id']); ?>
                    </label>
                    <div class="col-md-8 no-padding">
                        <input type="text" 
                               name="advanced_search_title" 
                               class="form-control <?php if (isset($this->compound_id)): ?> advanced_search_title_<?php echo $this->compound_id ?>_<?php echo $property['id'] ?>_<?php echo $this->index_id ?><?php endif; ?>"
                               id="advanced_search_title_<?php echo $property['id'] ?>"
                               placeholder="<?php _e('Type the 3 first letters to activate autocomplete', 'tainacan'); ?>">
                    </div>
                </div>
            <?php endif; ?>           
            <?php if (isset($property_data)): ?>
                <?php
                foreach ($property_data as $property) {
                    $properties_autocomplete[] = $property['id'];
                    ?>
                    <div class="row col-md-12">   
                        <label class="col-md-12 no-padding" for="advanced_search_tags">
                            <?php echo $property['name']; ?>
                            <?php if ($property['metas']['socialdb_property_help'] && !empty(trim($property['metas']['socialdb_property_help']))) {
                                ?>
                                <a  
                                    style="margin-right: 20px;" >
                                    <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                                          data-toggle="tooltip" 
                                          data-placement="bottom" 
                                          class="glyphicon glyphicon-question-sign"></span>
                                </a>
                            <?php } ?>
                        </label> 
                        <?php if ($property['type'] == 'text') { ?>   
                            <div class="col-md-8 no-padding">
                                <input type="text" 
                                       class="form-control" 
                                       id="autocomplete_value_<?php echo $property['id']; ?>" 
                                       name="socialdb_property_<?php echo $property['id']; ?>" 
                                       placeholder="">
                            </div> 
                            <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals', 'tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals', 'tainacan'); ?></option>
                                    <option selected="selected" value="3"><?php _e('Contains', 'tainacan'); ?></option>
                                    <option value="4"><?php _e('Does not Contain', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <?php
                        } elseif ($property['type'] == 'textarea') {
                            $properties_autocomplete[] = $property['id'];
                            ?>   
                            <div class="col-md-8 no-padding">
                                <input type="text" class="form-control" id="autocomplete_value_<?php echo $property['id']; ?>" name="socialdb_property_<?php echo $property['id']; ?>"  placeholder="<?php echo $property['name']; ?>"></textarea>
                            </div> 
                            <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals', 'tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals', 'tainacan'); ?></option>
                                    <option selected="selected"  value="3"><?php _e('Contains', 'tainacan'); ?></option>
                                    <option value="4"><?php _e('Does not Contain', 'tainacan'); ?></option>
                                </select>
                            </div> 
                        <?php } elseif ($property['type'] == 'numeric') { ?> 
                            <div class="col-md-8 no-padding">
                                <input class="form-control"  placeholder="<?php echo $property['name']; ?>" type="numeric"  name="socialdb_property_<?php echo $property['id']; ?>" >
                            </div>     
                            <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals', 'tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals', 'tainacan'); ?></option>
                                    <option selected="selected"  value="3"><?php _e('Higher', 'tainacan'); ?></option>
                                    <option value="4"><?php _e('Lower', 'tainacan'); ?></option>
                                </select>
                            </div>
                            <?php
                        } elseif ($property['type'] == 'date') {
                            $properties_autocomplete[] = $property['id'];
                            ?> 
                            <div class="col-md-8 no-padding">
                                <input class="form-control input_date" id="autocomplete_value_<?php echo $property['id']; ?>"  placeholder="<?php echo $property['name']; ?>" type="text"  name="socialdb_property_<?php echo $property['id']; ?>" >
                            </div>     
                            <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals', 'tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals', 'tainacan'); ?></option>
                                    <option value="3"><?php _e('After', 'tainacan'); ?></option>
                                    <option value="4"><?php _e('Before', 'tainacan'); ?></option>
                                </select>
                            </div>
                        <?php } ?> 
                    </div>
                <?php } ?>
                <?php
            endif;


            if ((isset($property_term) && count($property_term) > 0)):
                ?>
                <?php foreach ($property_term as $property) { ?>
                    <div class="row col-md-12" >
                        <label class="col-md-12 no-padding" >
                            <?php echo $property['name']; ?>
                            <?php if ($property['metas']['socialdb_property_help'] && !empty(trim($property['metas']['socialdb_property_help']))) {
                                ?>
                                <a 
                                    style="margin-right: 20px;" >
                                    <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                                          data-toggle="tooltip" 
                                          data-placement="bottom" 
                                          class="glyphicon glyphicon-question-sign"></span>
                                </a>
                            <?php } ?>
                        </label> 
                        <div class="col-md-8 no-padding">
                            <?php
                            if ($property['type'] == 'radio') {
                                $properties_terms_radio[] = $property['id'];
                                ?>
                                <div id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                                <?php
                            } elseif ($property['type'] == 'tree') {
                                $properties_terms_tree[] = $property['id'];
                                ?>
                                <div class="row">
                                    <div  style='height: 150px;padding-left: 15px;'   id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                                    <input type="hidden" 
                                           id='socialdb_propertyterm_<?php echo $property['id']; ?>'
                                           name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                                           value="">
                                </div>
                                <?php
                            } elseif ($property['type'] == 'selectbox') {
                                $properties_terms_selectbox[] = $property['id'];
                                ?>
                                <select class="form-control"
                                        onchange="onSelectValue(this,<?php echo $property['id']; ?>)"
                                        name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                                        id='search_field_property_term_<?php echo $property['id']; ?>' <?php ?>></select>
                                        <?php
                                    } elseif ($property['type'] == 'checkbox') {
                                        $properties_terms_checkbox[] = $property['id'];
                                        ?>
                                <div id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                                <?php
                            } elseif ($property['type'] == 'multipleselect') {
                                $properties_terms_multipleselect[] = $property['id'];
                                ?>
                                <select   onchange="onSelectValue(this,<?php echo $property['id']; ?>)" multiple class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>" id='search_field_property_term_<?php echo $property['id']; ?>' ></select>
                                <?php
                            } elseif ($property['type'] == 'tree_checkbox') {
                                $properties_terms_treecheckbox[] = $property['id'];
                                ?>
                                <div class="row">
                                    <div style='height: 150px;'  id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                                    <div id='socialdb_propertyterm_<?php echo $property['id']; ?>' ></div>
                                </div>
                                <?php
                            }
                            ?> 
                        </div>              
                        <div class="col-md-4 no-padding padding-left-space">
                            <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                <option  selected="selected"  value="in"><?php _e('Contains', 'tainacan'); ?></option>
                                <option value="not_in"><?php _e('Does not Contain', 'tainacan'); ?></option>
                            </select>
                        </div>  
                        <div class="col-md-12" id="append_properties_categories_<?php echo $property['id']; ?>_adv"></div> 
                    </div> 
                <?php } ?>
            <?php endif;
            ?>

            <?php if (isset($property_object)):
                ?>
                <?php foreach ($property_object as $property) { ?>
                    <?php //if($property['metas']['socialdb_property_object_is_facet']=='false'):  ?>
                    <div class="form-group col-md-12 no-padding">
                        <label class="col-md-12 no-padding" for="object_tags">
                            <?php echo $property['name']; ?>
                            <?php if ($property['metas']['socialdb_property_help'] && !empty(trim($property['metas']['socialdb_property_help']))) {
                                ?>
                                <a  
                                    style="margin-right: 20px;" >
                                    <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                                          data-toggle="tooltip" 
                                          data-placement="bottom" 
                                          class="glyphicon glyphicon-question-sign"></span>
                                </a>
                            <?php } ?>
                        </label>
                        <div class="col-md-8 no-padding">
                            <input type="text" onkeyup="autocomplete_object_property_add('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" id="autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" placeholder="<?php _e('Type the three first letters of the item of this collection ', 'tainacan'); ?>"  class="chosen-selected form-control"  />  
                            <select onclick="clear_select_object_property(this);" id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_add" multiple class="chosen-selected2 form-control" style="height: auto;" name="socialdb_property_<?php echo $property['id']; ?>[]"
                                    >
                                        <?php
                                        if (!empty($property['metas']['objects'])) {
                                            
                                        } else {
                                            ?>   
                                    <option value=""><?php _e('No objects added in this collection', 'tainacan'); ?></option>
                                <?php } ?>       
                            </select>
                        </div>
                        <div class="col-md-4 no-padding padding-left-space">
                            <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                <option  selected="selected"  value="in"><?php _e('Contains', 'tainacan'); ?></option>
                                <option value="not_in"><?php _e('Does not Contain', 'tainacan'); ?></option>
                            </select>
                        </div>   
                    </div>                
                <?php } ?>
            <?php endif; ?>
            <?php if (isset($rankings)): ?>
                <?php foreach ($rankings as $property) { ?>
                    <div class="form-group col-md-12 no-padding">
                        <label class="col-md-12 no-padding" for="object_tags">
                            <?php echo $property['name']; ?>
                            <?php if ($property['metas']['socialdb_property_help'] && !empty(trim($property['metas']['socialdb_property_help']))) {
                                ?>
                                <a  
                                    style="margin-right: 20px;" >
                                    <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                                          data-toggle="tooltip" 
                                          data-placement="bottom" 
                                          class="glyphicon glyphicon-question-sign"></span>
                                </a>
                            <?php } ?>
                        </label>
                        <div class="col-md-8 no-padding">
                            <?php if (in_array($property['type'], ['like', 'binary'])): ?>
                                <input style="width: 30%" 
                                       size="7" 
                                       type="number"  
                                       value="" 
                                       id="facet_<?php echo $facet['id']; ?>_1" 
                                       name="socialdb_property_<?php echo $property['id']; ?>_1"> 
                                       <?php _e('until', 'tainacan') ?> 
                                <input style="width: 30%" 
                                       type="number" 
                                       size="7" 
                                       value="" 
                                       id="facet_<?php echo $facet['id']; ?>_2" 
                                       name="socialdb_property_<?php echo $property['id']; ?>_2">
                                   <?php elseif ($property['type'] == 'stars'): ?>
                                <input type="radio" value="4.1_5" name="socialdb_property_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star5.png' ?>"></a><br>
                                <input type="radio" value="3.1_4" name="socialdb_property_<?php echo $compound_id; ?>_<?php echo $propert_id; ?>_<?php echo $index_id; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star4.png' ?>"></a><br>
                                <input type="radio" value="2.1_3" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star3.png' ?>"></a><br>
                                <input type="radio" value="1.1_2" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star2.png' ?>"></a><br>
                                <input type="radio" value="0_1" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star1.png' ?>"></a><br>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 no-padding padding-left-space">
                            <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                <option value="1"><?php _e('Between', 'tainacan'); ?></option>
                                <option value="2"><?php _e('Not Between', 'tainacan'); ?></option>
                            </select>
                        </div>   
                    </div>                
                <?php } ?>
            <?php endif; ?>
            <?php if (isset($property_compounds)): ?>
                <?php $advanced_search_helper->list_properties_compounds_search($property_compounds) ?>
            <?php endif; ?>
            <input type="hidden" name="search_properties_autocomplete" id='search_properties_autocomplete' value="<?php echo implode(',', $properties_autocomplete); ?>">
            <input type="hidden" name="properties_terms_radio" id='search_properties_terms_radio' value="<?php echo implode(',', $properties_terms_radio); ?>">
            <input type="hidden" name="properties_terms_tree" id='search_properties_terms_tree' value="<?php echo implode(',', $properties_terms_tree); ?>">
            <input type="hidden" name="properties_terms_selectbox" id='search_properties_terms_selectbox' value="<?php echo implode(',', $properties_terms_selectbox); ?>">
            <input type="hidden" name="properties_terms_checkbox" id='search_properties_terms_checkbox' value="<?php echo implode(',', $properties_terms_checkbox); ?>">
            <input type="hidden" name="properties_terms_multipleselect" id='search_properties_terms_multipleselect' value="<?php echo implode(',', $properties_terms_multipleselect); ?>">
            <input type="hidden" name="properties_terms_treecheckbox" id='search_properties_terms_treecheckbox' value="<?php echo implode(',', $properties_terms_treecheckbox); ?>">
            <?php if (isset($all_ids)): ?>
                <input type="hidden" id="properties_id_avoid" name="properties_id" value="<?php echo $all_ids; ?>">
            <?php endif; ?>  
            <input type="hidden" name="operation" value="searchItemFormItem">        
            <div class="col-md-12 no-padding" style="margin-top: 15px;">
                <button type="button" onclick="clear_all_field('<?php echo $form ?>')" class="btn btn-lg btn-default pull-left"><?php _e('Clear search', 'tainacan') ?></button>
                <button type="submit"  class="btn btn-lg btn-success pull-right"><?php _e('Find', 'tainacan') ?></button>
            </div>
        </form>            
        <?php
    }

    /**
     * 
     * @param type $categories
     * @return type
     */
    public function get_labels_search_obejcts($categories) {
        $title_labels = [];
        $categories = (is_array($categories)) ? $categories : explode(',', $categories);
        foreach ($categories as $value) {
            $collection = $this->get_collection_by_category_root($value);
            if ($collection && isset($collection[0])) {
                $labels_collection = ($collection[0]->ID != '') ? get_post_meta($collection[0]->ID, 'socialdb_collection_fixed_properties_labels', true) : false;
                $labels_collection = ($labels_collection) ? unserialize($labels_collection) : false;
                if ($labels_collection && $labels_collection[$this->terms_fixed['title']->term_id]) {
                    $title_labels[] = $labels_collection[$this->terms_fixed['title']->term_id];
                } else {
                    $title_labels[] = $this->terms_fixed['title']->name;
                }
            }
        }
        return implode('/', $title_labels);
    }

}
