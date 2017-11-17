<?php
include_once (dirname(__FILE__) . '/../input/selectbox.class.php');
include_once (dirname(__FILE__) . '/../input/simpletree.class.php');
include_once (dirname(__FILE__) . '/../input/radio.class.php');
include_once (dirname(__FILE__) . '/../input/checkbox.class.php');
include_once (dirname(__FILE__) . '/../input/multipletree.class.php');

class FormItemCategory extends FormItemMultiple{
    public $selectboxClass;
    public $simpleTreeClass;
    public $radioClass;
    public $checkboxClass;
    public $multipleTreeClass;


    public function widget($property, $item_id) {
        $this->selectboxClass = new SelectboxClass(0,'',$this->value);
        $this->simpleTreeClass = new SimpleTreeMultipleClass(0,'',$this->value);
        $this->radioClass = new RadioClass(0,'',$this->value);
        $this->checkboxClass = new CheckboxClass(0,'',$this->value);
        $this->multipleTreeClass = new MultipleTreeClass(0,'',$this->value);
        $isRequired = ($property['metas'] && $property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] != 'false') ? true : false;
        ?>
        <style>
            .category-properties h2{
                font-size: 12px;
                text-indent: 2%;
                font-weight: bold;
                color: black;
                margin-left: -30px;
            }
        </style>
        <div id="meta-item-<?php echo $property['id']; ?>" class="form-group" >
             <h2>
                <?php echo $property['name']; ?>
                <?php $this->hasTextHelper($property);  ?>
	             <?php
	             add_helpText($property, $this);
	             ?>
                <?php if ($isRequired): ?>
                *
                <?php $this->validateIcon('alert-compound-'.$property['id'],__('Required field','tainacan')) ?>
                <?php endif ?>
            </h2>
            <div>
                <?php
                if(empty($property['has_children']))
                {
	                echo '<div class="alert alert-info">'.__('This category has no children', 'tainacan').'</div>';
                }else
                    if($property['type'] == 'selectbox'): ?>
                    <?php $this->selectboxClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php elseif($property['type'] == 'tree'): ?>
                    <?php
                        $this->simpleTreeClass->generate($property,['id'=>0], $item_id, 0);
                    ?>
                <?php elseif($property['type'] == 'radio'): ?>
                    <?php $this->radioClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php elseif($property['type'] == 'checkbox' || $property['type'] == 'multipleselect'): ?>
                    <?php $this->checkboxClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php elseif($property['type'] == 'tree_checkbox'): ?>
                    <?php $this->multipleTreeClass->generate($property,['id'=>0], $item_id, 0) ?>
                <?php endif; ?>
                <div class="category-properties" style="float:left;width: 100%;padding-bottom:15px;" id="appendCategoryMetadata_<?php echo $property['id']; ?>_0_0">
                </div>
	            <?php

	            if(strcmp($property['metas']['socialdb_property_habilitate_new_category'],'true') === 0)
	            {
		            ?>
                    <button type="button" class="btn btn-primary btn-xs pull-right" onclick="add_new_category(<?php echo $property['metas']['socialdb_property_term_root']; ?>, '<?php echo $property['name']?>');">
			            <?php _e("Add new category", "tainacan"); ?>
                    </button>

                    <!-- TAINACAN: modal padrao bootstrap para adicao de categorias    -->
                    <div class="modal fade" id="modalAddCategoria_<?php echo $property['metas']['socialdb_property_term_root']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form  id="submit_adicionar_category_single">
                                    <input type="hidden" id="category_single_add_id" name="category_single_add_id" value="">
                                    <input type="hidden" id="operation_event_create_category" name="operation" value="add_event_term_create">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
                                        <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-plus"></span>
								            <?php _e('Add Category', 'tainacan'); ?>
								            <?php do_action('add_option_in_add_category'); ?>
                                        </h4>
                                    </div>
                                    <div id="form_add_category">
                                        <div class="modal-body">

                                            <div class="create_form-group">
                                                <label for="category_single_name"><?php _e('Category name', 'tainacan'); ?></label>
                                                <input type="text" class="form-control" id="category_single_name" name="socialdb_event_term_suggested_name" required="required" placeholder="<?php _e('Category name', 'tainacan'); ?>">
                                            </div>

                                            <div class="form-group">
                                                <label for="category_single_parent_name"><?php _e('Selected parent', 'tainacan'); ?></label>
                                                <input disabled="disabled" type="text" class="form-control" id="category_single_parent_name" placeholder="<?php _e('Right click on the tree and select the category as parent', 'tainacan'); ?>" name="category_single_parent_name">
                                                <input type="hidden"  id="category_single_parent_id"  name="socialdb_event_term_parent" value="0" >
                                            </div>

                                            <input type="radio" id="rootCategorySelected" name="rootSelector" value="realRoot" checked> <?php _e("Root category", "tainacan");?>:
                                            <label id="realRootName"></label>
                                            <input type="hidden" id="realRootID" value="">
                                            <br>
                                            <input type="radio" id="childCategorySelected" name="rootSelector" value="child"> <?php _e("Child category", "tainacan");?>

                                            <br>
                                            <div id="childrenSelect" style="display: none;">
		                                        <?php $this->simpleTreeClass->generate($property,['id'=>0], $item_id, 0, true) ?>
                                            </div>

                                            <br>

                                            <div class="form-group">
                                                <label for="category_add_description"><?php _e('Category description', 'tainacan'); ?>&nbsp;<span style="font-size: 10px;">(<?php _e('Optional', 'tainacan'); ?>)</span></label>
                                                <textarea class="form-control" id="category_add_description" name="socialdb_event_term_description"
                                                          placeholder="<?php _e('Describe your category', 'tainacan'); ?>"></textarea>
                                            </div>
                                            <input type="hidden" id="category_single_add_collection_id" name="socialdb_event_collection_id" value="<?php echo get_the_ID(); ?>">
                                            <input type="hidden" id="category_single_add_create_time" name="socialdb_event_create_date" value="<?php echo mktime(); ?>">
                                            <input type="hidden" id="category_single_add_user_id" name="socialdb_event_user_id" value="<?php echo get_current_user_id(); ?>">
                                            <input type="hidden" id="category_single_add_dynatree_id" name="dynatree_id" value="">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php _t('Close', 1); ?></button>
                                            <button type="button" class="btn btn-primary" onclick="send('<?php echo $property['metas']['socialdb_property_term_root']; ?>');"><?php _t('Save', 1); ?></button>
                                        </div>
                                    </div>
                                    <div id="another_option_category" style="display: none;">
							            <?php
							            do_action('show_option_in_add_category'); ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

		            <?php
	            }
	            ?>
            </div>
        </div>

