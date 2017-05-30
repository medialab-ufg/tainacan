<!-----
  View que mostra o formulario de busca da propriedade de objeto
-->
<!----------------------------   definindo o formulario ----------------------->
<?php if (isset($property['compound_id'])): ?>
    <?php $form = "#property_object_search_submit_" . $property['compound_id'] . "_" . $property['id'] . "_" . $property['contador']; ?>
    <form id="property_object_search_submit_<?php echo $property['compound_id'] ?>_<?php echo $property['id'] ?>_<?php echo $property['contador'] ?>">
        <input type="hidden" name="property_id" value="<?php echo $property['id'] ?>">
        <input type="hidden" name="collection_id" value="0">
        <input type="hidden" name="compound_id" value="<?php echo $property['compound_id'] ?>">
        <input type="hidden" name="contador" value="<?php echo $property['contador'] ?>">
<?php else: ?>   
    <?php $form = "#property_object_search_submit_" . $property['id'] ?>   
    <form id="property_object_search_submit_<?php echo $property['id'] ?>">
        <input type="hidden" name="property_id" value="<?php echo $property['id'] ?>">
        <input type="hidden" name="collection_id" value="0">   
<?php endif; ?>
<!------------------------------------------------------------------------------>
        <input type="hidden" name="avoid_selected_items" id="avoid_selected_items_<?php echo $property['id'] ?>" value="<?php echo (isset($property['metas']['socialdb_property_avoid_items']) && $property['metas']['socialdb_property_avoid_items'] == 'true') ? 'true' : 'false' ?>">
        <input type="hidden" name="categories" value="<?php echo (is_array($property['metas']['socialdb_property_object_category_id'])) ? implode(",",$property['metas']['socialdb_property_object_category_id']) : $property['metas']['socialdb_property_object_category_id'] ?>">
        <input type="hidden" name="properties_id" value="<?php echo (is_array($properties)) ? implode(',', $properties) : '' ?>">
        <?php
            include_once(dirname(__FILE__) . '/../../helpers/view_helper.php');
            include_once(dirname(__FILE__) . '/../../helpers/advanced_search/advanced_search_helper.php');
            if (!isset($property['compound_id'])) {
                include('js/search_property_object_metadata_js.php');
            } else {
                include('js/compounds_search_property_object_metadata_js.php');
            }
            $advanced_search_helper = new AdvancedSearchHelper();
            $objectHelper = new ObjectWidgetsHelper;
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
                    <?php echo  $objectHelper->get_labels_search_obejcts($property['metas']['socialdb_property_object_category_id']); ?>
                </label>
                <div class="col-md-8 no-padding">
                    <input type="text" 
                           name="advanced_search_title" 
                           class="form-control <?php if (isset($property['compound_id'])): ?> advanced_search_title_<?php echo $property['compound_id'] ?>_<?php echo $property['id'] ?>_<?php echo $property['contador'] ?><?php endif; ?>"
                           id="advanced_search_title_<?php echo $property['id'] ?>"
                           placeholder="<?php _e('Type the 3 first letters to activate autocomplete', 'tainacan'); ?>">
                </div>
            </div>
    <?php endif; ?>           
    <?php if (isset($property_data)): ?>
                        <?php foreach ($property_data as $property) {
                            $properties_autocomplete[] = $property['id']; ?>
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
            <?php } elseif ($property['type'] == 'textarea') {
                $properties_autocomplete[] = $property['id']; ?>   
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
            <?php } elseif ($property['type'] == 'date') {
                $properties_autocomplete[] = $property['id']; ?> 
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
                                <?php if (!empty($property['metas']['objects'])) {
                                    
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
                                <input type="radio" value="4.1_5" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star5.png' ?>"></a><br>
                                <input type="radio" value="3.1_4" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star4.png' ?>"></a><br>
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
        <input type="hidden" name="operation" value="search_items_property_object">        
        <div class="col-md-12 no-padding" style="margin-top: 15px;">
            <button type="button" onclick="clear_all_field('<?php echo $form ?>')" class="btn btn-lg btn-default pull-left"><?php _e('Clear search', 'tainacan') ?></button>
            <button type="submit"  class="btn btn-lg btn-success pull-right"><?php _e('Find', 'tainacan') ?></button>
        </div>
    </form>            