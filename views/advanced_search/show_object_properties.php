    <h5 style="margin-bottom: 15px;margin-top: 15px;">
        <b>
        <?php _e('Properties','tainacan'); ?>
        </b>
    </h5>
    <div id="fields">
        <div class="form-group">
            <label class="col-md-12 no-padding" for="advanced_search_title"><?php _e('Title or description', 'tainacan'); ?></label>
            <div class="col-md-8 no-padding">
                <input type="text" 
                      
                       class="form-control" 
                       name="advanced_search_title" 
                       id="advanced_search_title" 
                       placeholder="<?php if ($collection_id != get_option('collection_root_id')) _e('Type the item title or its description', 'tainacan'); ?>">
            </div>
            <!--div class="col-md-4 no-padding padding-left-space">
                    <select class="form-control" id="advanced_search_property_title_operation" name="advanced_search_property_title_operation">
                        <option value="1"><?php _e('Equals','tainacan'); ?></option>
                        <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                        <option value="3"><?php _e('Contains','tainacan'); ?></option>
                        <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                    </select>
             </div-->
        </div>
        <div class="form-group">
            <label class="col-md-12 no-padding" for="advanced_search_type"><?php _e('Type', 'tainacan'); ?></label>
            <div class="col-md-8 no-padding">
               <select class="form-control" id="advanced_search_type" name="advanced_search_type">
                        <option value=""><?php _e('Select...','tainacan'); ?></option>
                        <option value="text"><?php _e('Text','tainacan'); ?></option>
                        <option value="image"><?php _e('Image','tainacan'); ?></option>
                        <option value="pdf"><?php _e('PDF','tainacan'); ?></option>
                        <option value="video"><?php _e('Video','tainacan'); ?></option>
                        <option value="audio"><?php _e('Audio','tainacan'); ?></option>
                        <option value="other"><?php _e('Other','tainacan'); ?></option>
                </select>
            </div>
             <div class="col-md-4 no-padding padding-left-space">
                    <select class="form-control" id="socialdb_property_type_operation" name="socialdb_property_type_operation">
                        <option value="1"><?php _e('Equals','tainacan'); ?></option>
                        <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                        <option value="3" selected><?php _e('Contains','tainacan'); ?></option>
                        <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                    </select>
             </div>
        </div>
        <div class="form-group">
            <label class="col-md-12 no-padding" for="advanced_search_source"><?php _e('Item Source', 'tainacan'); ?></label>
            <div class="col-md-8 no-padding">
               <input type="text" 
                       value="" 
                       class="form-control" 
                       name="advanced_search_source" 
                       id="advanced_search_source" 
                       placeholder="<?php _e('Item source', 'tainacan'); ?>">
            </div>
             <div class="col-md-4 no-padding padding-left-space">
                    <select class="form-control" id="socialdb_property_source_operation" name="socialdb_property_source_operation">
                        <option value="1"><?php _e('Equals','tainacan'); ?></option>
                        <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                        <option value="3" selected><?php _e('Contains','tainacan'); ?></option>
                        <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                    </select>
             </div>
        </div>
        <div class="form-group">
            <label class="col-md-12 no-padding" for="advanced_search_tags"><?php _e('Tags', 'tainacan'); ?></label>
            <div class="col-md-8 no-padding">
                <input type="text" 
                       class="form-control" 
                       name="advanced_search_tags" 
                       id="advanced_search_tags" 
                       placeholder="<?php _e('A set of tags may be searched by comma ', 'tainacan'); ?>">
            </div>
            <div class="col-md-4 no-padding padding-left-space">
                   <select class="form-control" id="socialdb_property_tag_operation" name="socialdb_property_tag_operation">
                       <option value="1"><?php _e('Equals','tainacan'); ?></option>
                       <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                       <option value="3" selected><?php _e('Contains','tainacan'); ?></option>
                       <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                   </select>
            </div>     
        </div>
    <?php if (isset($property_data)||isset($property_term)||isset($property_object)): ?>
    
    <?php
    include_once ('../../../../../wp-config.php');
    include_once ('../../../../../wp-load.php');
    include_once ('../../../../../wp-includes/wp-db.php');
    include_once(dirname(__FILE__).'/../../helpers/view_helper.php');
    include_once(dirname(__FILE__).'/../../helpers/advanced_search/advanced_search_helper.php');
    include_once ('js/show_insert_object_properties_js.php');
    $advanced_search_helper = new AdvancedSearchHelper();
    $properties_terms_radio = [];
    $properties_terms_tree = [];
    $properties_terms_selectbox = [];
    $properties_terms_checkbox = [];
    $properties_terms_multipleselect = [];
    $properties_terms_treecheckbox = [];
    $properties_autocomplete = [];
    ?>
    <?php if (isset($property_data)): ?>
             
            <?php foreach ($property_data as $property) { $properties_autocomplete[] = $property['id']; ?>
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
                                    <option value="3" selected><?php _e('Contains','tainacan'); ?></option>
                                    <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                                </select>
                           </div>
                        <?php }elseif ($property['type'] == 'textarea') { $properties_autocomplete[] = $property['id']; ?>   
                            <div class="col-md-8 no-padding">
                                <input type="text" class="form-control" id="autocomplete_value_<?php echo $property['id']; ?>" name="socialdb_property_<?php echo $property['id']; ?>"  placeholder="<?php echo $property['name']; ?>"></textarea>
                            </div> 
                           <div class="col-md-4 no-padding padding-left-space">
                                <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                                    <option value="1"><?php _e('Equals','tainacan'); ?></option>
                                    <option value="2"><?php _e('Not equals','tainacan'); ?></option>
                                    <option value="3" selected><?php _e('Contains','tainacan'); ?></option>
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
                        <?php }elseif($property['type'] == 'date') { $properties_autocomplete[] = $property['id']; ?> 
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
                </div>
            <?php } ?>
            <?php
        endif;
        
        
         if((isset($property_term)&&count($property_term)>1)||(count($property_term)==1&&!empty($property_term[0]['has_children']))): 
            ?>
            <?php foreach ($property_term as $property) { ?>
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
                                    id='search_field_property_term_<?php echo $property['id']; ?>' <?php
                            
                            ?>></select>
                                    <?php
                                }elseif ($property['type'] == 'checkbox') {
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
                       <option value=""><?php _e('Select','tainacan'); ?>...</option>
                       <option value="3"><?php _e('Contains','tainacan'); ?></option>
                       <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
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
                        <input type="text" onkeyup="autocomplete_object_property_add('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" id="autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" placeholder="<?php _e('Type the three first letters of the item of this collection ','tainacan'); ?>"  class="chosen-selected form-control"  />  
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
                            <option value="3"><?php _e('Contains','tainacan'); ?></option>
                            <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
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
                        <?php if(in_array($property['type'], ['like','binary'])): ?>
                            <input style="width: 30%" 
                                   size="7" 
                                   type="number"  
                                   value="" 
                                   id="facet_<?php echo $facet['id']; ?>_1" 
                                   name="socialdb_property_<?php echo $property['id']; ?>_1"> 
                                       <?php _e('until','tainacan') ?> 
                            <input style="width: 30%" 
                                   type="number" 
                                   size="7" 
                                   value="" 
                                   id="facet_<?php echo $facet['id']; ?>_2" 
                                   name="socialdb_property_<?php echo $property['id']; ?>_2">
                        <?php elseif($property['type']=='stars'): ?>
                                <input type="radio" value="4.1_5" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star5.png' ?>"></a><br>
                                <input type="radio" value="3.1_4" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star4.png' ?>"></a><br>
                                <input type="radio" value="2.1_3" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star3.png' ?>"></a><br>
                                <input type="radio" value="1.1_2" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star2.png' ?>"></a><br>
                                <input type="radio" value="0_1" name="socialdb_property_<?php echo $property['id']; ?>_stars"><img src="<?php echo get_template_directory_uri() . '/libraries/images/star1.png' ?>"></a><br>
                        <?php endif;?>
                    </div>
                    <div class="col-md-4 no-padding padding-left-space">
                        <select class="form-control" id="socialdb_property_<?php echo $property['id']; ?>_operation" name="socialdb_property_<?php echo $property['id']; ?>_operation">
                            <option value="1"><?php _e('Between','tainacan'); ?></option>
                            <option value="2"><?php _e('Not Between','tainacan'); ?></option>
                        </select>
                   </div>   
                 </div>                
            <?php } ?>
        <?php endif; ?>
        <?php if(isset($property_compounds)): ?>
            <?php $advanced_search_helper->list_properties_compounds_search($property_compounds) ?>
        <?php endif; ?>
        <div id="list_licenses_items" class="form-group col-md-12 no-padding" >
             <label class="col-md-12 no-padding" for="object_tags">
                        <?php echo __('Licenses','tainacan'); ?>
                        <a  
                                       style="margin-right: 20px;" >
                                        <span title="<?php echo __('Available Licenses','tainacan') ?>" 
                                              data-toggle="tooltip" 
                                              data-placement="bottom" 
                                              class="glyphicon glyphicon-question-sign"></span>
                        </a>
            </label>
            <div class="col-md-8 no-padding" id="show_form_licenses">
                       
            </div>
            <div class="col-md-4 no-padding padding-left-space">
                <select class="form-control" id="object_license_operation" name="object_license_operation">
                    <option value="3"><?php _e('Contains','tainacan'); ?></option>
                    <option value="4"><?php _e('Does not Contain','tainacan'); ?></option>
                </select>
           </div>   
        </div>
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
<?php endif; ?>
    </div>
<div class="col-md-12 no-padding">
    <a style="margin-top:5px;cursor:pointer;" class="pull-right" onclick="slideFormAdvancedDown()">
        <?php _e('Advanced Search', 'tainacan'); ?>
        <span class="glyphicon glyphicon-triangle-top" style="font-size: 14px;"></span>
    </a>
</div>
<div class="col-md-12 no-padding" style="margin-top: 15px;">
                <button type="button" onclick="reboot_form()" class="btn btn-lg btn-default pull-left"><?php _e('Clear search', 'tainacan') ?></button>
                <button type="submit" onclick="$('#advanced_search_general').val('')" class="btn btn-lg btn-success pull-right"><?php _e('Find', 'tainacan') ?></button>
    </div>