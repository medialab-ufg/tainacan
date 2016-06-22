<?php
include_once ('js/list_ranking_js.php');
/*
 * View responsavel em mostrar os rankings de uma colecao
 */
?>
  <!-- TAINACAN: div responsavel em mostrar os rankings de uma colecao,scripts acima possibilitam a criacao dos mesmos, 
  nenhum estilo aplicado nas votacoes, apenas o de estrelas que utliza a biblioteca raty para sua montagem, o icone eh da propria biblioteca (assim como todo o html desta votacao) -->
<div>
    <input type="hidden" id="single_stars_id_<?php echo $object_id; ?>" value="<?php echo $stars_id; ?>">
    <?php if (!isset($likes) && !isset($binaries) && !isset($stars)): ?>
        <!-- TAINACAN: se nao existir nenhum ranking -->
        <div id="single_no_rankings_<?php echo $object_id; ?>">
            <?php _e('No rankings available','tainacan'); ?>
        </div>
    <?php else: ?>
         <!-- TAINACAN: mostra os rankings do tipo estrela -->
        <div id="single_stars_<?php echo $object_id; ?>">
            <?php if (isset($stars)): ?>    
                <?php foreach ($stars as $star) { ?>
                    <input type="hidden" id="single_star_<?php echo $object_id; ?>_<?php echo $star['id']; ?>" value="<?php echo $star['value']; ?>">
                    <span><b><?php echo $star['name']; ?></b></span>&nbsp;(<?php echo __('Votes: ','tainacan') ?>
                    <span id="single_counter_<?php echo $object_id; ?>_<?php echo $star['id']; ?>"><?php echo $star['count'] ?></span>)
                     <!-- TAINACAN: div onde eh gerado o html da votacao do tipo raty -->
                    <div id="single_rating_<?php echo $object_id; ?>_<?php echo $star['id']; ?>"></div>
                <?php } ?>
            <?php endif; ?>
        </div>   
          <!-- TAINACAN: mostra os rankings do tipo like, icone do glyphicons -->
        <div id="single_likes_<?php echo $object_id; ?>">
            <?php if (isset($likes)): ?>    
                <?php foreach ($likes as $like) { ?>
                    <input type="hidden" id="single_like_<?php echo $object_id; ?>_<?php echo $like['id']; ?>" value="<?php echo $like['value']; ?>">
                    <span><b><?php echo $like['name']; ?></b></span>&nbsp;
                    <br>
                    <a style="text-decoration: none;font-size: 20px;" onclick="single_save_vote_like( '<?php echo $like['id']; ?>', '<?php echo $object_id; ?>')" href="#">
                        <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                    </a>
                    <span id="single_counter_<?php echo $object_id; ?>_<?php echo $like['id']; ?>"><?php echo $like['count'] ?></span><br>
                <?php } ?>
            <?php endif; ?>
        </div>  
         <!-- TAINACAN: mostra os rankings do tipo like, icones do glyphicons  -->
        <div id="single_binaries_<?php echo $object_id; ?>">
            <?php if (isset($binaries)): ?>    
                <?php foreach ($binaries as $binary) { ?>
                    <span><b><?php echo $binary['name']; ?></b></span>&nbsp;<br>
                    <a style="text-decoration: none;font-size: 20px;" onclick="single_save_vote_binary_up('<?php echo $binary['id']; ?>', '<?php echo $object_id; ?>')" href="#counter_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>">
                        <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                    </a> 
                    <span id="single_counter_up_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['count_up'] ?></span>  
                    <a style="text-decoration: none;font-size: 20px;" onclick="single_save_vote_binary_down('<?php echo $binary['id']; ?>', '<?php echo $object_id; ?>')" href="#counter_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>">
                        <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                    </a>
                    <span id="single_counter_down_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['count_down'] ?></span>
                    (<b> <?php _e('Score: ','tainacan') ?><span id="single_score_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['value'] ?></span> </b>)<br>
                    
                <?php } ?>
            <?php endif; ?>
        </div>   
    <?php endif; ?>

</div>

