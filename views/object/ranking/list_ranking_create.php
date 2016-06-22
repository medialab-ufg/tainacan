<?php
include_once ('js/list_ranking_create_js.php');
/*
 * View responsavel em mostrar os rankings de uma colecao
 */
?>
  <!-- TAINACAN: div responsavel em mostrar os rankings de uma colecao,scripts acima possibilitam a criacao dos mesmos, 
  nenhum estilo aplicado nas votacoes, apenas o de estrelas que utliza a biblioteca raty para sua montagem, o icone eh da propria biblioteca (assim como todo o html desta votacao)

  -->
    <input type="hidden" id="create_stars_id_<?php echo $object_id; ?>" value="<?php echo $stars_id; ?>">
    <?php if (!isset($likes) && !isset($binaries) && !isset($stars)): ?>
        <input type="hidden" class='hide_rankings' value="true">
    <?php else: ?>
         <!-- TAINACAN: mostra os rankings do tipo estrela -->
            <?php if (isset($stars)): ?>    
                <?php foreach ($stars as $star) { ?>
                <div id="meta-item-<?php echo $star['id']; ?>"  class="form-group"> 
                    <h2>
                       <?php echo $star['name']; ?>
                    </h2>
                    <div>
                        &nbsp;(<?php echo __('Votes: ','tainacan') ?> 
                        <input type="hidden" id="create_star_<?php echo $object_id; ?>_<?php echo $star['id']; ?>" value="<?php echo $star['value']; ?>">
                        <span id="create_counter_<?php echo $object_id; ?>_<?php echo $star['id']; ?>"><?php echo $star['count'] ?></span>)
                         <!-- TAINACAN: div onde eh gerado o html da votacao do tipo raty -->
                         <br>
                        <div id="create_rating_<?php echo $object_id; ?>_<?php echo $star['id']; ?>"></div>
                    </div>
                </div>    
                <?php } ?>
            <?php endif; ?> 
          <!-- TAINACAN: mostra os rankings do tipo like, icone do glyphicons -->
            <?php if (isset($likes)): ?>    
                <?php foreach ($likes as $like) { ?>
                    <div id="meta-item-<?php echo $like['id']; ?>"  class="form-group"> 
                        <h2>
                             <?php echo $like['name']; ?>
                        </h2>
                        <div>
                             &nbsp;
                            <input type="hidden" id="create_like_<?php echo $object_id; ?>_<?php echo $like['id']; ?>" value="<?php echo $like['value']; ?>">
                            <a style="text-decoration: none;font-size: 20px;cursor: pointer;" 
                               onclick="create_save_vote_like( '<?php echo $like['id']; ?>', '<?php echo $object_id; ?>')" 
                               >
                                <span class="glyphicon glyphicon-thumbs-up" ></span>
                            </a>
                            <span id="create_counter_<?php echo $object_id; ?>_<?php echo $like['id']; ?>"><?php echo $like['count'] ?></span>
                        </div>
                    </div>    
                <?php } ?>
            <?php endif; ?>
         <!-- TAINACAN: mostra os rankings do tipo like, icones do glyphicons  -->
            <?php if (isset($binaries)): ?>    
                <?php foreach ($binaries as $binary) { ?>
                    <div id="meta-item-<?php echo $binary['id']; ?>"  class="form-group"> 
                        <h2>
                            <?php echo $binary['name']; ?>
                        </h2>
                        <div>
                        &nbsp;
                            <a style="text-decoration: none;font-size: 20px;" onclick="create_save_vote_binary_up('<?php echo $binary['id']; ?>', '<?php echo $object_id; ?>')" href="#counter_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>">
                                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                            </a> 
                            <span id="create_counter_up_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['count_up'] ?></span>  
                            <a style="text-decoration: none;font-size: 20px;" onclick="create_save_vote_binary_down('<?php echo $binary['id']; ?>', '<?php echo $object_id; ?>')" href="#counter_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>">
                                <span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>
                            </a>
                            <span id="create_counter_down_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['count_down'] ?></span>
                            (<b> <?php _e('Score: ','tainacan') ?><span id="create_score_<?php echo $object_id; ?>_<?php echo $binary['id']; ?>"><?php echo $binary['value'] ?></span> </b>)<br>
                        </div>    
                   </div>
                <?php } ?>
            <?php endif; ?>
    <?php endif;

