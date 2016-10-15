<?php require_once(dirname(__FILE__).'/js/question-js.php'); ?>
<?php require_once(dirname(__FILE__).'../../../helpers/view_helper.php'); ?>
<?php $post = get_post($collection_id); ?>
<?php $ranking = get_term_by('name', __('In favor / Against', 'tainacan'),'socialdb_property_type') ?>
<?php $view_helper = new ViewHelper; ?>
<?php 
    $temp = $object;
    while($temp->post_parent!==0){
        $temp = get_post($temp->post_parent);
        $parents[] = $temp;
    }
?>  
<input type="hidden" id="socialdb_permalink_object" name="socialdb_permalink_object" value="<?php echo get_the_permalink($collection_id) . '?item=' . $object->post_name; ?>" />
<input type="hidden" id="item_id" value="<?php echo $object->ID; ?>">
<div class="chatContainer">
    <ol class="breadcrumb item-breadcrumbs" style="padding-top: 10px;">
        <li> <a href="<?php echo get_permalink(get_option('collection_root_id')); ?>"> <?php _e('Repository', 'tainacan') ?> </a> </li>
        <li> <a href="#" onclick="backToMainPageSingleItem()"> <?php echo $post->post_title; ?> </a> </li>
        <?php 
           $parents = (isset($parents) && is_array($parents)) ? array_reverse($parents) : [];
           foreach ($parents as $parent) {
               ?>
                <li> <a href="#" onclick="showSingleObject('<?php echo $parent->ID; ?>', $('#src').val())" > <?php echo $parent->post_title; ?> </a> </li>
               <?php
           }
        ?>
        <li class="active"> <?php echo $object->post_title; ?> </li>
    </ol>
    <br>
    <div class="chatHistoryContainer">

        <ul class="formComments">
            <li class="commentLi commentstep-1" data-commentid="<?php echo $object->ID; ?>">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp"><?php echo $object->post_date_gmt ?></div></td>
                        <td><div class="comment-user"><?php echo get_user_by('id', $object->post_author)->display_name ?></div></td>
                        <td>
                            <div class="comment-avatar">
                                <?php echo get_avatar($object->post_author) ?>
                            </div>
                        </td>
                        <td style="padding-right: 15px;">
                            <div id="comment-<?php echo $object->ID; ?>" 
                                 data-commentid="<?php echo $object->ID; ?>" 
                                 class="comment comment-step1">
                                <h5>   
                                    &nbsp;<b id="text-comment-<?php echo $object->ID; ?>"><?php echo $object->post_title; ?></b>
                                </h5>    
                                <div id="commentactions-<?php echo $object->ID; ?>" class="comment-actions">                             
                                    <div class="btn-group" role="group" aria-label="...">
                                        <!--button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-comment"></span> <?php _e('Reply','tainacan') ?></button-->
                                        <?php if($object->post_author   ==  get_current_user_id()): ?>
                                        <button type="button" 
                                                onclick="edit_comment( '<?php echo $object->ID; ?>')" 
                                                class="btn btn-default btn-sm">
                                            <span class="glyphicon glyphicon-edit"></span> <?php _e('Edit','tainacan') ?>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span> <?php _e('Remove','tainacan') ?></button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-alert"></span><?php _e('Report abuse','tainacan') ?></button>
                                        <?php endif; ?>
                                        <button type="button" 
                                                onclick="share_comment( '<?php echo $object->ID; ?>','<?php  echo htmlentities($object->post_title) ?>',$('#url-argument').val())" 
                                                class="btn btn-default btn-sm">
                                            <span class="glyphicon glyphicon-share"></span><?php _e('Share','tainacan') ?>
                                        </button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                        <td>
                           <button 
                               onclick="add_answer('<?php echo $object->ID; ?>')"
                               class="btn btn-default pull-right">
                               <span class="glyphicon glyphicon-plus"></span>
                           </button> 
                        </td>
                    </tr>
                </table>
            </li>  
            <?php
                $parent = $object->ID;
                $depth = 2;
                $direct_children = get_children(array('post_parent' => $parent));
                if(is_array($direct_children)): ?>    
                    <?php foreach ($direct_children as $child): 
                        ?>
                        <?php $position =  get_post_meta($child->ID, 'socialdb_object_contest_position', true);  ?>
                        <li class="commentLi commentstep-<?php echo $depth ?>" data-commentid="<?php echo $child->ID ?>">
                        <?php if($child->post_status != 'draft'): ?>    
                        <table class="form-comments-table">
                            <tr>
                                <td><div class="comment-timestamp"><?php echo $child->post_date_gmt ?></div></td>
                                <td><div class="comment-user"><?php echo get_user_by('id', $child->post_author)->display_name ?></div></td>
                                <td>
                                    <div class="comment-avatar">
                                         <?php echo get_avatar($object->post_author) ?>
                                    </div>
                                </td>
                                <td>
                                    <div id="comment-<?php echo $child->ID ?>" data-commentid="<?php echo $child->ID ?>" class="comment commentstep-<?php echo $depth ?>">
                                        <span class="label label-info">
                                                <span id="constest_score_<?php echo $child->ID; ?>"><?php echo $view_helper->get_counter_ranking($ranking->term_id, $child->ID) ?></span>
                                         </span>&nbsp;  
                                        <span id="text-comment-<?php echo $child->ID; ?>"><?php echo $child->post_title ?></span>
                                        <div id="commentactions-<?php echo $child->ID ?>" class="comment-actions">
                                            <div class="btn-group" role="group" aria-label="...">
                                                <button type="button" onclick="contest_save_vote_binary_up('<?php echo $ranking->term_id; ?>', '<?php echo $child->ID; ?>')" class="btn btn-success btn-sm">
                                                    <span class="glyphicon glyphicon-menu-up"></span>
                                                </button>
                                                <button type="button" onclick="contest_save_vote_binary_down('<?php echo $ranking->term_id; ?>', '<?php echo $child->ID; ?>')" class="btn btn-danger btn-sm">
                                                    <span class="glyphicon glyphicon-menu-down"></span>
                                                </button>
                                            </div>                                
                                            <div class="btn-group" role="group" aria-label="...">
                                                <button type="button" 
                                                        onclick="showSingleObject('<?php echo $child->ID; ?>', $('#src').val())" 
                                                        class="btn btn-default btn-sm"><span class="glyphicon glyphicon-zoom-in"></span> <?php _e('Page','tainacan') ?></button>
                                                <?php if($child->post_author   ==  get_current_user_id()): ?>
                                                <button type="button" 
                                                        onclick="edit_comment( '<?php echo $child->ID; ?>',true)" 
                                                        class="btn btn-default btn-sm"><span class="glyphicon glyphicon-edit"></span> <?php _e('Edit','tainacan') ?></button>
                                                <button type="button" 
                                                        onclick="delete_comment('<?php echo $child->ID; ?>') "
                                                        class="btn btn-danger btn-sm">
                                                    <span class="glyphicon glyphicon-trash"></span> <?php _e('Remove','tainacan') ?>
                                                </button>
                                                <?php else: ?>
                                                <button type="button" 
                                                         onclick="report_abuse('<?php echo $child->ID; ?>') "
                                                        class="btn btn-default btn-sm">
                                                    <span class="glyphicon glyphicon-alert"></span>&nbsp;<?php _e('Report abuse','tainacan') ?></button>
                                                <?php endif; ?>
                                                <button type="button" 
                                                        onclick="share_comment( '<?php echo $child->ID; ?>','<?php  echo htmlentities($child->post_title) ?>',$('#url-argument').val())" 
                                                        class="btn btn-default btn-sm">
                                                    <span class="glyphicon glyphicon-share"></span><?php _e('Share','tainacan') ?>
                                                </button>
                                            </div>                                  
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php else: ?>     
                        <table class="form-comments-table">  
                            <tr>
                                <td><div class="comment-timestamp"><?php echo $child->post_date_gmt ?></div></td>
                                <td><div class="comment-user"><?php echo get_user_by('id', $child->post_author)->display_name ?></div></td>
                                <td>
                                    <div class="comment-avatar">
                                         <?php echo get_avatar($object->post_author) ?>
                                    </div>
                                </td>
                                <td>
                                    <div id="comment-<?php echo $child->ID ?>" data-commentid="<?php echo $child->ID ?>" class="comment commentstep-<?php echo $depth ?>">
                                        <span id="text-comment-<?php echo $child->ID; ?>"><i><b><?php echo __('Comment sent to the trash','tainacan') ?></b></i></span>
                                    </div>
                                </td>
                            </tr>
                        </table>    
                        <?php endif; ?>        
                        </li>
                    <?php
                        $view_helper->getChildrenItems($ranking,$child->ID, $depth+1);
                    ?>
                    <?php endforeach;  ?>
            <?php 
                endif; 
            ?>
        </ul>
    </div>
    <div class="input-group input-group-sm chatMessageControls">
        <h4>
            <?php _e('Related questions','tainacan') ?>
        </h4>
        <hr>
        <?php
          $view_helper->getRelated($collection_id, $object->ID);
        ?>
    </div>
</div>
<?php include_once 'modals.php'; ?>
<script>
    var rootComment = '<?php echo $object->ID; ?>';
    $(document).ready(function () {

        initUIEvents();

    });

    function initUIEvents() {

        $(".comment").unbind().click(function () {

            var currentComment = $(this).data("commentid");

            //$("#commentactions-" + currentComment).slideDown("fast");

        });


        $(".commentLi").hover(function () {

            var currentComment = $(this).data("commentid");
            $("#commentactions-" + currentComment).slideDown("fast");    
            $("#comment-" + currentComment).stop().animate({opacity: "1", backgroundColor: "#f8f8f8", borderLeftWidth: "4px"}, {duration: 100, complete: function () {}});

        }, function () {

            var currentComment = $(this).data("commentid");

            $("#comment-" + currentComment).stop().animate({opacity: "1", backgroundColor: "#fff", borderLeftWidth: "1px"}, {duration: 100, complete: function () {}});

            $("#commentactions-" + currentComment).slideUp("fast");

        });

    }
</script>    

