<?php
include_once ('../../../../../wp-config.php');
include_once ('../../../../../wp-load.php');
include_once ('../../../../../wp-includes/wp-db.php');
include_once ('js/create_js.php');
include_once ('js/extract_metadata_js.php');
/**
 * View responsavel em inicializar o envio de arquivos locais
 */
?>
<div class="row" id='upload_container' >
    <div class="col-md-3 menu-left-size" id='container-menu-left' 
         style="background: white;border: 3px solid #E8E8E8;font: 13px Arial;min-height: 500px;padding-right: 15px;">
        <h3>
           <?php _e('Through this feature you can enter:', 'tainacan'); ?>
        </h3>
        <ul style="list-style: circle;">
                <li<?php _e('Files (ex. pdf, jpg, png, etc)', 'tainacan'); ?></li>
                <li><?php _e('Sites', 'tainacan'); ?></li>
                <li><?php _e('Video from Youtube or Vimeo', 'tainacan'); ?></li>
                <li><?php _e('Multiple videos from a Youtube Channel or Vimeo Channel', 'tainacan'); ?></li>
                <li><?php _e('Images from Flickr, Facebook or Instagram', 'tainacan'); ?></li>
        </ul>   
    </div> 
    <div class="col-md-9" style=" background: white;border: 3px solid #E8E8E8;margin-left: 15px;">
        <h3>
          <?php _e('Add new item - Insert URL','tainacan') ?>
          <button onclick="back_main_list();" class="btn btn-default pull-right">
            <?php _e('Cancel','tainacan') ?>
          </button>
        </h3>
        <hr>
        <div id='container-input' class="col-md-12" style="padding-left: 0px;">
            <div class="form-group">
                <label class="col-sm-1" for="item_url_import_all"><?php _e('URL', 'tainacan'); ?>:</label>
                <div class="col-sm-9" onpaste="verify_import_type();">
                    <input type="text" onkeyup="verify_import_type()"
                       name="item_url_import_all" id="item_url_import_all" class="form-control"
                       placeholder="<?php _e('Type here', 'tainacan'); ?>" />
                </div>
                <div class="col-sm-2 no-padding">
                    <button type="button" style="padding-bottom: 5px;padding-top: 5px;" onclick="importAll_verify()" class="col-sm-12 no-padding btn btn-primary"><?php _e('Submit', 'tainacan'); ?></button>
                    <div class="col-sm-12 no-padding">
                        <input type="checkbox" id="extract_metadata" name="extract_metadata">&nbsp;<?php _e('Extract metadata','tainacan') ?>
                    </div>    
                </div>
            </div>
            
        </div> 
        <div class="col-md-12" style="margin-top: 15px;">
            <center>
                <p>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/facebook.png' ?>" id="facebook_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/youtube.png' ?>" id="youtube_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/flickr.png' ?>" id="flickr_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/vimeo.png' ?>" id="vimeo_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/instagram.png' ?>" id="instagram_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/files.png' ?>" id="files_import_icon"/>
                   <img class="grayscale" src="<?php echo get_template_directory_uri() . '/libraries/images/icons_import/sites.png' ?>" id="sites_import_icon"/>
               </p>
            </center>
        </div>
    </div>    
</div>    
<!-- TAINACAN: MAPEAMENTO DOS ITEMS -->
<div style="margin-bottom: 50px;" id='editor_items'>
    <!-- MOSTRA O EDITOR DOS ITENS AO FINAL DO UPLOAD -->
</div>
<div class="modal fade" id="modal_mapping_metadata" tabindex="-1" role="dialog" aria-labelledby="modal_mapping_metadata" aria-hidden="true">
    <div class="modal-dialog">
        <div  class="modal-content" id="mapping_metadata_content"> 
            
        </div>
    </div>
</div>
