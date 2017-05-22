<?php
/*
 * View Responsavel em mostrar as propriedades na hora de EDITAR do objeto, NAO UTILIZADA NOS EVENTOS
 */
include_once ('js/properties_categories_accordion_js.php');
include_once(dirname(__FILE__).'/../../../helpers/view_helper.php');
include_once(dirname(__FILE__).'/../../../helpers/object/object_properties_widgets_helper.php');

$properties_to_avoid = (explode(',', $properties_to_avoid));

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
    'categories' => &$categories,
    'properties_autocomplete' => &$properties_autocomplete,
    'properties_terms_radio' => &$properties_terms_radio,
    'properties_terms_checkbox' => &$properties_terms_checkbox,
    'properties_terms_tree' => &$properties_terms_tree,
    'properties_terms_selectbox' => &$properties_terms_selectbox,
    'properties_terms_multipleselect' => &$properties_terms_multipleselect,
    'properties_terms_treecheckbox' => &$properties_terms_treecheckbox,   
    'properties_to_avoid' => &$properties_to_avoid,   
];
if($is_view_mode){
    $references['is_view_mode'] = true;
    $references['object_id'] = $object_id;
}
if($isEdit){
    $references['is_edit'] = true;
}

if(isset($not_block)){
     $references['operation'] = true;
}

$properties_concatenated = [];
if (isset($property_object)):
    $ids = [];
    foreach ($property_object as $property) {
        if(in_array($property['id'], $properties_to_avoid)){
            continue;
        }
        $ids[] = $property['id'];
        $property['tipo'] = 'object';
        $properties_concatenated[$property['id']] = $property;
    }
    ?>
    <input type="hidden" name="properties_object_ids" id='properties_object_ids' value="<?php echo implode(',', $ids); ?>">
    <?php
endif;   


if (isset($property_data)): 
    $ids = [];
    foreach ($property_data as $property) { 
        if(in_array($property['id'], $properties_to_avoid)){
            continue;
        }
        $ids[] = $property['id'];
        $properties_autocomplete[] = $property['id']; 
        $property['tipo'] = 'data';
        $properties_concatenated[$property['id']] = $property;
    }    
endif;   

if ((isset($property_term) && count($property_term) > 1) || (count($property_term) == 1 )):
    $ids = [];
    foreach ($property_term as $property) { 
        if(in_array($property['id'], $properties_to_avoid)){
            continue;
        }
        $ids[] = $property['id'];
        $property['tipo'] = 'term';
        $properties_concatenated[$property['id']] = $property;
    }
endif;    

if (isset($property_compounds)):
    foreach ($property_compounds as $property) { 
        if(is_array($references['properties_to_avoid'])&&in_array($property['id'], $references['properties_to_avoid'])){
            continue;
        }
        $result['ids'][] = $property['id']; 
        $references['compound_id'] = $property['id']; 
        $property['tipo'] = 'compound';
        $properties_concatenated[$property['id']] = $property;
    }
    ?>
    <input type="hidden" 
        name="pc_properties_compounds[]" 
        id="pc_properties_compounds_<?php echo $references['categories'] ?>"
        value="<?php echo implode(',', $result['ids']); ?>"> 
    <?php
