<?php require_once(dirname(__FILE__).'/js/item-js.php'); ?>
<?php require_once(dirname(__FILE__).'../../../helpers/view_helper.php'); ?>
<?php $post = get_post($collection_id); ?>
<?php $ranking = get_term_by('name', __('In favor / Against', 'tainacan'),'socialdb_property_type') ?>
<?php $view_helper = new ViewHelper; ?>
<div class="chatContainer">
    <ol class="breadcrumb item-breadcrumbs" style="padding-top: 10px;">
        <li> <a href="<?php echo get_permalink(get_option('collection_root_id')); ?>"> <?php _e('Repository', 'tainacan') ?> </a> </li>
        <li> <a href="#" onclick="backToMainPageSingleItem()"> <?php echo $post->post_title; ?> </a> </li>
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
                        <td>
                            <div id="comment-<?php echo $object->ID; ?>" 
                                 data-commentid="<?php echo $object->ID; ?>" 
                                 class="comment comment-step1">
                                <h5>
                                    <span class="label label-info">
                                        <span id="constest_score_<?php echo $object->ID; ?>"><?php echo $view_helper->get_counter_ranking($ranking->term_id, $object->ID) ?></span>
                                    </span>   
                                    &nbsp;<b id="text-comment-<?php echo $object->ID; ?>"><?php echo $object->post_title; ?></b>
                                </h5>    
                                <div id="commentactions-<?php echo $object->ID; ?>" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" onclick="contest_save_vote_binary_up('<?php echo $ranking->term_id; ?>', '<?php echo $object->ID; ?>')" class="btn btn-success btn-sm">
                                            <span class="glyphicon glyphicon-menu-up"></span>
                                        </button>
                                        <button type="button" onclick="contest_save_vote_binary_down('<?php echo $ranking->term_id; ?>', '<?php echo $object->ID; ?>')" class="btn btn-danger btn-sm">
                                            <span class="glyphicon glyphicon-menu-down"></span>
                                        </button>
                                    </div>                                
                                    <div class="btn-group" role="group" aria-label="...">
                                        <!--button type="button" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-comment"></span> <?php _e('Reply','tainacan') ?></button-->
                                        <?php if($object->post_author   ==  get_current_user_id()): ?>
                                        <button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-edit"></span> <?php _e('Edit','tainacan') ?></button>
                                        <button type="button" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span> <?php _e('Remove','tainacan') ?></button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-alert"></span><?php _e('Report abuse','tainacan') ?></button>
                                        <?php endif; ?>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>  
            <?php
                $view_helper->getChildrenItems($ranking,$object->ID, 2);
            ?>
            <!--li class="commentLi commentstep-1" data-commentid="5">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-5" data-commentid="5" class="comment comment-step1">
                                This is a comment HELLO!!!!
                                <div id="commentactions-5" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>


            <li class="commentLi commentstep-1" data-commentid="6">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-6" data-commentid="6" class="comment">
                                This is a comment HELLO!!!!
                                <div id="commentactions-6" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>


            <li class="commentLi commentstep-2" data-commentid="7">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-7" data-commentid="7" class="comment">
                                This is a comment HELLO!!!!
                                <div id="commentactions-7" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>

            
            <li class="commentLi commentstep-3" data-commentid="8">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-8" data-commentid="8" class="comment">
                                This is a comment HELLO!!!!
                                <div id="commentactions-8" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>

            <li class="commentLi commentstep-3" data-commentid="10">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-10" data-commentid="10" class="comment">
                                This is a comment HELLO!!!!
                                <div id="commentactions-10" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>

            <li class="commentLi commentstep-2" data-commentid="9">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-9" data-commentid="9" class="comment">
                                This is a comment HELLO!!!!
                                <div id="commentactions-9" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>


            <li class="commentLi commentstep-1" data-commentid="11">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-11" data-commentid="11" class="comment comment-step1">
                                This is a comment HELLO!!!!
                                <div id="commentactions-11" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>

            <li class="commentLi commentstep-1" data-commentid="12">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-12" data-commentid="12" class="comment comment-step1">
                                This is a comment HELLO!!!!
                                <div id="commentactions-12" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>

            <li class="commentLi commentstep-1" data-commentid="13">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-13" data-commentid="13" class="comment comment-step1">
                                This is a comment HELLO!!!!
                                <div id="commentactions-13" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li>

            <li class="commentLi commentstep-1" data-commentid="14">
                <table class="form-comments-table">
                    <tr>
                        <td><div class="comment-timestamp">12:03 25/4/2016</div></td>
                        <td><div class="comment-user">Ollie Bott</div></td>
                        <td>
                            <div class="comment-avatar">
                                <img src="">
                            </div>
                        </td>
                        <td>
                            <div id="comment-14" data-commentid="14" class="comment comment-step1">
                                This is a comment HELLO!!!!
                                <div id="commentactions-14" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Reply</button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fa fa-pencil"></i> Edit</button>
                                        <button type="button" class="btn btn-danger btn-sm"<i class="fa fa-trash"></i >Delete</button>
                                    </div>                                
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </li-->
        </ul>
    </div>
