<?php
/*
 * View Responsavel em mostrar as propriedades na hora de EDITAR do objeto, NAO UTILIZADA NOS EVENTOS
 */
include_once ('js/multiple_properties_categories_accordion_js.php');
include_once(dirname(__FILE__) . '/../../../helpers/view_helper.php');

$properties_to_avoid = (explode(',', $properties_to_avoid));

$view_helper = new ViewHelper();
$ids = [];
$properties_terms_radio = [];
$properties_terms_tree = [];
$properties_terms_selectbox = [];
$properties_terms_checkbox = [];
$properties_terms_multipleselect = [];
$properties_terms_treecheckbox = [];
if (isset($property_object)):
    foreach ($property_object as $property) {
        if (in_array($property['id'], $properties_to_avoid)) {
            continue;
        }
        $ids[] = $property['id'];
        $object_properties[] = $property['id'];
        $all_properties[] = $property['id'];
        ?>
        <?php //if($property['metas']['socialdb_property_object_is_facet']=='false'):  ?>
        <div id="meta-item-<?php echo $property['id']; ?>"
             property="<?php echo $property['id']; ?>"
             type="object"
             class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?>">
            <h2> <?php echo $property['name']; ?> </h2>
            <div class="form-group">                        
                <?php
                // botao que leva a colecao relacionada
                if (isset($property['metas']['collection_data'][0]->post_title)):
                    ?>
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
                            html: true,
                            container: '#editor_items',
                            placement: 'right',
                            title: '<?php echo _e('Add item in the collection', 'tainacan') . ' ' . $property['metas']['collection_data'][0]->post_title; ?>',
                            content: function () {
                                return $("#popover_content_<?php echo $property['id']; ?>_<?php echo $object_id; ?>").html();
                            }
                        });
                    </script>
                    <div id="popover_content_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"   class="hide ">
                        <form class="form-inline"  style="font-size: 12px;width: 300px;">
                            <div class="form-group">
                                <input type="text" 
                                       placeholder="<?php _e('Type the title', 'tainacan') ?>"
                                       class="form-control title_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                                       >
                            </div>
                            <button type="button" 
                                    onclick="add_new_item_by_title('<?php echo $property['metas']['collection_data'][0]->ID; ?>', '#add_item_popover_<?php echo $property['id']; ?>_<?php echo $object_id; ?>',<?php echo $property['id']; ?>, '<?php echo $object_id; ?>')"
                                    class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span></button>
                        </form>
                    </div> 
                    <br><br>
                    <?php
                endif;
                ?>
                <input type="hidden" 
                       id="cardinality_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"  
                       value="<?php echo $view_helper->render_cardinality_property($property); ?>">        
                <input type="text" 
                       onkeyup="multiple_autocomplete_object_property_add('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" 
                       id="multiple_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                       placeholder="<?php _e('Type the three first letters of the object of this collection ', 'tainacan'); ?>"  
                       class="chosen-selected form-control"  />  
                <select onclick="clear_select_object_property(this, '<?php echo $property['id']; ?>');" 
                        id="multiple_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_add" 
                        multiple class="chosen-selected2 form-control" 
                        style="height: auto;" 
                        name="socialdb_property_<?php echo $property['id']; ?>[]" 
                            <?php if ($property['metas']['socialdb_property_required'] == 'true'): echo 'required="required"';
                            endif; ?> >
                    <?php if (!empty($property['metas']['objects'])) { ?>     

                    <?php } else { ?>   
                        <option value=""><?php _e('No objects added in this collection', 'tainacan'); ?></option>
        <?php } ?>       
                </select>
            </div>  
        </div>    
        <?php // endif;  ?>
    <?php } ?>
    <input type="hidden" name="properties_object_ids" id='properties_object_ids' value="<?php echo implode(',', $ids); ?>">
<?php endif; ?>

