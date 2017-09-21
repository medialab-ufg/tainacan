<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/editor_items_js.php');
if(!isset($edit_multiple)){
    include_once (dirname(__FILE__).'/../js/multiple_draft_js.php');
}
include_once(dirname(__FILE__).'/../../../helpers/view_helper.php');

$view_helper = new ViewHelper($collection_id);

$properties_terms_radio = [];
$properties_terms_tree = [];
$properties_terms_selectbox = [];
$properties_terms_checkbox = [];
$properties_terms_multipleselect = [];
$properties_terms_treecheckbox = [];
$data_properties_id= [];
$object_properties= [];
$term_properties_id= [];
$all_properties= [];
$files= [];
$filesImage= [];
$filesVideo= [];
$filesAudio= [];
$filesPdf= [];
$filesOther= [];
?>
<div class="row" style="padding-right: 0px;padding-left: 0px;">
       
        <!-------------- METADADOS - BLOCO ESQUERDO (COL-MD-3) --------------------->
        <div style="
         display:none;
         background: white;
         border: 3px solid #E8E8E8;
         font: 11px Arial;
         max-height: 655px;
         overflow-y: scroll;
         min-height: 449px;" 
             id='form_properties_items' 
             class="col-md-3 menu_left_files menu-left-size">
            <h3 style="display:none;" id='labels_items_selected' ><?php _e('Editting ','tainacan') ?>
                <span id='number_of_items_selected'></span>
                <?php _e(' item/items ','tainacan') ?>
            </h3>
            <div class="expand-all-item btn white tainacan-default-tags">
                <div class="action-text" 
                     style="display: inline-block">
                         <?php _e('Expand all', 'tainacan') ?></div>
                &nbsp;&nbsp;<span class="glyphicon-triangle-bottom white glyphicon"></span>
            </div>
            <!---------------- FORMULARIO COM OS METADADOS DOS ITEMS -------------------------------------------------->
             <!--div class="list-group" id="accordion" aria-multiselectable="true">  
                <div class="list-group-item list-head" id="headingOne">  
                    <a style="cursor: pointer;" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" for="collapseOne">
                        <abel for="object_name">
                            <?php _e('Item name','tainacan'); ?>
                        </label>
                    </a>
                </div>  
                <div id="collapseOne" class="collapse in" aria-labelledby="headingOne">
                    <div class="list-group list-group-item form-group">   
                        <input class="form-control" 
                               type="text" 
                               class="form-control" 
                               id="multiple_object_name" 
                               name="object_name" 
                               required="required" 
                               onkeyup="setTitle(this)"
                               placeholder="<?php _e('Item name','tainacan'); ?>">
                    </div> 
                </div>
            </div-->  
       <div id="accordion_socialnetwork" class="multiple-items-accordion"> 
            <div id="item_name"
                 <?php echo $view_helper->get_visibility($view_helper->terms_fixed['title']) ?> 
                 >
                <h2> 
                    <?php echo ($view_helper->terms_fixed['title']) ? $view_helper->terms_fixed['title']->name :  _e('Title','tainacan') ?>
                </h2>
                <div class="form-group">                
                    <input 
                           type="text" 
                           class="form-control auto-save" 
                           id="multiple_object_name" 
                           name="object_name" 
                           required="required" 
                           onkeyup="setTitle(this)"
                           placeholder="<?php _e('Item name','tainacan'); ?>">
                </div> 
            </div> 
            <!-- TAINACAN: a descricao do item -->
            <div id="post_content" 
                 <?php echo $view_helper->get_visibility($view_helper->terms_fixed['description']) ?> 
                 >
                <h2> 
                    <?php echo ($view_helper->terms_fixed['description']) ? $view_helper->terms_fixed['description']->name :  __('Description','tainacan') ?> 
                </h2>
                <div id="object_description" class="form-group">          
                    <textarea class="form-control auto-save" 
                              id="multiple_object_description" 
                              onkeyup="setDescription(this)"
                               name="multiple_object_description" ></textarea>     
                </div>
            </div>    
            <div id="tag" 
                <?php echo $view_helper->get_visibility($view_helper->terms_fixed['tags']) ?>>
                <h2>
                    <?php echo ($view_helper->terms_fixed['tags']) ? $view_helper->terms_fixed['tags']->name :  _e('Tags','tainacan') ?> 
                </h2>
                <div class="form-group">                
                    <input onkeyup="setTags(this)" type="text" class="form-control auto-save" id="multiple_object_tags" placeholder="" name="object_tags" >
                    <span style="font-size: 7px;word-wrap: break-word;" class="label label-default">*<?php _e('The set of tags may be inserted by commas','tainacan') ?></span>
               </div> 
            </div>    
            <div id="socialdb_object_dc_source"
                <?php echo $view_helper->get_visibility($view_helper->terms_fixed['source']) ?>  
                 >
                <h2> 
                    <?php echo ($view_helper->terms_fixed['source']) ? $view_helper->terms_fixed['source']->name :  _e('Source','tainacan') ?>
                </h2>
                <div class="form-group">                
                    <input onkeyup="setSource(this)" type="text" class="form-control auto-save" id="multiple_object_source" name="object_source"  placeholder="<?php _e('Source of the item','tainacan') ?>">
                </div> 
            </div>       
        <?php
        // lista as propriedades de objeto da colecao atual
        if(isset($properties['property_object'])): ?>
            <!--h4><?php _e('Object Properties','tainacan'); ?></h4-->
            <?php foreach ($properties['property_object'] as $property) { 
                 $object_properties[] = $property['id']; 
                 $all_properties[] = $property['id'];
                 ?>
                <?php //if($property['metas']['socialdb_property_object_is_facet']=='false'): ?>
                     <div id="meta-item-<?php echo $property['id']; ?>"
                         property="<?php echo $property['id']; ?>"
                        class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?>">
                        <h2> <?php echo $property['name']; ?> </h2>
                        <div class="form-group">                                             
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
                                           container: '#configuration',
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
                                                     class="form-control title_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                                                     >
                                            </div>
                                            <button type="button" 
                                                    onclick="add_new_item_by_title('<?php echo $property['metas']['collection_data'][0]->ID; ?>','#add_item_popover_<?php echo $property['id']; ?>_<?php echo $object_id; ?>',<?php echo $property['id']; ?>,'<?php echo $object_id; ?>')"
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
                                   onkeyup="multiple_autocomplete_object_property_add('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" 
                                   id="multiple_autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" 
                                   placeholder="<?php _e('Type the three first letters of the object of this collection ','tainacan'); ?>"  
                                   class="chosen-selected form-control"  />  
                            <select onclick="clear_select_object_property(this,'<?php echo $property['id']; ?>');" 
                                    id="multiple_property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_add" 
                                    multiple class="chosen-selected2 form-control auto-save" 
                                    style="height: auto;" 
                                    name="socialdb_property_<?php echo $property['id']; ?>[]" 
                                        <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?> >
                                <?php if(!empty($property['metas']['objects'])){ ?>     

                                <?php }else { ?>   
                                     <option value=""><?php _e('No objects added in this collection','tainacan'); ?></option>
                                <?php } ?>       
                           </select>
                     </div>  
                </div>  
                <?php// endif; ?>
            <?php  } ?>
        <?php endif; 
        //lista as propriedades de dados da colecao atual
        if(isset($properties['property_data'])): ?>
            <!--h4><?php _e('Data properties','tainacan'); ?></h4-->
            <?php foreach ($properties['property_data'] as $property) { 
                $data_properties_id[] = $property['id'];  
                $data_properties[] = ['id'=>$property['id'],'default_value'=>$property['metas']['socialdb_property_default_value']];  
                $all_properties[] = $property['id']; ?>
                 <div id="meta-item-<?php echo $property['id']; ?>"
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
                                       class="form-control auto-save multiple_socialdb_property_<?php echo $property['id']; ?>" 
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
                                     class="form-control auto-save multiple_socialdb_property_<?php echo $property['id']; ?>"
                                     name="socialdb_property_<?php echo $property['id']; ?>" 
                                     <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                         <?php }elseif($property['type']=='autoincrement') {  ?>   
                              <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                      onchange="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                     disabled="disabled"  
                                      id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                     type="number" 
                                     class="form-control auto-save multiple_socialdb_property_<?php echo $property['id']; ?>" 
                                     name="only_showed_<?php echo $property['id']; ?>" value="<?php if(is_numeric($property['metas']['socialdb_property_data_value_increment'])): echo $property['metas']['socialdb_property_data_value_increment']+1; endif; ?>">
                              <!--input type="hidden"  name="socialdb_property_<?php echo $property['id']; ?>" value="<?php if($property['metas']['socialdb_property_data_value_increment']): echo $property['metas']['socialdb_property_data_value_increment']+1; endif; ?>" -->
                        <?php }else{ ?>
                              <input onblur="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                      onchange="setPropertyData(this,'<?php echo $property['id']  ?>')"
                                     type="date" 
                                      id='multiple_socialdb_property_<?php echo $property['id']; ?>'
                                     value="<?php if($property['metas']['socialdb_property_default_value']): echo $property['metas']['socialdb_property_default_value']; endif; ?>" 
                                     class="form-control auto-save multiple_socialdb_property_<?php echo $property['id']; ?>" 
                                     name="socialdb_property_<?php echo $property['id']; ?>" <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
                        <?php } ?> 
                              <?php echo $view_helper->render_button_cardinality($property,$i) ?>    
                            </div>         
                    <?php endfor;  ?>      
                    </div>              
                </div>              
             <?php  } ?>
        <?php endif; 
        //lista as propriedades de dados
         if((isset($properties['property_term']))): ?>
            <!--h4><?php _e('Term properties','tainacan'); ?></h4-->
            <?php foreach ( $properties['property_term'] as $property ) { 
                $all_properties[] = $property['id'];
                $term_properties_id[] = $property['id'];  
            ?>
             <div id="meta-item-<?php echo $property['id']; ?>"
                 property="<?php echo $property['id']; ?>"
                 class="category-<?php echo $property['metas']['socialdb_property_created_category'] ?>">
                    <h2> <?php echo $property['name']; ?> </h2>
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
                             <div style='height: 150px;overflow: scroll;'  id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                                 <!--select name='socialdb_propertyterm_<?php echo $property['id']; ?>' size='2' class='col-lg-6' id='socialdb_propertyterm_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>></select-->
                            <input type="hidden" 
                               id='socialdb_propertyterm_<?php echo $property['id']; ?>'
                               name="socialdb_propertyterm_<?php echo $property['id']; ?>" 
                               value="">
                             <?php
                         }elseif($property['type']=='selectbox') { 
                            $properties_terms_selectbox[] = $property['id']; 
                             ?>
                             <select onchange="setCategoriesSelect('<?php echo $property['id']; ?>',this)" class="form-control auto-save" name="multiple_socialdb_propertyterm_<?php echo $property['id']; ?>" id='multiple_field_property_term_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>>
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
                                     multiple class="form-control auto-save" 
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
                            <div style='height: 150px;overflow: scroll;'   id='multiple_field_property_term_<?php echo $property['id']; ?>'></div>
                                 <!--select multiple size='6' class='col-lg-6' name='socialdb_propertyterm_<?php echo $property['id']; ?>[]' id='socialdb_propertyterm_<?php echo $property['id']; ?>' <?php if($property['metas']['socialdb_property_required']=='true'): echo 'required="required"'; endif; ?>></select -->
                            <div id='socialdb_propertyterm_<?php echo $property['id']; ?>' ></div>
                            <?php
                          }
                         ?> 
                    </div>             
                   <div id="append_properties_categories_<?php echo $property['id']; ?>"></div>
                </div>
             <?php  } ?>
        <?php endif; ?>
            <!-- TAINACAN: a licencas do item -->
            <div id="list_licenses_items"
                 <?php echo $view_helper->get_visibility($view_helper->terms_fixed['license']) ?>  
                 >
                <h2>
                    <?php echo ($view_helper->terms_fixed['license']) ? $view_helper->terms_fixed['license']->name :  __('Licenses','tainacan') ?> 
                </h2>
                <div id="show_form_licenses"></div>
            </div>           
            <div id="create_list_ranking"></div>        
        </div> <!-- Closes #accordion --> 
        <?php if(isset($all_ids)): ?>
        <input type="hidden" name="properties_id" value="<?php echo $all_ids; ?>">
        <?php endif; ?>

    </div> 
    <div id='no_properties_items' style="height: 655px;background: white;border: 3px solid #E8E8E8;font: 11px Arial;"  
         class="col-md-3 menu-left-size">
         <h3> <?php _e('Select items to edit...','tainacan') ?> </h3>
    </div>
    <div id='selectingAttachment'
         style="height: 655px;display:none;background: white;border: 3px solid #E8E8E8;font: 11px Arial;"  
         class="col-md-3 menu-left-size">
         <h3 ><?php _e('Select attachments to ','tainacan') ?>
             <span id="nameItemAttachment"></span>
         </h3>
    </div>
