<?php
/*
 *
 * View responsavel em mostrar uma categoria especifica
 *
 *
 */

include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/comments_js.php');
?>
    <style>
        .right_column{
            background: white;
            border: 3px solid #E8E8E8;
            min-height: 260px;
            padding: 15px;
            border-top: none;
        }
    </style>
    <div class='right_column' >
        <h3>
            <?php echo __('Comments','tainacan'); ?>
             <button onclick="back_and_clean_url()" id="btn_back_collection" class="btn btn-default pull-right"><?php _e('Back to collection','tainacan') ?></button>
        </h3>
        <hr>
        <div class="row">
               <div id="comments_term"></div>
         </div>
    </div>