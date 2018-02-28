<?php
/*
 * View responsavel em mostrar os arquivos de um objeto
 */
include_once ('js/list_file_js.php');
?>
<!-- TAINACAN: mostra os os arquivos de um objeto, o icone do arquivo 
   é gerado automaticamente pelo wordpress, apenas o título que colocamos manualmente
-->

<?php
// Hook para exibir posts relacionados ao item (acima dos anexos)
if (has_action('header_sidebar_item')) {
    do_action( 'header_sidebar_item', $object_id );
}
?>

<div> 
    <h3 id="text_title">
        <?php _e('Attachments', 'tainacan'); ?>
        <br>
    </h3>    
    <hr>

    <?php if (!$attachments['posts']): ?>
        <div id="no_file_<?php echo $object_id; ?>" class="text-center">
            <?php _e('No Attachments','tainacan'); ?>
        </div>
    <?php else: ?>
        <div id="files_<?php echo $object_id; ?>" style="text-align: center;">
            <?php
            $counter = 0;
            foreach ($attachments['posts'] as $attachment):
                $attachment_url = wp_get_attachment_url( $attachment->ID );
                $attach_description = wp_trim_words($attachment->post_content,15);

                echo '<div class="col-md-12" style="display:block; margin-bottom: 20px;">';
                    if(wp_attachment_is_image( $attachment->ID )): ?>
                        <a onclick="showSlideShow('<?php echo $counter ?>')" class="btn btn-default btn-sm" style="border: none; display: block">
                            <img src="<?php echo $attachment_url ?>" alt="" class="img-responsive" style="display: inline-block; max-height: 180px;"/>
                        </a>
                    <?php else: ?>
                        <a class="btn btn-default" href="<?php echo $attachment_url; ?>"
                           download="<?php echo $attachment->post_title; ?>" onclick="downloadItem('<?php echo $attachment->ID; ?>');">
                            <?php echo $attachment->post_title; ?>
                        </a>
                    <?php
                    endif;
                echo '</div>';

                $counter++;
            endforeach;
            ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Hook pronto para adicionar novas informações à sidebar do item (abaixo dos anexos)
if (has_action('footer_sidebar_item')) {
    do_action( 'footer_sidebar_item', $object_id );
}
?>

<!-- TAINACAN: modal padrao bootstrap aberto via javascript pelo seu id, slideshow anexos -->
<div class="modal fade" id="modalSlideShow" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _t('Attachments',1); ?> </h4>
            </div>
            <div class="modal-body">
                <div id="carousel-attachment" class="col-md-12">
                    <?php if(isset($attachments['image']) && is_array($attachments['image'])): ?>
                        <?php foreach ($attachments['image'] as $image): ?>
                            <div id="div_show_image_modal" style="height: 750px">
                                <img style="max-height: 100%;" src="<?= $image->guid ?>" class="img-responsive"/>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>