<?php
include_once ('js/list_ranking_js.php');
/*
 * View responsavel em mostrar o ranking ou a ordenacao selecionada no dropdown
 */
?>
  <!-- TAINACAN: div responsavel em mostrar os rankings de uma colecao,scripts acima possibilitam a criacao dos mesmos, 
  nenhum estilo aplicado nas votacoes, apenas o de estrelas que utliza a biblioteca raty para sua montagem, o icone eh da propria biblioteca (assim como todo o html desta votacao)

  -->
<div> 
    <input type="hidden" id="single_stars_id_<?php echo $object_id; ?>" value="<?php echo $stars_id; ?>">
    <?php if (!isset($likes) && !isset($binaries) && !isset($stars)): ?>
        <?php if(isset($property_data)): ?>
        <!-- TAINACAN: mostra o valor da proriedade de dados -->
        <div id="property_data_<?php echo $object_id; ?>">
            <?php echo $property_data['name']; ?>:
            <?php if(!empty($property_data['value'])): echo $property_data['value']; else: echo '('.__('empty field','tainacan').')'; endif; ?>
        </div>
        <?php elseif(isset($recent)): ?>
        <!-- TAINACAN: mostra o valor da proriedade de dados -->
        <div id="recent_<?php echo $object_id; ?>">
            <?php // echo $recent['value']; ?>
        </div>
        <?php elseif(isset($type)): ?>
        <!-- TAINACAN: mostra o valor da proriedade de dados -->
        <div id="type_<?php echo $object_id; ?>">
            <strong><?php _e('Type','tainacan') ?>:</strong> <?php echo $type['value']; ?>
        </div>
         <?php elseif(isset($format)): ?>
        <!-- TAINACAN: mostra o valor da proriedade de dados -->
        <div id="format_<?php echo $object_id; ?>">
            <strong><?php _e('Format','tainacan') ?>:</strong> <?php echo $format['value']; ?>
        </div>
        <?php elseif(isset($popular)): ?>
        <!-- TAINACAN: mostra o total de comentarios -->
        <div id="popular_<?php echo $object_id; ?>">
            <strong><?php _e('Comments','tainacan') ?>:</strong> <?php echo $popular['value']; ?>
        </div>
        <?php elseif(isset($license)): ?>
        <!-- TAINACAN: mostra o total de comentarios -->
        <div id="license_<?php echo $object_id; ?>">
            <strong><?php _e('License','tainacan') ?>:</strong> <?php echo $license['value']; ?>
        </div>
        <?php endif; ?>
    <?php else: ?>
         <!-- TAINACAN: mostra os rankings do tipo estrela -->
        <div id="single_stars_<?php echo $object_id; ?>" class="single_stars">
            <?php if (isset($stars)): ?>    
                <?php foreach ($stars as $star) { ?>
                    <input type="hidden" id="single_star_<?php echo $object_id; ?>_<?php echo $star['id']; ?>" value="<?php echo $star['value']; ?>">
                    <!--span><!--b><?php echo $star['name']; ?></b></span>&nbsp;(<?php echo __('Votes: ') ?>
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
                    <!--span><b><?php echo $like['name']; ?></b></span>&nbsp;
                    <br-->
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
                    <!--span><b><?php echo $binary['name']; ?></b></span>&nbsp;<br-->
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