        <script>
            function add_new_category(fathers_id, fathers_name)
            {
                $('#modalAddCategoria_'+fathers_id).modal('show');
                $("#category_single_parent_name").val(fathers_name);
                $("#realRootName").text(fathers_name);

                $("#category_single_parent_id").val(fathers_id);
                $("#realRootID").val(fathers_id);
            }

            function send (fathers_id){
                $('#modalAddCategoria_'+fathers_id).modal('hide');
                $('#modalImportMain').modal('show');//mostro o modal de carregamento

                let form_data = new FormData();

                form_data.append("socialdb_event_term_suggested_name", $("#category_single_name").val());
                form_data.append("socialdb_event_term_description", $("#category_add_description").val());

                form_data.append("category_single_add_id", $("#category_single_add_id").val());
                form_data.append("socialdb_event_term_parent", $("#category_single_parent_id").val());
                form_data.append("category_single_parent_name", $("#category_single_parent_name").val());
                form_data.append("operation", "add_event_term_create");
                form_data.append("socialdb_event_collection_id", $("#collection_id").val());

                form_data.append("category_single_add_create_time", $("#category_single_add_create_time").val());
                form_data.append("socialdb_event_user_id", $("#category_single_add_user_id").val());
                form_data.append("category_single_add_dynatree_id", $("#category_single_add_dynatree_id").val());

                $.ajax({
                    url: $('#src').val() + '/controllers/event/event_controller.php',
                    type: 'POST',
                    data: form_data,
                    processData: false,
                    contentType: false
                }).done(function (result) {
                    $('#modalImportMain').modal('hide');//escondo o modal de carregamento
                    elem = jQuery.parseJSON(result);
                    showAlertGeneral(elem.title, elem.msg, elem.type);

                    update_edit_multiple_itens();
                });
            }

            $("#childCategorySelected").click(function(){
                $("#childrenSelect").show();
            });

            $("#rootCategorySelected").click(function(){
                $("#childrenSelect").hide();
                $("#category_single_parent_name").val($("#realRootName").text());
                $("#category_single_parent_id").val($("#realRootID").val());
            });

            function update_edit_multiple_itens()
            {
                $.ajax({
                    url: $('#src').val() + '/controllers/object/object_controller.php',
                    type: 'POST',
                    data: {
                        operation: 'update_edit_multiple_item',
                        collection_id: $("#collection_id").val()
                    }
                }).done(function(result){
                    var selected = $("item-multiple-selected").val();

                    $("item-multiple-selected").remove();
                    $("#form_properties_items").remove();
                    $("#no_properties_items").remove();


                    $("#configuration").prepend(result);
                    $("#item-multiple-selected").val(selected);
                    $("#form_properties_items").show();
                    $("#no_properties_items").hide();
                });

            }
        </script>

        <?php 
        //CASO EXISTA VALORES DE CATEGORIAS,BUSCO SEUS METADADOS
        if($this->value && is_array($this->getValues($this->value[0][0])) && !empty($this->getValues($this->value[0][0]))): 
        ?>
        <script>
        var ids = '<?php echo implode(',', $this->getValues($this->value[0][0])) ?>';
        Hook.register('appendCategoryMetadataHere',function(args){
             var categories = args[0]
             var item_id = args[1];
             var seletor = args[2];
             $(seletor)
                  .html('<center><img width="100" heigth="100" src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>"><?php _e('Loading metadata for this field', 'tainacan') ?></center>');
             $.ajax({
                 url: $('#src').val() + '/controllers/object/object_controller.php',
                 type: 'POST',
                 data: {
                     operation: 'appendCategoryMetadata',
                     properties_to_avoid: '<?php echo implode(',', $this->allPropertiesIds) ?>', categories: categories,object_id:item_id ,item_id:item_id,collection_id:$('#collection_id').val()}
             }).done(function (result) {
                 if(result !== ''){
                     $(seletor).css('border','1px solid #ccc');
                     $(seletor).css('padding','5px;');
                     $(seletor).css('margin-top','10px');
                     $(seletor).css('height','auto');
                     $(seletor).html(result);
                 }else{
                     $(seletor).html('');
                 }
             });
           });
        Hook.call('appendCategoryMetadataHere',[ids, <?php echo $item_id ?>, '#appendCategoryMetadata_<?php echo $property['id']; ?>_0_0']);
       </script>
        <?php 
        endif; ?>
        <?php
    }
}
