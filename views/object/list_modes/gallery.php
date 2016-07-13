 <div class="col-md-3 gallery-view-container top-div <?php if ( ($countLine % 4) == 1 ) { ?> first-el <?php  } ?>"
     <?php if ($collection_list_mode != "gallery"): ?> style="display: none" <?php endif ?> >
     <div class="row">
         <div class="item-thumb">
             <a href="<?php echo get_collection_item_href($collection_id); ?>"
                onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                 <?php echo get_item_thumb_image(get_the_ID()); ?>
             </a>
         </div>
     </div>

     <div class="row title-container">
         <h5 class="item-display-title">
             <a href="<?php echo get_collection_item_href($collection_id); ?>"
                onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                 <?php echo wp_trim_words( get_the_title(), 5 ); ?>
             </a>
         </h5>
         <ul class="item-funcs col-md-5 right">
             <!-- TAINACAN: hidden com id do item -->
             <input type="hidden" class="post_id" name="post_id" value="<?= get_the_ID() ?>">

             <li>
                 <div class="item-redesocial">
                     <a id="modal_network<?php echo get_the_ID(); ?>" onclick="showModalShareNetwork(<?php echo get_the_ID(); ?>)">
                        <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                     </a>
                 </div>
             </li>

             <?php include "edit_btns.php"; ?>

         </ul>
     </div>
 </div>