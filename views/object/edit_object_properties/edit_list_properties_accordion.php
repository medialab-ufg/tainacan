<?php
/*
 * View Responsavel em mostrar as propriedades na hora de EDITAR do objeto, NAO UTILIZADA NOS EVENTOS
 */
include_once ('js/edit_list_properties_accordion_js.php');
include_once(dirname(__FILE__).'/../../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../../helpers/object/object_properties_widgets_helper.php');

$view_helper = new ViewHelper();
$object_properties_widgets_helper = new ObjectWidgetsHelper();
$ids = [];
$properties_autocomplete = [];
$properties_terms_radio = [];
$properties_terms_tree = [];
$properties_terms_selectbox = [];
$properties_terms_checkbox = [];
$properties_terms_multipleselect = [];
$properties_terms_treecheckbox = [];
//referencias
$references = [
    'properties_autocomplete' => &$properties_autocomplete,
    'properties_terms_radio' => &$properties_terms_radio,
    'properties_terms_checkbox' => &$properties_terms_checkbox,
    'properties_terms_tree' => &$properties_terms_tree,
    'properties_terms_selectbox' => &$properties_terms_selectbox,
    'properties_terms_multipleselect' => &$properties_terms_multipleselect,
    'properties_terms_treecheckbox' => &$properties_terms_treecheckbox   
];
if (isset($property_object)):
    foreach ($property_object as $property) {
        $ids[] = $property['id']; ?>
        <div id="meta-item-<?php echo $property['id']; ?>"
            property="<?php echo $property['id']; ?>"
            class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?> form-group">
            <h2>
                <?php echo $property['name']; ?>
                <?php
                if(has_action('modificate_label_edit_item_properties')):
                        do_action('modificate_label_insert_item_properties', $property);
                endif;
                //acao para modificaco da propriedade de objeto na edicao do item
                if(has_action('modificate_edit_item_properties_object')): 
                         do_action('modificate_edit_item_properties_object',$property); 
                endif;
                if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))) {
                     ?>
                        <a class="pull-right" 
                            style="margin-right: 20px;" >
                             <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                                   data-toggle="tooltip" 
                                   data-placement="bottom" 
                                   class="glyphicon glyphicon-question-sign"></span>
                        </a>
                    <?php  
                }
                if ($property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] == 'true') {
                        ?>
                        <a id='required_field_<?php echo $property['id']; ?>' style="padding: 3px;" >
                                <span  title="<?php echo __('This metadata is required!','tainacan')?>" 
                               data-toggle="tooltip" data-placement="top" >*</span>
                        </a>
                        <a id='ok_field_<?php echo $property['id']; ?>'  style="display: none;padding: 3px;margin-left: -30px;" >
                                 &nbsp;<span class="glyphicon  glyphicon-ok-circle" title="<?php echo __('Field filled successfully!','tainacan')?>" 
                               data-toggle="tooltip" data-placement="top" ></span>
                        </a>
                        <input type="hidden" 
                                 id='core_validation_<?php echo $property['id']; ?>' 
                                 class='core_validation' 
                                 value='false'>
                        <input type="hidden" 
                                 id='core_validation_<?php echo $property['id']; ?>_message'  
                                 value='<?php echo sprintf(__('The field %s is required','tainacan'),$property['name']); ?>'>
                        <script> 
                             <?php if(isset($property['metas']['value']) &&  is_array($property['metas']['value']) && !empty(array_filter($property['metas']['value']))): 
                                 echo "$('#core_validation_".$property['id']."').val('true')";  
                             endif; ?> 
                            set_field_valid(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['id']; ?>') 
                        </script> 
                        <?php  
                }
                ?>
            </h2>
            <div>
            <?php 
                // botao que leva a colecao relacionada
                    if (isset($property['metas']['collection_data'][0]->post_title)):  ?>
                        <a style="cursor: pointer;color: white;"
                           id="add_item_popover_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                           class="btn btn-primary btn-xs popover_item" 
                            >
                               <span class="glyphicon glyphicon-plus"></span>
                               <?php _e('Add new', 'tainacan'); ?>
                               <?php echo ' ' . $property['metas']['collection_data'][0]->post_title; ?>
                        </a>
                        <script>
                            $('#add_item_popover_<?php echo $property['id']; ?>_<?php echo $object_id; ?>').popover({ 
                               html : true,
                               placement: 'right',
                               title: '<?php echo _e('Add item in the collection','tainacan').' '.$property['metas']['collection_data'][0]->post_title; ?>',
                               content: function() {
                                 return $("#popover_content_<?php echo $property['id']; ?>_<?php echo $object_id; ?>").html();
                               }
                            });
                        </script>
                        <div id="popover_content_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"   class="hide ">
                            <form class="form-inline"  style="font-size: 12px;width: 300px;">
                                <div class="form-group">
                                  <input type="text" 
                                         placeholder="<?php _e('Type the title','tainacan') ?>"
                                         class="form-control" 
                                         id="title_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                                </div>
                                <button type="button" 
                                        onclick="add_new_item_by_title('<?php echo $property['metas']['collection_data'][0]->ID; ?>',$('#title_<?php echo $property['id']; ?>_<?php echo $object_id; ?>').val(),'#add_item_popover_<?php echo $property['id']; ?>_<?php echo $object_id; ?>',<?php echo $property['id']; ?>,<?php echo $object_id; ?>)"
                                        class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span></button>
                            </form>
                        </div> 
                        <br><br>
            <?php 
             endif; 
            ?>
            <input type="hidden" 
                        id="cardinality_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"  
                        value="<?php echo $view_helper->render_cardinality_property($property);   ?>">            
            <input type="text" 
                   onkeyup="autocomplete_object_property_edit('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" 
                   id="autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                   placeholder="<?php _e('Type the three first letters of the object of this collection ', 'tainacan'); ?>"  
                   class="chosen-selected form-control"  />    
            
            <select onclick="clear_select_object_property(this,'<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" 
                    id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_edit" 
                    multiple class="chosen-selected2 form-control" 
                    style="height: auto;" 
                    name="socialdb_property_<?php echo $property['id']; ?>[]"
                    <?php 
                        if ($property['metas']['socialdb_property_required'] == 'true'): 
                            echo 'required="required"';
                        endif;
                    ?> >
                    <?php 
                        if (!empty($property['metas']['objects'])) { ?>     
                            <?php foreach ($property['metas']['objects'] as $object) { ?>
                                <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && in_array($object->ID, $property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao   ?>    
                                     <option selected='selected' value="<?php echo $object->ID ?>"><?php echo $object->post_title ?></span>
                            <?php endif; ?>
                        <?php } ?> 
                    <?php 
                        }else { 
                    ?>   
                        <option value=""><?php _e('No objects added in this collection', 'tainacan'); ?></option>
                    <?php 
                        } 
                    ?>       
            </select>
        </div>  
    </div>     
    <?php } ?>
<input type="hidden" name="properties_object_ids" id='properties_object_ids' value="<?php echo implode(',', $ids); ?>">
<?php endif; ?>

<?php if (isset($property_data)): 
    foreach ($property_data as $property) { 
        if($property['id']=='license'):
            continue;
        endif;
        $properties_autocomplete[] = $property['id']; ?>
        <div id="meta-item-<?php echo $property['id']; ?>" 
             property="<?php echo $property['id']; ?>"
             class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?> form-group">
            <h2>
                <?php echo $property['name']; ?>
                <?php 
                if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                endif;
                if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))) {
                    ?>
                    <a class="pull-right" 
                       style="margin-right: 20px;" >
                        <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                              data-toggle="tooltip" 
                              data-placement="bottom" 
                              class="glyphicon glyphicon-question-sign"></span>
                    </a>
                    <?php  
                }
                if ($property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] == 'true') {
                    ?>
                      <a id='required_field_<?php echo $property['id']; ?>' style="padding: 3px;" >
                                <span  title="<?php echo __('This metadata is required!','tainacan')?>" 
                               data-toggle="tooltip" data-placement="top" >*</span>
                        </a>
                        <a id='ok_field_<?php echo $property['id']; ?>'  style="display: none;padding: 3px;margin-left: -30px;" >
                                 &nbsp;<span class="glyphicon  glyphicon-ok-circle" title="<?php echo __('Field filled successfully!','tainacan')?>" 
                               data-toggle="tooltip" data-placement="top" ></span>
                        </a>
                        <input type="hidden" 
                                 id='core_validation_<?php echo $property['id']; ?>' 
                                 class='core_validation' 
                                 value='false'>
                        <input type="hidden" 
                                 id='core_validation_<?php echo $property['id']; ?>_message'  
                                 value='<?php echo sprintf(__('The field %s is required','tainacan'),$property['name']); ?>'>
                        <script> 
                           <?php if(isset($property['metas']['value'][0])): echo "$('#core_validation_".$property['id']."').val('true')";  endif; ?> 
                           set_field_valid(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['id']; ?>') 
                        </script> 
                    <?php  
                }
                ?>
            </h2>
            <?php $cardinality = $view_helper->render_cardinality_property($property);   ?>
            <div>
                 <?php for($i = 0; $i<$cardinality;$i++):   ?>
                    <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                         style="padding-bottom: 10px;<?php echo ($i===0||(is_array($property['metas']['value'])&&$i<count($property['metas']['value']))) ? 'display:block': 'display:none'; ?>">
                    <?php if ($property['type'] == 'text') { ?>     
                            <input type="text" 
                                   id="form_edit_autocomplete_value_<?php echo $property['id']; ?>" 
                                   class="form-control form_autocomplete_value_<?php echo $property['id']; ?>" 
                                   value="<?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i])?$property['metas']['value'][$i]:''); ?>"
                                   name="socialdb_property_<?php echo $property['id']; ?>[]">
                    <?php }elseif ($property['type'] == 'textarea') { ?>   
                            <textarea class="form-control form_autocomplete_value_<?php echo $property['id']; ?>"
                                      rows="10"
                                      id="form_edit_autocomplete_value_<?php echo $property['id']; ?>" 
                                      name="socialdb_property_<?php echo $property['id']; ?>[]" ><?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i])?$property['metas']['value'][$i]:''); ?></textarea>
                    <?php }elseif ($property['type'] == 'numeric') { ?>   
                            <input type="text" 
                                   class="form-control form_autocomplete_value_<?php echo $property['id']; ?>"
                                   onkeypress='return onlyNumbers(event)'
                                   id="form_edit_autocomplete_value_<?php echo $property['id']; ?>" 
                                   name="socialdb_property_<?php echo $property['id']; ?>[]" 
                                   value="<?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>">
                               <?php }elseif ($property['type'] == 'autoincrement') { ?>   
                             <input disabled="disabled"  type="number" class="form-control" name="hidded_<?php echo $property['id']; ?>" value="<?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i])?$property['metas']['value'][$i]:''); ?>">
                    <?php }elseif ($property['type'] == 'radio' && $property['name'] == 'Status') { ?>   
                                <br>
                                <input   type="radio" <?php
                                if ($property['metas']['value'] && $property['metas']['value'][0] == 'current'): echo 'checked="checked"';
                                endif;
                                ?>  name="socialdb_property_<?php echo $property['id']; ?>" value="current"><?php _e('Current', 'tainacan') ?><br>
                                <input   type="radio" <?php
                                if ($property['metas']['value'] && $property['metas']['value'][0] == 'intermediate'): echo 'checked="checked"';
                                endif;
                                ?>  name="socialdb_property_<?php echo $property['id']; ?>" value="intermediate"><?php _e('Intermediate', 'tainacan') ?><br>
                                <input   type="radio" <?php
                                if ($property['metas']['value'] && $property['metas']['value'][0] == 'permanently'): echo 'checked="checked"';
                                endif;
                                ?> name="socialdb_property_<?php echo $property['id']; ?>" value="permanently"><?php _e('Permanently', 'tainacan') ?><br>
                        <?php } else if($property['type'] == 'date'&&!has_action('modificate_edit_item_properties_data')) { ?>
                                 <script>
                                    $(function() {
                                        $( "#socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>" ).datepicker({
                                            dateFormat: 'dd/mm/yy',
                                            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
                                            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
                                            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
                                            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                                            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                                            nextText: 'Próximo',
                                            prevText: 'Anterior',
                                            showOn: "button",
                                            buttonImage: "http://jqueryui.com/resources/demos/datepicker/images/calendar.gif",
                                            buttonImageOnly: true
                                        });
                                    });
                                </script>    
                                <input 
                                    style="margin-right: 5px;" 
                                    size="13" 
                                    class="input_date form_autocomplete_value_<?php echo $property['id']; ?>" 
                                    value="<?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i])?$property['metas']['value'][$i]:''); ?>"
                                    type="text" 
                                    id="socialdb_property_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                                    name="socialdb_property_<?php echo $property['id']; ?>[]">   
                        <?php }
                         // gancho para tipos de metadados de dados diferentes
                        else if(has_action('modificate_edit_item_properties_data')){
                            do_action('modificate_edit_item_properties_data',$property);
                            continue;
                        }else{ ?>
                            <input type="text"  
                                   value="<?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i])?$property['metas']['value'][$i]:''); ?>" 
                                   class="form-control form_autocomplete_value_<?php echo $property['id']; ?>" 
                                   name="socialdb_property_<?php echo $property['id']; ?>[]" >
                        <?php } ?> 
                 <?php echo $view_helper->render_button_cardinality($property,$i) ?>    
                     </div>         
                <?php endfor;  ?>                    
            </div>              
        </div>              
    <?php } ?>
    <?php