<?php
if (isset($property_data)):
    foreach ($property_data as $property) {
        if (in_array($property['id'], $properties_to_avoid)) {
            continue;
        }
        $ids[] = $property['id'];
        $properties_autocomplete[] = $property['id'];
        $data_properties_id[] = $property['id'];  
        $data_properties[] = ['id'=>$property['id'],'default_value'=>$property['metas']['socialdb_property_default_value']];  
        $all_properties[] = $property['id']; ?>
                <div id="meta-item-<?php echo $property['id']; ?>"
                     type="data"
                     property="<?php echo $property['id']; ?>"
                     class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?>">
                    <h2> <?php echo $property['name']; ?> </h2> 
                    <?php $cardinality = $view_helper->render_cardinality_property($property);   ?>
                    <div class="form-group">
                        <?php for($i = 0; $i<$cardinality;$i++):   ?>           
                            <div id="container_field_<?php echo $property['id']; ?>_<?php echo $i; ?>" 
                                 style="padding-bottom: 10px;<?php echo ($i===0||(is_array($property['metas']['value'])&&$i<count($property['metas']['value']))) ? 'display:block': 'display:none'; ?>">
                            <?php if($property['type']=='text'){ ?>     
                                    <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')" 
                                           onchange="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                           type="text" 
                                           id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                           class="form-control multiple_socialdb_property_<?php echo $property['id']; ?>" 
                                           value="<?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>" 
                                           name="socialdb_property_<?php echo $property['id']; ?>"
                                           <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                            <?php }elseif($property['type']=='textarea') { ?>   
                                  <textarea onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                            onchange="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                            class="form-control multiple_socialdb_property_<?php echo $property['id']; ?>" 
                                             id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                            name="socialdb_property_<?php echo $property['id']; ?>"
                                            <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>><?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>
                                  </textarea>
                             <?php }elseif($property['type']=='numeric') { ?>   
                                  <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                         onchange="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                         type="text" 
                                         onkeypress='return onlyNumbers(event)'
                                         id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                         value="<?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>" 
                                         class="form-control multiple_socialdb_property_<?php echo $property['id']; ?>"
                                         name="socialdb_property_<?php echo $property['id']; ?>" 
                                         <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                             <?php }elseif($property['type']=='autoincrement') {  ?>   
                                  <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                         onchange="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                         disabled="disabled"  
                                         onkeypress='return onlyNumbers(event)'
                                         id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                         type="number" 
                                         class="form-control multiple_socialdb_property_<?php echo $property['id']; ?>" 
                                         name="only_showed_<?php echo $property['id']; ?>" value="<?php if(is_numeric($property['metas']['socialdb_property_data_value_increment'])): echo $property['metas']['socialdb_property_data_value_increment']+1; endif; ?>">
                                  <!--input type="hidden"  name="socialdb_property_<?php echo $property['id']; ?>" value="<?php if($property['metas']['socialdb_property_data_value_increment']): echo $property['metas']['socialdb_property_data_value_increment']+1; endif; ?>" -->
                            <?php }else{ ?>
                                  <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                         onchange="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                         type="date" 
                                          id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                         value="<?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>" 
                                         class="form-control multiple_socialdb_property_<?php echo $property['id']; ?>" 
                                         name="socialdb_property_<?php echo $property['id']; ?>" <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                            <?php } ?> 
                            <?php echo $view_helper->render_button_cardinality($property,$i) ?>    
                            </div>         
                    <?php endfor;  ?>            
                    </div>       
                </div>    
             <?php  } ?>
    <?php
endif;

