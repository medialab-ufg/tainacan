<?php
/*
 * 
 * View responsavel em listar todas propriedades do objeto em questao, utilizada para pegar os valores para edicao dos eventos
 */

include_once ('js/show_list_event_properties_js.php');
$properties_terms_radio = [];
$properties_terms_tree = [];
$properties_terms_selectbox = [];
$properties_terms_checkbox = [];
$properties_terms_multipleselect = [];
$properties_terms_treecheckbox = [];
?>

<?php if (!isset($property_object) && !isset($property_data)): ?>
    <?php _e('No Properties available', 'tainacan'); ?>
<?php endif; ?>
<?php if (isset($property_object)):
    ?>
    <!-- TAINACAN: este container responsavel em listar todas propriedades  com seus widgets gerado dinamicamente --> 
    <h4><?php _e('Object Properties', 'tainacan'); ?></h4>
    <?php
    foreach ($property_object as $property) {
        $object_id = $property['metas']['object_id'];
        //if ($property['metas']['socialdb_property_object_is_facet'] == 'false'):
        ?>
        <div class="form-group">
            <label for="object_tags"><?php echo $property['name']; ?></label>
            <button type="button" onclick="cancel_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
            <button type="button" onclick="edit_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
            <button type="button" onclick="save_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button> <a target="_blank" class="btn btn-primary btn-xs" href="<?php echo get_permalink($property['metas']['collection_data'][0]->ID); ?>"><?php _e('Add new', 'tainacan'); ?><?php echo ' ' . $property['metas']['collection_data'][0]->post_title; ?></a>
            <input type="text" onkeyup="autocomplete_object_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');" id="autocomplete_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" placeholder="<?php _e('Type the three first letters of the object ', 'tainacan'); ?>"  class="chosen-selected form-control" disabled="disabled" />
            <select onclick="clear_select_object_property(this,'<?php echo $property['id']; ?>', '<?php echo $object_id; ?>');"  id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" multiple class="chosen-selected2 form-control" style="height: auto;" multiple name="category_moderators[]" id="chosen-selected2-user" disabled="disabled" >
                <?php if (!empty($property['metas']['objects'])) { ?>
                    <?php foreach ($property['metas']['objects'] as $object) { // percoro todos os objetos  ?>
                        <?php if (isset($property['metas']['value']) && !empty($property['metas']['value']) && in_array($object->ID, $property['metas']['value'])): // verifico se ele esta na lista de objetos da colecao  ?>    
                            <option  selected='selected' value="<?php echo $object->ID ?>"><?php echo $object->post_title ?></span>
                            <?php endif; ?>
                        <?php } ?> 
                    <?php }else { ?>   
                    <option value=""><?php _e('No objects added in this collection', 'tainacan'); ?></option>
                <?php } ?>             
            </select>
            <input type="hidden" id="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" name="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" value="<?php if (is_array($property['metas']['value'])) echo implode(',', is_array($property['metas']['value'])); ?>">
        </div>  
        <?php //endif; ?>
    <?php } ?>
<?php endif; ?>

<?php if (isset($property_data)): ?>
    <h4><?php _e('Data Properties', 'tainacan'); ?></h4>
    <?php
    foreach ($property_data as $property) {
        $object_id = $property['metas']['object_id'];
        ?>
        <div class="form-group">
            <label ><?php echo $property['name']; ?></label> 
            <p class="help-block"><?php
                echo "<b>";
                _e('Help: ', 'tainacan');
                echo "</b>";
                if ($property['metas']['socialdb_property_help']) {
                    echo $property['metas']['socialdb_property_help'];
                }
                ?></p>
            <?php if ($property['type'] !== 'autoincrement'): ?>
                <button type="button" onclick="cancel_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                <button type="button" onclick="edit_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button>
                <button type="button" onclick="save_data_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" style="display: none;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
            <?php else: ?>
                (<?php _e('Auto-Increment', 'tainacan') ?>)
            <?php endif; ?>
            <?php if ($property['type'] == 'text') { ?>     
                <input  disabled="disabled" value="<?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>" type="text" id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="form-control" name="socialdb_property_<?php echo $property['id']; ?>" <?php
                if (!$property['metas']['socialdb_property_required']): echo 'required="required"';
                endif;
                ?>>
            <?php }elseif ($property['type'] == 'textarea') { ?>   
                <textarea rows="15" disabled="disabled" id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="form-control" name="socialdb_property_<?php echo $property['id']; ?>" <?php
                          if (!$property['metas']['socialdb_property_required']): echo 'required="required"';
                          endif;
                          ?>><?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?></textarea>
            <?php }elseif ($property['type'] == 'numeric') { ?>   
                <input disabled="disabled" 
                       value="<?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>" 
                        type="text" 
                        onkeypress='return onlyNumbers(event)'
                       id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="form-control" name="socialdb_property_<?php echo $property['id']; ?>" <?php
                       if (!$property['metas']['socialdb_property_required']): echo 'required="required"';
                       endif;
                       ?>>
            <?php
            }else {
                $property['metas']['value'][0] = implode('/', array_reverse(explode('-', $property['metas']['value'][0])));
                ?> 
                <input disabled="disabled" value="<?php if ($property['metas']['value']) echo $property['metas']['value'][0]; ?>" id="property_value_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" type="text" class="form-control input_date" name="socialdb_property_<?php echo $property['id']; ?>" <?php
            if (!$property['metas']['socialdb_property_required']): echo 'required="required"';
            endif;
                ?>>
        <?php } ?> 
            <input type="hidden" id="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" name="property_<?php echo $property['id']; ?>_<?php echo $object_id; ?>_value_before" value="<?php if (is_array($property['metas']['value'])) echo implode(',', is_array($property['metas']['value'])); ?>">
        </div>              
    <?php } ?>
