<?php
/*
 * View responsavel em mostrar as classificacoes
 */
?>
 <!-- TAINACAN: envelopa todas as classifcacoes do objeto em questao -->
<div> 
    <?php if(!isset($categories)&&!isset($properties)&&!isset($tags)): ?>
        <div id="no_classifications_<?php echo $object_id; ?>">
        <?php _e('No classifications, drag here categories, properties and tags','tainacan'); ?>
        </div>
    <?php else: ?>
        <!-- TAINACAN: mostra as categorias se existir, estilo copiado do dynatree  -->
        <div id="categories_classifications_<?php echo $object_id; ?>">
            <?php if(isset($categories)): ?>    
                <?php foreach ($categories as $category) { ?>
                    <span class="dynatree-node dynatree-expanded dynatree-has-children <?= $category['class']; ?> dynatree-exp-e dynatree-ico-e ">
                        <span class="dynatree-icon"></span>
                        <?= $category['term']->name; ?> 
                         <?php  
                        // verifico se eh oferecido a possibilidade de remocao da categoria
                        if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_classification',$object_id)): ?>    
                        <a onclick="remove_event_category_classication('<?= __('Remove classification','tainacan') ?>','<?= __('Are you sure to remove the classification: ','tainacan'), $category['term']->name; ?>','<?= $category['term']->term_id ?>','<?php echo $object_id; ?>','<?php echo mktime(); ?>')" 
                           href="#object_<?php echo $object_id; ?>"><span class="glyphicon glyphicon-remove-circle"></span>
                        </a>
                        <?php endif; ?>
                    </span>
                <?php }  ?>
            <?php endif; ?>
        </div>   
          <!-- TAINACAN: mostra apenas as propriedades se existir, estilo copiado do dynatree  -->
        <div id="properties_classifications_<?php echo $object_id; ?>">
            <?php if(isset($properties)): ?>   
                <?php foreach ($properties as $property) { ?>
                    <span class="dynatree-node dynatree-expanded dynatree-has-children <?= $property['class']; ?> dynatree-exp-e dynatree-ico-e ">
                        <span class="dynatree-icon"></span>
                        <?= $property['relationship_name']; ?> 
                         <?php  
                        // verifico se eh oferecido a possibilidade de remcao do objeto vindulado
                        if(verify_allowed_action($collection_id,'socialdb_collection_permission_edit_property_object_value',$object_id)): ?>    
                        <a onclick="remove_event_property_classication('<?= __('Remove classification','tainacan') ?>','<?= __('Are you sure to remove the classification: ','tainacan'), $property['relationship_name']; ?>','<?= $property['relationship_id'] ?>','<?php echo $object_id; ?>','<?php echo mktime(); ?>','<?= $property['property_id'] ?>')" 
                           href="#object_<?php echo $object_id; ?>"><span class="glyphicon glyphicon-remove-circle"></span>
                        </a>
                        <?php endif; ?>
                    </span>
                <?php }  ?>
            <?php endif; ?>
        </div>   
          <!-- TAINACAN: mostra as tags se existir, estilo copiado do dynatree  -->
           <div id="tags_classifications_<?php echo $object_id; ?>">
            <?php if(isset($tags)): ?>    
                <?php foreach ($tags as $tag) { ?>
                    <span class="dynatree-node dynatree-expanded dynatree-has-children tag_img dynatree-exp-e dynatree-ico-e ">
                        <span class="dynatree-icon"></span>
                        <?= $tag->name ?> 
                         <?php  
                        // verifico se eh oferecido a possibilidade de remcao da tag
                        if(verify_allowed_action($collection_id,'socialdb_collection_permission_delete_classification',$object_id)): ?>    
                        <a onclick="remove_event_tag_classication('<?= __('Remove classification','tainacan') ?>','<?= __('Are you sure to remove the classification: ','tainacan'), $tag->name; ?>','<?= $tag->term_id ?>','<?php echo $object_id; ?>','<?php echo mktime(); ?>')" 
                           href="#object_<?php echo $object_id; ?>"><span class="glyphicon glyphicon-remove-circle"></span>
                        </a>
                         <?php endif; ?>
                    </span>
                <?php }  ?>
            <?php endif; ?>
        </div>   
        
    <?php endif; ?>
    
</div>


