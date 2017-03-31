<?php
/*
 * Object Controller's view helper 
 * */
class AdvancedSearchHelper extends ViewHelper {
    
    
    /**
     * 
     * @param array $properties_compounds
     */
    public function list_properties_compounds_search($properties_compounds) {
        $result = [];
        $coumpounds_id = [];
        if (isset($properties_compounds)):
            foreach ($properties_compounds as $property) { 
               $this->add_script_compound($property['id']);
               ?>
               <div  class="form-group col-md-12 no-padding" >
                    <b>
                        <?php echo $property['name']; ?>
                    </b> 
                    <?php $properties_compounded = $property['metas']['socialdb_property_compounds_properties_id']; ?>
                    <div style="margin-top: 15px;margin-right: 15px;margin-left: 15px;" > 
                            <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                                 class="col-md-12 no-padding"
                                 style="border-style: solid;border-width: 1px;border-color: #ccc; padding: 10px;">
                                <?php 
                                foreach ($properties_compounded as $property_compounded): 
                                    $coumpounds_id[] = $property_compounded['id'];  
                                    if(isset($property_compounded['metas']['socialdb_property_data_widget'])): 
                                        $this->widget_property_data($property_compounded, $property['id']);
                                    elseif(isset($property_compounded['metas']['socialdb_property_object_category_id'])): 
                                        $this->widget_property_object($property_compounded, $property['id']);
                                    elseif(isset($property_compounded['metas']['socialdb_property_term_widget'])): 
                                        $this->widget_property_term($property_compounded, $property['id']);
                                    endif; 
                                 endforeach; ?>    
                            </div>  
                    </div>     
                </div>   
               <?php
            }
        endif;    
    }
    
