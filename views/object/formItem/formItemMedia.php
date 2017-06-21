<?php
//$habilitateMedia = get_post_meta($collection_id, 'socialdb_collection_habilitate_media', true);
$css = ($habilitateMedia == 'true') ? 'width: 72%; margin-left: 15px;margin-right: 10px;padding-left: 15px;' : 'margin-left:1%;width: 98%;padding-left:15px;';
?>
<div class="row" style="background-color: #f1f2f2">
    <div style="display: none;margin-left:1%;padding-left:15px;min-height:500px;"
         class="col-md-12 menu_left_loader">
        <center>
            <img src="<?php echo get_template_directory_uri() . '/libraries/images/catalogo_loader_725.gif' ?>">
            <h4><?php _e('Loading metadata...', 'tainacan') ?></h4>
        </center>
    </div>
    <div style="<?php echo $css ?>"
         class="col-md-12 menu_left">
        <h4>
            <?php if (has_action('label_add_item')): ?>
                <?php do_action('label_add_item', $object_name) ?>
            <?php elseif(isset($formItem->title)): ?>
                <?php echo $formItem->title ?>
            <?php else: ?>
                <?php _e('Create new item - Write text', 'tainacan') ?>
            <?php endif; ?>
            <!--button type="button" onclick="back_main_list();"class="btn btn-default pull-right"-->
            <a class="btn btn-default pull-right" href="<?php echo get_the_permalink($collection_id) ?>">
                <b><?php _e('Back', 'tainacan') ?></b>
            </a>
            <br>
            <small id="draft-text"></small>
        </h4>
        <hr>
        <!-- ABAS e TODO FORMULARIO -->
        <?php $formItem->start($collection_id,$ID,$properties,true) ?>
    </div>
</div>
