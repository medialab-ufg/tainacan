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
                                        <span id="constest_score_<?php echo $child->ID; ?>"><?php echo $this->get_counter_ranking($ranking->term_id, $child->ID) ?></span>
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
    public function get_counter_ranking($ranking_id,$item_id) {
        $ranking_model = new RankingContestModel;
        $count = $ranking_model->count_votes_binary($ranking_id, $item_id);
        return $count['count_up'] - $count['count_down'];
    }
} // ViewHelper