endif;

if ((isset($property_term) && count($property_term) > 1) || (count($property_term) == 1 )):
    foreach ($property_term as $property) { 
//        if(!isset($property['has_children'])||empty($property['has_children'])){
//            continue;
//        } 
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" <?php do_action('item_property_term_attributes') ?> 
             property="<?php echo $property['id']; ?>"
             class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?> form-group">
            <h2>
                <?php echo $property['name']; ?>
                <?php 
                    if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                else: // validacoes e labels
                        $property['metas']['socialdb_property_help'] = ($property['metas']['socialdb_property_help']==''&&$property['type'] == 'tree')? __('Select one option','tainacan') : '';
                        if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))) {
                                ?>
                                <a class="pull-right" 
                                    style="margin-right: 20px;" >
                                     <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                                           data-toggle="tooltip" 
                                           data-placement="bottom" 
                                           class="glyphicon glyphicon-question-sign"></span>
                                </a>
                                <?php  
                        }
                        if ($property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] == 'true') {
                            ?>
                            <a id='required_field_<?php echo $property['id']; ?>' style="padding: 3px;" >
                                <span  title="<?php echo __('This metadata is required!','tainacan')?>" 
                               data-toggle="tooltip" data-placement="top" >*</span>
                        </a>
                        <a id='ok_field_<?php echo $property['id']; ?>'  style="display: none;padding: 3px;margin-left: -30px;" >
                                 &nbsp;<span class="glyphicon  glyphicon-ok-circle" title="<?php echo __('Field filled successfully!','tainacan')?>" 
                               data-toggle="tooltip" data-placement="top" ></span>
                        </a>
                            <input type="hidden" 
                                     id='core_validation_<?php echo $property['id']; ?>' 
                                     class='core_validation' 
                                     value='false'>
                            <input type="hidden" 
                                     id='core_validation_<?php echo $property['id']; ?>_message'  
                                     value='<?php echo sprintf(__('The field %s is required','tainacan'),$property['name']); ?>'>
                            <script> set_field_valid(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['id']; ?>') </script> 
                            <?php  
                        }
                 endif; 
                 ?>
            </h2>    
            <div class="form-group">
                <?php
                if ($property['type'] == 'radio') {
                    $properties_terms_radio[] = $property['id'];
                    ?>
                    <div id='field_property_term_<?php echo $property['id']; ?>'></div>
                    <?php
                } elseif ($property['type'] == 'tree') {
                    $properties_terms_tree[] = $property['id'];
                    ?>
                    <button type="button"
                        onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'field_property_term_<?php echo $property['id']; ?>')" 
                        class="btn btn-primary btn-xs"><?php _e('Add Category','tainacan'); ?>
                    </button>
                    <br><br>
                    <div class="row">
                        <div style='height: 150px;' 
                             class='col-lg-12'  
                             id='field_property_term_<?php echo $property['id']; ?>'>
                        </div>
                        <input type="hidden" 
                               id='socialdb_propertyterm_<?php echo $property['id']; ?>'
                               name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                               value="">
                    </div>
                    <?php
                }elseif ($property['type'] == 'selectbox') {
                    $properties_terms_selectbox[] = $property['id'];
                    ?>
                    <select class="form-control" 
                            name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                            onchange="edit_validate_selectbox(this,'<?php echo $property['id']; ?>')"
                            id='field_property_term_<?php echo $property['id']; ?>' >
                    </select>
                    <?php
                }elseif ($property['type'] == 'checkbox') {
                    $properties_terms_checkbox[] = $property['id']; ?>
                    <div id='field_property_term_<?php echo $property['id']; ?>'></div>
                    <?php
                } elseif ($property['type'] == 'multipleselect') {
                    $properties_terms_multipleselect[] = $property['id'];
                    ?>
                     <select size='1' 
                        multiple 
                        onclick="validate_multipleselectbox(this,'<?php echo $property['id']; ?>')"
                        class="form-control" 
                        name="socialdb_propertyterm_<?php echo $property['id']; ?>[]" 
                        id='field_property_term_<?php echo $property['id']; ?>' 
                        <?php 
                        if ($property['metas']['socialdb_property_required'] == 'true'): 
                            echo 'required="required"';
                        endif;
                        ?>>
                     </select>
                            <?php
                }elseif ($property['type'] == 'tree_checkbox') {
                    $properties_terms_treecheckbox[] = $property['id']; ?>
                    <button type="button"
                        onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'field_property_term_<?php echo $property['id']; ?>')" 
                        class="btn btn-primary btn-xs"><?php _e('Add Category','tainacan'); ?>
                    </button>
                    <br><br>
                    <div class="row">
                        <div style='height: 150px;' 
                             class='col-lg-12'  
                             id='field_property_term_<?php echo $property['id']; ?>'>
                        </div>
                        <div id='socialdb_propertyterm_<?php echo $property['id']; ?>' ></div>
                    </div>
                    <?php
                }
                ?> 
        </div>   
    </div>              
    <?php } ?>
