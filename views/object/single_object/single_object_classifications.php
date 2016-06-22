<?php
/*
 * View responsavel em mostrar as classificacoes
 */
?>

<div> 
    <?php if(!isset($categories)&&!isset($properties)&&!isset($tags)): ?>
        <div id="single_no_classifications_<?php echo $object_id; ?>">
        <?php _e('No classifications, drag here categories, properties and tags','tainacan'); ?>
        </div>
    <?php else: ?>
        <!--div id="single_categories_classifications_<?php echo $object_id; ?>">
            <?php if(isset($categories)): ?>    
                <?php foreach ($categories as $category) { ?>
                    <span class="dynatree-node dynatree-expanded dynatree-has-children <?= $category['class']; ?> dynatree-exp-e dynatree-ico-e ">
                        <span class="dynatree-icon"></span>
                        <?= $category['term']->name; ?> 
                        <a onclick="single_remove_event_category_classication('<?= __('Remove classification','tainacan') ?>','<?= __('Are you sure to remove the classification: ','tainacan'), $category['term']->name; ?>','<?= $category['term']->term_id ?>','<?php echo $object_id; ?>','<?php echo mktime(); ?>')" 
                           href="#object_<?php echo $object_id; ?>"><span class="glyphicon glyphicon-remove-circle"></span>
                        </a>
                    </span>
                <?php }  ?>
            <?php endif; ?>
        </div>   
        <!--- Properties --
        <div id="single_properties_classifications_<?php echo $object_id; ?>">
            <?php if(isset($properties)): ?>    
                <?php foreach ($properties as $property) { ?>
                    <span class="dynatree-node dynatree-expanded dynatree-has-children <?= $property['class']; ?> dynatree-exp-e dynatree-ico-e ">
                        <span class="dynatree-icon"></span>
                        <?= $property['relationship_name']; ?> 
                        <a onclick="single_remove_event_property_classication('<?= __('Remove classification','tainacan') ?>','<?= __('Are you sure to remove the classification: ','tainacan'), $property['relationship_name']; ?>','<?= $property['relationship_id'] ?>','<?php echo $object_id; ?>','<?php echo mktime(); ?>','<?= $property['property_id'] ?>')" 
                           href="#object_<?php echo $object_id; ?>"><span class="glyphicon glyphicon-remove-circle"></span>
                        </a>
                    </span>
                <?php }  ?>
            <?php endif; ?>
        </div>   
        
         <!--- Tags -->
           <div id="single_tags_classifications_<?php echo $object_id; ?>">
            <?php if(isset($tags)&&!empty($tags)): ?>    
                <?php foreach ($tags as $tag) { ?>
                    <span class="dynatree-node dynatree-expanded dynatree-has-children tag_img dynatree-exp-e dynatree-ico-e ">
                        <span class="dynatree-icon"></span>
                        <?= $tag->name ?> 
                        <?php  
                        // verifico se eh oferecido a possibilidade de remcao da tag
                        if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_classification')): ?>    
                        <a onclick="single_remove_event_tag_classication('<?= __('Remove classification','tainacan') ?>','<?= __('Are you sure to remove the classification: ','tainacan'), $tag->name; ?>','<?= $tag->term_id ?>','<?php echo $object_id; ?>','<?php echo mktime(); ?>')" 
                           href="#object_<?php echo $object_id; ?>"><span class="glyphicon glyphicon-remove-circle"></span>
                        </a>
                        <?php endif; ?>
                    </span>
                <?php }  ?>
            <?php 
                else: 
                  echo '<p>'.__('empty field','tainacan').'</p>';
                endif; ?>
        </div>   
        
    <?php endif; ?>
    
</div>