if ((isset($property_term) && count($property_term) > 1) || (count($property_term) == 1 )):
    foreach ($property_term as $property) {
        if (in_array($property['id'], $properties_to_avoid)) {
            continue;
        }
        $ids[] = $property['id'];
         $all_properties[] = $property['id'];
                $term_properties_id[] = $property['id'];  
            ?>
            <div id="meta-item-<?php echo $property['id']; ?>"
                 property="<?php echo $property['id']; ?>"
                 type="term"
                 class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?>">
                <h2> <?php echo $property['name']; ?></h2>
                <div class="form-group">                     
                        <p><?php if($property['metas']['socialdb_property_help']){ echo $property['metas']['socialdb_property_help']; } ?></p> 
                        <?php if($property['type']=='radio'){ 
                            $properties_terms_radio[] = $property['id']; 
                            ?>
                            <div id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                            <input  type="hidden" 
                                    id='socialdb_propertyterm_<?php echo $property['id']; ?>_value'
                                    name="socialdb_propertyterm_<?php echo $property['id']; ?>_value" 
                                    value="">
                            <?php
                         }elseif($property['type']=='tree') { 
                            $properties_terms_tree[] = $property['id']; 
                             ?>
                            <button type="button"
                                onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'multiple_field_property_term_<?php echo $property['id']; ?>')" 
                                class="btn btn-primary btn-xs">
                                    <?php _e('Add Category','tainacan'); ?>
                            </button>
                             <br><br>  
                             <div style='height: 150px;'  id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                             <input type="hidden" 
                               id='socialdb_propertyterm_<?php echo $property['id']; ?>'
                               name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                               value="">
                           <?php
                         }elseif($property['type']=='selectbox') { 
                            $properties_terms_selectbox[] = $property['id']; 
                             ?>
                             <select onchange="setCategoriesSelect('<?php echo $property['id']; ?>',this)" 
                                     class="form-control" 
                                     name="multiple_socialdb_propertyterm_<?php echo $property['id']; ?>" 
                                     id='multiple_field_property_term_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                               
                             </select>
                             <input type="hidden" 
                                    id='socialdb_propertyterm_<?php echo $property['id']; ?>_value'
                                    name="socialdb_propertyterm_<?php echo $property['id']; ?>_value" 
                                    value="">
                            <?php
                          }elseif($property['type']=='checkbox') { 
                            $properties_terms_checkbox[] = $property['id']; 
                             ?>
                            <div id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                            <?php
                          }elseif($property['type']=='multipleselect') { 
                            $properties_terms_multipleselect[] = $property['id']; 
                             ?>
                             <select onchange="setCategoriesSelectMultiple('<?php echo $property['id']; ?>',this)" 
                                     multiple class="form-control" 
                                     name="multiple_socialdb_propertyterm_<?php echo $property['id']; ?>" 
                                     id='multiple_field_property_term_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>></select>
                            <?php
                          }elseif($property['type']=='tree_checkbox') { 
                            $properties_terms_treecheckbox[] = $property['id']; 
                             ?>
                             <button type="button"
                                onclick="showModalFilters('add_category','<?php echo get_term_by('id', $property['metas']['socialdb_property_term_root'] , 'socialdb_category_type')->name ?>',<?php echo $property['metas']['socialdb_property_term_root'] ?>,'multiple_field_property_term_<?php echo $property['id']; ?>')" 
                                class="btn btn-primary btn-xs">
                                    <?php _e('Add Category','tainacan'); ?>
                             </button>
                             <br><br>  
                              <div style='height: 150px;'   id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                              <div id='socialdb_propertyterm_<?php echo $property['id']; ?>' ></div>
                              <?php
                          }
                         ?> 
                    </div>  
                </div>
             <?php  } ?>
<?php endif;
?>
<input type="hidden" name="pc_properties" id='pc_properties' value="<?php echo implode(',', $ids); ?>">
<input type="hidden" name="categories" id='pc_categories' value="">
<input type="hidden" name="properties_autocomplete" id='pc_multiple_properties_autocomplete' value="<?php echo implode(',', $properties_autocomplete); ?>">
<input type="hidden" name="properties_terms_radio" id='pc_multiple_properties_terms_radio' value="<?php echo implode(',', $properties_terms_radio); ?>">
<input type="hidden" name="properties_terms_tree" id='pc_multiple_properties_terms_tree' value="<?php echo implode(',', $properties_terms_tree); ?>">
<input type="hidden" name="properties_terms_selectbox" id='pc_multiple_properties_terms_selectbox' value="<?php echo implode(',', $properties_terms_selectbox); ?>">
<input type="hidden" name="properties_terms_checkbox" id='pc_multiple_properties_terms_checkbox' value="<?php echo implode(',', $properties_terms_checkbox); ?>">
<input type="hidden" name="properties_terms_multipleselect" id='pc_multiple_properties_terms_multipleselect' value="<?php echo implode(',', $properties_terms_multipleselect); ?>">
<input type="hidden" name="properties_terms_treecheckbox" id='pc_multiple_properties_terms_treecheckbox' value="<?php echo implode(',', $properties_terms_treecheckbox); ?>">



