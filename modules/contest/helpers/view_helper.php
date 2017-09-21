<?php
include_once(dirname(__FILE__).'/../models/ranking/ranking_model.php'); 

class ViewHelper {
    
    /**
     * 
     * @param int $parent O post parent dos filhos
     */
    public function getChildrenItems($ranking,$parent,$depth,$collection_id,$root_post) {
        $direct_children = get_children(array('post_parent' => $parent));
        if(is_array($direct_children)): ?>    
            <?php foreach ($direct_children as $child): 
                ?>
                <?php $position =  get_post_meta($child->ID, 'socialdb_object_contest_position', true);?>
                <li class="commentLi commentstep-<?php echo $depth ?>" data-commentid="<?php echo $child->ID ?>">
                <?php if($child->post_status != 'draft'): ?>    
                <table class="form-comments-table">
                    <tr>
                       <td class="row">
                            <div class="col-md-1 no-padding user-thumb-container">
                                <div class="comment-avatar">
                                    
                                    <img src="<?php echo get_template_directory_uri() ?>/modules/<?php echo MODULE_CONTEST ?>/libraries/images/<?php echo ($position=='positive') ? 'smile': 'rage' ?>-face.png">
                                    <?php echo get_avatar($child->post_author) ?>
                                </div>    
                            </div>
                            <div class="col-md-10 no-padding" style="line-height: 0.9;margin-left: 4%;">
                                <p><b><?php echo get_user_by('id', $child->post_author)->display_name ?></b></p>
                                <?php echo $child->post_date_gmt ?>
                            </div>
                            <div class="col-md-12">
                                 <div id="comment-<?php echo $object->ID; ?>" 
                                     data-commentid="<?php echo $child->ID; ?>" 
                                     class="comment">
                                     <span id="text-comment-<?php echo $child->ID; ?>"><?php echo $child->post_title; ?></span>
                                 </div>     
                            </div>
                            <div class="col-md-12 argument-operation">
                                <div class="score col-md-1 no-padding">
                                    <span  onclick="contest_save_vote_binary_up('<?php echo $ranking; ?>', '<?php echo $child->ID; ?>')" >
                                        <span class="glyphicon glyphicon-thumbs-up"></span>
                                        <span id="constest_score_<?php echo $child->ID; ?>_up"><?php echo $this->get_counter_ranking($ranking, $child->ID,'count_up') ?></span>
                                    </span>
                                    &nbsp;&nbsp;
                                    <span  onclick="contest_save_vote_binary_down('<?php echo $ranking; ?>', '<?php echo $child->ID; ?>')">
                                        <span class="glyphicon glyphicon-thumbs-down"></span>
                                        <span id="constest_score_<?php echo $child->ID; ?>_down"><?php echo $this->get_counter_ranking($ranking, $child->ID,'count_down') ?></span>
                                    </span>    
                                </div>
                                <div class="argument-operation-links  col-md-11 no-padding">
                                    <span class="pull-left">
                                        <a href="javascript:void(0)" onclick="open_positive_argument('<?php echo $child->ID; ?>')"><?php _e('Favorable argument','tainacan') ?></a>
                                        &nbsp;&nbsp;
                                        <a href="javascript:void(0)" onclick="open_negative_argument('<?php echo $child->ID; ?>')"><?php _e('Counter argument','tainacan') ?></a>
                                    </span>
                                    <span class="link-center" >
                                        <a href="javascript:void(0)" 
                                           onclick="open_properties_argument(<?php echo $child->ID; ?>)"
                                           id="open_properties_argument_<?php echo $child->ID; ?>">
                                            <span class="glyphicon glyphicon-chevron-down"/><?php _e('More information','tainacan') ?>
                                        </a>
                                        <a href="javascript:void(0)" 
                                           onclick="hide_properties_argument(<?php echo $child->ID; ?>)"
                                           id="hide_properties_argument_<?php echo $child->ID; ?>"
                                           style="display:none;"><span class="glyphicon glyphicon-chevron-up"/><?php _e('Hide information','tainacan') ?></a>
                                    </span>  
                                    <span class="pull-right">
                                        <ul class="nav navbar-bar navbar-right item-menu-container" style="margin-top: -15px;" >
                                            <li class="dropdown open_item_actions" id="action-<?php echo $child->ID;; ?>">
                                                <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                                    <?php echo ViewHelper::render_icon("config", "png", _t('Item options')); ?>
                                                </a>
                                                <ul class="dropdown-menu pull-right dropdown-show new-item-menu" role="menu" id="item-menu-options">    
                                                    <?php if($child->post_author   ==  get_current_user_id()): ?>
                                                        <li style="margin-bottom: 0px;" class="tainacan-museum-clear"><a href="javascript:void(0)" onclick="edit_comment( '<?php echo $child->ID; ?>')">
                                                               <span class="glyphicon glyphicon-edit"></span>&nbsp;<?php _e('Edit','tainacan') ?>
                                                            </a>
                                                        </li>
                                                        <li style="margin-bottom: 0px;" class="tainacan-museum-clear">
                                                            <a href="javascript:void(0)" onclick="delete_comment('<?php echo $child->ID; ?>')">
                                                                <span class="glyphicon glyphicon-trash"></span>&nbsp;<?php _e('Remove','tainacan') ?>
                                                            </a>
                                                        </li>
                                                    <?php else: ?>
                                                        <li style="margin-bottom: 0px;" class="tainacan-museum-clear">
                                                            <a href="javascript:void(0)"   onclick="report_abuse('<?php echo $child->ID; ?>') ">
                                                                <span class="glyphicon glyphicon-alert"></span>&nbsp;<?php _e('Report abuse','tainacan') ?>
                                                            </a>
                                                        </li>
                                                     <?php endif; ?>   
                                                        <li  style="margin-bottom: 0px;width: 200px;" class="item-redesocial">
                                                            <a href="javascript:void(0)" 
                                                                            onclick="share_comment( '<?php echo $child->ID; ?>','<?php  echo htmlentities($child->post_title) ?>',$('#url-argument').val())" >
                                                                <span class="glyphicon glyphicon-share"></span>&nbsp;<?php _e('Share','tainacan') ?>
                                                            </a>
                                                        </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </span>
                                </div>
                            </div>
                            <div style="display:none" class='col-md-12 positive-argument' id='positive-argument-<?php echo $child->ID; ?>'>
                                <form class="form_positive_argument">
                                    <input type="hidden" id="collection_postive_argument_id" name="collection_id" value="<?php echo $collection_id; ?>">
                                    <input type="hidden" name="classifications" value="">
                                    <input type="hidden" name="root_argument" value="<?php echo $root_post; ?>">
                                    <input type="hidden" name="argument_parent" value="<?php echo $child->ID; ?>">
                                    <input type="hidden" name="operation" value="add_reply_positive">
                                    <span class="col-md-1 no-padding user-thumb-container">
                                        <img src="<?php echo get_template_directory_uri() ?>/modules/<?php echo MODULE_CONTEST ?>/libraries/images/smile-face.png">
                                    </span>
                                    <div class="col-md-1 no-padding user-thumb-container">
                                        <div class="comment-avatar">
                                            <?php echo get_avatar(get_current_user_id()) ?>
                                        </div>    
                                    </div>
                                    <div class="col-md-8 text-area-container">
                                        <textarea rows="4" class="form-control positive_argument" name="positive_argument"></textarea>
                                    </div>
                                    <div class="col-md-2  no-padding">
                                        <span 
                                            style="cursor: pointer;"
                                            onclick="$('#positive-argument-<?php echo $child->ID; ?>').fadeOut()" 
                                            class="pull-right glyphicon glyphicon-remove"></span>
                                        <br><br><br>
                                        <button type="submit" class="btn btn-primary btn-block"><?php _e('Send','tainacan') ?></button>
                                    </div>
                                </form>    
                            </div>
                            <div  style="display:none" class='col-md-12 negative-argument' id='negative-argument-<?php echo $child->ID; ?>'>
                                <form class="form_negative_argument">
                                    <input type="hidden" name="collection_id" value="<?php echo $collection_id; ?>">
                                    <input type="hidden" name="classifications" value="">
                                    <input type="hidden" name="root_argument" value="<?php echo $root_post; ?>">
                                    <input type="hidden" name="argument_parent" value="<?php echo $child->ID; ?>">
                                    <input type="hidden" name="operation" value="add_reply_negative">
                                    <span class="col-md-1 no-padding user-thumb-container">
                                        <img src="<?php echo get_template_directory_uri() ?>/modules/<?php echo MODULE_CONTEST ?>/libraries/images/rage-face.png">
                                    </span>
                                    <div class="col-md-1 no-padding user-thumb-container">
                                        <div class="comment-avatar">
                                            <?php echo get_avatar(get_current_user_id()) ?>
                                        </div>    
                                    </div>
                                    <div class="col-md-8 text-area-container">
                                        <textarea rows="4" class="form-control negative_argument" name="negative_argument" ></textarea>
                                    </div>
                                    <div  class="col-md-2  no-padding">
                                        <span 
                                            style="cursor: pointer;"
                                            onclick="$('#negative-argument-<?php echo $child->ID; ?>').fadeOut()" 
                                            class="pull-right glyphicon glyphicon-remove"></span>
                                        <br><br><br>
                                        <button type="submit" class="btn btn-primary btn-block"><?php _e('Send','tainacan') ?></button>
                                    </div>
                                </form>    
                            </div>
                           <div style="display:none" class='col-md-12' id='properties-argument-<?php echo $child->ID; ?>'></div>
                            <!--
                            <div class="comment-timestamp"><?php echo $child->post_date_gmt ?></div>
                            <div class="comment-user"><?php echo get_user_by('id', $child->post_author)->display_name ?></div>
                            <div class="comment-avatar">
                                 <?php echo get_avatar($object->post_author) ?>
                            </div>
                            <div id="comment-<?php echo $child->ID ?>" data-commentid="<?php echo $child->ID ?>" class="comment commentstep-<?php echo $depth ?>">
                                <span class="label label-<?php echo ($position=='positive') ? 'success': 'danger' ?>">
                                        <span id="thumbs-<?php echo $child->ID; ?>" class="glyphicon glyphicon-thumbs-<?php echo ($position=='positive') ? 'up': 'down' ?>"></span>
                                        <span id="constest_score_<?php echo $child->ID; ?>"><?php echo $this->get_counter_ranking($ranking, $child->ID) ?></span>
                                 </span>&nbsp;  
                                 <span id='popover_positive_<?php echo $child->ID; ?>'></span><span id='popover_negative_<?php echo $child->ID; ?>'></span>
                                <span id="text-comment-<?php echo $child->ID; ?>"><?php echo $child->post_title ?></span>
                                <div id="commentactions-<?php echo $child->ID ?>" class="comment-actions">
                                    <div class="btn-group" role="group" aria-label="...">
                                        <button type="button" onclick="contest_save_vote_binary_up('<?php echo $ranking; ?>', '<?php echo $child->ID; ?>')" class="btn btn-success btn-sm">
                                            <span class="glyphicon glyphicon-menu-up"></span>
                                        </button>
                                        <button type="button" onclick="contest_save_vote_binary_down('<?php echo $ranking; ?>', '<?php echo $child->ID; ?>')" class="btn btn-danger btn-sm">
                                            <span class="glyphicon glyphicon-menu-down"></span>
                                        </button>
                                    </div>                                
                                    <div class="btn-group" role="group" aria-label="...">
                                         <button type="button" 
                                                        onclick="showSingleObject('<?php echo $child->ID; ?>', $('#src').val())" 
                                                        class="btn btn-default btn-sm"><span class="glyphicon glyphicon-zoom-in"></span> <?php _e('Page','tainacan') ?></button>
                                        <?php if($child->post_author   ==  get_current_user_id()): ?>
                                        <button type="button" 
                                                onclick="edit_comment( '<?php echo $child->ID; ?>')" 
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
                            -->
                        </td>
                    </tr>
                </table>
                <?php else: ?>     
                <table class="form-comments-table">  
                    <tr>
                            <td class="row">
                                 <div class="col-md-1 no-padding user-thumb-container">
                                     <div class="comment-avatar">
                                         <?php echo get_avatar($child->post_author) ?>
                                     </div>    
                                 </div>
                                 <div class="col-md-11 no-padding" style="line-height: 0.9;">
                                     <p><b><?php echo get_user_by('id', $child->post_author)->display_name ?></b></p>
                                     <?php echo $child->post_date_gmt ?>
                                 </div>
                                 <div class="col-md-12">
                                    <div id="comment-<?php echo $child->ID ?>" data-commentid="<?php echo $child->ID ?>" class="comment commentstep-<?php echo $depth ?>">
                                        <span id="text-comment-<?php echo $child->ID; ?>"><i><b><?php echo __('Comment sent to the trash','tainacan') ?></b></i></span>
                                    </div>
                                 </div>    
                             </td>
                    </tr>
                </table>    
                <?php endif; ?>        
                </li>
            <?php
                $this->getChildrenItems($ranking,$child->ID, $depth+1,$collection_id,$root_post);
            ?>
            <?php endforeach;  ?>
        <?php 
        endif; 
    }
    
