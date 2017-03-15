<?php
include_once(dirname(__FILE__).'/../models/ranking/ranking_model.php'); 

class ViewHelper {
    
    /**
     * 
     * @param int $parent O post parent dos filhos
     */
    public function getChildrenItems($ranking,$parent,$depth) {
        $direct_children = get_children(array('post_parent' => $parent));
        if(is_array($direct_children)): ?>    
            <?php foreach ($direct_children as $child): 
                ?>
                <?php $position =  get_post_meta($child->ID, 'socialdb_object_contest_position', true);?>
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
                $this->getChildrenItems($ranking,$child->ID, $depth+1);
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