    /**
     * busca o widget para o os metadados de texto
     * @param array $property
     * @param int $i O indice do for da cardinalidade
     */
    public function widget_property_data($property,$compound_id) {
            ?>
                <div class="form-group">   
                         <label class="col-md-12 no-padding" for="advanced_search_tags">
                            <?php echo $property['name']; ?>
                            <?php if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))) {
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
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('Contains','tainacan'); ?></option>
                                    <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                                </select>
                           </div>
                        <?php }elseif ($property['type'] == 'textarea') {  ?>   
                            <div class="col-md-8 no-padding">
                                <input type="text" class="form-control" id="autocomplete_value_<?php echo $property['id']; ?>" name="socialdb_property_<?php echo $property['id']; ?>"  placeholder="<?php echo $property['name']; ?>"></textarea>
                            </div> 
                           <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('Contains','tainacan'); ?></option>
                                    <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                                </select>
                           </div> 
                        <?php }elseif($property['type'] == 'numeric') { ?> 
                            <div class="col-md-8 no-padding">
                                 <input class="form-control"  placeholder="<?php echo $property['name']; ?>" type="numeric"  name="socialdb_property_<?php echo $property['id']; ?>" >
                            </div>     
                            <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('Higher','tainacan'); ?></option>
                                    <option value="4"><?php _e('Lower','tainacan'); ?></option>
                                </select>
                           </div>
                        <?php }elseif($property['type'] == 'date') {  ?> 
                            <div class="col-md-8 no-padding">
                                 <input class="form-control input_date" id="autocomplete_value_<?php echo $property['id']; ?>"  placeholder="<?php echo $property['name']; ?>" type="text"  name="socialdb_property_<?php echo $property['id']; ?>" >
                            </div>     
                            <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="advanced_search_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3"><?php _e('After','tainacan'); ?></option>
                                    <option value="4"><?php _e('Before','tainacan'); ?></option>
                                </select>
                           </div>
                        <?php } ?> 
                    <input type="hidden" class="advanced-search-autocomplete-<?php echo $compound_id; ?>" value="<?php echo $property['id']; ?>">
                </div>
            <?php 
    }
    
    /**
     * busca o widget para o os metadados de termo
     * @param array $property
     * @param int $i O indice do for da cardinalidade
     */
    public function widget_property_object($property,$compound_id) {
         ?>
            <div class="form-group col-md-12 no-padding">
                    <label class="col-md-12 no-padding" for="object_tags">
                        <?php echo $property['name']; ?>
                        <?php if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))) {
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
                        <input type="text" onkeyup="search_autocomplete_object_property_add('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" id="autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" placeholder="<?php _e('Type the three first letters of the item of this collection ','tainacan'); ?>"  class="chosen-selected form-control"  />  
                        <select onclick="clear_select_object_property(this);" id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_add" multiple class="chosen-selected2 form-control" style="height: auto;" name="socialdb_property_<?php echo $property['id']; ?>[]"
                        >
                            <?php if (!empty($property['metas']['objects'])) {  } 
                                    else { ?>   
                                <option value=""><?php _e('No objects added in this collection','tainacan'); ?></option>
                            <?php } ?>       
                        </select>
                    </div>
                    <div class="col-md-4 no-padding padding-left-space">
                        <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                            <option value="in"><?php _e('Contains','tainacan'); ?></option>
                            <option value="not_in"><?php _e('Does not Contain','tainacan'); ?></option>
                        </select>
                   </div>   
                 </div>       
        <?php
    }
    
    /**
     * busca o widget para o os metadados de relacionamento
     * @param array $property
     * @param int $i O indice do for da cardinalidade
     */
    public function widget_property_term($property,$compound_id) {
        ?>
             <div class="form-group col-md-12 no-padding" >
                <label class="col-md-12 no-padding" >
                    <?php echo $property['name']; ?>
                     <?php if ($property['metas']['socialdb_property_help']&&!empty(trim($property['metas']['socialdb_property_help']))) {
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
                            ?>
                            <input type="hidden" class="advanced-search-term-radio-<?php echo $compound_id; ?>" value="<?php echo $property['id']; ?>">
                            <div id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                            <?php
                        } elseif ($property['type'] == 'tree') {
                                ?>
                                <div class="row">
                                    <div  style='height: 150px;padding-left: 15px;'   id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                                     <input type="hidden" 
                                            id='socialdb_propertyterm_<?php echo $property['id']; ?>'
                                            name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                                            value="">
                                    <input type="hidden" class="advanced-search-term-tree-<?php echo $compound_id; ?>" value="<?php echo $property['id']; ?>">
                               </div>
                               <?php
                        } elseif ($property['type'] == 'selectbox') {
                            ?>
                            <input type="hidden" class="advanced-search-term-selectbox-<?php echo $compound_id; ?>" value="<?php echo $property['id']; ?>">
                            <select class="form-control"
                                    onchange="onSelectValue(this,<?php echo $property['id']; ?>)"
                                    name="socialdb_property_<?php echo $property['id']; ?>" 
                                    id='search_field_property_term_<?php echo $property['id']; ?>' <?php
                            
                            ?>></select>
                                    <?php
                        }elseif ($property['type'] == 'checkbox') {
                            ?>
                            <input type="hidden" class="advanced-search-term-checkbox-<?php echo $compound_id; ?>" value="<?php echo $property['id']; ?>">
                            <div id='search_field_property_term_<?php echo $property['id']; ?>'></div>
                            <?php
                        } elseif ($property['type'] == 'multipleselect') {
                            ?>
                             <input type="hidden" class="advanced-search-term-multipleselect-<?php echo $compound_id; ?>" value="<?php echo $property['id']; ?>">
                             <select   onchange="onSelectValue(this,<?php echo $property['id']; ?>)" multiple class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>" id='search_field_property_term_<?php echo $property['id']; ?>' ></select>
                            <?php
                        } elseif ($property['type'] == 'tree_checkbox') {
                            ?>
                             <input type="hidden" class="advanced-search-term-tree-checkbox-<?php echo $compound_id; ?>" value="<?php echo $property['id']; ?>"> 
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
                       <option value="in"><?php _e('Contains','tainacan'); ?></option>
                            <option value="not_in"><?php _e('Does not Contain','tainacan'); ?></option>
                    </select>
               </div>  
               <div class="col-md-12" id="append_properties_categories_<?php echo $property['id']; ?>_adv"></div> 
           </div> 
            <?php
    }
    
    /**
     * metodo que adciona a inicializacao dos scripts dos metadados compostos
     * @param type $compound_id O id da propriedade composta
     */
    private function add_script_compound($compound_id){
        ?>
        <script>
            $(function () {
                search_list_properties_compounds_insert_objects();
                var search_properties_autocomplete = search_get_val(".advanced-search-autocomplete-<?php echo $compound_id ?>");
                autocomplete_object_property_add(search_properties_autocomplete);
            });

        //************************* properties terms ******************************************//
            function search_list_properties_compounds_insert_objects() {
                var radios = get_values_classes(".advanced-search-term-radio-<?php echo $compound_id ?>");
                var selectboxes = get_values_classes(".advanced-search-term-selectbox-<?php echo $compound_id ?>");
                var trees = get_values_classes(".advanced-search-term-tree-<?php echo $compound_id ?>");
                var checkboxes = get_values_classes(".advanced-search-term-checkbox-<?php echo $compound_id ?>");
                var multipleSelects = get_values_classes(".advanced-search-term-multipleselect-<?php echo $compound_id ?>");
                var treecheckboxes = get_values_classes(".advanced-search-term-tree-checkbox-<?php echo $compound_id ?>");
                search_list_radios(radios);
                search_list_tree(trees);
                search_list_selectboxes(selectboxes);
                search_list_multipleselectboxes(multipleSelects);
                search_list_checkboxes(checkboxes);
                search_list_treecheckboxes(treecheckboxes);
            }

            function get_values_classes(seletor){
                var values = [];
                if($(seletor).length>0){
                    $.each($(seletor),function(index,value){
                        values.push($(value).val());
                    });
                }
                console.log('values',seletor,values);
                return values;
            }
        </script>
        <?php
    }
    
}