endif;
// ORDENACAO
$original_properties = [];
$ordenation = get_term_meta($categories, 'socialdb_category_properties_ordenation', true);
if($ordenation && $ordenation != ''){
    $explode = explode(',', $ordenation);
    foreach ($explode as $property_id) {
        $original_properties[] = $properties_concatenated[$property_id];
        unset($properties_concatenated[$property_id]);
    }
    if(count($properties_concatenated)>0){
       foreach ($properties_concatenated as $property) {
            $original_properties[] = $property;
        } 
    }
}else{
    $original_properties = $properties_concatenated;
}
?>
<div class="property-category-list" style="margin-bottom: 15px;">
<?php 
foreach($original_properties as $property): 
    if ($property['tipo'] == 'object'):
        if(in_array($property['id'], $properties_to_avoid)){
            continue;
        }
        $ids[] = $property['id'];
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" 
             property="<?php echo $property['id']; ?>" 
             class="category-<?php echo $categories ?> form-group" >
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
                if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))&&!$is_view_mode) {
                     ?>
                        <a class="pull-right" 
                            style="margin-right: 15px;<?php  if ($property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] == 'true') echo 'margin-left: -25px;' ?>" >
                             <span title="<?php echo $property['metas']['socialdb_property_help'] ?>" 
                                   data-toggle="tooltip" 
                                   data-placement="bottom" 
                                   class="glyphicon glyphicon-question-sign"></span>
                        </a>
                    <?php  
                }
                if ($property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] == 'true'&&!$is_view_mode) {
                        ?>
                       <a id='required_field_<?php echo $property['id']; ?>' class="pull-right" 
                            style="margin-right: 15px;color:red;" >
                                 <span class="glyphicon glyphicon-remove"  title="<?php echo __('This metadata is required!','tainacan')?>" 
                                data-toggle="tooltip" data-placement="top" ></span>
                         </a>
                         <a id='ok_field_<?php echo $property['id']; ?>' class="pull-right" style="display: none;margin-right: 15px;color:green;"  >
                                 <span class="glyphicon glyphicon-ok" title="<?php echo __('Field filled successfully!','tainacan')?>" 
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
                <?php if($is_view_mode  || (isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true' && !isset($not_block))): ?>
                     <div id="labels_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                        <?php if (!empty($property['metas']['objects']) && !empty($property['metas']['value'])) { ?>
                            <?php foreach ($property['metas']['objects'] as $object) { // percoro todos os objetos  ?>
                                <?php
                                if (isset($property['metas']['value']) && !empty($property['metas']['value']) && in_array($object->ID, $property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao
                                    echo '<input type="hidden" name="socialdb_property_'.$property['id'].'[]" value="'.$object->ID.'"><b><a  href="' . get_the_permalink($property['metas']['collection_data'][0]->ID) . '?item=' . $object->post_name . '" >' . $object->post_title . '</a></b><br>';
                                endif;
                                ?>
                            <?php } ?>
                            <?php
                        }else {
                            echo '<p>' . __('empty field', 'tainacan') . '</p>';
                        }
                        ?>
                    </div>
                <?php else: 
                     $object_properties_widgets_helper->generateWidgetPropertyRelated($property,$object_id,$collection_id) ;
                endif ?>    
            </div>  
    </div>     
    <?php elseif($property['tipo'] == 'data'): 
        if(in_array($property['id'], $properties_to_avoid)){
            continue;
        }
        $ids[] = $property['id'];
        $properties_autocomplete[] = $property['id']; ?>
        
        <div id="meta-item-<?php echo $property['id']; ?>" property="<?php echo $property['id']; ?>" class="category-<?php echo $categories ?> form-group">
            <h2>
                <?php echo $property['name']; ?>
                <?php 
                if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                endif;
                if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help'])) && !$is_view_mode) {
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
                if ($property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] == 'true'  && !$is_view_mode) {
                    ?>
                         <a id='required_field_<?php echo $property['id']; ?>' class="pull-right" 
                            style="margin-right: 15px;color:red;" >
                                 <span class="glyphicon glyphicon-remove"  title="<?php echo __('This metadata is required!','tainacan')?>" 
                                data-toggle="tooltip" data-placement="top" ></span>
                         </a>
                         <a id='ok_field_<?php echo $property['id']; ?>' class="pull-right" style="display: none;margin-right: 15px;color:green;"  >
                                 <span class="glyphicon glyphicon-ok" title="<?php echo __('Field filled successfully!','tainacan')?>" 
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
            <?php if($is_view_mode  || (isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true' && !isset($not_block))): ?>
                <div>
                    <!--?php if(isset($property['metas']['value'][0])): ?>
                        <p>< ?php  echo '<a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $property['metas']['value'][0] . "'" . ',' . $property['id'] . ')">' . $property['metas']['value'][0] . '</a>';  ?></p>
                    < ?php else: ?>
                        <p>< ?php  _e('empty field', 'tainacan') ?></p>
                    < ?php endif ?-->
                    
                    <?php if (is_plugin_active('data_aacr2/data_aacr2.php') && $property['type'] == 'date' && get_post_meta($object_id, "socialdb_property_{$property['id']}_date", true)): ?>
                        <?php $value = get_post_meta($object_id, "socialdb_property_{$property['id']}_date", true); ?>
                        <p><?php echo '<a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $value . "'" . ',' . $property['id'] . ')">' . $value . '</a>'; ?></p>
                    <?php elseif (isset($property['metas']['value'][0])): ?>
                        <?php $is_property_date = false; ?>
                        <?php if ($property['type'] == 'date'): ?>
                            <?php $is_property_date = true; ?>
                        <?php endif; ?>
                        <?php foreach ($property['metas']['value'] as $value): if (empty($value)) continue; ?>
                            <?php
                            if ($is_property_date):
                                $date_temp = explode('-', $value);
                                if (count($date_temp)>1):
                                    $value = $date_temp[2].'/'.$date_temp[1].'/'.$date_temp[0];
                                endif;
                            endif;
                            ?>
                            <p><?php echo '<a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $value . "'" . ',' . $property['id'] . ')">' . $value . '</a>'; ?></p>
                            <?php
                        endforeach;
                        ?>
                    <?php else: ?>
                        <p><?php _e('empty field', 'tainacan') ?></p>
                    <?php endif ?>
                </div> 
            <?php else: ?> 
                <?php $cardinality = $view_helper->render_cardinality_property($property);   ?>
                <div >
                     <input type="hidden" class="form_autocomplete_value_<?php echo $property['id']; ?>_mask" 
                                   value="<?php echo ($property['metas']['socialdb_property_data_mask'] ) ? $property['metas']['socialdb_property_data_mask'] : '' ?>">
                     <?php for($i = 0; $i<$cardinality;$i++):   ?>
                        <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                             style="padding-bottom: 10px;<?php echo ($i===0||(is_array($property['metas']['value'])&&$i<count($property['metas']['value']))) ? 'display:block': 'display:none'; ?>">
                        <?php if ($property['type'] == 'text') { ?>     
                                <input type="text" 
                                       id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>_origin" 
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
                                       id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>_origin" 
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
                                $property['contador'] = $i;
                                $property['operation'] = 'edit';
                                $property['object_id'] = $object_id;
                                do_action('modificate_edit_item_properties_data',$property);
                                //continue;
                            }else{ ?>
                                <input type="text"  
                                        id="form_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $i; ?>_origin" 
                                       value="<?php if ($property['metas']['value']) echo (isset($property['metas']['value'][$i])?$property['metas']['value'][$i]:''); ?>" 
                                       class="form-control form_autocomplete_value_<?php echo $property['id']; ?>" 
                                       name="socialdb_property_<?php echo $property['id']; ?>[]" >
                            <?php } ?> 
                     <?php echo $view_helper->render_button_cardinality($property,$i) ?>    
                         </div>         
                    <?php endfor;  ?>                    
                </div>   
            <?php endif; ?>
        </div>     
    <?php
    elseif ($property['tipo'] == 'term'):
        if(in_array($property['id'], $properties_to_avoid)){
            continue;
        }
        $ids[] = $property['id'];
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>" <?php do_action('item_property_term_attributes') ?> property="<?php echo $property['id']; ?>" class="category-<?php echo $categories ?>  form-group">
            <h2>
                <?php echo $property['name']; ?>
                <?php 
                    if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                else: // validacoes e labels
                        $property['metas']['socialdb_property_help'] = ($property['metas']['socialdb_property_help']==''&&$property['type'] == 'tree')? __('Select one option','tainacan') : '';
                        if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))  && !$is_view_mode) {
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
                        if ($property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] == 'true'  && !$is_view_mode) {
                            ?>
                             <a id='required_field_<?php echo $property['id']; ?>' class="pull-right" 
                                style="margin-right: 15px;color:red;" >
                                     <span class="glyphicon glyphicon-remove"  title="<?php echo __('This metadata is required!','tainacan')?>" 
                                    data-toggle="tooltip" data-placement="top" ></span>
                             </a>
                             <a id='ok_field_<?php echo $property['id']; ?>' class="pull-right" style="display: none;margin-right: 15px;color:green;"  >
                                     <span class="glyphicon glyphicon-ok" title="<?php echo __('Field filled successfully!','tainacan')?>" 
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
                if($is_view_mode  || (isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true' && !isset($not_block))):
                    switch ($property['type']){
                        case 'radio';
                            $properties_terms_radio[] = $property['id'];
                            break;
                        case 'tree';
                            $properties_terms_tree[] = $property['id'];
                            break;
                        case 'checkbox';
                            $properties_terms_checkbox[] = $property['id'];
                            break;
                        case 'multipleselect';
                            $properties_terms_multipleselect[] = $property['id'];
                            break;
                        case 'selectbox';
                            $properties_terms_selectbox[] = $property['id'];
                            break;
                        case 'tree_checkbox';
                            $properties_terms_treecheckbox[] = $property['id'];
                            break;
                    }

              ?>
                  <div id='labels_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>  
              <?php
                else:
              ?>
                <?php
                if ($property['type'] == 'radio') {
                    $properties_terms_radio[] = $property['id'];
                    ?>
                    <div id='field_property_term_<?php echo $property['id']; ?>'></div>
                    <?php
                } elseif ($property['type'] == 'tree') {
                    $properties_terms_tree[] = $property['id'];
                    ?>
                    <?php if($property['metas']['socialdb_property_habilitate_new_category'] && $property['metas']['socialdb_property_habilitate_new_category'] == 'true'): ?>
                            <button type="button"
                           <?php
                           echo (isset($is_view_mode)) ? 'style="display:none"' : ''
                           ?>
                                   onclick="showModalFilters('add_category', '<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'], 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>, 'field_property_term_<?php echo $property['id']; ?>')" 
                                   class="btn btn-primary btn-xs"><?php _e('Add Category', 'tainacan'); ?>
                           </button>
                           <br><br>
                    <?php endif; ?>
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
                    <select multiple 
                            class="form-control" 
                            name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                            id='field_property_term_<?php echo $property['id']; ?>' >
                    </select>
                            <?php
                }elseif ($property['type'] == 'tree_checkbox') {
                    $properties_terms_treecheckbox[] = $property['id']; ?>
                    <?php if($property['metas']['socialdb_property_habilitate_new_category'] && $property['metas']['socialdb_property_habilitate_new_category'] == 'true'): ?>
                            <button type="button"
                           <?php
                           echo (isset($is_view_mode)) ? 'style="display:none"' : ''
                           ?>
                                   onclick="showModalFilters('add_category', '<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'], 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>, 'field_property_term_<?php echo $property['id']; ?>')" 
                                   class="btn btn-primary btn-xs"><?php _e('Add Category', 'tainacan'); ?>
                           </button>
                           <br><br>
                        <?php endif; ?>
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
            <?php endif; ?>        
        </div>   
    </div>       
    <?php elseif($property['tipo'] == 'compound'): ?>
        <?php $object_properties_widgets_helper->list_properties_categories_compounds($property, $object_id,$references);  ?> 
    <?php endif; ?>   
  <?php endforeach; ?>  
</div>
<input type="hidden" name="pc_properties[]" id='pc_properties' value="<?php echo implode(',', $ids); ?>">
<input type="hidden" name="categories" id='pc_categories' value="">
<input type="hidden" name="properties_autocomplete" 
       id='pc_properties_autocomplete_<?php echo $categories ?>' 
       value="<?php echo  (isset($properties_autocomplete)&&is_array($properties_autocomplete))?implode(',', array_unique($properties_autocomplete)):''; ?>">
<input type="hidden" name="properties_terms_radio" id='pc_properties_terms_radio_<?php echo $categories ?>' value="<?php echo implode(',', array_unique($properties_terms_radio)); ?>">
<input type="hidden" name="properties_terms_tree" id='pc_properties_terms_tree_<?php echo $categories ?>' value="<?php echo implode(',', array_unique($properties_terms_tree)); ?>">
<input type="hidden" name="properties_terms_selectbox" id='pc_properties_terms_selectbox_<?php echo $categories ?>' value="<?php echo implode(',', array_unique($properties_terms_selectbox)); ?>">
<input type="hidden" name="properties_terms_checkbox" id='pc_properties_terms_checkbox_<?php echo $categories ?>' value="<?php echo implode(',', array_unique($properties_terms_checkbox)); ?>">
<input type="hidden" name="properties_terms_multipleselect" id='pc_properties_terms_multipleselect_<?php echo $categories ?>' value="<?php echo implode(',', array_unique($properties_terms_multipleselect)); ?>">
<input type="hidden" name="properties_terms_treecheckbox" id='pc_properties_terms_treecheckbox_<?php echo $categories ?>' value="<?php echo implode(',', array_unique($properties_terms_treecheckbox)); ?>">



