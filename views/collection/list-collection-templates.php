<?php
// Container responsavel em listar as colecoes templates 
?>
<style>
    /** Classe templates na insercao de colecoes **/
    .templates-collections{
        cursor: pointer;
        padding-right: 10px;
        border-bottom-style: solid;
        border-width: 1px;
        border-color: #1b809e;
        padding-top: 10px;
        padding-bottom: 10px;
        margin-top: 5px;
    }

    .templates-collections:hover{
        background-color: #dddddd;
    }
</style>
<?php if(!get_option('disable_empty_collection')||get_option('disable_empty_collection')=='false'): ?>
<div onclick="onClickTemplate('none')" class="row templates-collections" id="default_collection">
    <div class="col-sm-3" >
        <img class="img-responsive" src="<?php echo get_template_directory_uri() ?>/libraries/images/default_thumbnail.png">
    </div>   
    <div class="col-sm-9">
        <h2><?php _e('Empty Collection', 'tainacan') ?></h2>
        <p><?php _e('Create a default collection, extend default metadata from repository and no items inserted', 'tainacan') ?></p>
    </div>   
</div>
<?php endif; ?>
<?php if ($collectionTemplates && is_array($collectionTemplates)): ?>
    <?php foreach ($collectionTemplates as $collectionTemplate) : ?>
        <div onclick="onClickTemplate('<?php echo $collectionTemplate['directory']; ?>')" class="row templates-collections">
            <div class="col-sm-3" >
                <img class="img-responsive" src="<?php echo ($collectionTemplate['thumbnail']) ? $collectionTemplate['thumbnail'] : get_template_directory_uri() . '/libraries/images/default_thumbnail.png' ?>">
            </div>   
            <div class="col-sm-9">
                <h2><?php echo $collectionTemplate['title']; ?></h2>
                <p><?php echo $collectionTemplate['description']; ?></p>
            </div>   
        </div>
    <?php endforeach; ?>
    <?php

 endif; 