    /**
     * 
     * @param type $ranking_id
     * @param type $item_id
     * @return type
     */
    public function get_counter_ranking($ranking_id,$item_id,$type = null) {
        $ranking_model = new RankingContestModel;
        $count = $ranking_model->count_votes_binary($ranking_id, $item_id);
        //var_dump($count,$type);
        if(isset($type) && isset($count[$type])){
            return $count[$type];
        }
        return $count['count_up'] - $count['count_down'];
    }
    
    public static function render_icon($icon, $ext = "svg", $alt="") {
        if ($alt == "") { $alt = __( ucfirst( $icon ), 'tainacan'); }
        $img_path = get_template_directory_uri() . '/libraries/images/icons/icon-'.$icon.'.'.$ext;

        return "<img alt='$alt' title='$alt' src='$img_path' />";
    }
    
    /**
     * 
     * @param type $param
     */
    public function getRelated($collection_id,$item_id) {
        $property = get_term_by('id',get_post_meta($collection_id, 'socialdb_collection_property_related_id',true), 'socialdb_property_type');
        if($property){
           $metas = get_post_meta($item_id,'socialdb_property_'.$property->term_id);
           if($metas){
               foreach ($metas as $value) {
                   $related = get_post($value);
                   ?>
                    <li>
                        <a target="_blank" href="<?php echo get_the_permalink($collection_id).'?item='.$related->post_name ?>">
                        <i><?php echo $related->post_title ?></i>
                        </a>
                    </li>
                   <?php
               }
           }else{
               echo _e('No questions related','tainacan');
           }
        }
    }
    
   
} // ViewHelper