<?php endif;
?>
<?php $object_properties_widgets_helper->list_properties_compounds($property_compounds, $object_id,$references)  ?>  
<input type="hidden" name="properties_autocomplete" id='properties_autocomplete' value="<?php echo (is_array($properties_autocomplete))? implode(',', array_unique($properties_autocomplete)):''; ?>">
<input type="hidden" name="categories_id" id='edit_object_categories_id' value="<?php echo implode(',', $categories_id); ?>">   
<input type="hidden" name="properties_terms_radio" id='properties_terms_radio' value="<?php echo implode(',', array_unique($properties_terms_radio)); ?>">
<input type="hidden" name="properties_terms_tree" id='properties_terms_tree' value="<?php echo implode(',', array_unique($properties_terms_tree)); ?>">
<input type="hidden" name="properties_terms_selectbox" id='properties_terms_selectbox' value="<?php echo implode(',', array_unique($properties_terms_selectbox)); ?>">
<input type="hidden" name="properties_terms_checkbox" id='properties_terms_checkbox' value="<?php echo implode(',', array_unique($properties_terms_checkbox)); ?>">
<input type="hidden" name="properties_terms_multipleselect" id='properties_terms_multipleselect' value="<?php echo implode(',', array_unique($properties_terms_multipleselect)); ?>">
<input type="hidden" name="properties_terms_treecheckbox" id='properties_terms_treecheckbox' value="<?php echo implode(',', array_unique($properties_terms_treecheckbox)); ?>"><?php if (isset($all_ids)): ?>
    <input type="hidden" id="properties_id" name="properties_id" value="<?php echo $all_ids; ?>">
    <input type="hidden" id="property_origin" name="property_origin" value="<?php echo $all_ids; ?>">
    <input type="hidden" id="property_added" name="property_added" value="">
    <input type="hidden" id="selected_categories" name="selected_categories" value="">
    <div id="append_properties_categories" class="hide"></div>
<?php endif; ?>


