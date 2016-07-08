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

             <!-- TAINACAN:  modal para compartilahr o item -->
             <div class="modal fade" id="modal_share_network<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                 <div class="modal-dialog">
                     <div class="modal-content">
                         <form name="form_share_item<?php echo get_the_ID() ?>" id="form_share_item<?php echo get_the_ID() ?>" method="post">
                             <div class="modal-header">
                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                 <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-share"></span>&nbsp;<?php _e('Share', 'tainacan'); ?></h4>
                             </div>
                             <div class="modal-body">
                                 <div class="row">
                                     <div class="col-md-6">
                                         <?php echo __('Post it on: ', 'tainacan'); ?><br>
                                         <a target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>&amp;text=<?php echo htmlentities(get_the_title()); ?>&amp;via=socialdb"><?php echo ViewHelper::render_icon('twitter-square', 'png', 'Twitter'); ?></a>&nbsp;
                                         <a onclick="redirect_facebook('<?php echo get_the_ID() ?>');" href="#"><?php echo ViewHelper::render_icon('facebook-square', 'png', 'Facebook'); ?></a>&nbsp;
                                         <a target="_blank" href="https://plus.google.com/share?url=<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>"><?php echo ViewHelper::render_icon('googleplus-square', 'png', 'Google Plus'); ?></a>
                                         <br><br>
                                         <?php echo __('Link: ', 'tainacan'); ?>
                                         <input type="text" id="link_object_share<?php echo get_the_ID() ?>" class="form-control" value="<?php echo get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name; ?>" />
                                     </div>
                                     <div class="col-md-6">
                                         <?php echo __('Embed it: ', 'tainacan'); ?>
                                         <textarea id="embed_object<?php echo get_the_ID() ?>" class="form-control" rows="5"><?php echo '<iframe width="1024" height="768" src="' . get_the_permalink($collection_id) . '?item=' . get_post(get_the_ID())->post_name . '" frameborder="0"></iframe>'; ?></textarea>
                                     </div>
                                 </div>
                                 <hr>
                                 <div class="row">
                                     <div class="col-md-12">
                                         <?php echo __('Email: ', 'tainacan'); ?><br>
                                         <input type="text" id="email_object_share<?php echo get_the_ID() ?>" class="form-control" /><br>
                                         <?php echo __('Share in other collection: ', 'tainacan'); ?><br>
                                         <input type="text" id="collections_object_share<?php echo get_the_ID() ?>" class="form-control autocomplete_share_item" >
                                         <input type="hidden" name="collection_id" id="collections_object_share<?php echo get_the_ID() ?>_id"  >
                                         <input type="hidden" name="collection_id" id="collections_object_share<?php echo get_the_ID() ?>_url"  >
                                     </div>
                                 </div>
                             </div>
                             <div class="modal-footer">
                                 <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                                 <button onclick="send_share_item(<?php echo get_the_ID() ?>);" type="button" class="btn btn-primary"><?php echo __('Send', 'tainacan'); ?></button>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>

             <li >
                 <div class="item-redesocial">
                     <a id="modal_network<?php echo get_the_ID(); ?>" onclick="showModalShareNetwork(<?php echo get_the_ID(); ?>)">
                         <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                     </a>
                 </div>
             </li>

             <li style="display:none;">
                 <div class="item-redesocial">
                     <a id="popover_network<?php echo get_the_ID(); ?>" rel="popover" data-placement="left"
                        onclick="showPopover(<?php echo get_the_ID(); ?>)">
                         <div style="cursor:pointer;" data-icon="&#xe00b;"></div>
                     </a>
                 </div>
             </li>

             <?php if (get_option('collection_root_id') != $collection_id): ?>
                 <!--------------------------- DELETE AND EDIT OBJECT------------------------------------------------>
                 <?php if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()): ?>
                     <li>
                         <a onclick="delete_object('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>')" style="cursor: pointer;" class="remove">
                             <span class="glyphicon glyphicon-trash"></span>
                         </a>
                     </li>
                     <li>
                         <!-- onclick="edit_object">
                         </a-->
                         <a style="cursor: pointer;" onclick="edit_object_item('<?php echo get_the_ID() ?>')">
                             <span class="glyphicon glyphicon-pencil"></span>
                         </a>
                     </li>
                 <?php else: ?>
                     <?php
                     // verifico se eh oferecido a possibilidade de remocao do objeto vindulado
                     if (verify_allowed_action($collection_id, 'socialdb_collection_permission_delete_object')):
                         ?>
                         <li>
                             <a onclick="show_report_abuse('<?php echo get_the_ID() ?>')" href="#" class="report_abuse">
                                 <span class="glyphicon glyphicon-warning-sign"></span>
                             </a>
                         </li>
                     <?php endif; ?>
                     <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                     <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                         <div class="modal-dialog">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                     <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                 </div>
                                 <div class="modal-body">
                                     <?php echo __('Describe why the object: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                     <textarea id="observation_delete_object<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                 </div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan'); ?></button>
                                     <button onclick="report_abuse_object('<?= __('Delete Object') ?>', '<?= __('Are you sure to remove the object: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                 </div>
                                 </form>
                             </div>
                         </div>
                     </div>
                 <?php endif; ?>
                 <!--li><a href=""><span class="glyphicon glyphicon-comment"></span></a></li-->
             <?php else: ?>
                 <!-- TAINACAN: mostra o modal da biblioteca sweet alert para exclusao de uma colecao -->
                 <?php if ($is_moderator || get_post(get_the_ID())->post_author == get_current_user_id()): ?>
                     <li>
                         <a onclick="delete_collection('<?= __('Delete Object', 'tainacan') ?>', '<?= __('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" href="#" class="remove">
                             <span class="glyphicon glyphicon-trash"></span>
                         </a>
                     </li>
                 <?php else: ?>
                     <!-- TAINACAN: mostra o modal para reportar abusao em um item, gerando assim um evento -->
                     <li>
                         <a onclick="show_report_abuse('<?php echo get_the_ID() ?>')" href="#" class="report_abuse">
                             <span class="glyphicon glyphicon-warning-sign"></span>
                         </a>
                     </li>
                     <!-- TAINACAN:  modal padrao bootstrap para reportar abuso -->
                     <div class="modal fade" id="modal_delete_object<?php echo get_the_ID() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                         <div class="modal-dialog">
                             <div class="modal-content">
                                 <div class="modal-header">
                                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                     <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Report Abuse', 'tainacan'); ?></h4>
                                 </div>
                                 <div class="modal-body">
                                     <?php echo __('Describe why the collection: ', 'tainacan') . get_the_title() . __(' is abusive: ', 'tainacan'); ?>
                                     <textarea id="observation_delete_collection<?php echo get_the_ID() ?>" class="form-control"></textarea>
                                 </div>
                                 <div class="modal-footer">
                                     <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'tainacan'); ?></button>
                                     <button onclick="report_abuse_collection('<?php _e('Delete Collection', 'tainacan') ?>', '<?php _e('Are you sure to remove the collection: ', 'tainacan') . get_the_title() ?>', '<?php echo get_the_ID() ?>', '<?= mktime() ?>', '<?php echo get_option('collection_root_id') ?>')" type="button" class="btn btn-primary"><?php echo __('Delete', 'tainacan'); ?></button>
                                 </div>
                                 </form>
                             </div>
                         </div>
                     </div>
                 <?php endif; ?>
             <?php endif; ?>
         </ul>
     </div>
 </div>