<!------------------------------- LISTA ITEMS UPADOS - BLOCO CENTRO DIREITO (COL-MD-9) -------------------------------------------------------------->
    <form id='sumbit_multiple_items'>
        <div class='col-md-9' id="no_item_uploaded" style='display:none;'>
            <h3 style="text-align: center;"><?php _e('No items uploaded','tainacan') ?></h3>
        </div>
        <div class='col-md-9 pull-right' 
             style="background-color: white;border: 3px solid #E8E8E8;margin-left: 15px;">
            <?php if(!isset($is_beta_file)): ?>
            <h3>
                <?php if(isset($edit_multiple)): ?> 
                    <?php _e('Edit multiple items','tainacan') ?>
                    <button type="button" onclick="back_main_list();"
                            class="btn btn-default pull-right"> 
                                <?php _e('Cancel','tainacan') ?>
                    </button>
                <?php else: ?> 
                    <?php _e('Add new item - Insert URL','tainacan') ?>
                    <button type="button" onclick="back_main_list_socialnetwork();"
                            class="btn btn-default pull-right"> 
                                <?php _e('Cancel','tainacan') ?>
                    </button>
                <?php endif; ?> 
            </h3>
            <?php else: ?>
            <h3>
                <?php _e('Continue editting...  Insert URL','tainacan') ?>
                <button type="button" onclick="back_main_list_discard();"
                        class="btn btn-default pull-right"> 
                            <?php _e('Cancel','tainacan') ?>
                </button>
                <br>
                <small id="draft-text"></small>
            </h3>
            <?php endif ?>
            <hr>
            <!----------------------------- BUTTONS -------------------------------------->
           <div style="padding-bottom: 20px;" >
               <div class="btn-group">
                   <button id="selectOptions" type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <?php _e('Select','tainacan') ?> <span class="caret"></span>
                   </button>
                   <ul class="dropdown-menu">
                       <li><a onclick='selectAll()'  style="cursor: pointer;"> <?php _e('All','tainacan') ?></a></li>
                     <li><a onclick='unselectAll()'  style="cursor: pointer;"><?php _e('None','tainacan') ?></a></li>
                   </ul>
               </div>    
               <button id="removeSelectedButton"  onclick='removeSelected()' type="button" class="btn btn-default" >
                   <span  class="glyphicon glyphicon-trash"></span>
               </button>
               <button id="buttonSelectedAttachments" style="display: none;" onclick='selectedAttachments()' type="button" class="btn btn-default" >
                   <?php _e('Select Attachments','tainacan') ?>
               </button>
               <button id="buttonBackItems" style="display: none;" onclick='backItemsEditting()' type="button" class="btn btn-default" >
                   <?php _e('Edit Items','tainacan') ?>
               </button>
           </div>
           <!----------------------------- BUTTONS -------------------------------------->
           <div style="max-height: 500px;overflow-y: scroll">
                <div  id="selectable">
                <?php 
                // textos
                if(is_array($items['text'])){ 
                    ?>
                      <div  id="container_text"  class='col-md-12'>
                        <h4>
                            <input class="class_selected_items" 
                                   type='checkbox' id='selectAllImages' 
                                   onclick="selectImages()" value='#'> 
                            &nbsp;<?php _e('Text/Url','tainacan') ?>
                        </h4>                   
                        <?php
                        foreach ($items['text'] as $file) { 
                            $files[] = $file['ID'];
                            $filesImage[] = $file['ID'];
                            ?>
                            <div  id="wrapper_<?php echo $file['ID'] ?>"  
                                  class="col-md-3 item-default" 
                                style="padding-top: 20px;cursor: pointer;">
                                <center>
                                    <div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->   
                                        <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>" onchange="selectedItems()" type="checkbox" name="selected_items"  value="<?php echo $file['ID'] ?>">
                                        <input id="attachment_option_<?php echo $file['ID'] ?>"  onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                        <?php 
                                        if(get_the_post_thumbnail($file['ID'], 'thumbnail')){
                                           echo get_the_post_thumbnail($file['ID'], 'thumbnail');
                                        }else{ ?>
                                              <img src="<?php echo get_item_thumbnail_default($file['ID']); ?>" class="img-responsive">
                                        <?php }  ?> 
                                    </div>     
                                    <input required="required" 
                                           style="margin-top: 10px;" 
                                           placeholder="<?php _e('Add a title','tainacan') ?>" 
                                           type="text" 
                                           class='input_title'
                                           id='title_<?php echo $file['ID'] ?>' 
                                           name='title_<?php echo $file['ID'] ?>' 
                                           value='<?php echo $file['name'] ?>'>
                                    <!-- Hidden para as categorias, tags e attachments  -->
                                    <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value='<?php  echo $file['source'] ?>'>
                                    <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='image'>
                                    <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value='<?php  echo $file['content'] ?>'>
                                    <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                     <input type="hidden" id='license_<?php echo $file['ID'] ?>' name="license_<?php echo $file['ID'] ?>" value=''>
                                    <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                    <?php 
                                    if(is_array($data_properties)):
                                        foreach ($data_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                    value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>
                                    <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                    <?php 
                                    if(is_array($object_properties)):
                                        foreach ($object_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                    value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>    
                                    <!-- hiddens para valores das propriedades de TERMO dos items a serem criados -->
                                    <?php 
                                    if(is_array($term_properties_id)):
                                        foreach ($term_properties_id as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                    value=''>
                                    <?php  } 
                                    endif;   
                                    ?>    
                                </center>          
                            </div>    
                          <?php         
                        }
                    ?>
                    </div>
                    <?php
                }
                // images
                if(is_array($items['image'])){ 
                    ?>
                      <div  id="container_images"  class='col-md-12'>
                        <h4>
                            <input class="class_selected_items" 
                                   type='checkbox' id='selectAllImages' 
                                   onclick="selectImages()" value='#'> 
                            &nbsp;<?php _e('Image Files','tainacan') ?>
                        </h4>                   
                        <?php
                        foreach ($items['image'] as $file) { 
                            $files[] = $file['ID'];
                            $filesImage[] = $file['ID'];
                            ?>
                            <div  id="wrapper_<?php echo $file['ID'] ?>"  
                                  class="col-md-3 item-default" 
                                style="padding-top: 20px;cursor: pointer;">
                                <center>
                                    <div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->   
                                        <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>" onchange="selectedItems()" type="checkbox" name="selected_items"  value="<?php echo $file['ID'] ?>">
                                        <input id="attachment_option_<?php echo $file['ID'] ?>"  onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                        <?php 
                                        if(get_the_post_thumbnail($file['ID'], 'thumbnail')){
                                           echo get_the_post_thumbnail($file['ID'], 'thumbnail');
                                        }else{ ?>
                                              <img src="<?php echo get_item_thumbnail_default($file['ID']); ?>" class="img-responsive">
                                        <?php }  ?> 
                                    </div>     
                                    <input required="required" 
                                           style="margin-top: 10px;" 
                                           placeholder="<?php _e('Add a title','tainacan') ?>" 
                                           type="text" 
                                           class='input_title'
                                           id='title_<?php echo $file['ID'] ?>' 
                                           name='title_<?php echo $file['ID'] ?>' 
                                           value='<?php echo $file['name'] ?>'>
                                    <!-- Hidden para as categorias, tags e attachments  -->
                                    <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value='<?php  echo $file['source'] ?>'>
                                    <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='image'>
                                    <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value='<?php  echo $file['content'] ?>'>
                                    <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                     <input type="hidden" id='license_<?php echo $file['ID'] ?>' name="license_<?php echo $file['ID'] ?>" value=''>
                                    <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                    <?php 
                                    if(is_array($data_properties)):
                                        foreach ($data_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                     value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>
                                    <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                    <?php 
                                    if(is_array($object_properties)):
                                        foreach ($object_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                     value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>    
                                    <!-- hiddens para valores das propriedades de TERMO dos items a serem criados -->
                                    <?php 
                                    if(is_array($term_properties_id)):
                                        foreach ($term_properties_id as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                    value=''>
                                    <?php  } 
                                    endif;   
                                    ?>    
                                </center>          
                            </div>    
                          <?php         
                        }
                    ?>
                    </div>
                    <?php
                }
                // videos
                if(is_array($items['videos'])){ 
                    ?>
                     <div id="container_videos" class='col-md-12'>
                    <h4>
                        <input class="class_selected_items" 
                               type='checkbox' 
                               id='selectAllVideo'  
                               onclick="selectVideo()" value='#'>
                        &nbsp;<?php _e('Videos Files','tainacan') ?>
                    </h4>
                    <?php
                        foreach ($items['videos'] as $file) { 
                            $files[] = $file['ID'];
                            $filesVideo[] = $file['ID'];
                            ?>
                            <div  id="wrapper_<?php echo $file['ID'] ?>"  
                                  class="col-md-3 item-default" 
                                style="padding-top: 20px;cursor: pointer;">
                                <center><div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                                   <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>" onchange="selectedItems()" type="checkbox" name="selected_items" value="<?php echo $file['ID'] ?>" >
                                   <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                   <?php 
                                     if(get_the_post_thumbnail($file['ID'], 'thumbnail')){
                                        echo get_the_post_thumbnail($file['ID'], 'thumbnail');
                                     }else{ ?>
                                           <img src="<?php echo get_item_thumbnail_default($file['ID']); ?>" class="img-responsive">
                                     <?php }  ?>  
                                   </div>     
                                    <input required="required" 
                                           style="margin-top: 10px;" 
                                           placeholder="<?php _e('Add a title','tainacan') ?>" 
                                           type="text" 
                                           class='input_title'
                                           id='title_<?php echo $file['ID'] ?>' 
                                           name='title_<?php echo $file['ID'] ?>' 
                                           value='<?php echo $file['name'] ?>'>                                   <!-- Hidden para as categorias, tags e attachments  -->
                                   <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value='<?php  echo $file['source'] ?>'>
                                   <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='video'>
                                   <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                   <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                   <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value='<?php  echo $file['content'] ?>'>
                                   <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                   <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                   <input type="hidden" id='license_<?php echo $file['ID'] ?>' name="license_<?php echo $file['ID'] ?>" value=''>
                                   <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                   <?php 
                                   if(is_array($data_properties)):
                                       foreach ($data_properties as $value) { ?>
                                            <input type="hidden" 
                                                   name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                   id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                    value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                   <?php  } 
                                   endif;   
                                   ?>
                                   <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                   <?php 
                                   if(is_array($object_properties)):
                                       foreach ($object_properties as $value) { ?>
                                            <input type="hidden" 
                                                   name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                   id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                    value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                   <?php  } 
                                   endif;   
                                   ?>      
                                  <?php 
                                   if(is_array($term_properties_id)):
                                       foreach ($term_properties_id as $value) { ?>
                                            <input type="hidden" 
                                                   name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                   id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                   value=''>
                                   <?php  } 
                                   endif;   
                                   ?> 
                                </center>    
                            </div>    
                          <?php         
                        }
                    ?>
                    </div>
                    <hr>
                    <?php
                }
                // mostra os itens do tipo pdf
                if(is_array($items['pdf'])){ 
                    ?>
                    <div id="container_pdfs" class='col-md-12'>
                        <h4>
                            <input class="class_selected_items"
                                   type='checkbox' 
                                   id='selectAllPdf' 
                                   onclick="selectPdf()" 
                                   value='#'> &nbsp;<?php _e('PDF Files','tainacan') ?>
                        </h4>
                    <?php
                        foreach ($items['pdf'] as $file) { 
                            $files[] = $file['ID'];
                            $filesPdf[] = $file['ID'];
                            ?>
                        <div  id="wrapper_<?php echo $file['ID'] ?>"  
                                  class="col-md-3 item-default" 
                                style="padding-top: 20px;cursor: pointer;">
                                <center><div class="item"  style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                                    <input class="class_selected_items" 
                                           id="item_option_<?php echo $file['ID'] ?>" 
                                           onchange="selectedItems()" 
                                           type="checkbox" 
                                           style="display:none"
                                           name="selected_items" 
                                           value="<?php echo $file['ID'] ?>" >
                                    <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                    <?php echo wp_get_attachment_image( $file['ID'],'thumbnail',1,['alt'   =>'' ] ); ?>  
                                     </div>     
                                     <input required="required" 
                                           style="margin-top: 10px;" 
                                           placeholder="<?php _e('Add a title','tainacan') ?>" 
                                           type="text" 
                                           class='input_title'
                                           id='title_<?php echo $file['ID'] ?>' 
                                           name='title_<?php echo $file['ID'] ?>' 
                                           value='<?php echo $file['name'] ?>'>                                    <!-- Hidden para as categorias, tags e attachments  -->
                                    <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value='<?php  echo $file['source'] ?>'>
                                    <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='pdf'>
                                    <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                     <input type="hidden" id='license_<?php echo $file['ID'] ?>' name="license_<?php echo $file['ID'] ?>" value=''>
                                    <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                    <?php 
                                    if(is_array($data_properties)):
                                        foreach ($data_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                    value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>
                                    <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                    <?php 
                                    if(is_array($object_properties)):
                                        foreach ($object_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                     value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>     
                                    <?php 
                                    if(is_array($term_properties_id)):
                                        foreach ($term_properties_id as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                    value=''>
                                    <?php  } 
                                    endif;   
                                    ?> 
                                </center>               
                            </div>    
                          <?php         
                        }
                    ?>
                    </div>
                    <?php
                }
                  // AUDIO
                if(is_array($items['audio'])){ 
                    ?>
                   <div id="container_audios" class='col-md-12'>
                <h4>
                    <input class="class_selected_items" 
                           type='checkbox' 
                           id='selectAllAudio' 
                           onclick="selectAudio()" 
                           value='#'>
                    &nbsp;<?php _e('Audio Files','tainacan') ?>
                </h4>
                    <?php
                        foreach ($items['audio'] as $file) {
                            $files[] = $file['ID'];
                            $filesAudio[] = $file['ID'];
                            ?>
                             <div  id="wrapper_<?php echo $file['ID'] ?>"  
                                  class="col-md-3 item-default" 
                                style="padding-top: 20px;cursor: pointer;">
                                <center><div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                                    <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>"  onchange="selectedItems()" type="checkbox" name="selected_items" value="<?php echo $file['ID'] ?>" >
                                    <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                    <?php echo wp_get_attachment_image( $file['ID'],'thumbnail',1,['alt'   =>'' ] ); ?>  
                                     </div>     
                                     <input required="required" 
                                           style="margin-top: 10px;" 
                                           placeholder="<?php _e('Add a title','tainacan') ?>" 
                                           type="text" 
                                           class='input_title'
                                           id='title_<?php echo $file['ID'] ?>' 
                                           name='title_<?php echo $file['ID'] ?>' 
                                           value='<?php echo $file['name'] ?>'>                                    <!-- Hidden para as categorias, tags e attachments  -->
                                     <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value='<?php  echo $file['source'] ?>'>
                                    <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='audio'>
                                    <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                     <input type="hidden" id='license_<?php echo $file['ID'] ?>' name="license_<?php echo $file['ID'] ?>" value=''>
                                    <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                    <?php 
                                    if(is_array($data_properties)):
                                        foreach ($data_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                    value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>
                                    <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                    <?php 
                                    if(is_array($object_properties)):
                                        foreach ($object_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                     value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>
                                    <?php 
                                    if(is_array($term_properties_id)):
                                        foreach ($term_properties_id as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                    value=''>
                                    <?php  } 
                                    endif;   
                                    ?>  
                                 </center>  
                            </div>    
                          <?php         
                        }
                    ?>
                    </div>
                    <hr>
                    <?php
                }
                 // OUTROS
                if(is_array($items['others'])){ 
                    ?>
                    <div id="container_others" class='col-md-12'>
                <h4>
                    <input class="class_selected_items" 
                           type='checkbox' 
                           id='selectAllOther' 
                           onclick="selectOther()" 
                           value='#'> &nbsp;<?php _e('Others Files','tainacan') ?>
                </h4>
                    <?php
                        foreach ($items['others'] as $file) { 
                            $files[] = $file['ID'];
                            $filesOther[] = $file['ID'];
                            ?>
                             <div  id="wrapper_<?php echo $file['ID'] ?>"  
                                  class="col-md-3 item-default" 
                                style="padding-top: 20px;cursor: pointer;">
                                <center><div class="item" style="padding-top: 20px;padding-bottom: 20px;cursor: pointer;" id="panel_<?php echo $file['ID'] ?>"  onclick="focusItem('<?php echo $file['ID'] ?>')" ><!-- container do item -->      
                                    <input style="display:none" class="class_selected_items" id="item_option_<?php echo $file['ID'] ?>"  onchange="selectedItems()" type="checkbox" name="selected_items" value="<?php echo $file['ID'] ?>" >
                                    <input id="attachment_option_<?php echo $file['ID'] ?>" onchange="manipulateAttachaments('<?php echo $file['ID'] ?>')" class="class_checkboxAttachments" style="display:none" type="checkbox" name="checkboxAttachments"  value="<?php echo $file['ID'] ?>">
                                    <?php echo wp_get_attachment_image( $file['ID'],'thumbnail',1,['alt'   =>'' ] ); ?>  
                                     </div>     
                                     <input required="required" 
                                           style="margin-top: 10px;" 
                                           placeholder="<?php _e('Add a title','tainacan') ?>" 
                                           type="text" 
                                           class='input_title'
                                           id='title_<?php echo $file['ID'] ?>' 
                                           name='title_<?php echo $file['ID'] ?>' 
                                           value='<?php echo $file['name'] ?>'>                                    <!-- Hidden para as categorias, tags e attachments  -->
                                    <input type="hidden" id="source_<?php echo $file['ID'] ?>" name="source_<?php echo $file['ID'] ?>" value='<?php  echo $file['source'] ?>'>
                                    <input type="hidden" name="type_<?php echo $file['ID'] ?>" value='other'>
                                    <input type="hidden" id='parent_<?php echo $file['ID'] ?>' name="parent_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='attachments_<?php echo $file['ID'] ?>' name="attachments_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='description_<?php echo $file['ID'] ?>' name="description_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='categorias_<?php echo $file['ID'] ?>' name="categorias_<?php echo $file['ID'] ?>" value=''>
                                    <input type="hidden" id='tags_<?php echo $file['ID'] ?>' name="tags_<?php echo $file['ID'] ?>" value='<?php  echo $file['tags'] ?>'>
                                     <input type="hidden" id='license_<?php echo $file['ID'] ?>' name="license_<?php echo $file['ID'] ?>" value=''>
                                    <!-- hiddens para valores das propriedades de dados dos items a serem criados -->
                                    <?php 
                                    if(is_array($data_properties)):
                                        foreach ($data_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value['id'] ?>_<?php echo $file['ID'] ?>'
                                                     value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>
                                    <!-- hiddens para valores das propriedades de OBJETO dos items a serem criados -->
                                    <?php 
                                    if(is_array($object_properties)):
                                        foreach ($object_properties as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                     value='<?php if($file['properties'][$value['id']]&&!empty($file['properties'][$value['id']])): echo implode(',', $file['properties'][$value['id']]); endif; ?>'>
                                    <?php  } 
                                    endif;   
                                    ?>
                                    <?php 
                                    if(is_array($term_properties_id)):
                                        foreach ($term_properties_id as $value) { ?>
                                             <input type="hidden" 
                                                    name='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>' 
                                                    id='socialdb_property_<?php echo $value ?>_<?php echo $file['ID'] ?>'
                                                    value=''>
                                    <?php  } 
                                    endif;   
                                    ?> 
                               </center>               
                            </div>    
                          <?php         
                        }
                    ?>
                    </div>
                    <?php
                }
                ?>
                </div>    
            </div>
            <!--------------- FIM: container todos os itens  ----------------------------->
            <div style="display: none" class="col-md-12" id='attachments_item_upload'>
                 <h3><?php _e('Attachments','tainacan'); ?></h3>
                 <div  id="dropzone_new" class="dropzone" style="min-height: 150px;">
                 </div>
             </div>
              <div class="col-md-12" style="padding: 15px;">
                 <?php if(isset($edit_multiple)): ?> 
                  <input type="hidden" id="edit_multiple" name="edit_multiple" value="true">
                 <button type="button" onclick="back_main_list();"
                        class="btn btn-lg btn-default pull-left"> 
                            <?php _e('Cancel','tainacan') ?>
                </button>
                 <?php else: ?>   
                 <button type="button" onclick="back_main_list_socialnetwork();"
                        class="btn btn-lg btn-default pull-left"> 
                            <?php _e('Cancel','tainacan') ?>
                </button>
                 <?php endif; ?>   
                 <button type="submit" 
                          
                         id="submit_button" 
                         class="btn btn-lg btn-success pull-right">
                             <?php _e('Submit','tainacan'); ?>
                 </button>
             </div>
        </div>    
        <div class="col-md-12">
         <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
        <input type="hidden" name="operation" value="add_multiples_socialnetwork">
        <input type="hidden" name="multiple_properties_terms_radio" id='multiple_properties_terms_radio' value="<?php echo implode(',',$properties_terms_radio); ?>">
        <input type="hidden" name="multiple_properties_terms_tree" id='multiple_properties_terms_tree' value="<?php echo implode(',',$properties_terms_tree); ?>">
        <input type="hidden" name="multiple_properties_terms_selectbox" id='multiple_properties_terms_selectbox' value="<?php echo implode(',',$properties_terms_selectbox); ?>">
        <input type="hidden" name="multiple_properties_terms_checkbox" id='multiple_properties_terms_checkbox' value="<?php echo implode(',',$properties_terms_checkbox); ?>">
        <input type="hidden" name="multiple_properties_terms_multipleselect" id='multiple_properties_terms_multipleselect' value="<?php echo implode(',',$properties_terms_multipleselect); ?>">
        <input type="hidden" name="multiple_properties_terms_treecheckbox" id='multiple_properties_terms_treecheckbox' value="<?php echo implode(',',$properties_terms_treecheckbox); ?>">
        <input type="hidden" id='multiple_properties_data_id' name="multiple_properties_data_id" value="<?php echo implode(',', $data_properties_id); ?>">
        <input type="hidden" id='multiple_properties_object_id' name="multiple_properties_object_id" value="<?php echo implode(',', $object_properties); ?>">
        <input type="hidden" id='multiple_properties_term_id' name="multiple_properties_term_id" value="<?php echo implode(',', $term_properties_id); ?>">
        <input type="hidden" id='properties_id' name="properties_id" value="<?php echo implode(',', $all_properties); ?>">
        <input type="hidden" id='selected_items_id'  name="selected_items_id" value="">
        <input type="hidden" id='items_id'  name="items_id" value="<?php echo implode(',', $files); ?>">
        <input type="hidden" id='items_images'  name="items_image" value="<?php echo implode(',', $filesImage); ?>">
        <input type="hidden" id='items_video'  name="items_video" value="<?php echo implode(',', $filesVideo); ?>">
        <input type="hidden" id='items_audio'  name="items_audio" value="<?php echo implode(',', $filesAudio); ?>">
        <input type="hidden"  id='items_pdf' name="items_pdf" value="<?php echo implode(',', $filesPdf); ?>">
        <input type="hidden" id='items_other'  name="items_other" value="<?php echo implode(',', $filesOther); ?>">
        <input type="hidden" id="property_origin" name="property_origin" value="<?php echo $all_ids; ?>">
        <input type="hidden" id="property_added" name="property_added" value="">
        <input type="hidden" id="selected_categories" name="selected_categories" value="">
        <?php if(isset($edit_multiple)): ?>
         <input type="hidden" id="edit_multiple" name="edit_multiple" value="true">
        <?php endif; ?>
        <div id="append_properties_categories" class="hide"></div>
        </div>
    </form>    
</div>