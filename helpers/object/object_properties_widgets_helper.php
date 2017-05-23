<?php
/*
 * Object Controller's view helper 
 * */
class ObjectWidgetsHelper extends ViewHelper {
    
    public function generateValidationIcons($property,$is_compound = false) {
        if ($property['metas'] &&$property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))) {
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
        if ($property['metas'] && $property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] != 'false') {
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
                     value='<?php echo ($property['metas']['value']) ?  'true' : 'false'; ?>'>
            <input type="hidden" 
                     id='core_validation_<?php echo $property['id']; ?>_message'  
                     value='<?php echo sprintf(__('The field %s is required','tainacan'),$property['name']); ?>'>
            <?php
            if($is_compound):
            ?>
                <script> validate_all_fields_compounds(<?php echo $property['id']; ?>) </script> 
            <?php  
            else:
            ?>   
                <script> set_field_valid(<?php echo $property['id']; ?>,'core_validation_<?php echo $property['id']; ?>') </script> 
            <?php
            endif;
        }
    }
    /**
     * 
     * @param array $properties_compounds
     */
    public function list_properties_compounds($properties_compounds,$object_id,$references) {
        include_once ( dirname(__FILE__).'/../../views/object/js/properties_compounds_js.php');
        $result = [];
        $coumpounds_id = [];
        if (isset($properties_compounds)):
            foreach ($properties_compounds as $property) { 
               if(isset($references['properties_to_avoid']) && is_array($references['properties_to_avoid'])&&in_array($property['id'], $references['properties_to_avoid'])){
                    continue;
                }
               $result['ids'][] = $property['id']; 
               $references['compound_id'] = $property['id']; 
               ?>
               <div id="meta-item-<?php echo $property['id']; ?>"  class="form-group">
                    <h2>
                        <?php echo $property['name']; ?>
                        <?php 
                            if(has_action('modificate_label_insert_item_properties')):
                                do_action('modificate_label_insert_item_properties', $property);
                            endif;
                            //acao para modificaco da propriedade de objeto na insercao do item
                            if(has_action('modificate_insert_item_properties_compounds')): 
                                     do_action('modificate_insert_item_properties_compounds',$property,$object_id,'property_value_'. $property['id'] .'_'.$object_id.'_add'); 
                            endif;
                            $this->generateValidationIcons($property, true);
                            $all_fields_validate =  $property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] != 'false';
                            ?>
                    </h2> 
                    <?php $cardinality_bigger = $this->render_cardinality_property($property);   ?>
                    <?php $properties_compounded = $property['metas']['socialdb_property_compounds_properties_id']; ?>
                    <?php //$class = 'col-md-'. (12/count($properties_compounded)); ?>
                    <div style="margin-right: 15px;margin-left: 15px;" > 
                         <input  type="hidden" 
                                id='main_compound_id' 
                                value='<?php echo $references['compound_id'] ?>'>
                        <?php 
                            $coumpounds_id = [];
                            foreach ($properties_compounded as $property_compounded): 
                                    if(!isset( $property_compounded['id']) || empty($property_compounded['id'])){
                                        continue;
                                    }
                                    $coumpounds_id[] = $property_compounded['id']; 
                            endforeach;    ?>
                        <input type="hidden" 
                               name="compounds_<?php echo $property['id']; ?>" 
                               id="compounds_<?php echo $property['id']; ?>"
                               value="<?php echo implode(',', array_unique($coumpounds_id)); ?>">       
                        <input  type="hidden"
                                name="cardinality_<?php echo $property['id']; ?>"
                                id="cardinality_<?php echo $property['id']; ?>"
                                value="<?php echo $cardinality_bigger; ?>">
                        <?php $coumpounds_id = []; ?>
                        <?php if( $property['metas']['socialdb_property_required'] == 'true_one_field'): ?> 
                        <input  type="hidden" 
                                id='type_required_<?php echo $references['compound_id'] ?>' 
                                value='<?php echo $property['metas']['socialdb_property_required'] ?>'> 
                        <input  type="hidden" 
                                id='count_fields_<?php echo $references['compound_id'] ?>' 
                                value='<?php echo count($properties_compounded) ?>'>
                        <?php endif; ?> 
                        <?php for($i = 0; $i<$cardinality_bigger;$i++): 
                                $is_show_container =  $this->is_set_container($object_id,$property,$property_compounded,$i);
                                $fields_filled =  $this->count_fields_container_value($object_id,$property,$property_compounded,$i);
                                $position = 0;
                                ?>
                                <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                                     class="col-md-12 no-padding" 
                                     style="border-color: #ccc;
                                     <?php echo ($is_show_container) ? ( ( ( isset($references['is_view_mode']) || $references['is_edit'] ) && $all_fields_validate && $fields_filled != count($properties_compounded)) ? 'display:none' : 'display:block' ) : 'display:none'; ?>">                                    <div class="col-md-11">
                                <?php foreach ($properties_compounded as $property_compounded): 
                                    if(!isset( $property_compounded['id']) || empty($property_compounded['id'])){
                                        continue;
                                    }
                                    $coumpounds_id[] = $property_compounded['id']; 
                                    $value = $this->get_value($object_id, $property['id'], $property_compounded, $i, $position,$references);
                                    ?>
                                    <input type="hidden" 
                                           class="form_autocomplete_value_<?php echo $property_compounded['id']; ?>_mask" 
                                            value="<?php echo ($property_compounded['metas']['socialdb_property_data_mask'] ) ? $property_compounded['metas']['socialdb_property_data_mask'] : '' ?>">
                                   <input   type="hidden" 
                                            id='core_validation_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>' 
                                            class='core_validation_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>' 
                                        <?php if(!$all_fields_validate && (!$property_compounded['metas']['socialdb_property_required'] || $property_compounded['metas']['socialdb_property_required'] == 'false')):  ?>
                                            value='true' validate_compound="false">
                                        <?php else: ?>
                                            value='<?php echo (!$value && $is_show_container) ? 'false' : 'true' ; ?>'>
                                        <?php endif; ?>
                                    <div style="padding-bottom: 15px; " class="col-md-12" id="only_field_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>">
                                        <p style="color: black;"><b><?php echo $property_compounded['name']; ?></b>
                                            <?php
                                               if ((!$property['metas']['socialdb_property_required'] || $property['metas']['socialdb_property_required'] == 'false') && $property_compounded['metas']['socialdb_property_required']&&$property_compounded['metas']['socialdb_property_required'] == 'true') {
                                            ?>
                                               <a id='required_field_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>' class="pull-right" 
                                                    style="margin-right: 15px;color:red;" >
                                                         <span class="glyphicon glyphicon-remove"  title="<?php echo __('This metadata is required!','tainacan')?>" 
                                                        data-toggle="tooltip" data-placement="top" ></span>
                                                </a>
                                                <a id='ok_field_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>' class="pull-right" style="display: none;margin-right: 15px;color:green;"  >
                                                         <span class="glyphicon glyphicon-ok" title="<?php echo __('Field filled successfully!','tainacan')?>" 
                                                        data-toggle="tooltip" data-placement="top" ></span>
                                                </a>
                                                <input  type="hidden" 
                                                        id='core_validation_<?php echo $property['id']; ?>' 
                                                        name='core_validation_<?php echo $property['id']; ?>' 
                                                        class="core_validation core_validation_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                                                <?php if(!$is_show_container): ?>
                                                      value='true'>
                                                <?php else: ?>     
                                                     value='<?php echo (!$value) ? 'false' : 'true' ; ?>'>
                                                <?php endif; ?>
                                                <input type="hidden" 
                                                         id='core_validation_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>_message'  
                                                         value='<?php echo sprintf(__('The field %s is required','tainacan'),$property['name']); ?>'>
                                                  <script> set_field_valid_compounds(<?php echo $property['id']; ?>,'core_validation_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>',<?php echo $property['id']; ?>)</script> 
                                            <?php  }  ?>
                                        </p>
                                        <?php 
                                        $val = (is_bool($value)) ? false : $value;
                                        if(isset($property_compounded['metas']['socialdb_property_data_widget'])): 
                                            $this->widget_property_data($property_compounded, $i,$references,$val);
                                        elseif(isset($property_compounded['metas']['socialdb_property_object_category_id'])): 
                                            $cardinality = $this->render_cardinality_property($property_compounded); 
                                            $this->widget_property_object($property_compounded, $i,$references,$val);
                                        elseif(isset($property_compounded['metas']['socialdb_property_term_widget'])): 
                                            $cardinality = $this->render_cardinality_property($property_compounded); 
                                            $this->widget_property_term($property_compounded, $i,$references,$val);
                                        endif; 
                                        ?>
                                        <input type="hidden" 
                                                    name="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>" 
                                                    id="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>"
                                                    value="<?php echo $cardinality; ?>"> 
                                    </div>
                                <?php $position++ ?>
                                <?php endforeach; ?>
                                </div>    
                                <?php if($i>0 && !isset($references['is_view_mode'])): ?>
                                <div class="col-md-1">
                                    <a style="cursor: pointer;" onclick="remove_container_compounds(<?php echo $property['id'] ?>,<?php echo $i ?>)" class="pull-right">
                                        <span class="glyphicon glyphicon-remove"></span>
                                    </a>
                                </div>    
                                <?php endif; ?>    
                               <?php  if($references['is_edit'] && !isset($references['is_view_mode'])): 
                                    $fields_filled =  $this->count_fields_container_value($object_id,$property,$property_compounded,$i+1);
                                    $count =  count($properties_compounded);
                                    if($all_fields_validate)
                                        echo ($val && $fields_filled == $count ) ? ''  : $this->render_button_cardinality($property,$i) ;
                                    else    
                                        echo ($val && ($this->is_set_container($object_id,$property,$property_compounded,$i+1))) ? ''  : $this->render_button_cardinality($property,$i) ?>     
                                <?php else: ?>    
                                    <?php echo ($is_show_container==1) ? ''  : (!isset($references['is_view_mode'])) ? $this->render_button_cardinality($property,$i) : '' ?>     
                                <?php endif; ?>        
                            </div>  
                        <?php endfor; ?>
                        <!--input type="hidden" 
                               name="cardinality_<?php echo $property['id']; ?>" 
                               id="cardinality_<?php echo $property['id']; ?>"
                               value="<?php echo $cardinality_bigger; ?>"--> 
                    </div>     
                </div>   
               <?php
            }
        ?>
        <input type="hidden" 
            name="properties_compounds" 
            id="properties_compounds"
            value="<?php echo implode(',', $result['ids']); ?>"> 
        <?php
        endif;    
    }
    
    /**
     * busca o widget para o os metadados de texto
     * @param array $property
     * @param int $i O indice do for da cardinalidade
     */
    public function widget_property_data($property,$i,$references,$value = false) {
        $references['properties_autocomplete'][] = $property['id'];
        if($references['is_view_mode'] 
                || (isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true' && !isset($references['operation']))){
            if(isset($value) && !empty($value)): ?>
                <p><?php  echo '<a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $value . "'" . ',' . $property['id'] . ')">' . $value . '</a>';  ?></p>
            <?php else: ?>
                <p><?php  _e('empty field', 'tainacan') ?></p>
            <?php endif;
            
            return;
        }
        // inputs
        if ($property['type'] == 'text') { ?>     
            <input type="text" 
                   id="compounds_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                   class="form-control form_autocomplete_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                   value="<?php if ($value) echo $value; ?>"
                   name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]">
        <?php }elseif ($property['type'] == 'textarea') {
                    if(has_filter("tainacan_show_reason_modal") && $property['name'] == "Motivo")
                    {
                        $disabled = "disabled";
                    }else $disabled = "";
            ?>   
            <textarea class="form-control form_autocomplete_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                      rows="10"
                      <?php echo $disabled ?>
                      id="compounds_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                      name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" ><?php if ($value) echo $value; ?></textarea>
        <?php }elseif ($property['type'] == 'numeric') { ?>   
            <input  type="text" 
                    class="form-control form_autocomplete_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                    onkeypress='return onlyNumbers(event)'
                    id="compounds_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                    name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
                    value="<?php if ($value) echo $value; ?>">
        <?php }elseif ($property['type'] == 'autoincrement') { ?>   
            <input disabled="disabled"  
                   type="number" 
                   class="form-control" 
                   name="hidded_<?php echo $property['id']; ?>" 
                   value="<?php if ($value) echo $value; ?>">
        <?php } else if ($property['type'] == 'date' && !has_action('modificate_edit_item_properties_data')) { ?>
            <script>
               $(function() {
                   $( "#compounds_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>" ).datepicker({
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
               class="input_date form_autocomplete_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
               value="<?php if ($value) echo $value; ?>"
               type="text" 
               id="compounds_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
               name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]">   
        <?php
        }
        // gancho para tipos de metadados de dados diferentes
        else if (has_action('modificate_edit_item_properties_data')) {
            $property['contador'] = $i;
            $property['operation'] = 'edit';
            $property['object_id'] = $object_id;
            $property['compound_id'] = $references['compound_id'];
            $property['name_field'] = 'socialdb_property_'. $references['compound_id'].'_'. $property['id'].'_'. $i.'[]';
            do_action('modificate_edit_item_properties_data', $property);
            //return false;
        } else {
            ?>
            <input type="text" 
                   id="compounds_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                   value="<?php if ($value) echo $value; ?>"
                   class="form-control form_autocomplete_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                   name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" >
        <?php
        }
    }
    /**
     * busca o widget para o os metadados de termo
     * @param array $property
     * @param int $i O indice do for da cardinalidade
     */
    public function widget_property_object($property,$i,$references,$value = false) {
        if($references['is_view_mode'] || (isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true' && !isset($references['operation']))){
            if(isset($value)): 
                if(is_array($value) && $value[$i])  
                    $val = $value[$i];
                else
                     $val = $value; 
             ?>
             <div id="labels_<?php echo $property['id']; ?>_<?php echo $object_id; ?>">
                <?php if (!empty($property['metas']['objects']) && !empty($val)) { ?>
                    <?php foreach ($property['metas']['objects'] as $object) { // percoro todos os objetos  ?>
                        <?php
                        if (isset($val) && !empty($val) && $object->post_status == 'publish' && ((is_array($val) && in_array($object->ID, $val) ) || ($object->ID == $val) )): // verifico se ele esta na lista de objetos da colecao
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
            <?php endif;
            
            return;
        }
        // inputs
        if($value){
            $property['metas']['value'] = (is_array($value)) ? $value : [$value];
        }
        $this->generateWidgetPropertyRelatedCompound($property, $references['compound_id'], 0,$references['compound_id'],$i);
        ?>   
        <?php
    }
    
    /**
     * busca o widget para o os metadados de relacionamento
     * @param array $property
     * @param int $i O indice do for da cardinalidade
     */
    public function widget_property_term($property,$i,$references,$value = false) {
        ?>
        <input 
            type="hidden" 
            id='actual_value_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'
            value="<?php if ($value) echo $value; ?>">
        <?php
        if($references['is_view_mode'] || (isset($property['metas']['socialdb_property_locked']) && $property['metas']['socialdb_property_locked'] == 'true' && !isset($references['operation']))){
                if($property['metas']['socialdb_property_term_cardinality'] && $property['metas']['socialdb_property_term_cardinality'] == '1' && is_numeric($value)):
                    ?>
                    <div id='label_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'>
                         <?php echo get_term_by('id', $value,'socialdb_category_type')->name ?>
                    </div>  
                    <?php
                else:  
                    ?>
                    <div id='label_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'>
                         <?php $this->get_category_value($references['object_id'],$property['id'],$property['metas']['socialdb_property_term_root']); ?>
                    </div>  
                    <?php
                endif;
            return;
        }
        if ($property['type'] == 'radio') {
            $references['properties_terms_radio'][] = $property['id'];
            ?>
            <div id='field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'></div>
            <input type="hidden" 
                       id='actual_field_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'
                       name="actual_field_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
                       value="<?php if ($value) echo $value; ?>">
            <?php
        } elseif ($property['type'] == 'tree') {
            $references['properties_terms_tree'][] = $property['id'];
            ?>
            <button type="button"
                onclick="showModalFilters('add_category',
                            '<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 
                                    'socialdb_category_type')->name ?>',
                                    <?php echo $property['metas']['socialdb_property_term_root'] ?>,
                                                'field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>')" 
                class="btn btn-primary btn-xs"><?php _e('Add Category','tainacan'); ?>
            </button>
            <br><br>
            <div class="row">
                <div style='height: 150px;' 
                     class='col-lg-12'  
                     id='field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'>
                </div>
                <input type="hidden" 
                       id='field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'
                       name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
                       value="<?php if ($value) echo $value; ?>">
            </div>
            <?php
        }elseif ($property['type'] == 'selectbox') {
            $references['properties_terms_selectbox'][] = $property['id'];
            ?>
            <select class="form-control" 
                    name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
                    onchange="compounds_validate_selectbox(this,'<?php echo $property['id']; ?>','<?php echo $references['compound_id']; ?>','<?php echo $i ?>')"
                    id='field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>' >
            </select>
            <?php
        }elseif ($property['type'] == 'checkbox') {
            $references['properties_terms_checkbox'][] = $property['id']; ?>
            <div id='field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'></div>
            <?php
        } elseif ($property['type'] == 'multipleselect') {
            $references['properties_terms_multipleselect'][] = $property['id'];
            ?>
             <select size='1' 
                multiple 
                onclick="compounds_validate_multipleselectbox(this,'<?php echo $property['id']; ?>','<?php echo $references['compound_id']; ?>','<?php echo $i ?>');"
                class="form-control field_property_term_<?php echo $property['id']; ?>" 
                id="field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
                <?php 
                if ($property['metas']['socialdb_property_required'] == 'true'): 
                    echo 'required="required"';
                endif;
                ?>>
             </select>
                    <?php
        }elseif ($property['type'] == 'tree_checkbox') {
            $references['properties_terms_treecheckbox'][] = $property['id']; ?>
            <button type="button"
                onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',
                <?php echo $property['metas']['socialdb_property_term_root'] ?>,'field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>')" 
                class="btn btn-primary btn-xs"><?php _e('Add Category','tainacan'); ?>
            </button>
            <br><br>
            <div class="row">
                <div style='height: 150px;' 
                     class='col-lg-12'  
                     id='field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'>
                </div>
                <div id='socialdb_propertyterm_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>' ></div>
            </div>
            <?php
        }
    }
    
    /**
     * 
     * @param type $item_id
     * @param type $compound_id
     * @param type $property
     * @param type $i
     * @return string/boolean
     */
    public function get_value($item_id,$compound_id,$property,$i,$position,$references = []) {
        if(isset($references['operation']) && $references['operation'] == 'add'){
            return false;
        }
        $values = get_post_meta($item_id,'socialdb_property_'.$compound_id.'_'.$i,true);
        if($values&&$values!=''){
            $values = explode(',', $values);
            $value = $values[$position];
            if(strpos($value, '_cat')!==false){
                return str_replace('_cat', '', $value);
            }else {
                $object = get_metadata_by_mid('post', $value);;
                if(is_object($object)){
                    return $object->meta_value;
                }else if(isset ($property['metas']['value']) && is_array($property['metas']['value']) && $property['metas']['value'][$i] && get_post( $property['metas']['value'][$i])){
                     return $property['metas']['value'][$i];
                }else{
                    return false;
                }
            }
        }else{
            if($i===0){
                return false;
            }else{
                return true;
            }
        }
    }
    
    /**
     * 
     * @param type $values
     * @param type $item_id
     * @param type $compound
     * @param type $property
     * @param type $i
     */
    public function hasNoValues($values,$item_id,$compound,$property,$i) {
        $values = explode(',', $values);
        $emptyFields = 0;
        $count = count($values);
        foreach ($values as $value) {
            if(strpos($value, '_cat')!==false){
                if(!get_term_by('id',str_replace('_cat', '', $value),'socialdb_category_type'))
                      $emptyFields++;  
            }else {
                $object = get_metadata_by_mid('post', $value);
                if(!is_object($object) || $object->meta_value ==='')
                    $emptyFields++;
            }
        }
        
        if($count==$emptyFields){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 
     * @param type $item_id
     * @param type $compound_id
     * @param type $property
     * @param type $i
     * @return boolean|int
     */
    public function is_set_container($item_id,$compound,$property,$i) {
        $compound_id = $compound['id'];
        $values = get_post_meta($item_id,'socialdb_property_'.$compound_id.'_'.$i,true);
        if($values&&$values!=''&& !$this->hasNoValues($values,$item_id,$compound,$property,$i) ){
            if($this->has_next_compound($item_id, $compound,($i+1))){
                return 1;
            }else{
                return 2;  
            }
        }else{
            if($i==0){
                return 3;
            }else{
                return false;
            }
            
        }
    }
    
    public function count_fields_container_value($item_id,$compound,$property,$i) {
        $compound_id = $compound['id'];
        $values = get_post_meta($item_id,'socialdb_property_'.$compound_id.'_'.$i,true);
        if($values&&$values!=''){
            $array = array_filter(explode(',', $values));
            return count($array);
        }
        return 0;
    }
    
    /**
     * verifico se existe algum valor nas proximas
     */
    public function has_next_compound($item_id,$compound,$i) {
       $max = $this->render_cardinality_property($compound);
       while($i<$max){
           $value = get_post_meta($item_id,'socialdb_property_'.$compound['id'].'_'.$i,true);
           $values = array_filter( explode(',', $value) );
           if(count($values)>0){
               return true;
           }
           $i++;
       }
       return false;
    }
    
    /**
     * metodo de propriedade de categorias
     * 
     * @param type $properties_compounds
     * @param type $object_id
     * @param type $references
     */
    public function list_properties_categories_compounds($properties_compounds,$object_id,$references) {
        include_once( dirname(__FILE__).'/../../views/object/append_properties_categories/js/pc_compounds_js.php');
        $result = [];
        $coumpounds_id = [];
        $property = $properties_compounds;
        if(is_array($references['properties_to_avoid'])&&in_array($property['id'], $references['properties_to_avoid'])) {
             return '';
         }

        $result['ids'][] = $property['id'];
        $references['compound_id'] = $property['id'];
        if(isset($references['is_view_mode']))
             echo '<script> $(".glyphicon").hide() </script>'
        ?>
        <div id="meta-item-<?php echo $property['id']; ?>"  class="form-group">
            <h2>
                <?php echo $property['name']; ?>
                <?php
                if(has_action('modificate_label_insert_item_properties')):
                    do_action('modificate_label_insert_item_properties', $property);
                endif;
                //acao para modificaco da propriedade de objeto na insercao do item
                if(has_action('modificate_insert_item_properties_compounds')):
                    do_action('modificate_insert_item_properties_compounds',$property,$object_id,'property_value_'. $property['id'] .'_'.$object_id.'_add');
                endif;
                   $this->generateValidationIcons($property, true);
                   $all_fields_validate =  $property['metas']['socialdb_property_required']&&$property['metas']['socialdb_property_required'] != 'false';
                ?>
            </h2>
            <?php $cardinality_bigger = $this->render_cardinality_property($property);   ?>
            <?php $properties_compounded = $property['metas']['socialdb_property_compounds_properties_id']; ?>
            <?php //$class = 'col-md-'. (12/count($properties_compounded)); ?>
            <div class="form-group" style="margin-bottom: 15px; margin-right: 15px;margin-left: 15px;">
                <input  type="hidden" id='main_compound_id'
                        value='<?php echo $references['compound_id'] ?>'>
                <?php if( $property['metas']['socialdb_property_required'] == 'true_one_field'): ?> 
                <input  type="hidden" 
                        id='type_required_<?php echo $references['compound_id'] ?>' 
                        value='<?php echo $property['metas']['socialdb_property_required'] ?>'> 
                <input  type="hidden" 
                        id='count_fields_<?php echo $references['compound_id'] ?>' 
                        value='<?php echo count($properties_compounded) ?>'>
                <?php endif; ?> 
                <?php 
                $coumpounds_id = []; 
                    foreach ($properties_compounded as $property_compounded): 
                            if(!isset( $property_compounded['id']) || empty($property_compounded['id'])){
                                continue;
                            }
                            $coumpounds_id[] = $property_compounded['id']; 
                    endforeach;    ?>
                <input type="hidden" 
                       name="compounds_<?php echo $property['id']; ?>" 
                       id="compounds_<?php echo $property['id']; ?>"
                       value="<?php echo implode(',', array_unique($coumpounds_id)); ?>">      
                <input type="hidden"
                       name="cardinality_<?php echo $property['id']; ?>"
                       id="cardinality_<?php echo $property['id']; ?>"
                       value="<?php echo $cardinality_bigger; ?>">
                <?php $coumpounds_id = []; ?>
                <?php for($i = 0; $i<$cardinality_bigger;$i++):
                    $is_show_container =  $this->is_set_container($object_id,$property,$property_compounded,$i);
                    $fields_filled =  $this->count_fields_container_value($object_id,$property,$property_compounded,$i);
                    $position = 0;
                    ?>
                    <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                         class="col-md-12 no-padding" 
                         style="border-color: #ccc;
                         <?php echo ($is_show_container) ? ( ( ( isset($references['is_view_mode']) || $references['is_edit'] ) && $all_fields_validate && $fields_filled != count($properties_compounded)) ? 'display:none' : 'display:block' ) : 'display:none'; ?>">
                        <div class="col-md-12 no-padding">
                            <?php foreach ($properties_compounded as $property_compounded):
                                $coumpounds_id[] = $property_compounded['id'];
                                $value = $this->get_value($object_id, $property['id'], $property_compounded, $i, $position,$references);
                                if(isset($property_compounded['metas']['socialdb_property_object_category_id']))
                                                    $value = $property_compounded['metas']['value'];
                                ?>
                                <input type="hidden" 
                                        class="form_autocomplete_value_<?php echo $property_compounded['id']; ?>_mask" 
                                        value="<?php echo ($property_compounded['metas']['socialdb_property_data_mask'] ) ? $property_compounded['metas']['socialdb_property_data_mask'] : '' ?>">
                                <input  type="hidden" 
                                        id='core_validation_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>' 
                                        class='core_validation_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>' 
                                    <?php if(!$all_fields_validate && (!$property_compounded['metas']['socialdb_property_required'] || $property_compounded['metas']['socialdb_property_required'] == 'false')):  ?>
                                        value='true' validate_compound="false">
                                    <?php else: ?>
                                        value='<?php echo (!$value) ? 'false' : 'true' ; ?>'>
                                    <?php endif; ?>
                                <div style="margin-bottom: 15px; border-bottom: 1px solid #e8e8e8" class="col-md-12" id="only_field_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>">
                                    <p style="color: black;"><?php echo $property_compounded['name']; ?>
                                        <?php
                                            if ((!$property['metas']['socialdb_property_required'] || $property['metas']['socialdb_property_required'] == 'false') && $property_compounded['metas']['socialdb_property_required']&&$property_compounded['metas']['socialdb_property_required'] == 'true') {
                                                 ?>
                                            <a id='required_field_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>' class="pull-right" 
                                                 style="margin-right: 15px;color:red;" >
                                                      <span class="glyphicon glyphicon-remove"  title="<?php echo __('This metadata is required!','tainacan')?>" 
                                                     data-toggle="tooltip" data-placement="top" ></span>
                                             </a>
                                             <a id='ok_field_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>' class="pull-right" style="display: none;margin-right: 15px;color:green;"  >
                                                      <span class="glyphicon glyphicon-ok" title="<?php echo __('Field filled successfully!','tainacan')?>" 
                                                     data-toggle="tooltip" data-placement="top" ></span>
                                             </a>
                                             <input  type="hidden" 
                                                     id='core_validation_<?php echo $property['id']; ?>' 
                                                     name='core_validation_<?php echo $property['id']; ?>' 
                                                     class='core_validation core_validation_<?php echo $property['id']; ?>_<?php echo $i; ?>' 
                                                     value='<?php echo (!$value) ? 'false' : 'true' ; ?>'>
                                             <input type="hidden" 
                                                      id='core_validation_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>_message'  
                                                      value='<?php echo sprintf(__('The field %s is required','tainacan'),$property['name']); ?>'>
                                               <script> set_field_valid_compounds(<?php echo $property['id']; ?>,'core_validation_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>',<?php echo $property['id']; ?>)</script> 
                                         <?php  }  ?>
                                    </p>
                                    <?php
                                    $val = (is_bool($value)) ? false : $value;
                                    if(isset($property_compounded['metas']['socialdb_property_data_widget'])):
                                        $this->widget_property_data($property_compounded, $i,$references,$val);
                                    elseif(isset($property_compounded['metas']['socialdb_property_object_category_id'])):
                                        $cardinality = $this->render_cardinality_property($property_compounded);
                                        $this->widget_property_object($property_compounded, $i,$references,$val);
                                    elseif(isset($property_compounded['metas']['socialdb_property_term_widget'])):
                                        $cardinality = $this->render_cardinality_property($property_compounded);
                                        $this->widget_property_term($property_compounded, $i,$references,$val);
                                    endif;
                                    ?>
                                    <input type="hidden" name="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>"
                                           id="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>"
                                           value="<?php echo $cardinality; ?>">
                                </div>
                                <?php $position++ ?>
                            <?php endforeach; ?>
                                </div>    
                        <?php if($i>0 && !isset($references['is_view_mode'])): ?>
                            <div class="col-md-1">
                                <a style="cursor: pointer;" onclick="remove_container_compounds(<?php echo $property['id'] ?>,<?php echo $i ?>)" class="pull-right">
                                    <span class="glyphicon glyphicon-remove"></span>
                                </a>
                            </div>
                        <?php endif; ?>
                            <?php  if($references['is_edit'] && !isset($references['is_view_mode'])): 
                                    $fields_filled =  $this->count_fields_container_value($object_id,$property,$property_compounded,$i+1);
                                    $count =  count($properties_compounded);
                                    if($all_fields_validate)
                                        echo ($val && $fields_filled == $count ) ? ''  : $this->render_button_cardinality($property,$i) ;
                                    else    
                                        echo ($val && ($this->is_set_container($object_id,$property,$property_compounded,$i+1))) ? ''  : $this->render_button_cardinality($property,$i) ?>     
                           <?php else: ?>    
                               <?php echo ($is_show_container==1) ? ''  : (!isset($references['is_view_mode'])) ? $this->render_button_cardinality($property,$i) : '' ?>     
                           <?php endif; ?>        
                    </div>
                <?php endfor; ?>
                <!--input type="hidden"
                       name="compounds_<?php echo $property['id']; ?>"
                       id="compounds_<?php echo $property['id']; ?>"
                       value="<?php echo implode(',', array_unique($coumpounds_id)); ?>"-->
                <!--input type="hidden"
                       name="cardinality_<?php echo $property['id']; ?>"
                       id="cardinality_<?php echo $property['id']; ?>"
                       value="<?php echo $cardinality_bigger; ?>"-->
                <?php //$coumpounds_id = []; ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * metodo que retorna o html
     * 
      * @param type $property
     */
    public function search_related_properties_to_search($property,$collection_id){
        $propertyModel = new PropertyModel;
        $property_data = [];
        $property_object = [];
        $property_term = [];
        $property_compounds = [];
        $properties = $property['metas']["socialdb_property_to_search_in"];
        if(isset($properties) && $properties != ''){
            $properties = explode(',', $properties);
            foreach ($properties as $property_related) {
                $property_related = $propertyModel->get_all_property($property_related, true);
                if($property_related['id'] == $this->terms_fixed['title']->term_id):    
                    $has_title = true;
                elseif(isset($property_related['metas']['socialdb_property_data_widget'])): 
                    $property_data[] = $property_related;
                elseif(isset($property_related['metas']['socialdb_property_object_category_id'])): 
                    $property_object[] = $property_related;
                elseif(isset($property_related['metas']['socialdb_property_term_widget'])): 
                    $property_term[] = $property_related;
                elseif(isset($property_related['metas']['socialdb_property_compounds_properties_id'])): 
                    $all_values = [];
                    $values = explode(',', $property_related['metas']['socialdb_property_compounds_properties_id']);
                    foreach ($values as $value) {
                        $all_values[] = $propertyModel->get_all_property($value, true);
                    }
                    $property_related['metas']['socialdb_property_compounds_properties_id'] = $all_values;
                    $property_compounds[] = $property_related;
                endif; 
            }
        }
        include dirname(__FILE__).'/../../views/advanced_search/search_property_object_metadata.php';
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
                    $title_labels[] =  $this->terms_fixed['title']->name;
                }
            }
        }
        return implode('/', $title_labels);
    }

    /**
     * 
     * @param type $property
     * @param type $object_id
     * @param type $collection_id
     */
    public function generateWidgetPropertyRelated($property,$object_id,$collection_id) {
        ?>
        <div class="metadata-related">
            <h6><b><?php _e('Related items', 'tainacan') ?></b></h6>
            <?php $this->insert_button_add_other_collection($property, $object_id, $collection_id) ?>
            <span id="no_results_property_<?php echo $property['id']; ?>">
                 <?php if (!isset($property['metas']['value']) || empty($property['metas']['value']) || !is_array($property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao   ?>    
                    <input type="text" 
                           disabled="disabled"
                           placeholder="<?php _e('No registers', 'tainacan') ?>"
                           class="form-control" >
                <?php endif; ?>
            </span>
            <span id="results_property_<?php echo $property['id']; ?>">
                <ul>
                    <?php if((!isset($i) || empty($i)) && !empty($property['metas']['value']) && is_array($property['metas']['value'])):  
                        $property['metas']['value'] = array_unique($property['metas']['value']);
                        foreach ($property['metas']['value'] as $id): ?>
                             <li id="inserted_property_object_<?php echo $property['id']; ?>_<?php echo $id; ?>" 
                                 item="<?php echo $id; ?>" class="selected-items-property-object property-<?php echo $property['id']; ?>">
                                     <?php echo get_post($id)->post_title; ?>
                                 <span  onclick="$('#inserted_property_object_<?php echo $property['id']; ?>_<?php echo $id; ?>').remove();$('select[name=socialdb_property_<?php echo $property['id']; ?>[]]  option[value=<?php echo $id; ?>]').remove()" 
                                        style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span>
                             </li>       
                        <?php endforeach; ?>    
                    <?php elseif (isset($i) && isset($property['metas']['value']) && !empty($property['metas']['value']) && is_array($property['metas']['value']) && $property['metas']['value'][$i]): // verifico se ele esta na lista de objetos da colecao   ?>    
                        <?php  
                        //$property['metas']['value'] = array_unique($property['metas']['value']);
                        $id = $property['metas']['value'][$i];
                        //foreach ($property['metas']['value'] as $id): ?>
                             <li id="inserted_property_object_<?php echo $property['id']; ?>_<?php echo $id; ?>" 
                                 item="<?php echo $id; ?>" class="selected-items-property-object property-<?php echo $property['id']; ?>">
                                     <?php echo get_post($id)->post_title; ?>
                                 <span  onclick="$('#inserted_property_object_<?php echo $property['id']; ?>_<?php echo $id; ?>').remove();$('select[name=socialdb_property_<?php echo $property['id']; ?>[]]  option[value=<?php echo $id; ?>]').remove()" 
                                        style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span>
                             </li>       
                        <?php //endforeach; ?>    
                   <?php endif; ?>
                </ul>
            </span>
            <select 
                id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_add" 
                multiple 
                style="display: none;" 
                    name="socialdb_property_<?php echo $property['id']; ?>[]" 
                >   
                 <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && is_array($property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao   ?>    
                        <?php foreach ($property['metas']['value'] as $id): ?>
                        <option selected="selected" value="<?php echo $id; ?>"><?php echo $id; ?></option>
                        <?php endforeach; ?>    
               <?php endif; ?>
            </select>
            <input type="hidden" 
                   id="cardinality_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"  
                   value="<?php echo $this->render_cardinality_property($property); ?>"> 
            <button class="btn  btn-lg btn-primary btn-primary pull-right"
                    type="button"
                    onclick="$('#metadata-search-<?php echo $property['id']; ?>').show();$('#metadata-result-<?php echo $property['id']; ?>').hide();$(this).hide()"
                    ><?php _e('Add', 'tainacan') ?></button>
        </div>
        <div class="metadata-search"
             id="metadata-search-<?php echo $property['id']; ?>"
             style="display:none"
             >
                 <?php $this->search_related_properties_to_search($property, $collection_id); ?>     
        </div>
        <div class="metadata-matching"
             style="display:none"
             id="metadata-result-<?php echo $property['id']; ?>" >
        </div>   
        <?php    
    }
    
    /**
     * 
     * @param type $property
     * @param type $object_id
     * @param type $collection_id
     * @param type $compound_id
     * @param type $i
     */
    public function generateWidgetPropertyRelatedCompound($property,$object_id,$collection_id,$compound_id,$i) {
        $property['compound_id'] = $compound_id;
        $property['contador'] = $i;
        ?>
        <div class="metadata-related">
            <h6><b><?php _e('Related items', 'tainacan') ?></b></h6>
            <?php $this->insert_button_add_other_collection($property, $object_id, $collection_id) ?>
            <span id="no_results_property_<?php echo $compound_id; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>">
                 <?php if (!isset($property['metas']['value']) || empty($property['metas']['value']) || !is_array($property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao   ?>    
                    <input type="text" 
                           disabled="disabled"
                           placeholder="<?php _e('No registers', 'tainacan') ?>"
                           class="form-control" >
                <?php endif; ?>
            </span>
            <span id="results_property_<?php echo $compound_id; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>">
                <ul>
                    <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && is_array($property['metas']['value']) && $property['metas']['value'][$i]): // verifico se ele esta na lista de objetos da colecao   ?>    
                        <?php  
                        //$property['metas']['value'] = array_unique($property['metas']['value']);
                        $id = $property['metas']['value'][$i];
                        //foreach ($property['metas']['value'] as $id): ?>
                             <li id="inserted_property_object_<?php echo $compound_id ?>_<?php echo $property['id'] ?>_<?php echo $i ?>_<?php echo $id; ?>" 
                                 item="<?php echo $id; ?>" class="selected-items-property-object property-<?php echo $property['id']; ?>">
                                     <?php echo get_post($id)->post_title; ?>
                                 <span  onclick="$('#inserted_property_object_<?php echo $compound_id ?>_<?php echo $property['id'] ?>_<?php echo $i ?>_<?php echo $id; ?>').remove();$('select[name=socialdb_property_<?php echo $property['id']; ?>[]]  option[value=<?php echo $id; ?>]').remove()" 
                                        style="cursor:pointer;" class="pull-right glyphicon glyphicon-trash"></span>
                             </li>       
                        <?php// endforeach; ?>    
                   <?php endif; ?>
                </ul>
            </span>
            <select 
                id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_<?php echo $i; ?>" 
                multiple 
                style="display: none;" 
                name="socialdb_property_<?php echo $compound_id; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
                >   
                 <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && is_array($property['metas']['value']) && $property['metas']['value'][$i]): // verifico se ele esta na lista de objetos da colecao   ?>    
                       <?php  
                        $property['metas']['value'] = array_unique($property['metas']['value']);
                        $id = $property['metas']['value'][$i];
                        //foreach ($property['metas']['value'] as $id): ?>
                        <option selected="selected" value="<?php echo $id; ?>"><?php echo $id; ?></option>
                        <?php //endforeach; ?>    
               <?php endif; ?>
            </select>
             <input type="hidden" 
                        id="cardinality_<?php echo $compound_id; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>"  
                        value="<?php echo $this->render_cardinality_property($property);   ?>">   
            <button class="btn  btn-lg btn-primary btn-primary pull-right"
                    type="button"
                    onclick="$('#metadata-search-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>').show();$('#metadata-result-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>').hide();$(this).hide()"
                    ><?php _e('Add', 'tainacan') ?></button>
        </div>
        <div class="metadata-search"
             id="metadata-search-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>"
             style="display:none"
             >
                 <?php $this->search_related_properties_to_search($property, $collection_id); ?>     
        </div>
        <div class="metadata-matching"
             style="display:none"
             id="metadata-result-<?php echo $compound_id; ?>-<?php echo $property['id']; ?>-<?php echo $i; ?>" >
        </div>   
        <?php    
    }
    
    
    public function insert_button_add_other_collection($property,$object_id,$collection_id) {
        // botao que leva a colecao relacionada
            if (isset($property['metas']['collection_data'][0]->post_title) 
                    && ( isset($property['metas']['socialdb_property_habilitate_new_item']) && $property['metas']['socialdb_property_habilitate_new_item'] == 'true')):  ?>
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
                                 style="margin-bottom: 13px;"
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
    }
    
    /**
     * 
     * @param type $property_id
     * @param type $item_id
     */
    public function is_selected_property($property_id,$item_id) {
        global $wpdb;
        $wp_posts = $wpdb->prefix . "posts";
        $wp_postmeta = $wpdb->prefix . "postmeta";
        if ($meta_key == '') {
            $meta_key = 'socialdb_property_' . $property_id;
        }
        $query = "
                        SELECT pm.* FROM $wp_posts p
                        INNER JOIN $wp_postmeta pm ON p.ID = pm.post_id    
                        WHERE p.post_status LIKE 'publish' and pm.meta_key like '$meta_key' and pm.meta_value LIKE '%{$item_id}%'
                ";
        $result = $wpdb->get_results($query);
        if ($result && is_array($result) && count(array_filter($result)) > 0) {
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * 
     */
    public function get_category_value($object_id,$property_id,$parent) {
        $has_value = false;
        $terms = wp_get_post_terms( $object_id, 'socialdb_category_type' );
        if($terms && is_array($terms)){
            foreach ($terms as $term) {
                $hierarchy = get_ancestors($term->term_id, 'socialdb_category_type');
                if(is_array($hierarchy) && in_array($parent, $hierarchy)){
                    $has_value = true;
                    ?>
                    <input type="hidden" name="socialdb_propertyterm_<?php echo $property_id; ?>[]" value="<?php echo $term->term_id ?>">
                    <p>
                       <a style="cursor:pointer;" onclick="wpquery_term_filter('<?php echo $term->term_id ?>','<?php echo $property_id  ?>')">
                           <?php echo $term->name  ?>
                       </a>
                    </p><br>
                    <script>
                        setTimeout(function(){
                            append_category_properties('<?php echo $term->term_id ?>',0,'<?php echo $property_id ?>');
                        }, 3000);
                    </script>
                    <?php   
                }
            }
        }
        if(!$has_value){
            ?>
                <p><?php  _e('empty field', 'tainacan') ?></p>
            <?php
        }
    }
}