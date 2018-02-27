<?php
/*
 * Object Controller's view helper 
 * */
include_once dirname(__FILE__).'/../view_helper.php';
class ObjectSingleWidgetsHelper extends ViewHelper {

    public function appendNewContainer(PropertyModel $modelProperty,$object_id,$compound_id,$index){
        $properties_terms_radio = [];
        $properties_terms_tree = [];
        $properties_terms_selectbox = [];
        $properties_terms_checkbox = [];
        $properties_terms_multipleselect = [];
        $properties_terms_treecheckbox = [];
        //referencias
                $references = [
                    'properties_terms_radio' => &$properties_terms_radio,
                    'properties_terms_checkbox' => &$properties_terms_checkbox,
                    'properties_terms_tree' => &$properties_terms_tree,
                    'properties_terms_selectbox' => &$properties_terms_selectbox,
                    'properties_terms_multipleselect' => &$properties_terms_multipleselect,
                    'properties_terms_treecheckbox' => &$properties_terms_treecheckbox
                ];
        $references['compound_id'] = $compound_id;
        $property = $modelProperty->get_all_property($compound_id,true);
        $i = $index;
        $properties_compounded = explode(',',$property['metas']['socialdb_property_compounds_properties_id']);
        ?>
        <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>"
             class="col-md-12 no-padding"
             style="border-style: solid;border-width: 1px;border-color: #ccc; padding: 10px;margin-bottom: 5px;"
             data-edit-required=""
        >
            <div class="col-md-1 no-padding">
                <div style="display: none;" class="pull-right compounds_buttons_<?php echo $property['id']; ?> ">
                    <button type="button"
                            style="margin-bottom: 5px;"
                            title="Limpar campos"
                            onclick="clear_compounds(<?php echo $object_id ?>,<?php echo $property['id'] ?>,<?php echo $i ?>)"
                            class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-erase"></span>
                    </button>
                    <button type="button"
                            onclick="save_compounds(<?php echo $object_id ?>,<?php echo $property['id'] ?>,<?php echo $i ?>)"
                            class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-floppy-disk"></span>
                    </button>
                </div>
            </div>
            <div class="col-md-11">
                <?php foreach ($properties_compounded as $property_compounded):
                    if(!is_numeric($property_compounded)){
                        continue;
                    }
                    $property_compounded = $modelProperty->get_all_property($property_compounded,true);
                    $coumpounds_id[] = $property_compounded['id'];
                    $value = $this->get_value($object_id, $property['id'], $property_compounded['id'], $i, $position);
                    ?>
                    <input  type="hidden"
                            id='core_validation_<?php echo $property['id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>'
                            class='core_validation_compounds_<?php echo $property['id']; ?>'
                            value='<?php echo (!$value) ? 'false' : 'true' ; ?>'>
                    <div style="padding-bottom: 15px;border: none;background: white !important; " class="col-md-12">
                        <p style="color: black;"><?php echo $property_compounded['name']; ?></p>
                        <input type="hidden"
                               name="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>"
                               id="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>"
                               value="<?php //echo $cardinality; ?>">

                        <?php
                        $val = (is_bool($value)) ? false : $value;
                        if(isset($property_compounded['metas']['socialdb_property_data_widget'])):
                            ?>
                            <div class="compounds_fields_text_<?php echo $property['id']; ?>">
                                <?php echo ($val) ? '<b><a style="cursor:pointer;" onclick="wpquery_link_filter(' . "'" . $val . "'" . ',' . $property['id'] . ')"  >'.$val.'</a></b>' : '<button type="button" onclick="edit_compounds_property('. $property['id'] .', '.$object_id.')" class="btn btn-default btn-xs">'.__('Empty field!','tainacan').'</button>' ?>
                            </div>
                            <div style="display: none;" class="compounds_fields_value_<?php echo $property['id']; ?>">
                                <?php
                                $this->widget_property_data($property_compounded, $i,$references,$val);
                                ?>
                            </div>
                            <?php
                        elseif(isset($property_compounded['metas']['socialdb_property_object_category_id'])):
                            ?>
                            <div class="compounds_fields_text_<?php echo $property['id']; ?>">
                                <?php echo ($val) ? '<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' . "'" . $val . "'" . ',' . $property['id'] . ')" >'.get_post($val)->post_title.'</a></b>' : '<button type="button" onclick="edit_compounds_property('. $property['id'] .', '.$object_id.')" class="btn btn-default btn-xs">'.__('Empty field!','tainacan').'</button>' ?>
                            </div>
                            <div style="display: none;" class="compounds_fields_value_<?php echo $property['id']; ?>">
                                <?php

                                $this->widget_property_object($property_compounded, $i,$references,$val);
                                ?>
                            </div>
                            <?php
                        elseif(isset($property_compounded['metas']['socialdb_property_term_widget'])):
                            ?>
                            <div class="compounds_fields_text_<?php echo $property['id']; ?>">
                                <?php echo ($val) ? '<b><a style="cursor:pointer;" onclick="wpquery_term_filter(' . "'" . $val . "'" . ',' . $property['id'] . ')" >'.get_term_by('id',$val,'socialdb_category_type')->name.'</a></b>' : '<button onclick="edit_compounds_property('. $property['id'] .', '.$object_id.')" type="button" class="btn btn-default btn-xs">'.__('Empty field!','tainacan').'</button>' ?>
                            </div>
                            <div style="display: none;" class="compounds_fields_value_<?php echo $property['id']; ?>">
                                <?php
                                $this->widget_property_term($property_compounded, $i,$references,$val);
                                ?>
                            </div>
                            <?php
                        endif;
                        ?>
                    </div>
                    <?php $position++ ?>
                <?php endforeach; ?>
            </div>
        </div>
        <input type="hidden" name="properties_terms_radio" id='append_properties_terms_radio_<?php echo $property['id']; ?>_<?php echo $i; ?>' value="<?php echo implode(',', array_unique($properties_terms_radio)); ?>">
        <input type="hidden" name="properties_terms_tree" id='append_properties_terms_tree_<?php echo $property['id']; ?>_<?php echo $i; ?>' value="<?php echo implode(',', array_unique($properties_terms_tree)); ?>">
        <input type="hidden" name="properties_terms_selectbox" id='append_properties_terms_selectbox_<?php echo $property['id']; ?>_<?php echo $i; ?>' value="<?php echo implode(',', array_unique($properties_terms_selectbox)); ?>">
        <input type="hidden" name="properties_terms_checkbox" id='append_properties_terms_checkbox_<?php echo $property['id']; ?>_<?php echo $i; ?>' value="<?php echo implode(',', array_unique($properties_terms_checkbox)); ?>">
        <input type="hidden" name="properties_terms_multipleselect" id='append_properties_terms_multipleselect_<?php echo $property['id']; ?>_<?php echo $i; ?>' value="<?php echo implode(',', array_unique($properties_terms_multipleselect)); ?>">
        <input type="hidden" name="properties_terms_treecheckbox" id='append_properties_terms_treecheckbox_<?php echo $property['id']; ?>_<?php echo $i; ?>' value="<?php echo implode(',', array_unique($properties_terms_treecheckbox)); ?>">
        <script>
            initializeTerms('<?php echo $property['id']; ?>','<?php echo $i; ?>');
        </script>
        <?php
    }
    /**
     * 
     * @param array $properties_compounds
     */
    public function list_properties_compounds($properties_compounds,$object_id,$references, $collumns_to_show = 6) {
        include_once ( dirname(__FILE__).'/../../views/object/single_object/js/single_properties_compounds_js.php');
        $result = [];
        $coumpounds_id = [];
        ?>
        <?php
        if (isset($properties_compounds)):
            foreach ($properties_compounds as $property) {
               $limit =  1;
               $key = 1;

                if(!$this->is_public_property($property))
                    continue;
                $result['ids'][] = $property['id'];
                $references['compound_id'] = $property['id'];
                $meta = get_post_meta($object_id, 'socialdb_property_helper_' . $property['id'], true);
                if ($meta && $meta != '') {
                    $array = unserialize($meta);
                    $limit =  count($array);
                    end($array);         // move the internal pointer to the end of the array
                    $key = key($array)+1;
                }
               ?>
                <div class="col-md-<?php echo $collumns_to_show; ?> property-compounds no-padding">
                     <div class="box-item-paddings">
                        <h4 class="title-pipe single-title">
                            <?php echo $property['name']; ?>
                            <?php 
                                if(has_action('modificate_label_insert_item_properties')):
                                    do_action('modificate_label_insert_item_properties', $property);
                                endif;
                                //acao para modificaco da propriedade de objeto na insercao do item
                                if(has_action('modificate_insert_item_properties_compounds')): 
                                         do_action('modificate_insert_item_properties_compounds',$property,$object_id,'property_value_'. $property['id'] .'_'.$object_id.'_add'); 
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
                                ?>
                        </h4> 
                        <div class="edit-field-btn">
                            <button type="button" onclick="cancel_compounds_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" 
                                    id="single_cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                                    class="btn btn-default btn-xs"
                                    style="display: none;" >
                                <span class="glyphicon glyphicon-arrow-left" ></span>
                            </button>
                            <button type="button" 
                                    onclick="edit_compounds_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" 
                                    id="single_edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" >
                                <span class="glyphicon glyphicon-edit"></span>
                            </button>
                        </div>
                        <?php $cardinality = $this->render_cardinality_property($property);   ?>
                        <?php $properties_compounded = array_values(array_filter($property['metas']['socialdb_property_compounds_properties_id'])); ?>
                        <?php 
                        $class = 'col-md-'. (12/count($properties_compounded)); ?>

                        <div class="form-group"> 
                             <input  type="hidden" 
                                    id='main_compound_id' 
                                    value='<?php echo $references['compound_id'] ?>'>

                            <?php for($i = 0; $i<$limit;$i++):
                                $position = 0;
                                ?>
                                <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                                     class="col-md-12 no-padding"
                                     style="border-style: solid;border-width: 1px;border-color: #ccc; padding: 10px;margin-bottom: 5px;"
                                >
                                    <input type="hidden" id="socialdb_property_required_<?php echo $property['id']; ?>"
                                           value="<?php echo($property['metas']['socialdb_property_required']); ?>">
                                    <div class="col-md-1 no-padding">
                                        <div style="display: none;" class="pull-right compounds_buttons_<?php echo $property['id']; ?> ">    
                                            <button type="button" 
                                                    title="Limpar campos"
                                                    style="margin-bottom: 5px;"
                                                    onclick="clear_compounds(<?php echo $object_id ?>,<?php echo $property['id'] ?>,<?php echo $i ?>)" 
                                                    class="btn btn-default btn-xs">
                                                <span class="glyphicon glyphicon-erase"></span>
                                            </button>
                                            <button type="button" 
                                                    onclick="save_compounds(<?php echo $object_id ?>,<?php echo $property['id'] ?>,<?php echo $i ?>)" 
                                                    class="btn btn-default btn-xs">
                                                <span class="glyphicon glyphicon-floppy-disk"></span>
                                            </button>
                                        </div>    
                                    </div>      
                                    <div class="col-md-11">
                                    <?php foreach ($properties_compounded as $property_compounded): 
                                        if(!$property_compounded['id']){
                                            continue;
                                        }
                                        $coumpounds_id[] = $property_compounded['id']; 
                                        $value = $this->get_value_helper($object_id, $property['id'], $property_compounded['id'], $i, $position, true);
                                        ?>
                                        <input  type="hidden" 
                                                id='core_validation_<?php echo $references['compound_id'] ?>_<?php echo $property_compounded['id']; ?>_<?php echo $i ?>' 
                                                class='core_validation_compounds_<?php echo $property['id']; ?>' 
                                                value='<?php echo (!$value) ? 'false' : 'true' ; ?>'>
                                        <div style="padding-bottom: 15px;border: none;background: white !important; " class="col-md-12">
                                                    <p style="color: black;">
                                                        <b>
                                                            <?php echo $property_compounded['name']; ?>
                                                        </b>
                                                    </p>
                                                    <input type="hidden" 
                                                        name="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>" 
                                                        id="cardinality_compound_<?php echo $property['id']; ?>_<?php echo $property_compounded['id']; ?>"
                                                        value="<?php echo $cardinality; ?>"> 

                                                    <?php 
                                                    $val = (is_bool($value)) ? false : $value;
                                                    if(isset($property_compounded['metas']['socialdb_property_data_widget'])): 
                                                        ?>
                                                        <div class="compounds_fields_text_<?php echo $property['id']; ?>">
                                                            <?php echo ($val) ? '<a style="color: black;" /*onclick="wpquery_link_filter(' . "'" . $val . "'" . ',' . $property['id'] . ')"*/  >'.$val.'</a>' : '<button type="button" onclick="edit_compounds_property('. $property['id'] .', '.$object_id.')" class="btn btn-default btn-xs">'.__('Empty field!','tainacan').'</button>' ?>
                                                        </div> 
                                                        <div style="display: none;" class="compounds_fields_value_<?php echo $property['id']; ?>">
                                                            <?php
                                                            $this->widget_property_data($property_compounded, $i,$references,$val);
                                                            ?>
                                                        </div>
                                                        <?php
                                                    elseif(isset($property_compounded['metas']['socialdb_property_object_category_id'])): 
                                                        ?>
                                                        <div class="compounds_fields_text_<?php echo $property['id']; ?>">
                                                            <?php echo ($val) ? '<a style="color: black;" /*onclick="wpquery_term_filter(' . "'" . $val . "'" . ',' . $property['id'] . ')"*/ >'.get_post($val)->post_title.'</a>' : '<button type="button" onclick="edit_compounds_property('. $property['id'] .', '.$object_id.')" class="btn btn-default btn-xs">'.__('Empty field!','tainacan').'</button>' ?>
                                                        </div> 
                                                        <div style="display: none;" class="compounds_fields_value_<?php echo $property['id']; ?>">
                                                            <?php 
                                                                 $this->widget_property_object($property_compounded, $i,$references,$val);
                                                            ?>
                                                        </div>
                                                        <?php 
                                                    elseif(isset($property_compounded['metas']['socialdb_property_term_widget'])): 
                                                         ?>
                                                        <div class="compounds_fields_text_<?php echo $property['id']; ?>">
                                                           <?php echo ($val) ? '<a style="color: black;" /*onclick="wpquery_term_filter(' . "'" . $val . "'" . ',' . $property['id'] . ')"*/ >'.get_term_by('id',$val,'socialdb_category_type')->name.'</a>' : '<button onclick="edit_compounds_property('. $property['id'] .', '.$object_id.')" type="button" class="btn btn-default btn-xs">'.__('Empty field!','tainacan').'</button>' ?>
                                                        </div> 
                                                        <div style="display: none;" class="compounds_fields_value_<?php echo $property['id']; ?>">
                                                            <?php 
                                                                 $this->widget_property_term($property_compounded, $i,$references,$val);
                                                            ?>
                                                        </div>
                                                        <?php 
                                                    endif; 
                                                    ?>
                                        </div>
                                    <?php $position++ ?>
                                    <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endfor; ?>

                            <?php if($property['metas']['socialdb_property_compounds_cardinality'] && $property['metas']['socialdb_property_compounds_cardinality'] == 'n'): ?>
                                <script>
                                    localStorage.setItem("index_<?php echo $property['id']; ?>", "<?php echo $key; ?>");
                                </script>
                                <div id="new-fields-compound-<?php echo $property['id']; ?>"></div>
                                <button type="button"
                                        onclick="appendNewContainer('<?php echo $object_id; ?>','<?php echo $property['id']; ?>')"
                                        style="margin-top: 5px;display: none;"
                                        class="btn btn-primary btn-lg btn-xs btn-block  btn-new-field-<?php echo $property['id']; ?>">
                                    <span class="glyphicon glyphicon-plus"></span><?php _e('Add field', 'tainacan') ?>
                                </button>
                            <?php  endif; ?>

                            <input type="hidden" 
                                   name="compounds_<?php echo $property['id']; ?>" 
                                   id="compounds_<?php echo $property['id']; ?>"
                                   value="<?php echo implode(',', array_filter( array_unique($coumpounds_id))); ?>"> 
                            <input type="hidden" 
                                   name="cardinality_<?php echo $property['id']; ?>" 
                                   id="cardinality_<?php echo $property['id']; ?>"
                                   value="<?php echo $cardinality; ?>"> 
                            <?php $coumpounds_id = []; ?>
                        </div>
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
        if ($property['type'] == 'text') { ?>
            <input type="text" 
                   id="compounds_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                   class="form-control form_autocomplete_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                   value="<?php if ($value) echo $value; ?>"
                   name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]">
        <?php }elseif ($property['type'] == 'textarea') { ?>   
            <textarea class="form-control form_autocomplete_compounds_<?php echo $property['id']; ?>_<?php echo $i; ?>"
                      rows="10"
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
            do_action('modificate_edit_item_properties_data', $property);
            return false;
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
        ?>
        <input type="hidden" 
                        id="cardinality_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"  
                        value="<?php echo $this->render_cardinality_property($property);   ?>">            
        <input type="text" 
               onkeyup="autocomplete_object_property_compound('<?php echo $references['compound_id']; ?>','<?php echo $property['id']; ?>', '<?php echo $i; ?>');" 
               id="autocomplete_value_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
               placeholder="<?php _e('Type the three first letters of the object of this collection ', 'tainacan'); ?>"  
               class="chosen-selected form-control"  />    

        <select onclick="clear_select_object_property_compound(this,'<?php echo $references['compound_id']; ?>','<?php echo $property['id']; ?>', '<?php echo $i; ?>');" 
                id="property_value_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>_edit" 
                multiple class="chosen-selected2 form-control" 
                style="height: auto;" 
                name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]"
                <?php 
                    if ($property['metas']['socialdb_property_required'] == 'true'): 
                        echo 'required="required"';
                    endif;
                ?> >
                <?php 
                    if (!empty($property['metas']['objects'])) { ?>     
                        <?php foreach ($property['metas']['objects'] as $object) { ?>
                            <?php if ($value && $object->ID==$value): // verifico se ele esta na lista de objetos da colecao   ?>    
                                 <option selected='selected' value="<?php echo $object->ID ?>"><?php echo $object->post_title ?></option>
                        <?php endif; ?>
                    <?php } ?> 
                <?php 
                    }
                ?>       
        </select>    
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
                onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'field_property_term_<?php echo $property['id']; ?>')" 
                class="btn btn-primary btn-xs"><?php _e('Add Category','tainacan'); ?>
            </button>
            <br><br>
            <div class="row">
                <div style='height: 150px;' 
                     class='col-lg-12'  
                     id='dynatree_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'>
                </div>
                <input type="hidden" 
                       id='field_property_term_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>'
                       name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
                       value="<?php if ($value != false) echo $value;?>">
            </div>
            <?php
        }elseif ($property['type'] == 'selectbox') {
            $references['properties_terms_selectbox'][] = $property['id'];
            ?>
            <select class="form-control" 
                    name="socialdb_property_<?php echo $references['compound_id']; ?>_<?php echo $property['id']; ?>_<?php echo $i; ?>[]" 
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
                onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'field_property_term_<?php echo $property['id']; ?>_<?php echo $i; ?>')" 
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
    public function get_value($item_id,$compound_id,$property,$i,$position) {
        $values = get_post_meta($item_id,'socialdb_property_'.$compound_id.'_'.$i,true);
        if($values&&$values!=''){
            $values = explode(',', $values);
            $value = $values[$position];
            if(strpos($value, '_cat')!==false){
                return str_replace('_cat', '', $value);
            }else {
                $object = get_metadata_by_mid('post', $value);
                return (is_object($object)) ? $object->meta_value : false;
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
     * @param type $item_id
     * @param type $compound_id
     * @param type $property
     * @param type $i
     * @return string/boolean
     */
    public function get_value_helper($item_id,$compound_id,$property,$i,$position, $single = false) {
        $meta = unserialize(get_post_meta($item_id, 'socialdb_property_helper_' . $compound_id, true));
        $indexed_properties = [];
        if($meta && !empty($meta) && is_array($meta) && isset($meta[$i][$property])){
            $values = $meta[$i][$property]['values'];

            if($single)
            {
	            $meta_value = $this->sdb_get_post_meta($values[count($values) - 1]);
	            if(isset($meta_value->meta_value))
	            {
		            $indexed_properties[] = $meta_value->meta_value;
	            }
            }
            else{
	            foreach ($values as $value) {
		            $meta_value = $this->sdb_get_post_meta($value);
		            if(isset($meta_value->meta_value))
		            {
			            $indexed_properties[] = $meta_value->meta_value;
		            }
	            }
            }

            return implode(',',$indexed_properties);
        }
        return false;
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
        if($values&&$values!='' && !$this->hasNoValues($values,$item_id,$compound,$property,$i)){
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
    
    /**
     * verifico se existe algum valor nas proximas
     */
    public function has_next_compound($item_id,$compound,$i) {
       $max = $this->render_cardinality_property($compound);
       while($i<$max){
           if(get_post_meta($item_id,'socialdb_property_'.$compound['id'].'_'.$i,true)){
               return true;
           }
           $i++;
       }
       return false;
    }
    
    public function is_public_property($property) {
        if($property['metas']['socialdb_property_visualization']&&$property['metas']['socialdb_property_visualization']=='restrict'){
            return false;
        }else{
            return true;
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
}