<?php
endif;

if (isset($property_term)):
    ?>
    <div <?php do_action('item_property_term_attributes') ?>>
     <h4><?php _e('Term properties', 'tainacan'); ?></h4>
    <?php
    foreach ($property_term as $property) {
        if (count($property['has_children']) > 0):
            ?>
            <div class="form-group">
                <label ><?php echo $property['name']; ?></label> 
                    <!--button type="button" onclick="cancel_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="cancel_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" style="display: none;" ><span class="glyphicon glyphicon-arrow-left" ></span></button>
                    <button type="button" onclick="edit_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="edit_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-edit"></span></button-->
                    <!--button type="button" onclick="save_term_property('<?php echo $property['id']; ?>', '<?php echo $object_id; ?>')" id="save_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"class="btn btn-default btn-xs" ><span class="glyphicon glyphicon-floppy-disk"></span></button-->
                <p><?php
            if ($property['metas']['socialdb_property_help']) {
                echo $property['metas']['socialdb_property_help'];
            }
            ?></p> 
                <?php
                if ($property['type'] == 'radio') {
                    $properties_terms_radio[] = $property['id'];
                    ?>
                    <!-- TAINACAN: div (Ajax), opcoes de selecao montadas via javascript -->
                    <div id='field_event_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                    <input type="hidden" value="" name="value_radio_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_radio_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                    <?php
                } elseif ($property['type'] == 'tree') {
                    $properties_terms_tree[] = $property['id'];
                    ?>
                           <div class="row">
                        <div style='height: 150px;overflow: scroll;' class='col-lg-6'  id='field_event_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                        <!-- TAINACAN: div (Ajax), opcoes de selecao montadas via javascript -->
                        <select name='socialdb_propertyterm_<?php echo $property['id']; ?>' size='2' class='col-lg-6' id='socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                        <input type="hidden" value="" name="value_tree_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_tree_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                    </div>
                    <?php
                } elseif ($property['type'] == 'selectbox') {
                    $properties_terms_selectbox[] = $property['id'];
                    ?>
                    <!-- TAINACAN: div (Ajax), opcoes de selecao montadas via javascript -->
                    <select onchange="get_event_select(this,<?php echo $property['id']; ?>,<?php echo $object_id; ?>)" class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id='field_event_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                    <input type="hidden" value="" name="value_select_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id="value_select_<?php echo $property['id']; ?>_<?php echo $object_id; ?>"
                           <?php
                       } elseif ($property['type'] == 'checkbox') {
                           $properties_terms_checkbox[] = $property['id'];
                           ?>
                           <!-- TAINACAN: div (Ajax), opcoes de selecao montadas via javascript -->
                           <div id='field_event_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                    <?php
                } elseif ($property['type'] == 'multipleselect') {
                    $properties_terms_multipleselect[] = $property['id'];
                    ?>
                    <!-- TAINACAN: div (Ajax), opcoes de selecao montadas via javascript -->
                    <select  multiple class="form-control" name="socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>" id='field_event_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                    <?php
                } elseif ($property['type'] == 'tree_checkbox') {
                    $properties_terms_treecheckbox[] = $property['id'];
                    ?>
                    <div class="row">
                        <!-- TAINACAN: div (Ajax), opcoes de selecao montadas via javascript -->
                        <div style='height: 150px;overflow: scroll;' class='col-lg-6'  id='field_event_property_term_<?php echo $property['id']; ?>_<?php echo $object_id; ?>'></div>
                        <select onclick="remove_classification_multiple('<?php _e('Remove classification', 'tainacan') ?>', '<?php _e('Are you sure to remove this classification', 'tainacan') ?>', $(this).val(),<?php echo $object_id; ?>, '<?php echo mktime(); ?>')" multiple size='6' class='col-lg-6' name='socialdb_propertyterm_<?php echo $property['id']; ?>[]' id='socialdb_propertyterm_<?php echo $property['id']; ?>_<?php echo $object_id; ?>' ></select>
                    </div>
                <?php
            }
            ?> 
            </div>              
        <?php
        endif;
    }
    ?>
<?php endif;

?>
     </div>
<!-- TAINACAN: Hiddens para acoes desta view --> 
<input type="hidden" name="categories_id" id='event_object_categories_id_<?php echo $object_id; ?>' value="<?php echo implode(',', $categories_id); ?>">   
<input type="hidden" name="properties_terms_radio" id='event_properties_terms_radio' value="<?php echo implode(',', $properties_terms_radio); ?>">
<input type="hidden" name="properties_terms_tree" id='event_properties_terms_tree' value="<?php echo implode(',', $properties_terms_tree); ?>">
<input type="hidden" name="properties_terms_selectbox" id='event_properties_terms_selectbox' value="<?php echo implode(',', $properties_terms_selectbox); ?>">
<input type="hidden" name="properties_terms_checkbox" id='event_properties_terms_checkbox' value="<?php echo implode(',', $properties_terms_checkbox); ?>">
<input type="hidden" name="properties_terms_multipleselect" id='event_properties_terms_multipleselect' value="<?php echo implode(',', $properties_terms_multipleselect); ?>">
<input type="hidden" name="properties_terms_treecheckbox" id='event_properties_terms_treecheckbox' value="<?php echo implode(',', $properties_terms_treecheckbox); ?>">
<input type="hidden" id="object_classifications_event_<?php echo $object_id; ?>" name="object_classifications" value="<?php echo implode(',', $categories_id); ?>">    
<?php if (isset($all_ids)): ?>
    <input type="hidden" name="properties_id" value="<?php echo $all_ids; ?>">
    <?php


 endif; 


