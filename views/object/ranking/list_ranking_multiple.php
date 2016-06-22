<?php
include_once ('js/list_ranking_multiple_js.php');
/*
 * View responsavel em mostrar os rankings de uma colecao
 */
?>
  <!-- TAINACAN: div responsavel em mostrar os rankings de uma colecao,scripts acima possibilitam a criacao dos mesmos, 
  nenhum estilo aplicado nas votacoes, apenas o de estrelas que utliza a biblioteca raty para sua montagem, o icone eh da propria biblioteca (assim como todo o html desta votacao)

  -->
<input type="hidden" id="ids_multiple" value="<?php echo $ids; ?>">
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
                            <input type="hidden" 
                                   id="create_star_<?php echo $object_id; ?>_<?php echo $star['id']; ?>" 
                                   value="<?php echo $star['value']; ?>">
                             <!-- TAINACAN: div onde eh gerado o html da votacao do tipo raty -->
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
                            <input type="hidden" 
                                   id="create_like_<?php echo $object_id; ?>_<?php echo $like['id']; ?>" 
                                   value="<?php echo $like['value']; ?>">
                            &nbsp;
                            <br>
                            <a style="text-decoration: none;cursor:pointer; font-size: 20px;" 
                               onclick="multiple_save_vote_like( '<?php echo $like['id']; ?>', $('#ids_multiple').val())" 
                               >
                               <span class="glyphicon glyphicon-thumbs-up" ></span>
                            </a>
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
                            <a style="text-decoration: none;cursor:pointer;font-size: 20px;" 
                               onclick="multiple_save_vote_binary_up('<?php echo $binary['id']; ?>', $('#ids_multiple').val())" 
                               >
                                <span class="glyphicon glyphicon-thumbs-up" ></span>
                            </a> 
                            <a style="text-decoration: none;cursor:pointer;font-size: 20px;" 
                               onclick="multiple_save_vote_binary_down('<?php echo $binary['id']; ?>', $('#ids_multiple').val())" 
                               >
                                <span class="glyphicon glyphicon-thumbs-down"></span>
                            </a>
                        </div>
                    </div>
                    
                <?php } ?>
            <?php endif; 
     endif; 




