<?php 
/*
 * 
 * View responsavel em mostrar as todas as propriedas para edicao e exclusao no dropdpw da view list
 * 
 * 
 */
include_once ('js/show_list_event_properties_edit_remove_js.php'); ?>
<?php
$has_property = false;
if (isset($property_object)):
    $has_property = true;
    ?>
    <?php
    foreach ($property_object as $property) {
        $object_id = $property['metas']['object_id'];
        $category_root_id = $property['metas']['socialdb_property_created_category'];
        ?>  
        <li>&nbsp;&nbsp;<?php echo $property['name']; ?>&nbsp;&nbsp;
            <?php  
            // verifico se o metadado pode ser alterado
            if(verify_allowed_action($collection_id,'socialdb_collection_permission_edit_property_object')): ?>    
                <button onclick="show_edit_object_property_form('<?php echo $object_id ?>','<?php echo $property['id'] ?>')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-edit"></span></button>&nbsp;
            <?php endif; ?>    
             <?php  
            // verifico se o metadado pode ser alterado
            if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_property_object')): ?>    
                <button onclick="show_confirmation_delete_property_object_event('<?php echo $object_id ?>','<?php echo $property['id'] ?>','<?php echo $property['name'] ?>','<?php echo $category_root_id ?>')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span></button>
            <?php endif; ?> 
        </li>   
    <?php } ?>
<?php endif; ?>

<?php
if (isset($property_data)):
    $has_property = true;
    foreach ($property_data as $property) {
        $object_id = $property['metas']['object_id'];
        $category_root_id = $property['metas']['socialdb_property_created_category'];
        ?>    
       <li>&nbsp;&nbsp;<?php echo $property['name']; ?>&nbsp;&nbsp;
            <?php  
            // verifico se o metadado pode ser alterado
            if(verify_allowed_action($collection_id,'socialdb_collection_permission_edit_property_data')): ?>    
                <button onclick="show_edit_data_property_form('<?php echo $object_id ?>','<?php echo $property['id'] ?>')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-edit"></span></button>&nbsp;
            <?php endif; ?>    
             <?php  
            // verifico se o metadado pode ser alterado
            if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_property_data')): ?>    
                <button onclick="show_confirmation_delete_property_data_event('<?php echo $object_id ?>','<?php echo $property['id'] ?>','<?php echo $property['name'] ?>','<?php echo $category_root_id ?>')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span></button>
            <?php endif; ?>     
       </li>         
 <?php } ?>
            <?php
        endif;
        if (!$has_property) {
            ?>
    <li>&nbsp;&nbsp;<?php echo __('No properties added!','tainacan'); ?></li>   
    <?php
}

//Metadado termo
if($property_term){
    foreach ($property_term as $property) {
        $category_root_id = $property['metas']['socialdb_property_created_category'];
        ?>
        <li>&nbsp;&nbsp;<?php echo $property['name']; ?>&nbsp;&nbsp;
            <?php
            // verifico se o metadado pode ser alterado
            if(verify_allowed_action($collection_id,'socialdb_collection_permission_edit_property_term')): ?>
                <button onclick="show_edit_term_property_form('<?php echo $object_id ?>','<?php echo $property['id'] ?>')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-edit"></span></button>&nbsp;
            <?php endif; ?>
            <?php
            // verifico se o metadado pode ser apagado
            if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_property_term')): ?>
                <button onclick="show_confirmation_delete_property_data_event('<?php echo $object_id ?>','<?php echo $property['id'] ?>','<?php echo $property['name'] ?>','<?php echo $category_root_id ?>')" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-remove"></span></button>
            <?php endif; ?>
        </li>
        <?php
    }
}



