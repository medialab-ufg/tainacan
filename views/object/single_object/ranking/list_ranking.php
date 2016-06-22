<?php

/*
 * View responsavel em mostrar os rankings de um item especifico
 */
if (!isset($likes) && !isset($binaries) && !isset($stars)): ?>
         
<?php else: 
    include_once ('js/list_ranking_js.php');
    ?>
 <div class="col-md-12">
    <div> 
        <input type="hidden" id="stars_id_<?php echo $object_id; ?>" value="<?php echo $stars_id; ?>">
        <?php if (!isset($likes) && !isset($binaries) && !isset($stars)): ?>
            <div id="no_rankings_<?php echo $object_id; ?>">
                <?php _e('No rankings available','tainacan'); ?>
            </div>
        <?php else: ?>
            <div id="stars_<?php echo $object_id; ?>">
                <?php if (isset($stars)): ?>    
                    <?php foreach ($stars as $star) { ?>
                        <input type="hidden" id="star_<?php echo $object_id; ?>_<?php echo $star['id']; ?>" value="<?php echo $star['value']; ?>">
                        <span><b><?php echo $star['name']; ?></b></span>&nbsp;(<?php echo __('Votes: ','tainacan') ?>
                        <span id="counter_<?php echo $object_id; ?>_<?php echo $star['id']; ?>"><?php echo $star['count'] ?></span>)
                        <div id="rating_<?php echo $object_id; ?>_<?php echo $star['id']; ?>"></div>
                    <?php } ?>
                <?php endif; ?>
            </div>   
            <div id="likes_<?php echo $object_id; ?>">
                <?php if (isset($likes)): ?>    
                    <?php foreach ($likes as $like) { ?>
                        <input type="hidden" id="like_<?php echo $object_id; ?>_<?php echo $like['id']; ?>" value="<?php echo $like['value']; ?>">
                        <span><b><?php echo $like['name']; ?></b></span>&nbsp;
                        <br>
                        <a style="text-decoration: none;font-size: 20px;" onclick="save_vote_like( '<?php echo $like['id']; ?>', '<?php echo $object_id; ?>')" href="#">
                            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                        </a>
                        <span id="counter_<?php echo $object_id; ?>_<?php echo $like['id']; ?>"><?php echo $like['count'] ?></span>
                    <?php } ?>
                <?php endif; ?>
            </div>   
            <div id="binaries_<?php echo $object_id; ?>">
                <?php if (isset($binaries)): ?>    
                    <?php foreach ($binaries as $binary) { ?>
                        <span><b><?php echo $binary['name']; ?></b></span>&nbsp;<br>
                        <a style="text-decoration: none;font-size: 20px;" onclick="save_vote_binary_up('<?php echo $binary['id']; ?>', '<?php echo $object_id; ?>')" href="#counter_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>">
                            <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                        </a> 
                        <span id="counter_up_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['count_up'] ?></span>  
                        <a style="text-decoration: none;font-size: 20px;" onclick="save_vote_binary_down('<?php echo $binary['id']; ?>', '<?php echo $object_id; ?>')" href="#counter_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>">
                            <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                        </a>
                        <span id="counter_down_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['count_down'] ?></span>
                        (<b> <?php _e('Score: ','tainacan') ?><span id="score_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['value'] ?></span> </b>)

                    <?php } ?>
                <?php endif; ?>
            </div>   
        <?php endif; ?>

    </div>
 </div>
<?php endif; ?>