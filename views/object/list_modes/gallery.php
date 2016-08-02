<div class="col-md-3 gallery-view-container top-div <?php if ( ($countLine % 4) == 1 ) { ?> first-el <?php  } ?>"
     <?php if ($collection_list_mode != "gallery"): ?> style="display: none" <?php endif ?> >
     <div class="row">
         <div class="item-thumb">
             <a href="<?php echo get_collection_item_href($collection_id); ?>"
                onclick="<?php get_item_click_event($collection_id, $curr_id )?>">
                 <?php echo get_item_thumb_image($curr_id); ?>
             </a>
         </div>
     </div>

     <div class="row title-container">
         <h5 class="item-display-title">
             <a href="<?php echo get_collection_item_href($collection_id); ?>"
                onclick="<?php get_item_click_event($collection_id, $curr_id )?>">
                 <?php echo wp_trim_words( get_the_title(), 4 ); ?>
             </a>
         </h5>
         
         <div class="gallery-metadata">
             <div class="col-md-5 no-padding">
                 <?php if (get_option('collection_root_id') != $collection_id): ?>                
                     <!-- TAINACAN: container(AJAX) que mostra o html com os rankings do objeto-->
                     <div id="r_gallery_<?php echo $curr_id ?>" class="rankings-container"></div>                     
                 <?php endif; ?>
             </div>
             <ul class="item-funcs col-md-6 right">
                 <input type="hidden" class="post_id" name="post_id" value="<?= $curr_id ?>">
                 <li>
                     <a id="modal_network<?php echo $curr_id; ?>" onclick="showModalShareNetwork(<?php echo $curr_id; ?>)">
                         <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                     </a>
                 </li>

                 <?php include "edit_btns.php"; ?>
             </ul>

         </div>
         
     </div>
 </div>