</div>
<!-- Positive argument -->
    <div class="modal fade" 
         id="modalReplyPositiveArgument" 
         tabindex="-1" 
         role="dialog" 
         aria-labelledby="modalReplyPositiveArgument" 
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_positive_argument">
                    <div class="modal-header">
                        <button type="button" 
                                style="color:black;" 
                                class="close" 
                                data-dismiss="modal" 
                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" 
                            id="myModalLabel">
                                <?php _e('New argument','tainacan') ?>
                        </h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                            <i><b>"<span id="argument_positive_text"></span>"</b></i>
                        </div>
                        <hr>
                        <div  class="form-group">
                           <label for="exampleInputPassword1"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<?php _e('Describe a positive argument','tainacan') ?></label>
                           <textarea name="positive_argument" class="form-control"   ></textarea>
                        </div>
                        <div  class="form-group" style="margin-bottom: -10px;">
                            <center>
                                <a style="cursor: pointer;" onclick="toggle_additional_information('#properties_positive')">
                                    <?php _e('Additional informations','tainacan') ?><br>
                                    <span class="glyphicon glyphicon-chevron-down"></span>
                                </a>    
                            </center>
                            <div style="display: none;" id="properties_positive"></div>
                        </div>    
                        <input type="hidden" id="collection_postive_argument_id" name="collection_id" value="">
                        <input type="hidden" name="classifications" value="">
                        <input type="hidden" name="argument_parent" value="">
                        <input type="hidden" name="operation" value="add_reply_positive">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                        <button type="submit" class="btn btn-primary" ><?php _e('Save', 'tainacan') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- negative argument -->
    <div class="modal fade" 
         id="modalReplyNegativeArgument" 
         tabindex="-1" 
         role="dialog" 
         aria-labelledby="modalReplyNegativeArgument" 
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_negative_argument">
                    <div class="modal-header">
                        <button type="button" 
                                style="color:black;" 
                                class="close" 
                                data-dismiss="modal" 
                                aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" 
                            id="myModalLabel">
                                <?php _e('New argument','tainacan') ?>
                        </h4>
                    </div>
                    <div class="modal-body"  >
                        <div class="form-group">
                            <i><b>"<span id="argument_negative_text"></span>"</b></i>
                        </div>
                        <hr>
                        <div  class="form-group">
                           <label for="exampleInputPassword1"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;<?php _e('Describe a positive argument','tainacan') ?></label>
                           <textarea name="negative_argument" class="form-control"   ></textarea>
                        </div>
                        <div  class="form-group" style="margin-bottom: -10px;">
                            <center>
                                <a style="cursor: pointer;" onclick="toggle_additional_information('#properties_negative')">
                                    <?php _e('Additional informations','tainacan') ?><br>
                                    <span class="glyphicon glyphicon-chevron-down"></span>
                                </a>    
                            </center>
                            <div style="display: none;" id="properties_negative"></div>
                        </div>    
                        <input type="hidden" name="collection_id" value="">
                        <input type="hidden" name="classifications" value="">
                        <input type="hidden" name="argument_parent" value="">
                        <input type="hidden" name="operation" value="add_reply_negative">
                    </div>
                    <div class="modal-footer">
                        <button style="color:grey;" type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', 'tainacan') ?></button>
                        <button type="submit" class="btn btn-primary" ><?php _e('Save', 'tainacan') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
    $(document).ready(function () {

        initUIEvents();

    });

    function initUIEvents() {

        $(".comment").unbind().click(function () {

            var currentComment = $(this).data("commentid");

            $("#commentactions-" + currentComment).slideDown("fast");

        });


        $(".commentLi").hover(function () {

            var currentComment = $(this).data("commentid");
            //$("#commentactions-" + currentComment).slideDown("fast");    
            $("#comment-" + currentComment).stop().animate({opacity: "1", backgroundColor: "#f8f8f8", borderLeftWidth: "4px"}, {duration: 100, complete: function () {}});

        }, function () {

            var currentComment = $(this).data("commentid");

            $("#comment-" + currentComment).stop().animate({opacity: "1", backgroundColor: "#fff", borderLeftWidth: "1px"}, {duration: 100, complete: function () {}});

            $("#commentactions-" + currentComment).slideUp("fast");

        });

    }
</script>    

