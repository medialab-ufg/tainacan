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
         <h4 class="item-display-title">
             <a href="<?php echo get_collection_item_href($collection_id); ?>"
                onclick="<?php get_item_click_event($collection_id, get_the_ID() )?>">
                 <?php the_title(); ?>
             </a>
         </h4>
     </div>
 </div>